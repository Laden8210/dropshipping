<?php
// Simple User Reports page
?>

<div class="main-container">
    <div class="header-section text-center">
        <h2 class="mb-3">Reports</h2>
        <p class="lead">Generate and view business reports</p>
    </div>

    <div class="main-content">
        <!-- Report Selection -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Generate Report</h5>
            </div>
                <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label for="reportType" class="form-label">Report Type</label>
                        <select class="form-select" id="reportType">
                            <option value="">Select Report Type</option>
                            <option value="sales">Sales Report</option>
                            <option value="products">Product Report</option>
                            <option value="orders">Orders Report</option>
                            <option value="complete">Complete Report</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="dateRange" class="form-label">Date Range</label>
                        <select class="form-select" id="dateRange">
                            <option value="last7days">Last 7 Days</option>
                            <option value="last30days">Last 30 Days</option>
                            <option value="last3months">Last 3 Months</option>
                            <option value="last6months">Last 6 Months</option>
                            <option value="lastyear">Last Year</option>
                            <option value="all">All Time</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Actions</label>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary" onclick="viewReport()">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn btn-success" onclick="downloadReport()">
                                <i class="fas fa-download"></i> Download PDF
                            </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Report Data Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Report Data</h5>
                <span id="reportInfo" class="text-muted">Select a report type to view data</span>
                </div>
                <div class="card-body">
                        <div class="table-responsive">
                    <table class="table table-hover" id="reportTable">
                        <thead class="table-dark">
                            <tr id="tableHeaders">
                                <th>Select a report to view data</th>
                                    </tr>
                                </thead>
                        <tbody id="tableBody">
                            <tr>
                                <td class="text-center text-muted">
                                    <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                    <p>Choose a report type and click "View" to display data</p>
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
let currentReportData = null;

function viewReport() {
    const reportType = document.getElementById('reportType').value;
    const dateRange = document.getElementById('dateRange').value;
    
    if (!reportType) {
        Swal.fire({
            title: 'Error',
            text: 'Please select a report type',
            icon: 'error'
        });
        return;
    }
    
    // Show loading
    Swal.fire({
        title: 'Loading Report...',
        text: 'Please wait while we fetch the data',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Fetch report data
    const params = new URLSearchParams({
        type: reportType,
        date_range: dateRange,
        format: 'json'
    });
    
    fetch(`controller/user/reports/generate-report.php?${params}`)
        .then(response => response.json())
        .then(data => {
            Swal.close();
            if (data.status === 'success') {
                currentReportData = data.data;
                displayReportData(data.data, reportType, dateRange);
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Error loading report:', error);
            Swal.fire({
                title: 'Error',
                text: 'Failed to load report data',
                icon: 'error'
            });
        });
}

function downloadReport() {
    const reportType = document.getElementById('reportType').value;
    const dateRange = document.getElementById('dateRange').value;
    
    if (!reportType) {
        Swal.fire({
            title: 'Error',
            text: 'Please select a report type',
            icon: 'error'
        });
        return;
    }
    
    const params = new URLSearchParams({
        type: reportType,
        date_range: dateRange,
        format: 'pdf'
    });
    
    window.open(`controller/user/reports/generate-pdf.php?${params}`, '_blank');
}

function displayReportData(data, reportType, dateRange) {
    const reportInfo = document.getElementById('reportInfo');
    const tableHeaders = document.getElementById('tableHeaders');
    const tableBody = document.getElementById('tableBody');
    
    // Update report info
    const dateRangeText = formatDateRange(dateRange);
    const reportTitle = getReportTitle(reportType);
    reportInfo.textContent = `${reportTitle} - ${dateRangeText}`;
    
    // Clear and populate table based on report type
    switch (reportType) {
        case 'sales':
            displaySalesData(data, tableHeaders, tableBody);
            break;
        case 'products':
            displayProductsData(data, tableHeaders, tableBody);
            break;
        case 'orders':
            displayOrdersData(data, tableHeaders, tableBody);
            break;
        case 'complete':
            displayCompleteData(data, tableHeaders, tableBody);
            break;
    }
}

function displaySalesData(data, tableHeaders, tableBody) {
    if (!data.sales_data || data.sales_data.length === 0) {
        tableHeaders.innerHTML = '<th>No Data Available</th>';
        tableBody.innerHTML = '<tr><td class="text-center text-muted">No sales data found for the selected period</td></tr>';
        return;
    }
    
    tableHeaders.innerHTML = `
        <th>Date</th>
        <th>Orders</th>
        <th>Revenue</th>
    `;
    
    let html = '';
    data.sales_data.forEach(item => {
        html += `
            <tr>
                <td>${item.date}</td>
                <td>${item.orders}</td>
                <td>₱${parseFloat(item.revenue).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            </tr>
        `;
    });
    
    tableBody.innerHTML = html;
}

function displayProductsData(data, tableHeaders, tableBody) {
    if (!data.products_data || data.products_data.length === 0) {
        tableHeaders.innerHTML = '<th>No Data Available</th>';
        tableBody.innerHTML = '<tr><td class="text-center text-muted">No product data found for the selected period</td></tr>';
        return;
    }
    
    tableHeaders.innerHTML = `
        <th>Product Name</th>
        <th>Category</th>
        <th>Sales</th>
        <th>Revenue</th>
        <th>Orders</th>
    `;
    
    let html = '';
    data.products_data.forEach(item => {
        html += `
            <tr>
                <td>${item.product_name}</td>
                <td>${item.category_name}</td>
                <td>${item.total_sales}</td>
                <td>₱${parseFloat(item.total_revenue).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                <td>${item.order_count}</td>
            </tr>
        `;
    });
    
    tableBody.innerHTML = html;
}

function displayOrdersData(data, tableHeaders, tableBody) {
    if (!data.orders_data || data.orders_data.length === 0) {
        tableHeaders.innerHTML = '<th>No Data Available</th>';
        tableBody.innerHTML = '<tr><td class="text-center text-muted">No order data found for the selected period</td></tr>';
        return;
    }
    
    tableHeaders.innerHTML = `
        <th>Order Number</th>
        <th>Customer</th>
        <th>Date</th>
        <th>Amount</th>
        <th>Items</th>
    `;
    
    let html = '';
    data.orders_data.forEach(item => {
        html += `
            <tr>
                <td>${item.order_number}</td>
                <td>${item.customer_name}</td>
                <td>${new Date(item.created_at).toLocaleDateString()}</td>
                <td>₱${parseFloat(item.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                <td>${item.item_count}</td>
            </tr>
        `;
    });
    
    tableBody.innerHTML = html;
}

function displayCompleteData(data, tableHeaders, tableBody) {
    // For complete report, show summary first
    if (data.summary) {
        tableHeaders.innerHTML = `
            <th>Metric</th>
            <th>Value</th>
        `;
        
        let html = `
            <tr>
                <td><strong>Total Revenue</strong></td>
                <td>₱${parseFloat(data.summary.total_revenue).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            </tr>
            <tr>
                <td><strong>Total Orders</strong></td>
                <td>${data.summary.total_orders}</td>
            </tr>
            <tr>
                <td><strong>Total Products</strong></td>
                <td>${data.summary.total_products}</td>
            </tr>
            <tr>
                <td><strong>Conversion Rate</strong></td>
                <td>${data.summary.conversion_rate}%</td>
            </tr>
        `;
        
        tableBody.innerHTML = html;
    } else {
        tableHeaders.innerHTML = '<th>No Data Available</th>';
        tableBody.innerHTML = '<tr><td class="text-center text-muted">No complete data found for the selected period</td></tr>';
    }
}

function formatDateRange(dateRange) {
    switch (dateRange) {
        case 'last7days': return 'Last 7 Days';
        case 'last30days': return 'Last 30 Days';
        case 'last3months': return 'Last 3 Months';
        case 'last6months': return 'Last 6 Months';
        case 'lastyear': return 'Last Year';
        case 'all': return 'All Time';
        default: return dateRange;
    }
}

function getReportTitle(type) {
    switch (type) {
        case 'sales': return 'Sales Report';
        case 'products': return 'Product Report';
        case 'orders': return 'Orders Report';
        case 'complete': return 'Complete Report';
        default: return 'Report';
    }
}
</script>

<style>
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

.table td {
    vertical-align: middle;
}

.btn {
    border-radius: 6px;
}

.form-control, .form-select {
    border-radius: 6px;
}

.table-responsive {
    border-radius: 8px;
    overflow: hidden;
}
</style>