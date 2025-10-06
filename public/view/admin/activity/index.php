<?php
// System activity monitoring page for admin
?>

<div class="main-container" id="main-container">
    <div class="header-section">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-2">System Activity</h2>
                <p class="text-muted mb-0">Monitor system performance and user activities</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="refreshActivity()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <button class="btn btn-outline-success" onclick="exportActivity()">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>
        </div>
    </div>

    <div class="main-content">
        <!-- Activity Stats Cards -->
        <div class="stats-container mb-4">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Active Sessions</div>
                    <div class="stat-icon" style="background-color: rgba(67, 97, 238, 0.1); color: var(--primary);">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="stat-value" id="active-sessions">Loading...</div>
                <div class="stat-change change-up">
                    <i class="fas fa-arrow-up"></i>
                    Currently online
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Orders Today</div>
                    <div class="stat-icon" style="background-color: rgba(46, 204, 113, 0.1); color: var(--success);">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
                <div class="stat-value" id="orders-today">Loading...</div>
                <div class="stat-change change-up">
                    <i class="fas fa-arrow-up"></i>
                    Orders placed today
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Revenue Today</div>
                    <div class="stat-icon" style="background-color: rgba(76, 201, 240, 0.1); color: var(--accent);">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
                <div class="stat-value" id="revenue-today">Loading...</div>
                <div class="stat-change change-up">
                    <i class="fas fa-arrow-up"></i>
                    Today's earnings
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">New Users Today</div>
                    <div class="stat-icon" style="background-color: rgba(243, 156, 18, 0.1); color: var(--warning);">
                        <i class="fas fa-user-plus"></i>
                    </div>
                </div>
                <div class="stat-value" id="new-users-today">Loading...</div>
                <div class="stat-change change-up">
                    <i class="fas fa-arrow-up"></i>
                    Registered today
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-section mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <select class="form-select" id="activity-type-filter" onchange="filterActivity()">
                        <option value="">All Activities</option>
                        <option value="login">Login</option>
                        <option value="order">Order</option>
                        <option value="product">Product</option>
                        <option value="payment">Payment</option>
                        <option value="support">Support</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="user-role-filter" onchange="filterActivity()">
                        <option value="">All Roles</option>
                        <option value="user">Business Owner</option>
                        <option value="supplier">Supplier</option>
                        <option value="courier">Courier</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" id="date-filter" onchange="filterActivity()">
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" id="search-activity" placeholder="Search activities..." onkeyup="filterActivity()">
                </div>
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="table-container mb-4">
            <div class="table-header">
                <h3>Recent Activity</h3>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="loadActivityData()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button class="btn btn-outline-success btn-sm" onclick="exportActivityLog()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
            <div class="table-wrapper">
                <div class="activity-timeline" id="activity-timeline">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

      

        <!-- Error Logs -->
        <div class="table-container">
            <div class="table-header">
                <h3>System Errors</h3>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-danger btn-sm" onclick="clearErrorLogs()">
                        <i class="fas fa-trash"></i> Clear Logs
                    </button>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Error Type</th>
                            <th>Message</th>
                            <th>User</th>
                            <th>Severity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="error-logs-body">
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.activity-timeline {
    max-height: 600px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 16px;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 600;
    margin-bottom: 4px;
}

.activity-description {
    color: #666;
    font-size: 14px;
    margin-bottom: 4px;
}

.activity-meta {
    font-size: 12px;
    color: #999;
}

.activity-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-login { background-color: #e3f2fd; color: #1976d2; }
.badge-order { background-color: #e8f5e8; color: #388e3c; }
.badge-product { background-color: #fff3e0; color: #f57c00; }
.badge-payment { background-color: #fce4ec; color: #c2185b; }
.badge-support { background-color: #f3e5f5; color: #7b1fa2; }
.badge-error { background-color: #ffebee; color: #d32f2f; }
</style>

<script>
let activityData = [];
let filteredActivity = [];
let performanceData = {};

// Load activity data
document.addEventListener('DOMContentLoaded', function() {
    loadActivityData();
    loadSystemStats();
    loadErrorLogs();

});

function loadActivityData() {
    new GetRequest({
        getUrl: 'controller/admin/activity/get-logs.php',
        params: {},
        showLoading: false,
        showSuccess: false,
        callback: (err, data) => {
            if (err) {
                console.error('Error loading activity data:', err);
                showError('Failed to load activity data');
                return;
            }
            activityData = data || [];
            filteredActivity = [...activityData];
            renderActivityTimeline();
        }
    }).send();
}

function loadSystemStats() {
    new GetRequest({
        getUrl: 'controller/admin/activity/get-stats.php',
        params: {},
        showLoading: false,
        showSuccess: false,
        callback: (err, data) => {
            if (err) {
                console.error('Error loading system stats:', err);
                return;
            }
            performanceData = data || {};
            updateStatsCards();
        }
    }).send();
}

function loadErrorLogs() {
    new GetRequest({
        getUrl: 'controller/admin/activity/get-errors.php',
        params: {},
        showLoading: false,
        showSuccess: false,
        callback: (err, data) => {
            if (err) {
                console.error('Error loading error logs:', err);
                return;
            }
            renderErrorLogs(data || []);
        }
    }).send();
}

function updateStatsCards() {
    document.getElementById('active-sessions').textContent = performanceData.active_sessions || 0;
    document.getElementById('orders-today').textContent = performanceData.orders_today || 0;
    document.getElementById('revenue-today').textContent = '$' + (performanceData.revenue_today || 0).toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('new-users-today').textContent = performanceData.new_users_today || 0;
}

function renderActivityTimeline() {
    const timeline = document.getElementById('activity-timeline');
    
    if (filteredActivity.length === 0) {
        timeline.innerHTML = '<div class="text-center py-4 text-muted">No activity found</div>';
        return;
    }

    timeline.innerHTML = filteredActivity.map(activity => `
        <div class="activity-item">
            <div class="activity-icon" style="background-color: ${getActivityIconColor(activity.type)}">
                <i class="fas ${getActivityIcon(activity.type)}"></i>
            </div>
            <div class="activity-content">
                <div class="activity-title">${activity.title}</div>
                <div class="activity-description">${activity.description}</div>
                <div class="activity-meta">
                    ${activity.user_name} • ${new Date(activity.created_at).toLocaleString()}
                </div>
            </div>
            <div>
                <span class="activity-badge badge-${activity.type}">${activity.type}</span>
            </div>
        </div>
    `).join('');
}

function renderErrorLogs(errors) {
    const tbody = document.getElementById('error-logs-body');
    
    if (errors.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No errors found</td></tr>';
        return;
    }

    tbody.innerHTML = errors.map(error => `
        <tr>
            <td>${new Date(error.timestamp).toLocaleString()}</td>
            <td><code>${error.error_type}</code></td>
            <td>${error.message}</td>
            <td>${error.user_name || 'System'}</td>
            <td>
                <span class="badge bg-${getSeverityBadgeClass(error.severity)}">
                    ${error.severity}
                </span>
            </td>
            <td>
                <button class="btn btn-outline-danger btn-sm" onclick="viewErrorDetails('${error.id}')">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function getActivityIcon(type) {
    const icons = {
        'login': 'fa-sign-in-alt',
        'order': 'fa-shopping-cart',
        'product': 'fa-box',
        'payment': 'fa-credit-card',
        'support': 'fa-headset',
        'error': 'fa-exclamation-triangle'
    };
    return icons[type] || 'fa-info-circle';
}

function getActivityIconColor(type) {
    const colors = {
        'login': 'rgba(25, 118, 210, 0.1)',
        'order': 'rgba(56, 142, 60, 0.1)',
        'product': 'rgba(245, 124, 0, 0.1)',
        'payment': 'rgba(194, 24, 91, 0.1)',
        'support': 'rgba(123, 31, 162, 0.1)',
        'error': 'rgba(211, 47, 47, 0.1)'
    };
    return colors[type] || 'rgba(158, 158, 158, 0.1)';
}

function getSeverityBadgeClass(severity) {
    const classes = {
        'low': 'success',
        'medium': 'warning',
        'high': 'danger',
        'critical': 'danger'
    };
    return classes[severity] || 'secondary';
}

function filterActivity() {
    const typeFilter = document.getElementById('activity-type-filter').value;
    const roleFilter = document.getElementById('user-role-filter').value;
    const dateFilter = document.getElementById('date-filter').value;
    const searchTerm = document.getElementById('search-activity').value.toLowerCase();

    filteredActivity = activityData.filter(activity => {
        const matchesType = !typeFilter || activity.type === typeFilter;
        const matchesRole = !roleFilter || activity.user_role === roleFilter;
        const matchesDate = !dateFilter || activity.created_at.startsWith(dateFilter);
        const matchesSearch = !searchTerm || 
            activity.title.toLowerCase().includes(searchTerm) ||
            activity.description.toLowerCase().includes(searchTerm) ||
            activity.user_name.toLowerCase().includes(searchTerm);

        return matchesType && matchesRole && matchesDate && matchesSearch;
    });

    renderActivityTimeline();
}

function initializeCharts() {
    // Performance Chart
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    new Chart(performanceCtx, {
        type: 'line',
        data: {
            labels: performanceData.hourly_labels || [],
            datasets: [{
                label: 'Response Time (ms)',
                data: performanceData.response_times || [],
                borderColor: '#4361ee',
                backgroundColor: 'rgba(67, 97, 238, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'CPU Usage (%)',
                data: performanceData.cpu_usage || [],
                borderColor: '#4cc9f0',
                backgroundColor: 'rgba(76, 201, 240, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Activity Distribution Chart
    const activityCtx = document.getElementById('activityChart').getContext('2d');
    new Chart(activityCtx, {
        type: 'doughnut',
        data: {
            labels: performanceData.activity_labels || [],
            datasets: [{
                data: performanceData.activity_counts || [],
                backgroundColor: [
                    '#4361ee',
                    '#4cc9f0',
                    '#2ecc71',
                    '#f39c12',
                    '#e74c3c',
                    '#9b59b6'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
}

function refreshActivity() {
    loadActivityData();
    loadSystemStats();
    loadErrorLogs();
}

function exportActivity() {
    const data = filteredActivity.map(activity => ({
        'Timestamp': new Date(activity.created_at).toLocaleString(),
        'Type': activity.type,
        'Title': activity.title,
        'Description': activity.description,
        'User': activity.user_name,
        'Role': activity.user_role
    }));

    const csv = convertToCSV(data);
    downloadCSV(csv, 'system_activity.csv');
}

function exportActivityLog() {
    exportActivity();
}

function clearErrorLogs() {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will permanently delete all error logs!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, clear logs!'
    }).then((result) => {
        if (result.isConfirmed) {
            new PostRequest({
                postUrl: 'controller/admin/activity/clear-errors.php',
                params: {},
                showLoading: true,
                showSuccess: true,
                callback: (err, data) => {
                    if (!err) {
                        loadErrorLogs();
                    }
                }
            }).send();
        }
    });
}

function viewErrorDetails(errorId) {
    // Implementation for viewing error details
    Swal.fire('Info', 'Error details feature coming soon!', 'info');
}

function saveChartAsPDF(chartId) {
    const canvas = document.getElementById(chartId.replace('-chart', 'Chart'));
    const imgData = canvas.toDataURL('image/png');
    const pdf = new window.jspdf.jsPDF({
        orientation: 'landscape',
        unit: 'pt',
        format: [canvas.width, canvas.height]
    });
    pdf.addImage(imgData, 'PNG', 0, 0, canvas.width, canvas.height);
    pdf.save(chartId + '.pdf');
}

function convertToCSV(data) {
    if (data.length === 0) return '';
    
    const headers = Object.keys(data[0]);
    const csvContent = [
        headers.join(','),
        ...data.map(row => headers.map(header => `"${row[header]}"`).join(','))
    ].join('\n');
    
    return csvContent;
}

function downloadCSV(csv, filename) {
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    window.URL.revokeObjectURL(url);
}
</script>
