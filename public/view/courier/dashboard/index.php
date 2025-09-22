<?php
// Dashboard data will be loaded via JavaScript using GetRequest
?>

<div class="main-container" id="main-container">
    <div class="header-section text-center">
        <h2 class="mb-3">Courier Dashboard</h2>
        <p class="lead">Track your deliveries and performance</p>
    </div>

    <div class="main-content">
        <!-- Stats Cards -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Total Deliveries</div>
                    <div class="stat-icon" style="background-color: rgba(67, 97, 238, 0.1); color: var(--primary);">
                        <i class="fas fa-truck"></i>
                </div>
                </div>
                <div class="stat-value" id="total-deliveries-value">Loading...</div>
                <div class="stat-change change-up">
                    <i class="fas fa-arrow-up"></i>
                    All time deliveries
        </div>
    </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Completed</div>
                    <div class="stat-icon" style="background-color: rgba(46, 204, 113, 0.1); color: var(--success);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="stat-value" id="completed-deliveries-value">Loading...</div>
                <div class="stat-change change-up">
                    <i class="fas fa-arrow-up"></i>
                    Successfully delivered
            </div>
        </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Pending</div>
                    <div class="stat-icon" style="background-color: rgba(243, 156, 18, 0.1); color: var(--warning);">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-value" id="pending-deliveries-value">Loading...</div>
                <div class="stat-change change-up">
                    <i class="fas fa-arrow-up"></i>
                    In progress
            </div>
        </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Today's Deliveries</div>
                    <div class="stat-icon" style="background-color: rgba(76, 201, 240, 0.1); color: var(--accent);">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
                <div class="stat-value" id="today-deliveries-value">Loading...</div>
                <div class="stat-change change-up">
                    <i class="fas fa-arrow-up"></i>
                    Delivered today
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-row">
            <div class="chart-container" id="delivery-performance">
                <div class="chart-header">
                    <h3>Weekly Delivery Performance</h3>
                    <div class="chart-actions">
                        <button onclick="saveDashboardAsPDF('delivery-performance')"><i class="fas fa-download"></i></button>
                        <button><i class="fas fa-expand"></i></button>
                    </div>
                </div>
                <div class="chart-wrapper">
                    <canvas id="deliveryChart"></canvas>
                </div>
            </div>

            <div class="chart-container" id="delivery-status">
                <div class="chart-header">
                    <h3>Delivery Status Distribution</h3>
                    <div class="chart-actions">
                        <button onclick="saveDashboardAsPDF('delivery-status')"><i class="fas fa-download"></i></button>
        </div>
                </div>
                <div class="chart-wrapper">
                    <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

        <!-- Report Tables -->
        <div class="tables-row">
            <div class="table-container" id="recent-deliveries">
                <div class="table-header">
                    <h3>Recent Deliveries</h3>
                    <button class="export-btn" style="padding: 8px 15px; font-size: 14px;" onclick="saveDashboardAsPDF('recent-deliveries')">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
                <div class="table-wrapper">
                    <table>
                                <thead>
                                    <tr>
                                <th>Order ID</th>
                                        <th>Customer</th>
                                <th>Address</th>
                                <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                        <tbody id="recent-deliveries-body">
                            <tr><td colspan="5" class="text-center">Loading...</td></tr>
                                </tbody>
                            </table>
        </div>
    </div>

            <div class="table-container" id="delivery-summary">
                <div class="table-header">
                    <h3>Delivery Summary</h3>
                    <button class="export-btn" style="padding: 8px 15px; font-size: 14px;" onclick="saveDashboardAsPDF('delivery-summary')">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Count</th>
                                <th>Percentage</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="delivery-summary-body">
                            <tr><td colspan="4" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Dashboard data will be loaded dynamically
let dashboardData = {
    stats: { total_deliveries: 0, completed_deliveries: 0, pending_deliveries: 0, today_deliveries: 0 },
    recent_deliveries: [],
    delivery_status: [],
    weekly_performance: []
};

// Load dashboard data using GetRequest
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
});

function loadDashboardData() {
    new GetRequest({
        getUrl: 'controller/courier/dashboard/index.php',
        params: {},
        showLoading: false,
        showSuccess: false,
        callback: (err, data) => {
            if (err) {
                console.error('Error loading dashboard data:', err);
                // Show error message but continue with default data
                Swal.fire({
                    title: 'Warning',
                    text: 'Some dashboard data could not be loaded. Showing cached data.',
                    icon: 'warning',
                    timer: 3000,
                    showConfirmButton: false
                });
        } else {
                dashboardData = data;
                updateDashboardUI();
            }
            initializeCharts();
        }
    }).send();
}

function updateDashboardUI() {
    // Update stats cards
    document.getElementById('total-deliveries-value').textContent = dashboardData.stats.total_deliveries.toLocaleString();
    document.getElementById('completed-deliveries-value').textContent = dashboardData.stats.completed_deliveries.toLocaleString();
    document.getElementById('pending-deliveries-value').textContent = dashboardData.stats.pending_deliveries.toLocaleString();
    document.getElementById('today-deliveries-value').textContent = dashboardData.stats.today_deliveries.toLocaleString();

    // Update recent deliveries table
    const recentDeliveriesBody = document.getElementById('recent-deliveries-body');
    recentDeliveriesBody.innerHTML = '';
    dashboardData.recent_deliveries.forEach(delivery => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${delivery.order_number}</td>
            <td>${delivery.customer_name}</td>
            <td>${delivery.address_line1}, ${delivery.city}</td>
            <td>${new Date(delivery.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</td>
            <td><span class="status status-completed">In Transit</span></td>
        `;
        recentDeliveriesBody.appendChild(row);
    });

    // Update delivery summary table
    const deliverySummaryBody = document.getElementById('delivery-summary-body');
    deliverySummaryBody.innerHTML = '';
    const totalStatusCount = dashboardData.delivery_status.reduce((sum, status) => sum + status.count, 0);
    dashboardData.delivery_status.forEach(status => {
        const percentage = totalStatusCount > 0 ? Math.round((status.count / totalStatusCount) * 100) : 0;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${status.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</td>
            <td>${status.count.toLocaleString()}</td>
            <td>${percentage}%</td>
            <td>
                <span class="badge bg-${status.status === 'delivered' ? 'success' : 'warning'}">
                    ${status.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
                    </span>
                </td>
        `;
        deliverySummaryBody.appendChild(row);
    });
}

// Initialize charts
function initializeCharts() {
    // Weekly Delivery Performance Chart
    const deliveryCtx = document.getElementById('deliveryChart').getContext('2d');
    const deliveryChart = new Chart(deliveryCtx, {
        type: 'bar',
        data: {
            labels: dashboardData.weekly_performance.map(item => item.delivery_date),
            datasets: [{
                label: 'Deliveries',
                data: dashboardData.weekly_performance.map(item => item.deliveries),
                backgroundColor: 'rgba(67, 97, 238, 0.8)',
                borderColor: '#4361ee',
                borderWidth: 1
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

    // Delivery Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: dashboardData.delivery_status.map(item => item.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())),
            datasets: [{
                data: dashboardData.delivery_status.map(item => item.count),
                backgroundColor: [
                    '#4361ee',
                    '#4cc9f0',
                    '#2ecc71',
                    '#f39c12',
                    '#7209b7'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                }
            },
            cutout: '70%'
        }
    });
}
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
function saveDashboardAsPDF(target) {
    const card = document.getElementById(target);
    const button = card.querySelector('button');
    button.style.display = 'none';

    html2canvas(card).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const pdf = new window.jspdf.jsPDF({
            orientation: 'landscape',
            unit: 'pt',
            format: [canvas.width, canvas.height]
        });
        pdf.addImage(imgData, 'PNG', 0, 0, canvas.width, canvas.height);
        pdf.save(target + '.pdf');
    });

    button.style.display = 'block';
}
</script>