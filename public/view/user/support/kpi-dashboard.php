<?php
// Admin Support KPI Dashboard
// Displays key performance indicators for support team
?>

<div class="main-container">
    <div class="header-section text-center">
        <h2 class="mb-3">Support Performance Dashboard</h2>
        <p class="lead">Monitor support team performance and customer satisfaction</p>
    </div>

    <div class="main-content">
        <!-- KPI Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center kpi-card">
                    <div class="card-body">
                        <div class="kpi-icon">
                            <i class="fas fa-ticket-alt fa-2x text-primary"></i>
                        </div>
                        <h3 class="kpi-value" id="totalTicketsKPI">0</h3>
                        <p class="kpi-label">Total Tickets</p>
                        <small class="text-muted" id="totalTicketsPeriod">This month</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center kpi-card">
                    <div class="card-body">
                        <div class="kpi-icon">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                        <h3 class="kpi-value" id="avgResponseTime">0h</h3>
                        <p class="kpi-label">Avg Response Time</p>
                        <small class="text-muted" id="responseTimePeriod">Last 30 days</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center kpi-card">
                    <div class="card-body">
                        <div class="kpi-icon">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                        <h3 class="kpi-value" id="resolutionRate">0%</h3>
                        <p class="kpi-label">Resolution Rate</p>
                        <small class="text-muted" id="resolutionRatePeriod">Last 30 days</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center kpi-card">
                    <div class="card-body">
                        <div class="kpi-icon">
                            <i class="fas fa-smile fa-2x text-info"></i>
                        </div>
                        <h3 class="kpi-value" id="satisfactionScore">0.0</h3>
                        <p class="kpi-label">Satisfaction Score</p>
                        <small class="text-muted" id="satisfactionPeriod">Last 30 days</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Tickets by Status</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Tickets by Priority</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="priorityChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Response Time Trends</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="responseTimeChart" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Ticket Volume Trends</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="volumeChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Metrics Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detailed Performance Metrics</h5>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadKPIData('7days')">7 Days</button>
                    <button type="button" class="btn btn-sm btn-outline-primary active" onclick="loadKPIData('30days')">30 Days</button>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadKPIData('90days')">90 Days</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Metric</th>
                                <th>Current Period</th>
                                <th>Previous Period</th>
                                <th>Change</th>
                                <th>Trend</th>
                            </tr>
                        </thead>
                        <tbody id="metricsTableBody">
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Loading metrics...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let kpiData = {};
let currentPeriod = '30days';

document.addEventListener('DOMContentLoaded', function() {
    loadKPIData('30days');
});

function loadKPIData(period) {
    currentPeriod = period;
    
    // Update active button
    document.querySelectorAll('.btn-group button').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
        new GetRequest({
            getUrl: 'controller/user/support/get-kpi-data.php',
        params: { period: period },
        showLoading: false,
        showSuccess: false,
        callback: (err, data) => {
            if (err) {
                console.error('Error loading KPI data:', err);
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to load KPI data',
                    icon: 'error'
                });
            } else {
                kpiData = data;
                updateKPICards(data);
                updateCharts(data);
                updateMetricsTable(data);
            }
        }
    }).send();
}

function updateKPICards(data) {
    document.getElementById('totalTicketsKPI').textContent = data.summary.total_tickets;
    document.getElementById('avgResponseTime').textContent = data.summary.avg_response_time + 'h';
    document.getElementById('resolutionRate').textContent = data.summary.resolution_rate + '%';
    document.getElementById('satisfactionScore').textContent = data.summary.satisfaction_score;
    
    // Update period labels
    const periodText = getPeriodText(currentPeriod);
    document.getElementById('totalTicketsPeriod').textContent = periodText;
    document.getElementById('responseTimePeriod').textContent = periodText;
    document.getElementById('resolutionRatePeriod').textContent = periodText;
    document.getElementById('satisfactionPeriod').textContent = periodText;
}

function updateCharts(data) {
    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Open', 'In Progress', 'Resolved', 'Closed'],
            datasets: [{
                data: [
                    data.charts.status.open,
                    data.charts.status.in_progress,
                    data.charts.status.resolved,
                    data.charts.status.closed
                ],
                backgroundColor: ['#ffc107', '#17a2b8', '#28a745', '#6c757d']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Priority Chart
    const priorityCtx = document.getElementById('priorityChart').getContext('2d');
    new Chart(priorityCtx, {
        type: 'bar',
        data: {
            labels: ['Low', 'Medium', 'High', 'Urgent'],
            datasets: [{
                label: 'Tickets',
                data: [
                    data.charts.priority.low,
                    data.charts.priority.medium,
                    data.charts.priority.high,
                    data.charts.priority.urgent
                ],
                backgroundColor: ['#28a745', '#17a2b8', '#ffc107', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Response Time Chart
    const responseCtx = document.getElementById('responseTimeChart').getContext('2d');
    new Chart(responseCtx, {
        type: 'line',
        data: {
            labels: data.charts.response_time.labels,
            datasets: [{
                label: 'Avg Response Time (hours)',
                data: data.charts.response_time.data,
                borderColor: '#17a2b8',
                backgroundColor: 'rgba(23, 162, 184, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Volume Chart
    const volumeCtx = document.getElementById('volumeChart').getContext('2d');
    new Chart(volumeCtx, {
        type: 'line',
        data: {
            labels: data.charts.volume.labels,
            datasets: [{
                label: 'Tickets Created',
                data: data.charts.volume.data,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function updateMetricsTable(data) {
    const tbody = document.getElementById('metricsTableBody');
    
    let html = '';
    data.metrics.forEach(metric => {
        const changeClass = metric.change >= 0 ? 'text-success' : 'text-danger';
        const trendIcon = metric.change >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
        
        html += `
            <tr>
                <td><strong>${metric.name}</strong></td>
                <td>${metric.current}</td>
                <td>${metric.previous}</td>
                <td class="${changeClass}">
                    <i class="fas ${trendIcon}"></i> ${Math.abs(metric.change)}%
                </td>
                <td>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar ${metric.change >= 0 ? 'bg-success' : 'bg-danger'}" 
                             style="width: ${Math.min(100, Math.abs(metric.change))}%">
                            ${metric.change >= 0 ? '+' : ''}${metric.change}%
                        </div>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function getPeriodText(period) {
    switch (period) {
        case '7days': return 'Last 7 days';
        case '30days': return 'Last 30 days';
        case '90days': return 'Last 90 days';
        default: return 'Current period';
    }
}
</script>

<style>
.kpi-card {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border-radius: 15px;
    transition: transform 0.2s ease;
}

.kpi-card:hover {
    transform: translateY(-5px);
}

.kpi-icon {
    margin-bottom: 1rem;
}

.kpi-value {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.kpi-label {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.table th {
    background-color: #343a40;
    color: white;
    border: none;
    font-weight: 600;
}

.btn-group .btn.active {
    background-color: #007bff;
    color: white;
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}
</style>
