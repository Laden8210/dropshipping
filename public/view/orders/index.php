<div class="main-container" id="main-container">
    <div class="header-section text-center">

        <p class="lead">Manage customer orders, track shipments, and update order status</p>
    </div>


    <div class="order-stats">
        <div class="stat-card">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="stat-content">
                <h3>142</h3>
                <p>Total Orders</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3>24</h3>
                <p>Pending Orders</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-info bg-opacity-10 text-info">
                <i class="fas fa-truck"></i>
            </div>
            <div class="stat-content">
                <h3>56</h3>
                <p>Processing Orders</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-success bg-opacity-10 text-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>62</h3>
                <p>Completed Orders</p>
            </div>
        </div>
    </div>

    <!-- Order Filter Form -->
    <div class="card mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Orders</h5>
            <button class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-redo me-1"></i>Reset Filters
            </button>
        </div>
        <div class="card-body">
            <form>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="order-id" class="form-label">Order ID</label>
                        <input type="text" class="form-control" id="order-id" placeholder="Order #">
                    </div>
                    <div class="col-md-3">
                        <label for="customer-name" class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="customer-name" placeholder="Customer name">
                    </div>
                    <div class="col-md-3">
                        <label for="order-status" class="form-label">Order Status</label>
                        <select class="form-select" id="order-status">
                            <option value="">All Statuses</option>
                            <option>Pending</option>
                            <option>Processing</option>
                            <option>Shipped</option>
                            <option>Delivered</option>
                            <option>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date-range" class="form-label">Date Range</label>
                        <select class="form-select" id="date-range">
                            <option>All Time</option>
                            <option>Today</option>
                            <option>Last 7 Days</option>
                            <option>Last 30 Days</option>
                            <option>This Month</option>
                            <option>Custom Range</option>
                        </select>
                    </div>
                    <div class="col-md-12 d-flex justify-content-end">
                        <div class="d-flex gap-2">
                            <button type="reset" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-redo me-2"></i>Clear
                            </button>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-search me-2"></i>Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Recent Orders</h5>
            <div>
                <span class="badge bg-primary rounded-pill me-2">24 new orders</span>
                <button class="btn btn-sm btn-success">
                    <i class="fas fa-plus me-1"></i>Create Order
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover order-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#ORD-2023-00142</td>
                            <td>
                                <div class="customer-info">
                                    <div class="customer-avatar">JS</div>
                                    <div>John Smith</div>
                                </div>
                            </td>
                            <td>Oct 12, 2023</td>
                            <td>3 items</td>
                            <td>$248.95</td>
                            <td><span class="status-badge status-processing">Processing</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary action-btn" data-bs-toggle="modal" data-bs-target="#orderModal">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info action-btn">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>#ORD-2023-00141</td>
                            <td>
                                <div class="customer-info">
                                    <div class="customer-avatar">MJ</div>
                                    <div>Mary Johnson</div>
                                </div>
                            </td>
                            <td>Oct 11, 2023</td>
                            <td>2 items</td>
                            <td>$129.99</td>
                            <td><span class="status-badge status-shipped">Shipped</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary action-btn" data-bs-toggle="modal" data-bs-target="#orderModal">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info action-btn">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>#ORD-2023-00140</td>
                            <td>
                                <div class="customer-info">
                                    <div class="customer-avatar">RW</div>
                                    <div>Robert Williams</div>
                                </div>
                            </td>
                            <td>Oct 10, 2023</td>
                            <td>1 item</td>
                            <td>$89.95</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary action-btn" data-bs-toggle="modal" data-bs-target="#orderModal">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info action-btn">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>#ORD-2023-00139</td>
                            <td>
                                <div class="customer-info">
                                    <div class="customer-avatar">ED</div>
                                    <div>Emily Davis</div>
                                </div>
                            </td>
                            <td>Oct 9, 2023</td>
                            <td>5 items</td>
                            <td>$421.50</td>
                            <td><span class="status-badge status-delivered">Delivered</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary action-btn" data-bs-toggle="modal" data-bs-target="#orderModal">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info action-btn">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>#ORD-2023-00138</td>
                            <td>
                                <div class="customer-info">
                                    <div class="customer-avatar">TB</div>
                                    <div>Thomas Brown</div>
                                </div>
                            </td>
                            <td>Oct 8, 2023</td>
                            <td>2 items</td>
                            <td>$75.25</td>
                            <td><span class="status-badge status-cancelled">Cancelled</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary action-btn" data-bs-toggle="modal" data-bs-target="#orderModal">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info action-btn">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

</div>

<!-- Order Detail Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalLabel">Order #ORD-2023-00142</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Order Details -->
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Order Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Order Date:</strong> Oct 12, 2023 14:25</p>
                                        <p><strong>Customer:</strong> John Smith</p>
                                        <p><strong>Email:</strong> john.smith@example.com</p>
                                        <p><strong>Phone:</strong> (555) 123-4567</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Payment Method:</strong> Credit Card (Visa **** 4242)</p>
                                        <p><strong>Payment Status:</strong> <span class="badge bg-success">Paid</span></p>
                                        <p><strong>Order Status:</strong> <span class="status-badge status-processing">Processing</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Timeline -->
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h6 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>Order Timeline</h6>
                            </div>
                            <div class="card-body">
                                <div class="order-timeline">
                                    <div class="timeline-step completed">
                                        <div class="timeline-content">
                                            <p class="mb-1"><strong>Order Placed</strong></p>
                                            <p class="text-muted mb-0">Oct 12, 2023 14:25</p>
                                        </div>
                                    </div>
                                    <div class="timeline-step completed">
                                        <div class="timeline-content">
                                            <p class="mb-1"><strong>Payment Confirmed</strong></p>
                                            <p class="text-muted mb-0">Oct 12, 2023 14:30</p>
                                        </div>
                                    </div>
                                    <div class="timeline-step active">
                                        <div class="timeline-content">
                                            <p class="mb-1"><strong>Processing</strong></p>
                                            <p class="text-muted mb-0">Currently in progress</p>
                                        </div>
                                    </div>
                                    <div class="timeline-step">
                                        <div class="timeline-content">
                                            <p class="mb-1"><strong>Shipped</strong></p>
                                            <p class="text-muted mb-0">Not yet shipped</p>
                                        </div>
                                    </div>
                                    <div class="timeline-step">
                                        <div class="timeline-content">
                                            <p class="mb-1"><strong>Delivered</strong></p>
                                            <p class="text-muted mb-0">Estimated delivery: Oct 18, 2023</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h6 class="mb-0"><i class="fas fa-box-open me-2"></i>Order Items</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4 pb-3 border-bottom order-items">
                                    <img src="https://via.placeholder.com/150/4361ee/ffffff?text=Headphones" alt="Product">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Wireless Bluetooth Headphones Pro</h6>
                                        <p class="mb-1 text-muted">SKU: HD-2023-PRO</p>
                                        <p class="mb-0">Qty: 1 × $89.99</p>
                                    </div>
                                    <div class="fw-bold">$89.99</div>
                                </div>
                                <div class="d-flex align-items-center mb-4 pb-3 border-bottom order-items">
                                    <img src="https://via.placeholder.com/150/3f37c9/ffffff?text=Smartwatch" alt="Product">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Smart Fitness Tracker Watch</h6>
                                        <p class="mb-1 text-muted">SKU: SW-FT-2023</p>
                                        <p class="mb-0">Qty: 1 × $129.99</p>
                                    </div>
                                    <div class="fw-bold">$129.99</div>
                                </div>
                                <div class="d-flex align-items-center order-items">
                                    <img src="https://via.placeholder.com/150/4cc9f0/ffffff?text=Charger" alt="Product">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Fast Wireless Charger</h6>
                                        <p class="mb-1 text-muted">SKU: CHG-WL-15W</p>
                                        <p class="mb-0">Qty: 1 × $28.97</p>
                                    </div>
                                    <div class="fw-bold">$28.97</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary and Actions -->
                    <div class="col-md-4">
                        <div class="order-summary-card mb-4">
                            <h6 class="mb-3"><i class="fas fa-receipt me-2"></i>Order Summary</h6>
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span>$248.95</span>
                            </div>
                            <div class="summary-row">
                                <span>Shipping:</span>
                                <span>$9.99</span>
                            </div>
                            <div class="summary-row">
                                <span>Tax:</span>
                                <span>$19.92</span>
                            </div>
                            <div class="summary-row">
                                <span>Discount:</span>
                                <span class="text-danger">-$10.00</span>
                            </div>
                            <div class="summary-row mt-3 pt-2 summary-total">
                                <span>Total:</span>
                                <span>$268.86</span>
                            </div>
                        </div>

                        <div class="order-summary-card mb-4">
                            <h6 class="mb-3"><i class="fas fa-map-marker-alt me-2"></i>Shipping Address</h6>
                            <p class="mb-1">John Smith</p>
                            <p class="mb-1">123 Main Street</p>
                            <p class="mb-1">Apt 4B</p>
                            <p class="mb-1">New York, NY 10001</p>
                            <p class="mb-0">United States</p>
                            <p class="mt-2 mb-0"><i class="fas fa-phone me-2"></i>(555) 123-4567</p>
                        </div>

                        <div class="order-summary-card">
                            <h6 class="mb-3"><i class="fas fa-cogs me-2"></i>Order Actions</h6>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary mb-2">
                                    <i class="fas fa-truck me-2"></i>Mark as Shipped
                                </button>
                                <button class="btn btn-outline-primary mb-2">
                                    <i class="fas fa-edit me-2"></i>Update Status
                                </button>
                                <button class="btn btn-outline-warning mb-2">
                                    <i class="fas fa-print me-2"></i>Print Invoice
                                </button>
                                <button class="btn btn-outline-danger">
                                    <i class="fas fa-times-circle me-2"></i>Cancel Order
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Simulated order timeline interaction
    document.addEventListener('DOMContentLoaded', function() {
        const timelineSteps = document.querySelectorAll('.timeline-step');

        timelineSteps.forEach(step => {
            step.addEventListener('click', function() {
                if (this.classList.contains('active')) {
                    this.classList.remove('active');
                    this.classList.add('completed');
                } else if (this.classList.contains('completed')) {
                    this.classList.remove('completed');
                } else {
                    this.classList.add('active');
                }
            });
        });
    });
</script>