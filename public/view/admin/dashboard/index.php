<?php
// Dashboard data will be loaded via JavaScript using GetRequest
?>

<div class="main-container" id="main-container">
    <div class="header-section text-center">
        <h2 class="mb-3">Admin Dashboard</h2>
        <p class="lead">System overview and analytics</p>
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
                    System-wide revenue
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Total Users</div>
                    <div class="stat-icon" style="background-color: rgba(46, 204, 113, 0.1); color: var(--success);">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="stat-value" id="users-value">Loading...</div>
                <div class="stat-change change-up">
                    <i class="fas fa-arrow-up"></i>
                    Registered users
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Total Orders</div>
                    <div class="stat-icon" style="background-color: rgba(76, 201, 240, 0.1); color: var(--accent);">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                </div>
                <div class="stat-value" id="orders-value">Loading...</div>
                <div class="stat-change change-up">
                    <i class="fas fa-arrow-up"></i>
                    All orders
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Active Stores</div>
                    <div class="stat-icon" style="background-color: rgba(243, 156, 18, 0.1); color: var(--warning);">
                        <i class="fas fa-store"></i>
                    </div>
                </div>
                <div class="stat-value" id="stores-value">Loading...</div>
                <div class="stat-change change-up">
                    <i class="fas fa-arrow-up"></i>
                    Operating stores
                </div>
            </div>
        </div>

        <!-- Additional Stats Row -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Suppliers</div>
                    <div class="stat-icon" style="background-color: rgba(155, 89, 182, 0.1); color: #9b59b6;">
                        <i class="fas fa-industry"></i>
                    </div>
                </div>
                <div class="stat-value" id="suppliers-value">Loading...</div>
                <div class="stat-change change-up">
                    <i class="fas fa-arrow-up"></i>
                    Active suppliers
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Couriers</div>
                    <div class="stat-icon" style="background-color: rgba(52, 152, 219, 0.1); color: #3498db;">
                        <i class="fas fa-truck"></i>
                    </div>
                </div>
                <div class="stat-value" id="couriers-value">Loading...</div>
                <div class="stat-change change-up">
                    <i class="fas fa-arrow-up"></i>
                    Active couriers
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Products</div>
                    <div class="stat-icon" style="background-color: rgba(230, 126, 34, 0.1); color: #e67e22;">
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
                    <div class="stat-title">Conversion Rate</div>
                    <div class="stat-icon" style="background-color: rgba(231, 76, 60, 0.1); color: #e74c3c;">
                        <i class="fas fa-percentage"></i>
                    </div>
                </div>
                <div class="stat-value" id="conversion-value">Loading...</div>
                <div class="stat-change change-up">
                    <i class="fas fa-arrow-up"></i>
                    Orders per user
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-row">
            <div class="chart-container" id="revenue-trends">
                <div class="chart-header">
                    <h3>Revenue Trends</h3>
                    <div class="chart-actions">
                        <button onclick="saveDashboardAsPDF('revenue-trends')"><i class="fas fa-download"></i></button>
                        <button><i class="fas fa-expand"></i></button>
                    </div>
                </div>
                <div class="chart-wrapper">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="chart-container" id="user-growth">
                <div class="chart-header">
                    <h3>User Growth</h3>
                    <div class="chart-actions">
                        <button onclick="saveDashboardAsPDF('user-growth')"><i class="fas fa-download"></i></button>
                    </div>
                </div>
                <div class="chart-wrapper">
                    <canvas id="userChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Report Tables -->
        <div class="tables-row">
            <div class="table-container" id="top-stores">
                <div class="table-header">
                    <h3>Top Performing Stores</h3>
                    <button class="export-btn" style="padding: 8px 15px; font-size: 14px;" onclick="saveDashboardAsPDF('top-stores')">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Store Name</th>
                                <th>Orders</th>
                                <th>Revenue</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody id="top-stores-body">
                            <tr>
                                <td colspan="4" class="text-center">Loading...</td>
                            </tr>
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
                                <th>Store</th>
                                <th>Date</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody id="recent-orders-body">
                            <tr>
                                <td colspan="5" class="text-center">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Order Status Overview -->
        <div class="tables-row">
            <div class="table-container" id="order-status-overview">
                <div class="table-header">
                    <h3>Order Status Overview</h3>
                    <button class="export-btn" style="padding: 8px 15px; font-size: 14px;" onclick="saveDashboardAsPDF('order-status-overview')">
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
                                <th>Trend</th>
                            </tr>
                        </thead>
                        <tbody id="order-status-body">
                            <tr>
                                <td colspan="4" class="text-center">Loading...</td>
                            </tr>
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
        stats: {
            total_revenue: 0,
            total_users: 0,
            total_orders: 0,
            active_stores: 0,
            total_suppliers: 0,
            total_couriers: 0,
            total_products: 0,
            conversion_rate: 0
        },
        recent_orders: [],
        top_stores: [],
        monthly_revenue: [],
        user_trends: [],
        order_status: []
    };

    // Load dashboard data using GetRequest
    document.addEventListener('DOMContentLoaded', function() {
        loadDashboardData();
    });

    function loadDashboardData() {
        new GetRequest({
            getUrl: 'controller/admin/dashboard/index.php',
            params: {},
            showLoading: false,
            showSuccess: false,
            callback: (err, data) => {
                if (err) {
                    console.error('Error loading dashboard data:', err);
                    return;
                }
                
                if (data && data.stats) {
                    dashboardData = data;
                    updateDashboardUI();
                    initializeCharts();
                } else {
                    console.error('Invalid dashboard data received:', data);
                }
            }
        }).send();
    }

    function updateDashboardUI() {
        if (!dashboardData || !dashboardData.stats) {
            console.error('Dashboard data or stats not available');
            return;
        }

        // Update stats cards
        document.getElementById('revenue-value').textContent = 'PHP ' + (dashboardData.stats.total_revenue || 0).toLocaleString();
        document.getElementById('users-value').textContent = (dashboardData.stats.total_users || 0).toLocaleString();
        document.getElementById('orders-value').textContent = (dashboardData.stats.total_orders || 0).toLocaleString();
        document.getElementById('stores-value').textContent = (dashboardData.stats.active_stores || 0).toLocaleString();
        document.getElementById('suppliers-value').textContent = (dashboardData.stats.total_suppliers || 0).toLocaleString();
        document.getElementById('couriers-value').textContent = (dashboardData.stats.total_couriers || 0).toLocaleString();
        document.getElementById('products-value').textContent = (dashboardData.stats.total_products || 0).toLocaleString();
        document.getElementById('conversion-value').textContent = (dashboardData.stats.conversion_rate || 0) + '%';

        // Update top stores table
        const topStoresBody = document.getElementById('top-stores-body');
        topStoresBody.innerHTML = '';
        if (dashboardData.top_stores && dashboardData.top_stores.length > 0) {
            dashboardData.top_stores.forEach(store => {
                const row = document.createElement('tr');
                row.innerHTML = `
                <td>${store.store_name || 'Unknown'}</td>
                <td>${(store.total_orders || 0).toLocaleString()}</td>
                <td>$${(store.total_revenue || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                <td><span class="badge bg-success">Active</span></td>
            `;
                topStoresBody.appendChild(row);
            });
        } else {
            topStoresBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No stores found</td></tr>';
        }

        // Update recent orders table
        const recentOrdersBody = document.getElementById('recent-orders-body');
        recentOrdersBody.innerHTML = '';
        if (dashboardData.recent_orders && dashboardData.recent_orders.length > 0) {
            dashboardData.recent_orders.forEach(order => {
                const row = document.createElement('tr');
                row.innerHTML = `
                <td>${order.order_number || 'N/A'}</td>
                <td>${order.customer_name || 'Unknown'}</td>
                <td>${order.store_name || 'N/A'}</td>
                <td>${order.created_at ? new Date(order.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'}) : 'N/A'}</td>
                <td>$${(order.total_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            `;
                recentOrdersBody.appendChild(row);
            });
        } else {
            recentOrdersBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No recent orders found</td></tr>';
        }

        // Update order status table
        const orderStatusBody = document.getElementById('order-status-body');
        orderStatusBody.innerHTML = '';
        const totalStatusCount = dashboardData.order_status.reduce((sum, status) => sum + status.count, 0);
        dashboardData.order_status.forEach(status => {
            const percentage = totalStatusCount > 0 ? Math.round((status.count / totalStatusCount) * 100) : 0;
            const row = document.createElement('tr');
            row.innerHTML = `
            <td>${status.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</td>
            <td>${status.count.toLocaleString()}</td>
            <td>${percentage}%</td>
            <td>
                <span class="badge bg-${status.status === 'delivered' ? 'success' : (status.status === 'pending' ? 'warning' : 'info')}">
                    ${status.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
                </span>
            </td>
        `;
            orderStatusBody.appendChild(row);
        });
    }

    // Initialize charts
    function initializeCharts() {
        // Revenue Trends Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: dashboardData.monthly_revenue.map(item => item.month),
                datasets: [{
                    label: 'Revenue',
                    data: dashboardData.monthly_revenue.map(item => item.revenue),
                    borderColor: '#4361ee',
                    backgroundColor: 'rgba(67, 97, 238, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Orders',
                    data: dashboardData.monthly_revenue.map(item => item.orders),
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

        // User Growth Chart
        const userCtx = document.getElementById('userChart').getContext('2d');
        const userChart = new Chart(userCtx, {
            type: 'bar',
            data: {
                labels: dashboardData.user_trends.map(item => item.month),
                datasets: [{
                    label: 'New Users',
                    data: dashboardData.user_trends.map(item => item.new_users),
                    backgroundColor: 'rgba(46, 204, 113, 0.8)',
                    borderColor: '#2ecc71',
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