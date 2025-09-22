<?php
// Admin API for Getting Support KPI Data
// Returns comprehensive performance metrics for support team

require_once '../../../core/config.php';
require_once '../../../core/request.php';

header('Content-Type: application/json');

// Check user authentication
session_start();
if (!isset($_SESSION['auth']['user_id']) || $_SESSION['auth']['role'] !== 'user') {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => 'Forbidden: User access required.'
    ]);
    exit;
}

try {
    $period = $_GET['period'] ?? '30days';
    $user_store_id = $_SESSION['auth']['store_id'];
    
    // Calculate date range based on period
    $dateCondition = '';
    switch ($period) {
        case '7days':
            $dateCondition = "AND t.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            break;
        case '30days':
            $dateCondition = "AND t.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            break;
        case '90days':
            $dateCondition = "AND t.created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)";
            break;
        default:
            $dateCondition = "AND t.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    }

    // Get summary statistics for user's store only
    $summary_query = "
        SELECT 
            COUNT(*) as total_tickets,
            SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_tickets,
            SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_tickets,
            SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_tickets,
            SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed_tickets,
            AVG(CASE WHEN resolved_at IS NOT NULL THEN TIMESTAMPDIFF(HOUR, created_at, resolved_at) END) as avg_resolution_time,
            AVG(customer_satisfaction) as avg_satisfaction
        FROM support_tickets t
        WHERE t.store_id = ? {$dateCondition} and t.store_id = ?
    ";
    
    $summary_stmt = $conn->prepare($summary_query);
    $summary_stmt->bind_param("ii", $user_store_id, $user_store_id);
    $summary_stmt->execute();
    $summary_result = $summary_stmt->get_result();
    $summary = $summary_result->fetch_assoc();
    
    // Calculate resolution rate
    $total_resolved = $summary['resolved_tickets'] + $summary['closed_tickets'];
    $resolution_rate = $summary['total_tickets'] > 0 ? round(($total_resolved / $summary['total_tickets']) * 100, 1) : 0;

    // Monthly tickets trend
    $month_query = "
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as total_tickets
        FROM support_tickets t
        WHERE 1=1 {$dateCondition} and t.store_id = ?
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month ASC
    ";
    $month_stmt = $conn->prepare($month_query);
    $month_stmt->bind_param("i", $user_store_id);
    $month_stmt->execute();
    $month_result = $month_stmt->get_result();

    $tickets_result = $month_result;
    $tickets_trend = ['labels' => [], 'data' => []];
    while ($row = $tickets_result->fetch_assoc()) {
        $tickets_trend['labels'][] = $row['month'];
        $tickets_trend['data'][] = (int)$row['total_tickets'];
    }
    
    // Status distribution
    $status_query = "
        SELECT 
            SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open,
            SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
            SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved,
            SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed
        FROM support_tickets t
        WHERE 1=1 {$dateCondition} and t.store_id = ?
    ";
    
    $status_stmt = $conn->prepare($status_query);
    $status_stmt->bind_param("i", $user_store_id);
    $status_stmt->execute();
    $status_result = $status_stmt->get_result();
    $status_data = $status_result->fetch_assoc();
    $status_chart = [
        'labels' => ['Open', 'In Progress', 'Resolved', 'Closed'],
        'data' => [
            (int)$status_data['open'],
            (int)$status_data['in_progress'],
            (int)$status_data['resolved'],
            (int)$status_data['closed']
        ]
    ];
    
    // Priority distribution
    $priority_query = "
        SELECT 
            SUM(CASE WHEN priority = 'low' THEN 1 ELSE 0 END) as low,
            SUM(CASE WHEN priority = 'medium' THEN 1 ELSE 0 END) as medium,
            SUM(CASE WHEN priority = 'high' THEN 1 ELSE 0 END) as high,
            SUM(CASE WHEN priority = 'urgent' THEN 1 ELSE 0 END) as urgent
        FROM support_tickets t
        WHERE 1=1 {$dateCondition} and t.store_id = ?
    ";
    
    $priority_stmt = $conn->prepare($priority_query);
    $priority_stmt->bind_param("i", $user_store_id);
    $priority_stmt->execute();
    $priority_result = $priority_stmt->get_result();
    $priority_data = $priority_result->fetch_assoc();
    $priority_chart = [
        'labels' => ['Low', 'Medium', 'High', 'Urgent'],
        'data' => [
            (int)$priority_data['low'],
            (int)$priority_data['medium'],
            (int)$priority_data['high'],
            (int)$priority_data['urgent']
        ],
        'colors' => ['#28a745', '#ffc107', '#dc3545', '#6f42c1']
    ];
    
    // Response time trends
    $trend_query = "
        SELECT 
            DATE_FORMAT(t.created_at, '%Y-%m-%d') as date_label,
            AVG(TIMESTAMPDIFF(HOUR, t.created_at, m.created_at)) as avg_response_time
        FROM support_tickets t
        LEFT JOIN support_messages m ON t.ticket_id = m.ticket_id AND m.sender_type = 'agent'
        WHERE 1=1 {$dateCondition} and t.store_id = ?
        GROUP BY DATE_FORMAT(t.created_at, '%Y-%m-%d')
        ORDER BY date_label ASC
    ";
    
    $trend_stmt = $conn->prepare($trend_query);
    $trend_stmt->bind_param("i", $user_store_id);
    $trend_stmt->execute();
    $trend_result = $trend_stmt->get_result();
    $response_time_data = ['labels' => [], 'data' => []];
    while ($row = $trend_result->fetch_assoc()) {
        $response_time_data['labels'][] = date('M j', strtotime($row['date_label']));
        $response_time_data['data'][] = round($row['avg_response_time'] ?? 0, 1);
    }
    
    // Volume trends
    $volume_query = "
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m-%d') as date_label,
            COUNT(*) as ticket_count
        FROM support_tickets t
        WHERE 1=1 {$dateCondition} and t.store_id = ?
        GROUP BY DATE_FORMAT(created_at, '%Y-%m-%d')
        ORDER BY date_label ASC
    ";
    
    $volume_stmt = $conn->prepare($volume_query);
    $volume_stmt->bind_param("i", $user_store_id);
    $volume_stmt->execute();
    $volume_result = $volume_stmt->get_result();
    $volume_data = ['labels' => [], 'data' => []];
    while ($row = $volume_result->fetch_assoc()) {
        $volume_data['labels'][] = date('M j', strtotime($row['date_label']));
        $volume_data['data'][] = (int)$row['ticket_count'];
    }
    
    // Previous period for comparison
    $prev_period_condition = '';
    switch ($period) {
        case '7days':
            $prev_period_condition = "AND t.created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY) AND t.created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)";
            break;
        case '30days':
            $prev_period_condition = "AND t.created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND t.created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)";
            break;
        case '90days':
            $prev_period_condition = "AND t.created_at >= DATE_SUB(NOW(), INTERVAL 180 DAY) AND t.created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)";
            break;
    }
    
    $prev_summary_query = "
        SELECT 
            COUNT(*) as total_tickets,
            AVG(CASE WHEN resolved_at IS NOT NULL THEN TIMESTAMPDIFF(HOUR, created_at, resolved_at) END) as avg_resolution_time,
            AVG(customer_satisfaction) as avg_satisfaction
        FROM support_tickets t
        WHERE 1=1 {$prev_period_condition} and t.store_id = ?       
    ";
    
    $prev_summary_stmt = $conn->prepare($prev_summary_query);
    $prev_summary_stmt->bind_param("i", $user_store_id);
    $prev_summary_stmt->execute();
    $prev_summary_result = $prev_summary_stmt->get_result();
    $prev_summary = $prev_summary_result->fetch_assoc();
    
    // Metrics
    $metrics = [];
    
    // Total tickets change
    $ticket_change = $prev_summary['total_tickets'] > 0 ? 
        round((($summary['total_tickets'] - $prev_summary['total_tickets']) / $prev_summary['total_tickets']) * 100, 1) : 0;
    $metrics[] = [
        'name' => 'Total Tickets',
        'current' => $summary['total_tickets'],
        'previous' => $prev_summary['total_tickets'],
        'change' => $ticket_change
    ];
    
    // Response time change
    $response_change = $prev_summary['avg_resolution_time'] > 0 ? 
        round((($summary['avg_resolution_time'] - $prev_summary['avg_resolution_time']) / $prev_summary['avg_resolution_time']) * 100, 1) : 0;
    $metrics[] = [
        'name' => 'Avg Response Time',
        'current' => round($summary['avg_resolution_time'] ?? 0, 1) . 'h',
        'previous' => round($prev_summary['avg_resolution_time'] ?? 0, 1) . 'h',
        'change' => -$response_change // Negative is good for response time
    ];

    // Satisfaction change
    $satisfaction_change = $prev_summary['avg_satisfaction'] > 0 ? 
        round((($summary['avg_satisfaction'] - $prev_summary['avg_satisfaction']) / $prev_summary['avg_satisfaction']) * 100, 1) : 0;
    $metrics[] = [
        'name' => 'Customer Satisfaction',
        'current' => round($summary['avg_satisfaction'] ?? 0, 1) . '/5',
        'previous' => round($prev_summary['avg_satisfaction'] ?? 0, 1) . '/5',
        'change' => $satisfaction_change
    ];

    echo json_encode([
        'status' => 'success',
        'message' => 'KPI data retrieved successfully',
        'http_code' => 200,
        'data' => [
            'summary' => [
                'total_tickets' => (int)$summary['total_tickets'],
                'open_tickets' => (int)$summary['open_tickets'],
                'in_progress_tickets' => (int)$summary['in_progress_tickets'],
                'resolved_tickets' => (int)$summary['resolved_tickets'],
                'closed_tickets' => (int)$summary['closed_tickets'],
                'avg_response_time' => round($summary['avg_resolution_time'] ?? 0, 1),
                'resolution_rate' => $resolution_rate,
                'satisfaction_score' => round($summary['avg_satisfaction'] ?? 0, 1)
            ],
            'charts' => [
                'status' => $status_chart,
                'priority' => $priority_chart,
                'response_time' => $response_time_data,
                'volume' => $volume_data,
                'tickets_trend' => $tickets_trend
            ],
            'metrics' => $metrics
        ]
    ]);

} catch (Exception $e) {
    error_log("Admin get KPI data error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error: ' . $e->getMessage(),
        'data' => null,
        'http_code' => 500
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
