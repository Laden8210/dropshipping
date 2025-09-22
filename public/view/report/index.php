<?php
require_once '../../../core/config.php';

$conn = require '../../../core/config.php';
$userRole = $_SESSION['role'] ?? 'user';
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    header('Location: ../../auth/login.php');
    exit;
}
?>

<div class="main-container" id="main-container">
    <div class="header-section text-center">
        <h2 class="mb-3">Reports & Analytics</h2>
        <p class="lead">Generate comprehensive reports for your business</p>
        </div>
        
    <div class="main-content">
        <!-- Report Filters -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Report Filters</h5>
            </div>
            <div class="card-body">
                <form id="reportFilters" class="row g-3">
                    <div class="col-md-3">
                        <label for="reportType" class="form-label">Report Type</label>
                        <select class="form-select" id="reportType" name="type">
                            <option value="dashboard">Dashboard Report</option>
                            <option value="sales">Sales Report</option>
                            <option value="inventory">Inventory Report</option>
                            <option value="orders">Orders Report</option>
                            <?php if ($userRole === 'admin'): ?>
                            <option value="performance">Performance Report</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="dateFrom" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="dateFrom" name="date_from" value="<?php echo date('Y-m-01'); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="dateTo" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="dateTo" name="date_to" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="format" class="form-label">Format</label>
                        <select class="form-select" id="format" name="format">
                            <option value="pdf">PDF</option>
                            <option value="json">JSON</option>
                        </select>
        </div>
            <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-download"></i> Generate Report
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="previewReport()">
                            <i class="fas fa-eye"></i> Preview
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Report Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card report-card" onclick="generateQuickReport('dashboard')">
                    <div class="card-body text-center">
                        <div class="report-icon mb-3">
                            <i class="fas fa-chart-pie fa-2x text-primary"></i>
                        </div>
                        <h5 class="card-title">Dashboard Report</h5>
                        <p class="card-text">Complete overview with charts and analytics</p>
                    </div>
                </div>
                                </div>
            <div class="col-md-3">
                <div class="card report-card" onclick="generateQuickReport('sales')">
                    <div class="card-body text-center">
                        <div class="report-icon mb-3">
                            <i class="fas fa-dollar-sign fa-2x text-success"></i>
                                </div>
                        <h5 class="card-title">Sales Report</h5>
                        <p class="card-text">Revenue analysis and top products</p>
                                </div>
                                </div>
                            </div>
            <div class="col-md-3">
                <div class="card report-card" onclick="generateQuickReport('inventory')">
                    <div class="card-body text-center">
                        <div class="report-icon mb-3">
                            <i class="fas fa-boxes fa-2x text-warning"></i>
                        </div>
                        <h5 class="card-title">Inventory Report</h5>
                        <p class="card-text">Stock levels and movements</p>
                    </div>
                                </div>
                            </div>
            <div class="col-md-3">
                <div class="card report-card" onclick="generateQuickReport('orders')">
                    <div class="card-body text-center">
                        <div class="report-icon mb-3">
                            <i class="fas fa-shopping-cart fa-2x text-info"></i>
                        </div>
                        <h5 class="card-title">Orders Report</h5>
                        <p class="card-text">Order status and customer analysis</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report History -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Reports</h5>
                <button class="btn btn-sm btn-outline-primary" onclick="refreshReportHistory()">
                    <i class="fas fa-sync"></i> Refresh
                </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Report Type</th>
                                <th>Date Range</th>
                                <th>Generated</th>
                                <th>Format</th>
                                <th>Actions</th>
                            </tr>
                                    </thead>
                        <tbody id="reportHistory">
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    <i class="fas fa-spinner fa-spin"></i> Loading report history...
                                </td>
                            </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<style>
.report-card {
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid #e9ecef;
}

.report-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.report-icon {
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.table th {
    background-color: #f8f9fa;
    border-top: none;
}

.btn {
    border-radius: 6px;
}

.form-control, .form-select {
    border-radius: 6px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load report history
    loadReportHistory();
    
    // Handle form submission
    document.getElementById('reportFilters').addEventListener('submit', function(e) {
        e.preventDefault();
        generateReport();
    });
});

function generateReport() {
    const form = document.getElementById('reportFilters');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    
    const format = formData.get('format');
    const reportType = formData.get('type');
    
    if (format === 'pdf') {
        // Generate PDF report
        window.open(`controller/reports/generate-pdf.php?${params}`, '_blank');
    } else {
        // Generate JSON report
        fetch(`controller/reports/index.php?${params}`)
            .then(response => response.json())
            .then(data => {
                // Download JSON file
                const blob = new Blob([JSON.stringify(data, null, 2)], {type: 'application/json'});
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `${reportType}_report_${new Date().toISOString().split('T')[0]}.json`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            })
            .catch(error => {
                console.error('Error generating report:', error);
                alert('Error generating report. Please try again.');
            });
    }
}

function generateQuickReport(type) {
    const today = new Date().toISOString().split('T')[0];
    const firstDay = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];
    
    const params = new URLSearchParams({
        type: type,
        date_from: firstDay,
        date_to: today,
        format: 'pdf'
    });
    
    window.open(`controller/reports/generate-pdf.php?${params}`, '_blank');
}

function previewReport() {
    const form = document.getElementById('reportFilters');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    
    // Open preview in new window
    window.open(`controller/reports/index.php?${params}`, '_blank');
}

function loadReportHistory() {
    // This would typically load from a database
    // For now, we'll show a placeholder
    const tbody = document.getElementById('reportHistory');
    tbody.innerHTML = `
        <tr>
            <td>Dashboard Report</td>
            <td>${new Date().toLocaleDateString()} - ${new Date().toLocaleDateString()}</td>
            <td>${new Date().toLocaleString()}</td>
            <td><span class="badge bg-danger">PDF</span></td>
            <td>
                <button class="btn btn-sm btn-outline-primary" onclick="downloadReport('dashboard', 'pdf')">
                    <i class="fas fa-download"></i>
                </button>
            </td>
        </tr>
        <tr>
            <td>Sales Report</td>
            <td>${new Date().toLocaleDateString()} - ${new Date().toLocaleDateString()}</td>
            <td>${new Date().toLocaleString()}</td>
            <td><span class="badge bg-success">JSON</span></td>
            <td>
                <button class="btn btn-sm btn-outline-primary" onclick="downloadReport('sales', 'json')">
                    <i class="fas fa-download"></i>
                </button>
            </td>
        </tr>
    `;
}

function refreshReportHistory() {
    loadReportHistory();
}

function downloadReport(type, format) {
    const today = new Date().toISOString().split('T')[0];
    const firstDay = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];
    
    const params = new URLSearchParams({
        type: type,
        date_from: firstDay,
        date_to: today,
        format: format
    });
    
    if (format === 'pdf') {
        window.open(`controller/reports/generate-pdf.php?${params}`, '_blank');
    } else {
        window.open(`controller/reports/index.php?${params}`, '_blank');
    }
}
</script>