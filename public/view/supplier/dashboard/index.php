<?php
// Dashboard data will be loaded via JavaScript using GetRequest
?>

<div class="main-container" id="main-container">
    <div class="header-section text-center">
        <h2 class="mb-3">Supplier Dashboard</h2>
        <p class="lead">Manage your products and track performance</p>
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
                    Total products
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Low Stock Alert</div>
                    <div class="stat-icon" style="background-color: rgba(243, 156, 18, 0.1); color: var(--warning);">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
                <div class="stat-value" id="low-stock-value">Loading...</div>
                <div class="stat-change" id="low-stock-change">
                    <i class="fas fa-arrow-up"></i>
                    Checking...
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
    </div>
</div>

<script>
// Dashboard data will be loaded dynamically
let dashboardData = {
    stats: { total_revenue: 0, total_orders: 0, total_products: 0, low_stock_count: 0 },
    recent_orders: [],
    top_products: [],
    monthly_sales: [],
    category_revenue: []
};

// Load dashboard data using GetRequest
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
});

function loadDashboardData() {
    new GetRequest({
        getUrl: 'controller/supplier/dashboard/index.php',
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
    document.getElementById('revenue-value').textContent = '₱' + dashboardData.stats.total_revenue.toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('orders-value').textContent = dashboardData.stats.total_orders.toLocaleString();
    document.getElementById('products-value').textContent = dashboardData.stats.total_products.toLocaleString();
    document.getElementById('low-stock-value').textContent = dashboardData.stats.low_stock_count;
    
    // Update low stock alert styling
    const lowStockChange = document.getElementById('low-stock-change');
    if (dashboardData.stats.low_stock_count > 0) {
        lowStockChange.className = 'stat-change change-down';
        lowStockChange.innerHTML = '<i class="fas fa-arrow-down"></i> Need restocking';
    } else {
        lowStockChange.className = 'stat-change change-up';
        lowStockChange.innerHTML = '<i class="fas fa-arrow-up"></i> All good';
    }

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