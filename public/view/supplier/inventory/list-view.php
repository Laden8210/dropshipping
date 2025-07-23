<div class="main-container" id="main-container">
    <div class="header-section text-center">

        <p class="lead">Manage your existing inventory and import new products from our catalog to expand your offerings.</p>
    </div>


    <!-- Inventory Stats -->
    <div class="inventory-stats">
        <div class="stat-card">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                <i class="fas fa-box-open"></i>
            </div>
            <div class="stat-content">
                <h3>0</h3>
                <p>Total Products</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-success bg-opacity-10 text-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>0</h3>
                <p>Active Products</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                <i class="fas fa-pause-circle"></i>
            </div>
            <div class="stat-content">
                <h3>0</h3>
                <p>Inactive Products</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-info bg-opacity-10 text-info">
                <i class="fas fa-truck-loading"></i>
            </div>
            <div class="stat-content">
                <h3>0</h3>
                <p>Low Stock Items</p>
            </div>
        </div>
    </div>


    <div class="tab-content" id="inventoryTabContent">
        <!-- My Inventory Tab -->
        <div class="tab-pane fade show active" id="inventory" role="tabpanel" aria-labelledby="inventory-tab">
            <!-- Inventory Search Form -->
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Inventory</h5>
                    <button class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-sync-alt me-1"></i>Reset Filters
                    </button>
                </div>
                <div class="card-body">
                    <form id="search-form">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="inv-keyword" class="form-label">Product Name or SKU</label>
                                <input type="text" class="form-control" id="inv-keyword" placeholder="Search...">
                            </div>

                            <div class="col-md-3">
                                <label for="inv-status" class="form-label">Status</label>
                                <select class="form-select" id="inv-status">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-3 d-flex align-items-end">
                                <div class="d-flex gap-2 w-100">
                                    <button type="button" class="btn btn-outline-secondary flex-grow-1" onclick="clearFilters()">
                                        <i class="fas fa-redo me-2"></i>Clear
                                    </button>
                                    <button type="button" onclick="applyFilters()" class="btn btn-primary flex-grow-1">
                                        <i class="fas fa-search me-2"></i>Apply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Inventory Table -->
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-box me-2"></i>Inventory Items</h5>
                    <div>
                        <span class="badge bg-primary rounded-pill me-2" id="stat">0 active items</span>
                        <a href="inventory?action=add" class="btn btn-sm btn-success">
                            <i class="fas fa-plus me-1"></i>Add Product
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover inventory-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th>Stock</th>
                                    <th>Price</th>
                                    <th>Forex Conversion(PHP)</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </div>


    </div>

</div>

<div class="modal fade" id="stockMovementModal" tabindex="-1" aria-labelledby="stockMovementLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Stock Movement History</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body table-responsive">

                <div class="d-flex align-items-center mb-3">
                    <p class="text-muted">View the history of stock movements for this product, including restocks, sales, and adjustments.</p>

                </div>
                <div class="row mb-3 text-center">
                    <div class="col-md-4">
                        <div class="card shadow-sm border-start border-success border-3">
                            <div class="card-body py-2 px-3">
                                <div class="text-muted small">Current Stock</div>
                                <div class="text-success fw-semibold fs-5" id="current-stock">0</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm border-start border-primary border-3">
                            <div class="card-body py-2 px-3">
                                <div class="text-muted small">Total Ingoing</div>
                                <div class="text-primary fw-semibold fs-5" id="total-ingoing">0</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm border-start border-danger border-3">
                            <div class="card-body py-2 px-3">
                                <div class="text-muted small">Total Outgoing</div>
                                <div class="text-danger fw-semibold fs-5" id="total-outgoing">0</div>
                            </div>
                        </div>
                    </div>
                </div>


                <table class="table table-hover inventory-table text-center">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>Movement ID</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody id="stock-movement-body">
                        <tr>
                            <td>SM-06222025-134512</td>
                            <td>2023-10-01</td>
                            <td><span class="badge bg-success">Restock</span></td>
                            <td>50</td>
                            <td>$500.00</td>
                            <td>Monthly restock</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Stock Modal -->
<div class="modal fade" id="addStockModal" tabindex="-1" aria-labelledby="addStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="add-stock-form" action="controller/supplier/inventory/index.php?action=add-stock-movement" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add Stock Movement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <input type="hidden" name="product_id" id="product_id" value="">

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="movement_type" class="form-label">Movement Type</label>
                        <select class="form-select" id="movement_type" name="movement_type" required>
                            <option value="in">Restock</option>
                            <option value="out">Stock Out</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <textarea class="form-control" id="reason" name="reason" rows="2"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="add-stock-btn">Save Movement</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Product Info -->
                <div class="row">
                    <div class="col-md-8">
                        <h4 id="productNameEn"></h4>
                        <p><strong>Description:</strong></p>
                        <div id="productDescription" class="mb-3"></div>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>SKU:</strong> <span id="productSku"></span></p>
                                <p><strong>Category:</strong> <span id="productCategory"></span></p>
                                <p><strong>Status:</strong> <span id="status"></span></p>
                                <p><strong>Warehouse:</strong> <span id="warehouseName"></span></p>
                                <p><strong>Warehouse Address:</strong> <span id="warehouseAddress"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Price:</strong> <span id="price"></span></p>
                                <p><strong>Currency:</strong> <span id="currency"></span></p>
                                <p><strong>Weight:</strong> <span id="productWeight"></span>g</p>
                                <p><strong>Stock:</strong> <span id="currentStock"></span></p>
                                <p><strong>Change Date:</strong> <span id="changeDate"></span></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 text-center">
                        <img id="primaryImage" src="" class="img-fluid rounded mb-3" style="max-height: 300px;">
                        <p class="text-muted"><small>Primary Image</small></p>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Simple gallery functionality
    document.querySelectorAll('.gallery-thumb').forEach(thumb => {
        thumb.addEventListener('click', function() {
            document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
            this.classList.add('active');


            const newSrc = this.querySelector('img').src.replace('100', '400');
            document.getElementById('primaryImage').src = newSrc;
        });
    });

    window.viewProduct = (keyword, status) => {
        new GetRequest({
            getUrl: "controller/supplier/inventory/?action=get-inventory",
            params: {
                keyword,
                status,
            },
            callback: (err, data) => {
                if (err) return console.error("Error fetching user data:", err);
                console.log("User data retrieved:", data);


                const totalProducts = data.length;
                const cardHeader = document.querySelector('.card-header h5');
                cardHeader.innerHTML = `<i class="fas fa-box me-2"></i>Inventory Items (${totalProducts})`;

                const statBadge = document.getElementById('stat');
                statBadge.textContent = `${data.filter(product => product.status_db === 'active').length} active items`;

                const totalActive = data ? data.filter(product => product.status_db === 'active').length : 0;
                const totalInactive = data ? data.filter(product => product.status_db === 'inactive').length : 0;
                const totalLowStock = data ? data.filter(product => product.totalInventory < 10).length : 0;

                const stats = document.querySelectorAll('.stat-card h3');
                stats[0].textContent = totalProducts;
                stats[1].textContent = totalActive;


                stats[2].textContent = totalInactive;
                stats[3].textContent = totalLowStock;



                const tableBody = document.querySelector('.inventory-table tbody');
                tableBody.innerHTML = '';

                data.forEach(product => {
                    const row = document.createElement('tr');


                    row.innerHTML = `
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="public/images/products/${product.primary_image}" class="img-thumbnail me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                <span>${product.product}</span>
                            </div>
                        </td>
                        <td>${product.sku}</td>
                        <td><span class="badge bg-info text-dark">${product.category}</span></td>
                        <td>${product.stock}</td>
                        <td>${product.currency} ${product.price}</td>
                        <td>${product.converted_currency} ${product.converted_price}</td>

                        <td>
                            ${product.status === 'active' 
                                ? '<span class="badge bg-success">Active</span>' 
                                : product.status === 'inactive'
                                    ? '<span class="badge bg-warning text-dark">Inactive</span>'
                                    : product.status === 'archived'
                                        ? '<span class="badge bg-secondary">Archived</span>'
                                        : `<span class="badge bg-light text-dark">${product.status}</span>`
                            }
                        </td>
                        <td>
                            <button class="btn btn-sm btn-success" onclick="retrieveProduct(${product.product_id})">
                                <i class="fas fa-eye"></i> View
                            </button>
                            
                            <button class="btn btn-sm btn-primary" 
                            onclick="retrieveStockMovement(${product.product_id})">
                                <i class="fas fa-exchange-alt me-1"></i> Stock Movement
                            </button>

                            <button class="btn btn-sm btn-secondary" 
                            onclick="showAddStockModal(${product.product_id})">
                                <i class="fas fa-plus me-1"></i> Adjust Stock 
                            </button>
                         
                        </td>   
                        `;
                    tableBody.appendChild(row);
                });


            }
        }).send();
    };

    function clearFilters() {
        document.getElementById('inv-keyword').value = '';
        document.getElementById('inv-status').value = '';
        window.viewProduct('', '');
    }

    function applyFilters() {
        const keyword = document.getElementById('inv-keyword').value;
        const status = document.getElementById('inv-status').value;
        console.log("Applying filters:", keyword, status);
        window.viewProduct(keyword, status);
    }

    function retrieveProduct(pid) {
        new GetRequest({
            getUrl: "controller/supplier/product?action=single-product",
            params: {
                pid
            },
            callback: (err, data) => {
                if (err) return console.error("Error fetching product data:", err);

                const modal = new bootstrap.Modal(document.getElementById('productModal'));

                document.getElementById('productModalLabel').textContent = data.product_name || 'No Name';
                document.getElementById('productNameEn').textContent = data.product_name || '—';

                document.getElementById('productDescription').textContent = data.description || '—';
                document.getElementById('productSku').textContent = data.product_sku || '—';


                document.getElementById('productCategory').textContent = data.category_name || '—';
                document.getElementById('status').textContent = data.status || '—';


                document.getElementById('warehouseName').textContent = data.warehouse_name || '—';
                document.getElementById('warehouseAddress').textContent = data.warehouse_address || '—';


                document.getElementById('price').textContent = data.price || '0.00';
                document.getElementById('currency').textContent = data.currency || '—';
                document.getElementById('productWeight').textContent = data.product_weight || '—';


                document.getElementById('currentStock').textContent = data.current_stock ?? 'N/A';
                document.getElementById('changeDate').textContent = data.change_date || '—';


                const imagePath = `public/images/products/${data.primary_image}`;
                document.getElementById('primaryImage').src = imagePath;

                modal.show();
            }
        }).send();
    }


    function showAddStockModal(productId) {
        document.getElementById('add-stock-form').reset();
        document.getElementById('product_id').value = productId;
        const addStockModal = new bootstrap.Modal(document.getElementById('addStockModal'));
        addStockModal.show();
    }

    const createExamRequest = new CreateRequest({
        formSelector: '#add-stock-form',
        submitButtonSelector: '#add-stock-btn',
        callback: (err, res) => err ? console.error("Form submission error:", err) : console.log("Form submitted successfully:", res),
        redirectUrl: 'inventory',
    });

    function retrieveStockMovement(product_id) {
        new GetRequest({
            getUrl: "controller/supplier/inventory?action=get-stock-movement",
            params: {
                product_id
            },
            showSuccess: false,
            callback: (err, data) => {
                if (err) return console.error("Error fetching stock movement data:", err);
                console.log("Stock movement data retrieved:", data);

                const stockMovementBody = document.getElementById('stock-movement-body');
                stockMovementBody.innerHTML = '';

                let currentStock = 0;
                let totalIngoing = 0;
                let totalOutgoing = 0;

                data.forEach(movement => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${movement.movement_number}</td>
                        <td>${movement.created_at}</td>
                        <td><span class="badge bg-${movement.movement_type === 'in' ? 'success' : 'danger'}">${movement.movement_type === 'in' ? 'Restock' : 'Stock Out'}</span></td>
                        <td>${movement.quantity}</td>
                        <td>${movement.price}</td>
                        <td>${movement.reason || '-'}</td>
                    `;
                    stockMovementBody.appendChild(row);

                    // Update totals
                    if (movement.movement_type === 'in') {
                        currentStock += movement.quantity;
                        totalIngoing += movement.quantity;
                    } else {
                        currentStock -= movement.quantity;
                        totalOutgoing += movement.quantity;
                    }
                });

                // Update summary stats
                document.getElementById('current-stock').textContent = currentStock;
                document.getElementById('total-ingoing').textContent = totalIngoing;
                document.getElementById('total-outgoing').textContent = totalOutgoing;

                // Show the modal
                const stockMovementModal = new bootstrap.Modal(document.getElementById('stockMovementModal'));
                stockMovementModal.show();
            }
        }).send();
    }


    onload = () => {
        const keyword = document.getElementById('inv-keyword').value;


        const status = document.getElementById('inv-status').value;


        window.viewProduct(keyword, status);
    };
</script>