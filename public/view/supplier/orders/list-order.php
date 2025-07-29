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

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="order-id" class="form-label">Order ID</label>
                    <input type="text" class="form-control" id="order-id" placeholder="Order #">
                </div>
                <div class="col-md-4">
                    <label for="customer-name" class="form-label">Customer Name</label>
                    <input type="text" class="form-control" id="customer-name" placeholder="Customer name">
                </div>
                <div class="col-md-4">
                    <label for="order-status" class="form-label">Order Status</label>
                    <select class="form-select" id="order-status">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="col-md-12 d-flex justify-content-end">
                    <div class="d-flex gap-2">
                        <button type="button" onclick="clearFilters()" class="btn btn-outline-secondary px-4">
                            <i class="fas fa-redo me-2"></i>Clear
                        </button>
                        <button type="button" onclick="applyFilters()" class="btn btn-primary px-4">
                            <i class="fas fa-search me-2"></i>Apply Filters
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Recent Orders</h5>
            <div>
                <span class="badge bg-primary rounded-pill me-2" id="new-orders-count">24 new orders</span>

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
                            <th>Tracking Number</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>

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
                                        <p><strong>Order Date:</strong> <span class="order-info-date"></span></p>

                                        <p><strong>Customer:</strong> John Smith</p>
                                        <p><strong>Email:</strong> john.smith@example.com</p>
                                        <p><strong>Phone:</strong> (555) 123-4567</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Payment Method:</strong> Credit Card (Visa **** 4242)</p>
                                        <p><strong>Transaction Num:</strong> <span class="order-info-transaction-id"></span></p>
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
                                <div class="order-timeline order-status-history d-flex flex-column gap-4"></div>
                            </div>
                        </div>


                        <!-- Order Items -->
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h6 class="mb-0"><i class="fas fa-box-open me-2"></i>Order Items</h6>
                            </div>
                            <div class="card-body order-items">


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

                                <button class="btn btn-outline-warning mb-2" id="printInvoiceBtn"
                                    onclick="printInvoice(this.getAttribute('data-order-id'))">
                                    <i class="fas fa-print me-2"></i>Print Invoice
                                </button>
                                <button class="btn btn-outline-danger" id="cancelOrderBtn"
                                    onclick="editOrder(this.getAttribute('data-order-id'))">
                                    <i class="fas fa-times me-2"></i>Cancel Order
                                </button>

                               
                                


                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

            </div>
        </div>
    </div>
</div>

<div class="modal class" id="update_status_modal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateStatusForm" action="controller/supplier/order/index.php?action=update-order-status" method="POST">
                    <input type="hidden" name="order_id" value="" id="order_id">
                    <div class="mb-3">
                        <label for="order-status-select" class="form-label">Select New Status</label>
                        <select class="form-select" id="order-status-select" required name="status">
                            <option value="" disabled selected>Select status</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="updateStatusForm" class="btn btn-primary" id="update-status-btn">Update Status</button>
                </form>
            </div>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
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

    new CreateRequest({
        formSelector: '#updateStatusForm',
        submitButtonSelector: '#update-status-btn',
        callback: (err, res) => err ? console.error("Form submission error:", err) : console.log("Form submitted successfully:", res),
        redirectUrl: 'orders',
    });


    printInvoice = (orderId) => {
        console.log("Printing invoice for order:", orderId);

        const printWindow = window.open(`print?action=print-invoice&order_id=${orderId}`, '_blank');
        if (printWindow) {
            printWindow.focus();
        } else {
            console.error("Failed to open print window. Please allow pop-ups for this site.");
        }
    };

    window.viewProduct = (order_number, customer_name, order_status) => {
        new GetRequest({
            getUrl: "controller/supplier/order?action=get-orders",
            params: {
                order_number: order_number || "",
                customer_name: customer_name || "",
                order_status: order_status || "",


            },
            callback: (err, data) => {
                if (err) return console.error("Error fetching user data:", err);
                console.log("User data retrieved:", data);



                const tableBody = document.querySelector(".order-table tbody");

                tableBody.innerHTML = "";

                const newOrdersCount = document.getElementById("new-orders-count");
                newOrdersCount.textContent = `${data.length} new orders`;

                // card kpi
                const totalOrders = data.length;
                const pendingOrders = data.filter(order => order.status === 'pending').length;
                const processingOrders = data.filter(order => order.status === 'processing').length;
                const completedOrders = data.filter(order => order.status === 'completed').length;
                document.querySelector('.stat-card:nth-child(1) h3').textContent = totalOrders;
                document.querySelector('.stat-card:nth-child(2) h3').textContent = pendingOrders;
                document.querySelector('.stat-card:nth-child(3) h3').textContent = processingOrders;
                document.querySelector('.stat-card:nth-child(4) h3').textContent = completedOrders;


                data.forEach(order => {
                    const row = document.createElement("tr");
                    // count the item quantity
                    order.items_count = order.items ?
                        order.items.reduce((sum, item) => sum + parseInt(item.quantity, 10), 0) :
                        0;

                    const customerName = `${order.user.first_name} ${order.user.last_name}`;
                    row.innerHTML = `
                        <td>${order.order_number}</td>
                        <td>
                            <div class="customer-info">
                                <div class="customer-avatar">${customerName.split(' ').map(n => n[0]).join('').toUpperCase()}</div>
                                <div>${customerName}</div>
                            </div>
                        </td>
                        <td>${order.created_at}</td>
                        <td>${order.items_count} items</td>
                        <td>${order.total_amount}</td>
                        <td><span class="status-badge status-${order.status.toLowerCase()}">${order.status}</span></td>
                        <td>${order.tracking_number || '-'}</td>
                        <td class="d-flex gap-1 align-content-center">
                            <button class="btn btn-sm btn-outline-primary action-btn"
                                onclick="getOrderDetails('${order.order_number}')">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-info action-btn" onclick="editOrder('${order.order_number}')">
                                <i class="fas fa-edit"></i>
                                Edit
                            </button>
                 
                            ${order.tracking_number ? `
                                <button class="btn btn-sm btn-outline-secondary action-btn" onclick="window.open('track?tracking_number=${order.tracking_number}', '_blank')">
                                    <i class="fas fa-truck"></i> Track
                                </button>
                                 <button class="btn btn-sm btn-outline-secondary action-btn" onclick="window.open('print?action=print-awb&tracking_number=${order.tracking_number}', '_blank')">
                                     <i class="fas fa-print"></i> Print AWB
                                 </button>
                            ` : ''}
                
                        </td>
                    `;

                    tableBody.appendChild(row);
                });
            }
        }).send();
    };

    function editOrder(orderNumber) {
        console.log("Editing order:", orderNumber);

        document.getElementById("order_id").value = orderNumber;
        const updateStatusModal = new bootstrap.Modal(document.getElementById('update_status_modal'));
        updateStatusModal.show();
    }

    // Function to fetch order details and populate the modal
    function getOrderDetails(orderNumber) {
        new GetRequest({
            getUrl: "controller/supplier/order?action=get-order-details",
            params: {
                order_number: orderNumber
            },
            callback: (err, data) => {


                console.log("Order details retrieved:", data.order_number);

                document.getElementById('orderModalLabel').textContent = `Order #${data.order_number}`;
                document.querySelector('.order-info-date').textContent = formatDateTime(data.created_at);


                const customerName = `${data.first_name} ${data.last_name}`;

                const orderModalElement = document.getElementById('orderModal');
                const orderItemsContainer = orderModalElement.querySelector('.order-items');
                if (orderItemsContainer) {
                    orderItemsContainer.innerHTML = "";
                    (data.items || []).forEach((item, idx) => {
                        const itemElement = document.createElement('div');
                        itemElement.className = 'd-flex align-items-center mb-4 pb-3 border-bottom';
                        itemElement.innerHTML = `
                            <img src="public/images/products/${item.primary_image   }" alt="Product">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${item.product_name || 'Product'}</h6>
                                <p class="mb-1 text-muted">SKU: ${item.sku || '-'}</p>
                                <p class="mb-0">Qty: ${item.quantity} × ₱${parseFloat(item.price).toFixed(2)}</p>
                            </div>
                            <div class="fw-bold">₱${(parseFloat(item.quantity) * parseFloat(item.price)).toFixed(2)}</div>
                        `;
                        orderItemsContainer.appendChild(itemElement);
                    });
                }


                const summaryCard = document.querySelectorAll('.order-summary-card')[0];
                if (summaryCard) {
                    const summaryRows = summaryCard.querySelectorAll('.summary-row');
                    if (summaryRows.length >= 4) {
                        summaryRows[0].children[1].textContent = `₱${parseFloat(data.subtotal).toFixed(2)}`;
                        summaryRows[1].children[1].textContent = `₱${parseFloat(data.shipping_fee).toFixed(2)}`;
                        summaryRows[2].children[1].textContent = `₱${parseFloat(data.tax).toFixed(2)}`;

                    }
                    const summaryTotal = summaryCard.querySelector('.summary-total span:last-child');
                    if (summaryTotal) summaryTotal.textContent = `₱${parseFloat(data.total_amount).toFixed(2)}`;
                }

                const shippingAddress = data.shipping_address || {};
                const shippingCard = document.querySelectorAll('.order-summary-card')[1];
                if (shippingCard) {
                    shippingCard.innerHTML = `
                        <h6 class="mb-3"><i class="fas fa-map-marker-alt me-2"></i>Shipping Address</h6>
                        <p class="mb-1"><i class="fas fa-user me-2"></i><strong>Name:</strong> ${customerName}</p>
                        <p class="mb-1"><i class="fas fa-home me-2"></i><strong>Address:</strong> ${shippingAddress.address_line || '-'}</p>
                        <p class="mb-1"><i class="fas fa-city me-2"></i><strong>City/Region:</strong> ${shippingAddress.city || '-'}, ${shippingAddress.region || '-'}</p>
                        <p class="mb-1"><i class="fas fa-map-pin me-2"></i><strong>Barangay:</strong> ${shippingAddress.brgy || '-'}</p>
                        <p class="mb-0"><i class="fas fa-mail-bulk me-2"></i><strong>Postal Code:</strong> ${shippingAddress.postal_code || '-'}</p>
                    `;

                }

                // Order Information (left column)
                const infoCol = document.querySelectorAll('.card-body .row .col-md-6')[0];
                if (infoCol) {
                    infoCol.innerHTML = `
                        <p><strong>Order Date:</strong> <span class="order-info-date">${formatDateTime(data.created_at)}</span></p>
                        <p><strong>Customer:</strong> ${customerName}</p>
                        <p><strong>Email:</strong> ${data.user_email}</p>
                        <p><strong>Phone:</strong> N/A</p>
                    `;
                }
                const infoCol2 = document.querySelectorAll('.card-body .row .col-md-6')[1];

                if (infoCol2) {
                    infoCol2.innerHTML = `
                        <p><strong>Payment Method:</strong> ${data.payment && data.payment.payment_method ? formatPaymentMethod(data.payment.payment_method) : '-'}</p>
                        <p><strong>Payment Status:</strong> <span class="badge bg-success">${data.payment && data.payment.status ? capitalize(data.payment.status) : '-'}</span></p>
                        <p><strong>Order Status:</strong> <span class="status-badge status-${data.status.toLowerCase()}">${capitalize(data.status)}</span></p>
                    
                        <p><strong>Transaction Num:</strong> <span class="order-info-transaction-id">${data.payment && data.payment.transaction_id ? data.payment.transaction_id : '-'}</span></p>`;
                }

                // get the buttons
                const printBtn = document.getElementById("printInvoiceBtn");
                // const cancelBtn = document.getElementById("cancelOrderBtn");

                printBtn.setAttribute("data-order-id", data.order_number);
                // cancelBtn.setAttribute("data-order-id", data.order_number);


                const statusHistory = data.status_history || [];
                renderStatusTimeline(statusHistory);

                const orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
                orderModal.show();
            }
        }).send();
    }

    function renderStatusTimeline(statusHistory) {
        const container = document.querySelector('.order-status-history');
        if (!container) return;

        container.innerHTML = "";

        const allStatuses = ['pending', 'processing', 'shipped', 'delivered', 'completed']; // expected flow
        const completedStatuses = statusHistory.map(s => s.status);
        const latestStatus = completedStatuses[completedStatuses.length - 1];

        allStatuses.forEach((status, index) => {
            const statusData = statusHistory.find(s => s.status === status);
            const isCompleted = completedStatuses.includes(status);
            const isActive = latestStatus === status && !['completed', 'cancelled', 'refunded', 'failed'].includes(status);

            const statusClass = isCompleted ? 'completed' : isActive ? 'active' : 'pending';
            const dateText = statusData ? new Date(statusData.created_at).toLocaleString() : 'Not yet occurred';

            const item = document.createElement('div');
            item.className = `timeline-step ${statusClass}`;
            item.innerHTML = `
            <div class="timeline-icon">
                <i class="fas ${isCompleted ? 'fa-check' : isActive ? 'fa-spinner fa-spin' : 'fa-clock'}"></i>
            </div>
            <div class="timeline-content">
                <p class="mb-1"><strong>${capitalize(status)}</strong></p>
                <p class="text-muted mb-0">${dateText}</p>
            </div>
        `;
            container.appendChild(item);
        });
    }

    // Helper
    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }


    function formatDateTime(datetimeStr) {
        const d = new Date(datetimeStr);
        return d.toLocaleString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function formatPaymentMethod(method) {
        switch (method) {
            case "credit_card":
                return "Credit Card (**** **** **** 4242)";
            default:
                return method.replace("_", " ");
        }
    }

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function getColor(index) {
        const colors = ["4361ee", "3f37c9", "4cc9f0"];
        return colors[index % colors.length];
    }

    function clearFilters() {
        document.getElementById("order-id").value = "";
        document.getElementById("customer-name").value = "";
        document.getElementById("order-status").value = "";
        document.getElementById("date-range").value = "";
        window.viewProduct("", "", "", "");
    }

    function applyFilters() {
        const orderNumber = document.getElementById("order-id").value;
        const customerName = document.getElementById("customer-name").value;
        const orderStatus = document.getElementById("order-status").value;
        const dateRange = document.getElementById("date-range").value;
        window.viewProduct(orderNumber, customerName, orderStatus);

    }

    onload = () => {
        const orderNumber = document.getElementById("order-id").value;
        const status = document.getElementById("order-status").value;

        const customerName = document.getElementById("customer-name").value;

        window.viewProduct(orderNumber, customerName, status);
    };
</script>