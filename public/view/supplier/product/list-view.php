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

            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Product</h5>
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


            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-box me-2"></i>Product</h5>
                    <div>
                        <span class="badge bg-primary rounded-pill me-2" id="stat">1,042 active items</span>
                        <a href="product?action=add" class="btn btn-sm btn-success">
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


<div class="modal fade" id="priceHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Price History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Price</th>
                            <th>Currency</th>
                        </tr>
                    </thead>
                    <tbody id="price-history-body">
                        <tr>
                            <td colspan="3">Loading...</td>
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
            <form id="add-stock-form">
                <div class="modal-header">
                    <h5 class="modal-title">Add Stock Movement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

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
                        <label for="price" class="form-label">Total Price</label>
                        <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <textarea class="form-control" id="reason" name="reason" rows="2"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save Movement</button>
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
            getUrl: "controller/supplier/product/?action=get-products",
            params: {
                keyword,

                status,

            },
            callback: (err, data) => {
                if (err) return console.error("Error fetching user data:", err);
                console.log("User data retrieved:", data);

                // update card header with total products
                const totalProducts = data.length;
                const cardHeader = document.querySelector('.card-header h5');
                cardHeader.innerHTML = `<i class="fas fa-box me-2"></i>Inventory Items (${totalProducts})`;

                const statBadge = document.getElementById('stat');
                statBadge.textContent = `${data.filter(product => product.status === 'active').length} active items`;
                // update inventory stats
                const totalActive = data.filter(product => product.status === 'active').length;
                const totalInactive = data.filter(product => product.status === 'inactive').length;
                const totalLowStock = data.filter(product => product.totalInventory < 10).length;

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
                            
                            <a href="product?action=edit&product_id=${product.product_id}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-edit"></i> Edit
                            </a>

                            <button class="btn btn-sm btn-info" onclick="viewProductHistory(${product.product_id})">
                                <i class="fas fa-history"></i> View Price History
                            </button>

                            <button class="btn btn-sm btn-warning" onclick="updateProductStatus(${product.product_id})">
                                <i class="fas fa-sync-alt"></i> Update Status 
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


    function viewProductHistory(productId) {
        new GetRequest({
            getUrl: "controller/supplier/inventory?action=price-history",
            params: {
                productId
            },
            callback: (err, response) => {
                const modal = new bootstrap.Modal(document.getElementById('priceHistoryModal'));
                const tbody = document.getElementById('price-history-body');
                tbody.innerHTML = '';

                if (err) {
                    console.error("Error fetching price history:", err);
                    tbody.innerHTML = '<tr><td colspan="3">Failed to load price history.</td></tr>';
                    modal.show();
                    return;
                }
      
                response.forEach(entry => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                    <td>${entry.change_date}</td>
                    <td>${entry.price}</td>
                    <td>${entry.currency}</td>
                `;
                    tbody.appendChild(row);
                });

                modal.show();
            }
        }).send();
    }


    onload = () => {
        const keyword = document.getElementById('inv-keyword').value;


        const status = document.getElementById('inv-status').value;


        window.viewProduct(keyword, status);
    };
</script>