<?php
// Dashboard data will be loaded via JavaScript using GetRequest
?>

<div class="main-container" id="main-container">
       <div class="header-section text-center">
        <h2 class="mb-3">Business Owner Dashboard</h2>
        <p class="lead">Manage your store and track performance</p>
        <!-- <div class="dashboard-actions mt-3">
            <button class="btn btn-primary me-2" onclick="generateReport()">
                <i class="fas fa-chart-line"></i> Generate Report
            </button>
            <button class="btn btn-success" onclick="exportData()">
                <i class="fas fa-download"></i> Export Data
            </button>
        </div> -->
       </div>

       <div class="main-content">
           <!-- Stats Cards -->
           <div class="stats-container">
               <div class="stat-card">
                   <div class="stat-header">
                       <div class="stat-title">Total Revenue</div>
                       <div class="stat-icon" style="background-color: rgba(67, 97, 238, 0.1); color: var(--primary);">
                           <i class="fas fa-dollar-sign"></i>
                       </div>
                   </div>
                <div class="stat-value" id="revenue-value">Loading...</div>
                   <div class="stat-change change-up">
                       <i class="fas fa-arrow-up"></i>
                    Revenue from all orders
                   </div>
               </div>

               <div class="stat-card">
                   <div class="stat-header">
                    <div class="stat-title">Total Orders</div>
                       <div class="stat-icon" style="background-color: rgba(46, 204, 113, 0.1); color: var(--success);">
                           <i class="fas fa-shopping-bag"></i>
                       </div>
                   </div>
                <div class="stat-value" id="orders-value">Loading...</div>
                   <div class="stat-change change-up">
                       <i class="fas fa-arrow-up"></i>
                    All time orders
                   </div>
               </div>

               <div class="stat-card">
                   <div class="stat-header">
                    <div class="stat-title">Products</div>
                       <div class="stat-icon" style="background-color: rgba(76, 201, 240, 0.1); color: var(--accent);">
                        <i class="fas fa-box"></i>
                       </div>
                   </div>
                <div class="stat-value" id="products-value">Loading...</div>
                   <div class="stat-change change-up">
                       <i class="fas fa-arrow-up"></i>
                    Imported products
                   </div>
               </div>

               <div class="stat-card">
                   <div class="stat-header">
                       <div class="stat-title">Conversion Rate</div>
                       <div class="stat-icon" style="background-color: rgba(243, 156, 18, 0.1); color: var(--warning);">
                           <i class="fas fa-percentage"></i>
                       </div>
                   </div>
                <div class="stat-value" id="conversion-value">Loading...</div>
                <div class="stat-change change-up">
                    <i class="fas fa-arrow-up"></i>
                    Orders per product
                   </div>
               </div>
           </div>

           <!-- Charts Section -->
           <div class="charts-row">
               <div class="chart-container" id="sales-performance">
                   <div class="chart-header">
                       <h3>Sales Performance</h3>
                       <div class="chart-actions">
                        <button onclick="saveDashboardAsPDF('sales-performance')"><i class="fas fa-download"></i></button>
                           <button><i class="fas fa-expand"></i></button>
                       </div>
                   </div>
                   <div class="chart-wrapper">
                       <canvas id="salesChart"></canvas>
                   </div>
               </div>

               <div class="chart-container" id="revenue-by-category">
                   <div class="chart-header">
                       <h3>Revenue by Category</h3>
                       <div class="chart-actions">
                        <button onclick="saveDashboardAsPDF('revenue-by-category')"><i class="fas fa-download"></i></button>
                       </div>
                   </div>
                   <div class="chart-wrapper">
                       <canvas id="revenueChart"></canvas>
                   </div>
               </div>
           </div>

           <!-- Report Tables -->
           <div class="tables-row">
               <div class="table-container" id="top-products">
                   <div class="table-header">
                       <h3>Top Performing Products</h3>
                       <button class="export-btn" style="padding: 8px 15px; font-size: 14px;" onclick="saveDashboardAsPDF('top-products')">
                           <i class="fas fa-download"></i> Export
                       </button>
                   </div>
                   <div class="table-wrapper">
                       <table>
                           <thead>
                               <tr>
                                   <th>Product</th>
                                   <th>Category</th>
                                   <th>Sales</th>
                                   <th>Revenue</th>
                                   <th>Stock</th>
                               </tr>
                           </thead>
                        <tbody id="top-products-body">
                            <tr><td colspan="5" class="text-center">Loading...</td></tr>
                           </tbody>
                       </table>
                   </div>
               </div>

               <div class="table-container" id="recent-orders">
                   <div class="table-header">
                       <h3>Recent Orders</h3>
                       <button class="export-btn" style="padding: 8px 15px; font-size: 14px;" onclick="saveDashboardAsPDF('recent-orders')">
                           <i class="fas fa-download"></i> Export
                       </button>
                   </div>
                   <div class="table-wrapper">
                       <table>
                           <thead>
                               <tr>
                                   <th>Order ID</th>
                                   <th>Customer</th>
                                   <th>Date</th>
                                   <th>Amount</th>
                                   <th>Status</th>
                               </tr>
                           </thead>
                        <tbody id="recent-orders-body">
                            <tr><td colspan="5" class="text-center">Loading...</td></tr>
                           </tbody>
                       </table>
                   </div>
               </div>
           </div>

        <!-- Report Display Section -->
        <div id="reportSection" class="mt-4" style="display: none;">
            <div class="card report-card">
                <div class="card-header report-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 id="reportTitle" class="mb-0"><i class="fas fa-chart-bar"></i> Report</h4>
                            <p id="reportSubtitle" class="text-muted mb-0">Generated Report</p>
                        </div>
                        <div class="report-actions">
                            <button class="btn btn-outline-primary btn-sm" onclick="downloadReportPDF()">
                                <i class="fas fa-file-pdf"></i> Download PDF
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="hideReport()">
                                <i class="fas fa-times"></i> Close
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body report-body">
                    <div id="reportContent">
                        <!-- Report content will be displayed here -->
                    </div>
                </div>
            </div>
       </div>
   </div>
   </div>

   <script>
// Dashboard data will be loaded dynamically
let dashboardData = {
    stats: { total_revenue: 0, total_orders: 0, total_products: 0, conversion_rate: 0 },
    recent_orders: [],
    top_products: [],
    monthly_sales: [],
    category_revenue: []
};

// Current report data
let currentReportData = null;
let currentReportType = null;
let currentDateRange = null;

// Load dashboard data using GetRequest
       document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
});

function loadDashboardData() {
    new GetRequest({
        getUrl: 'controller/user/dashboard/index.php',
        params: {},
        showLoading: false,
        showSuccess: false,
        callback: (err, data) => {
            if (err) {
                console.error('Error loading dashboard data:', err);
                // Show error message but continue with default data
        
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
    document.getElementById('revenue-value').textContent = '₱' + dashboardData.stats.total_revenue.toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('orders-value').textContent = dashboardData.stats.total_orders.toLocaleString();
    document.getElementById('products-value').textContent = dashboardData.stats.total_products.toLocaleString();
    document.getElementById('conversion-value').textContent = dashboardData.stats.conversion_rate + '%';

    // Update top products table
    const topProductsBody = document.getElementById('top-products-body');
    topProductsBody.innerHTML = '';
    dashboardData.top_products.forEach(product => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${product.product_name}</td>
            <td>${product.category_name}</td>
            <td>${product.total_sales.toLocaleString()}</td>
            <td>₱${product.total_revenue.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td>${(product.stock || 0).toLocaleString()}</td>
        `;
        topProductsBody.appendChild(row);
    });

    // Update recent orders table
    const recentOrdersBody = document.getElementById('recent-orders-body');
    recentOrdersBody.innerHTML = '';
    dashboardData.recent_orders.forEach(order => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${order.order_number}</td>
            <td>${order.customer_name}</td>
            <td>${new Date(order.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</td>
            <td>₱${order.total_amount.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td><span class="status status-completed">Completed</span></td>
        `;
        recentOrdersBody.appendChild(row);
    });
}

// Initialize charts
function initializeCharts() {
           // Sales Performance Chart
           const salesCtx = document.getElementById('salesChart').getContext('2d');
           const salesChart = new Chart(salesCtx, {
               type: 'line',
               data: {
            labels: dashboardData.monthly_sales.map(item => item.month),
                   datasets: [{
                       label: 'Revenue',
                data: dashboardData.monthly_sales.map(item => item.revenue),
                       borderColor: '#4361ee',
                       backgroundColor: 'rgba(67, 97, 238, 0.1)',
                       tension: 0.4,
                       fill: true
                   }, {
                       label: 'Orders',
                data: dashboardData.monthly_sales.map(item => item.orders),
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

           // Revenue by Category Chart
           const revenueCtx = document.getElementById('revenueChart').getContext('2d');
           const revenueChart = new Chart(revenueCtx, {
               type: 'doughnut',
               data: {
            labels: dashboardData.category_revenue.map(item => item.category_name),
                   datasets: [{
                data: dashboardData.category_revenue.map(item => item.revenue),
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

// Report generation functions
function generateReport() {
    Swal.fire({
        title: 'Generate Report',
        html: `
            <div class="report-options">
                <div class="mb-3">
                    <label class="form-label">Report Type</label>
                    <select id="reportType" class="form-select">
                        <option value="sales">Sales Report</option>
                        <option value="products">Product Performance</option>
                        <option value="orders">Order Summary</option>
                        <option value="complete">Complete Dashboard</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date Range</label>
                    <select id="dateRange" class="form-select">
                        <option value="last7days">Last 7 Days</option>
                        <option value="last30days">Last 30 Days</option>
                        <option value="last3months">Last 3 Months</option>
                        <option value="last6months">Last 6 Months</option>
                        <option value="lastyear">Last Year</option>
                        <option value="all">All Time</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Report Action</label>
                    <select id="reportFormat" class="form-select">
                        <option value="preview">Display Report</option>
                        <option value="pdf">Download PDF Only</option>
                    </select>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Generate Report',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const reportType = document.getElementById('reportType').value;
            const dateRange = document.getElementById('dateRange').value;
            const reportFormat = document.getElementById('reportFormat').value;
            
            if (!reportType || !dateRange || !reportFormat) {
                Swal.showValidationMessage('Please fill in all fields');
                return false;
            }
            
            return { reportType, dateRange, reportFormat };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { reportType, dateRange, reportFormat } = result.value;
            
            // Show loading
            Swal.fire({
                title: 'Generating Report...',
                text: 'Please wait while we generate your report',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Generate report
            const params = new URLSearchParams({
                type: reportType,
                date_range: dateRange,
                format: reportFormat
            });
            
            if (reportFormat === 'preview') {
                // Display report on page
                fetch(`controller/user/reports/generate-report.php?${params}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.close();
                            currentReportData = data.data;
                            currentReportType = reportType;
                            currentDateRange = dateRange;
                            displayReportOnPage(data.data, reportType, dateRange);
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error generating report:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to generate report: ' + error.message,
                            icon: 'error'
                        });
                    });
            } else if (reportFormat === 'pdf') {
                // Download PDF directly
                window.open(`controller/user/reports/generate-pdf.php?${params}`, '_blank');
                Swal.close();
            }
        }
    });
}

function displayReportOnPage(data, reportType, dateRange) {
    const dateRangeText = formatDateRange(dateRange);
    const reportTitle = getReportTitle(reportType);
    
    // Update report header
    document.getElementById('reportTitle').innerHTML = `<i class="fas fa-chart-bar"></i> ${reportTitle}`;
    document.getElementById('reportSubtitle').textContent = `${dateRangeText} • Generated: ${new Date().toLocaleString()}`;
    
    // Generate report content
    let reportHTML = '';
    
    switch (reportType) {
        case 'sales':
            reportHTML = generateProfessionalSalesReportHTML(data);
            break;
        case 'products':
            reportHTML = generateProfessionalProductsReportHTML(data);
            break;
        case 'orders':
            reportHTML = generateProfessionalOrdersReportHTML(data);
            break;
        case 'complete':
            reportHTML = generateProfessionalCompleteReportHTML(data);
            break;
    }
    
    // Display report
    document.getElementById('reportContent').innerHTML = reportHTML;
    document.getElementById('reportSection').style.display = 'block';
    
    // Scroll to report
    document.getElementById('reportSection').scrollIntoView({ behavior: 'smooth' });
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

function hideReport() {
    document.getElementById('reportSection').style.display = 'none';
    currentReportData = null;
    currentReportType = null;
    currentDateRange = null;
}

function downloadReportPDF() {
    if (currentReportType && currentDateRange) {
        const params = new URLSearchParams({
            type: currentReportType,
            date_range: currentDateRange,
            format: 'pdf'
        });
        window.open(`controller/user/reports/generate-pdf.php?${params}`, '_blank');
    }
}

function getReportTitle(type) {
    switch (type) {
        case 'sales': return 'Sales Report';
        case 'products': return 'Product Performance Report';
        case 'orders': return 'Order Summary Report';
        case 'complete': return 'Complete Dashboard Report';
        default: return 'Report';
    }
}

function generateProfessionalSalesReportHTML(data) {
    if (!data.sales_data || data.sales_data.length === 0) {
        return `
            <div class="report-empty-state">
                <div class="text-center py-5">
                    <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No Sales Data Available</h5>
                    <p class="text-muted">No sales data found for the selected period.</p>
                </div>
            </div>
        `;
    }
    
    // Calculate totals
    const totalOrders = data.sales_data.reduce((sum, item) => sum + parseInt(item.orders), 0);
    const totalRevenue = data.sales_data.reduce((sum, item) => sum + parseFloat(item.revenue), 0);
    
    let html = `
        <div class="report-summary mb-4">
            <div class="row">
                <div class="col-md-4">
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="summary-content">
                            <h3>${data.sales_data.length}</h3>
                            <p>Periods</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="summary-content">
                            <h3>${totalOrders.toLocaleString()}</h3>
                            <p>Total Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="summary-content">
                            <h3>₱${totalRevenue.toLocaleString('en-US', {minimumFractionDigits: 2})}</h3>
                            <p>Total Revenue</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="report-table-section">
            <h5 class="section-title">
                <i class="fas fa-table"></i> Sales Breakdown
            </h5>
            <div class="table-responsive">
                <table class="table table-hover report-table">
                    <thead class="table-dark">
                        <tr>
                            <th><i class="fas fa-calendar"></i> Date</th>
                            <th><i class="fas fa-shopping-bag"></i> Orders</th>
                            <th><i class="fas fa-dollar-sign"></i> Revenue</th>
                            <th><i class="fas fa-chart-line"></i> Trend</th>
                        </tr>
                    </thead>
                    <tbody>
    `;
    
    data.sales_data.forEach((item, index) => {
        const revenue = parseFloat(item.revenue);
        const orders = parseInt(item.orders);
        const trend = index > 0 ? 
            (revenue > parseFloat(data.sales_data[index-1].revenue) ? 'up' : 'down') : 'stable';
        
        html += `
            <tr>
                <td><strong>${item.date}</strong></td>
                <td><span class="badge bg-primary">${orders.toLocaleString()}</span></td>
                <td><strong>₱${revenue.toLocaleString('en-US', {minimumFractionDigits: 2})}</strong></td>
                <td>
                    <i class="fas fa-arrow-${trend} text-${trend === 'up' ? 'success' : trend === 'down' ? 'danger' : 'secondary'}"></i>
                    <span class="text-${trend === 'up' ? 'success' : trend === 'down' ? 'danger' : 'secondary'}">${trend}</span>
                </td>
            </tr>
        `;
    });
    
    html += `
                    </tbody>
                </table>
            </div>
        </div>
    `;
    
    return html;
}

function generateProfessionalProductsReportHTML(data) {
    if (!data.products_data || data.products_data.length === 0) {
        return `
            <div class="report-empty-state">
                <div class="text-center py-5">
                    <i class="fas fa-box fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No Product Data Available</h5>
                    <p class="text-muted">No product performance data found for the selected period.</p>
                </div>
            </div>
        `;
    }
    
    // Calculate totals
    const totalSales = data.products_data.reduce((sum, item) => sum + parseInt(item.total_sales), 0);
    const totalRevenue = data.products_data.reduce((sum, item) => sum + parseFloat(item.total_revenue), 0);
    const totalOrders = data.products_data.reduce((sum, item) => sum + parseInt(item.order_count), 0);
    
    let html = `
        <div class="report-summary mb-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="summary-content">
                            <h3>${data.products_data.length}</h3>
                            <p>Products</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="summary-content">
                            <h3>${totalSales.toLocaleString()}</h3>
                            <p>Total Sales</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="summary-content">
                            <h3>₱${totalRevenue.toLocaleString('en-US', {minimumFractionDigits: 2})}</h3>
                            <p>Total Revenue</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div class="summary-content">
                            <h3>${totalOrders.toLocaleString()}</h3>
                            <p>Total Orders</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="report-table-section">
            <h5 class="section-title">
                <i class="fas fa-trophy"></i> Top Performing Products
            </h5>
            <div class="table-responsive">
                <table class="table table-hover report-table">
                    <thead class="table-dark">
                        <tr>
                            <th><i class="fas fa-box"></i> Product</th>
                            <th><i class="fas fa-tags"></i> Category</th>
                            <th><i class="fas fa-shopping-cart"></i> Sales</th>
                            <th><i class="fas fa-dollar-sign"></i> Revenue</th>
                            <th><i class="fas fa-receipt"></i> Orders</th>
                            <th><i class="fas fa-chart-bar"></i> Performance</th>
                        </tr>
                    </thead>
                    <tbody>
    `;
    
    data.products_data.forEach(item => {
        const sales = parseInt(item.total_sales);
        const revenue = parseFloat(item.total_revenue);
        const orders = parseInt(item.order_count);
        const performance = orders > 0 ? (sales / orders).toFixed(1) : '0.0';
        
        html += `
            <tr>
                <td>
                    <div class="product-info">
                        <strong>${item.product_name}</strong>
                    </div>
                </td>
                <td><span class="badge bg-secondary">${item.category_name}</span></td>
                <td><span class="badge bg-success">${sales.toLocaleString()}</span></td>
                <td><strong>₱${revenue.toLocaleString('en-US', {minimumFractionDigits: 2})}</strong></td>
                <td><span class="badge bg-primary">${orders.toLocaleString()}</span></td>
                <td>
                    <div class="performance-bar">
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-gradient" style="width: ${Math.min(100, (sales / Math.max(...data.products_data.map(p => parseInt(p.total_sales)))) * 100)}%">
                                ${performance} avg/order
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        `;
    });
    
    html += `
                    </tbody>
                </table>
            </div>
        </div>
    `;
    
    return html;
}

function generateProfessionalOrdersReportHTML(data) {
    if (!data.orders_data || data.orders_data.length === 0) {
        return `
            <div class="report-empty-state">
                <div class="text-center py-5">
                    <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No Order Data Available</h5>
                    <p class="text-muted">No order data found for the selected period.</p>
                </div>
            </div>
        `;
    }
    
    // Calculate totals
    const totalAmount = data.orders_data.reduce((sum, item) => sum + parseFloat(item.total_amount), 0);
    const totalItems = data.orders_data.reduce((sum, item) => sum + parseInt(item.item_count), 0);
    
    let html = `
        <div class="report-summary mb-4">
            <div class="row">
                <div class="col-md-4">
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div class="summary-content">
                            <h3>${data.orders_data.length}</h3>
                            <p>Total Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="summary-content">
                            <h3>₱${totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2})}</h3>
                            <p>Total Amount</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="summary-content">
                            <h3>${totalItems.toLocaleString()}</h3>
                            <p>Total Items</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="report-table-section">
            <h5 class="section-title">
                <i class="fas fa-list"></i> Order Details
            </h5>
            <div class="table-responsive">
                <table class="table table-hover report-table">
                    <thead class="table-dark">
                        <tr>
                            <th><i class="fas fa-hashtag"></i> Order #</th>
                            <th><i class="fas fa-user"></i> Customer</th>
                            <th><i class="fas fa-calendar"></i> Date</th>
                            <th><i class="fas fa-dollar-sign"></i> Amount</th>
                            <th><i class="fas fa-box"></i> Items</th>
                            <th><i class="fas fa-chart-line"></i> Avg/Item</th>
                        </tr>
                    </thead>
                    <tbody>
    `;
    
    data.orders_data.forEach(item => {
        const amount = parseFloat(item.total_amount);
        const items = parseInt(item.item_count);
        const avgPerItem = items > 0 ? (amount / items).toFixed(2) : '0.00';
        
        html += `
            <tr>
                <td><strong>${item.order_number}</strong></td>
                <td>${item.customer_name}</td>
                <td>${new Date(item.created_at).toLocaleDateString()}</td>
                <td><strong>₱${amount.toLocaleString('en-US', {minimumFractionDigits: 2})}</strong></td>
                <td><span class="badge bg-primary">${items.toLocaleString()}</span></td>
                <td>₱${avgPerItem}</td>
            </tr>
        `;
    });
    
    html += `
                    </tbody>
                </table>
            </div>
        </div>
    `;
    
    return html;
}

function generateProfessionalCompleteReportHTML(data) {
    let html = '';
    
    if (data.summary) {
        html += `
            <div class="report-summary mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <div class="summary-card">
                            <div class="summary-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="summary-content">
                                <h3>₱${parseFloat(data.summary.total_revenue).toLocaleString('en-US', {minimumFractionDigits: 2})}</h3>
                                <p>Total Revenue</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="summary-card">
                            <div class="summary-icon">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                            <div class="summary-content">
                                <h3>${data.summary.total_orders}</h3>
                                <p>Total Orders</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="summary-card">
                            <div class="summary-icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="summary-content">
                                <h3>${data.summary.total_products}</h3>
                                <p>Total Products</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="summary-card">
                            <div class="summary-icon">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <div class="summary-content">
                                <h3>${data.summary.conversion_rate}%</h3>
                                <p>Conversion Rate</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    if (data.monthly_sales && data.monthly_sales.length > 0) {
        html += `
            <div class="report-table-section mb-4">
                <h5 class="section-title">
                    <i class="fas fa-chart-line"></i> Monthly Sales Performance
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover report-table">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="fas fa-calendar"></i> Month</th>
                                <th><i class="fas fa-shopping-bag"></i> Orders</th>
                                <th><i class="fas fa-dollar-sign"></i> Revenue</th>
                                <th><i class="fas fa-chart-bar"></i> Trend</th>
                            </tr>
                        </thead>
                        <tbody>
        `;
        
        data.monthly_sales.forEach((item, index) => {
            const revenue = parseFloat(item.revenue);
            const trend = index > 0 ? 
                (revenue > parseFloat(data.monthly_sales[index-1].revenue) ? 'up' : 'down') : 'stable';
            
            html += `
                <tr>
                    <td><strong>${item.month}</strong></td>
                    <td><span class="badge bg-primary">${item.orders}</span></td>
                    <td><strong>₱${revenue.toLocaleString('en-US', {minimumFractionDigits: 2})}</strong></td>
                    <td>
                        <i class="fas fa-arrow-${trend} text-${trend === 'up' ? 'success' : trend === 'down' ? 'danger' : 'secondary'}"></i>
                        <span class="text-${trend === 'up' ? 'success' : trend === 'down' ? 'danger' : 'secondary'}">${trend}</span>
                    </td>
                </tr>
            `;
        });
        
        html += `
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    }
    
    if (data.top_products && data.top_products.length > 0) {
        html += `
            <div class="report-table-section">
                <h5 class="section-title">
                    <i class="fas fa-trophy"></i> Top Performing Products
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover report-table">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="fas fa-box"></i> Product</th>
                                <th><i class="fas fa-tags"></i> Category</th>
                                <th><i class="fas fa-shopping-cart"></i> Sales</th>
                                <th><i class="fas fa-dollar-sign"></i> Revenue</th>
                                <th><i class="fas fa-warehouse"></i> Stock</th>
                                <th><i class="fas fa-chart-bar"></i> Performance</th>
                            </tr>
                        </thead>
                        <tbody>
        `;
        
        data.top_products.forEach(item => {
            const sales = parseInt(item.total_sales);
            const revenue = parseFloat(item.total_revenue);
            const stock = parseInt(item.stock);
            const performance = stock > 0 ? ((sales / stock) * 100).toFixed(1) : '0.0';
            
            html += `
                <tr>
                    <td><strong>${item.product_name}</strong></td>
                    <td><span class="badge bg-secondary">${item.category_name}</span></td>
                    <td><span class="badge bg-success">${sales.toLocaleString()}</span></td>
                    <td><strong>₱${revenue.toLocaleString('en-US', {minimumFractionDigits: 2})}</strong></td>
                    <td><span class="badge bg-info">${stock.toLocaleString()}</span></td>
                    <td>
                        <div class="performance-bar">
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-gradient" style="width: ${Math.min(100, parseFloat(performance))}%">
                                    ${performance}%
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        });
        
        html += `
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    }
    
    return html;
}

function exportData() {
    Swal.fire({
        title: 'Export Data',
        html: `
            <div class="export-options">
                <div class="mb-3">
                    <label class="form-label">Data Type</label>
                    <select id="dataType" class="form-select">
                        <option value="dashboard">Dashboard Data</option>
                        <option value="orders">Orders</option>
                        <option value="products">Products</option>
                        <option value="sales">Sales Data</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Format</label>
                    <select id="exportFormat" class="form-select">
                        <option value="json">JSON</option>
                        <option value="csv">CSV</option>
                        <option value="excel">Excel</option>
                    </select>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Export',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const dataType = document.getElementById('dataType').value;
            const exportFormat = document.getElementById('exportFormat').value;
            
            if (!dataType || !exportFormat) {
                Swal.showValidationMessage('Please fill in all fields');
                return false;
            }
            
            return { dataType, exportFormat };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { dataType, exportFormat } = result.value;
            
            // Show loading
            Swal.fire({
                title: 'Exporting Data...',
                text: 'Please wait while we prepare your data',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Export data
            const params = new URLSearchParams({
                data_type: dataType,
                format: exportFormat
            });
            
            fetch(`controller/user/reports/export-data.php?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Download file
                        const blob = new Blob([data.content], {type: data.mime_type});
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = data.filename;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        URL.revokeObjectURL(url);
                        
                        Swal.fire({
                            title: 'Success!',
                            text: 'Data exported successfully',
                            icon: 'success'
                        });
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error exporting data:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to export data: ' + error.message,
                        icon: 'error'
                    });
                });
        }
    });
}
   </script>

<style>
/* Professional Report Styling */
.report-card {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border-radius: 15px;
    overflow: hidden;
}

.report-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    border: none;
}

.report-header h4 {
    color: white;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.report-header p {
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 0;
}

.report-actions .btn {
    margin-left: 0.5rem;
    border-radius: 25px;
    padding: 0.5rem 1rem;
    font-weight: 500;
}

.report-body {
    padding: 2rem;
    background-color: #f8f9fa;
}

.report-summary {
    margin-bottom: 2rem;
}

.summary-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    border: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    height: 100%;
}

.summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.summary-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    color: white;
    font-size: 1.5rem;
}

.summary-content h3 {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.summary-content p {
    color: #6c757d;
    font-weight: 500;
    margin-bottom: 0;
    text-transform: uppercase;
    font-size: 0.875rem;
    letter-spacing: 0.5px;
}

.report-table-section {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    margin-bottom: 1.5rem;
}

.section-title {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e9ecef;
}

.section-title i {
    color: #667eea;
    margin-right: 0.5rem;
}

.report-table {
    margin-bottom: 0;
    border-radius: 10px;
    overflow: hidden;
}

.report-table thead th {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    border: none;
    font-weight: 600;
    padding: 1rem;
    text-transform: uppercase;
    font-size: 0.875rem;
    letter-spacing: 0.5px;
}

.report-table thead th i {
    margin-right: 0.5rem;
    opacity: 0.8;
}

.report-table tbody tr {
    transition: background-color 0.2s ease;
}

.report-table tbody tr:hover {
    background-color: #f8f9fa;
}

.report-table tbody td {
    padding: 1rem;
    vertical-align: middle;
    border-color: #e9ecef;
}

.report-table .badge {
    font-size: 0.75rem;
    padding: 0.5rem 0.75rem;
    border-radius: 20px;
    font-weight: 500;
}

.performance-bar .progress {
    border-radius: 10px;
    background-color: #e9ecef;
}

.performance-bar .progress-bar {
    border-radius: 10px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    font-size: 0.75rem;
    font-weight: 600;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
}

.report-empty-state {
    background: white;
    border-radius: 15px;
    padding: 3rem;
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
}

.report-empty-state i {
    opacity: 0.3;
}

.report-empty-state h5 {
    color: #6c757d;
    font-weight: 600;
}

.report-empty-state p {
    color: #adb5bd;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .report-body {
        padding: 1rem;
    }
    
    .summary-card {
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .summary-content h3 {
        font-size: 1.5rem;
    }
    
    .report-table-section {
        padding: 1rem;
    }
    
    .report-table thead th,
    .report-table tbody td {
        padding: 0.75rem 0.5rem;
        font-size: 0.875rem;
    }
}

/* Animation for report appearance */
#reportSection {
    animation: slideInUp 0.5s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Gradient backgrounds for different report types */
.report-header.sales { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.report-header.products { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.report-header.orders { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.report-header.complete { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
</style>

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
