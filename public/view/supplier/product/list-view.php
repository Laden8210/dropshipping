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
                <h3>1,248</h3>
                <p>Total Products</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-success bg-opacity-10 text-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>1,042</h3>
                <p>Active Products</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                <i class="fas fa-pause-circle"></i>
            </div>
            <div class="stat-content">
                <h3>206</h3>
                <p>Inactive Products</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-info bg-opacity-10 text-info">
                <i class="fas fa-truck-loading"></i>
            </div>
            <div class="stat-content">
                <h3>42</h3>
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
                                    <option>Active</option>
                                    <option>Inactive</option>
                                    <option>Low Stock</option>
                                </select>
                            </div>

                            <div class="col-md-3 d-flex align-items-end">
                                <div class="d-flex gap-2 w-100">
                                    <button type="reset" class="btn btn-outline-secondary flex-grow-1">
                                        <i class="fas fa-redo me-2"></i>Clear
                                    </button>
                                    <button type="submit" class="btn btn-primary flex-grow-1">
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
                    <button type="button" class="btn btn-sm btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#addStockModal">
                        + Add Stock
                    </button>
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

    document.addEventListener('DOMContentLoaded', function() {
        const searchForm = document.getElementById('search-form');
        searchForm.addEventListener('submit', function(event) {
            event.preventDefault(); 

            const keyword = document.getElementById('inv-keyword').value;

            const status = document.getElementById('inv-status').value;


            window.viewProduct(keyword, sort_by);
        });
    });

    window.viewProduct = (keyword, sort_by) => {
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
                statBadge.textContent = `${data.filter(product => product.status_db === 'active').length} active items`;
                // update inventory stats
                const totalActive = data.filter(product => product.status_db === 'active').length;
                const totalInactive = data.filter(product => product.status_db === 'inactive').length;
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
                            
                            <button href="inventory?action=edit&id=${product.product_id}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-edit"></i> Edit
                            </button>

                            <button class="btn btn-sm btn-info" onclick="retrieveStockMovement(${product.product_id})">
                                <i class="fas fa-history"></i> View Price History
                            </button>

                            <button class="btn btn-sm btn-warning" onclick="updateProductStatus(${product.product_id})">
                                <i class="fas fa-sync-alt"></i> Update Status 
                            </button>

                            

                            <button href="inventory?action=delete&id=${product.product_id}" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i> Delete 
                            </button>
                        </td>   
                        `;
                    tableBody.appendChild(row);
                });


            }
        }).send();
    };

    function retrieveProduct(pid) {
        new GetRequest({
            getUrl: "controller/product-import?action=single-product",
            params: {
                pid
            },
            callback: (err, data) => {

                if (err) return console.error("Error fetching product data:", err);
                console.log("Product data retrieved:", data);

                const modal = new bootstrap.Modal(document.getElementById('productModal'));

                // Set modal title
                document.getElementById('productModalLabel').textContent = data.productNameEn;

                // Set primary product info
                document.getElementById('productNameEn').textContent = data.productNameEn;
                document.getElementById('productDescription').innerHTML = data.description;
                document.getElementById('productSku').textContent = data.productSku;
                document.getElementById('productCategory').textContent = data.categoryName;
                document.getElementById('productType').textContent = data.productType;
                document.getElementById('supplierName').textContent = data.supplierName;
                document.getElementById('productWeight').textContent = data.productWeight;
                document.getElementById('packingWeight').textContent = data.packingWeight;
                document.getElementById('sellPrice').textContent = data.sellPrice;
                document.getElementById('suggestSellPrice').textContent = data.suggestSellPrice;
                document.getElementById('status').textContent = data.status;
                document.getElementById('listedNum').textContent = data.listedNum;
                document.getElementById('supplierId').textContent = data.supplierId;

                // Set material info
                document.getElementById('productMaterial').textContent =
                    data.materialNameEnSet.join(', ');

                // Set additional details
                document.getElementById('materialDetails').textContent =
                    `${data.materialNameSet.join(', ')} (${data.materialNameEnSet.join(', ')})`;

                document.getElementById('packingDetails').textContent =
                    `${data.packingNameSet.join(', ')} (${data.packingNameEnSet.join(', ')})`;

                document.getElementById('productProperties').textContent =
                    `${data.productProSet.join(', ')} (${data.productProEnSet.join(', ')})`;

                // Format and set created time
                const createrTime = new Date(data.createrTime);
                document.getElementById('createrTime').textContent =
                    createrTime.toLocaleString();

                // Set primary image (first image in the list)
                if (data.productImageSet.length > 0) {
                    document.getElementById('primaryImage').src = data.productImageSet[0];
                }

                // Display product images
                const imagesContainer = document.getElementById('productImages');
                imagesContainer.innerHTML = '';
                data.productImageSet.forEach(imgUrl => {
                    const imgWrapper = document.createElement('div');
                    imgWrapper.className = 'position-relative';
                    imgWrapper.style.width = '120px';
                    imgWrapper.style.height = '120px';
                    imgWrapper.innerHTML = `
                    <img src="${imgUrl}" 
                         class="img-thumbnail h-100 w-100" 
                         style="object-fit: cover; cursor: pointer"
                         onclick="this.classList.toggle('img-enlarged')">
                `;
                    imagesContainer.appendChild(imgWrapper);
                });

                // Display variants table
                const variantTableBody = document.getElementById('variantTableBody');
                variantTableBody.innerHTML = '';
                data.variants.forEach(variant => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                    <td>${variant.variantKey || 'N/A'}</td>
                    <td><img src="${variant.variantImage}" style="height:50px"></td>
                    <td>${variant.variantSku}</td>
                    <td>${variant.variantLength}×${variant.variantWidth}×${variant.variantHeight}mm</td>
                    <td>${variant.variantWeight}g</td>
                    <td>$${variant.variantSellPrice}</td>
                    <td>$${variant.variantSugSellPrice}</td>
                `;
                    variantTableBody.appendChild(row);
                });

                modal.show();
            }
        }).send();
    }

    function retrieveStockMovement(productId) {
        new GetRequest({
            getUrl: "controller/supplier/inventory?action=get-stock-movement",
            params: {
                productId
            },
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
                        <td>${movement.movement_id}</td>
                        <td>${new Date(movement.date).toLocaleDateString()}</td>
                        <td><span class="badge bg-${movement.type === 'in' ? 'success' : 'danger'}">${movement.type === 'in' ? 'Restock' : 'Stock Out'}</span></td>
                        <td>${movement.quantity}</td>
                        <td>$${movement.price.toFixed(2)}</td>
                        <td>${movement.reason || '-'}</td>
                    `;
                    stockMovementBody.appendChild(row);

                    // Update totals
                    if (movement.type === 'in') {
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