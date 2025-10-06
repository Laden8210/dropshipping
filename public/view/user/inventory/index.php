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

            <!-- Inventory Table -->
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-box me-2"></i>Inventory Items</h5>
                    <div>
                        <span class="badge bg-primary rounded-pill me-2" id="stat">1,042 active items</span>
                        <a href="product-import" class="btn btn-sm btn-success">
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
                                    <th>Profit Margin</th>
                                    <th>Selling Price</th>
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


<div class="modal fade" id="updateProfitMargin" tabindex="-1" aria-labelledby="updateProfitMarginLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateProfitMarginLabel">Update Profit Margin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="update-profit-margin-form" method="POST" action="controller/user/inventory/index.php?action=update-profit-margin">

                    <input type="hidden" id="product-id-for-margin" name="product_id">

                    <div class="mb-3">
                        <label for="new-profit-margin" class="form-label">New Profit Margin (%)</label>
                        <input type="number" class="form-control" id="new-profit-margin" name="profit_margin" required>
                    </div>

                    <div class="mb-3">
                        <label for="converted-currency" class="form-label">Converted Currency</label>
                        <input type="text" class="form-control" id="converted-currency" name="converted_currency" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="profit-margin-amount" class="form-label">Profit Margin Amount</label>
                        <input type="number" class="form-control" id="profit-margin-amount" name="profit_margin_amount" readonly>
                    </div>


                    <div class="mb-3">
                        <label for="selling-price" class="form-label">Selling Price</label>
                        <input type="number" class="form-control" id="selling-price" name="selling_price" readonly>
                    </div>

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="update-profit-margin-btn" class="btn btn-primary">Update Margin</button>

                </form>
            </div>

        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    new CreateRequest({
        formSelector: '#update-profit-margin-form',
        submitButtonSelector: '#update-profit-margin-btn',
        callback: (err, res) => err ? console.error("Form submission error:", err) : console.log("Form submitted successfully:", res),
        redirectUrl: 'inventory',
    });


    document.addEventListener('DOMContentLoaded', function() {
        const searchForm = document.getElementById('search-form');
        searchForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const keyword = document.getElementById('inv-keyword').value;

            const status = document.getElementById('inv-status').value;


            window.viewProduct(keyword, status);
        });
    });

    window.viewProduct = (keyword, status) => {
        new GetRequest({
            getUrl: "controller/user/inventory?action=search-product",
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
                                <span>${product.product_name}</span>
                            </div>
                        </td>
                        <td>${product.product_sku}</td>
                        <td><span class="badge bg-info text-dark">${product.category_name}</span></td>
                        <td>${product.current_stock !== null && product.current_stock !== undefined ? product.current_stock : 'No available stock'}</td>
                        <td>${product.currency} ${product.price}</td>
                        <td>${product.converted_currency} ${product.converted_price}</td>
                        <td>
                            ${product.profit_margin ? product.profit_margin + '%' : 'N/A'}
                            <br>
                            <small class="text-muted">
                                Total: 
                                ${
                                    (product.converted_price && product.profit_margin)
                                        ? (parseFloat(product.converted_price) * parseFloat(product.profit_margin) / 100).toFixed(2)
                                        : 'N/A'
                                }
                                ${product.converted_currency || ''}
                            </small>
                        </td>
                       <td>
                            ${
                                product.converted_currency && product.converted_price && product.profit_margin
                                    ? `${product.converted_currency} ${(parseFloat(product.converted_price) + (parseFloat(product.converted_price) * parseFloat(product.profit_margin) / 100)).toFixed(2)}`
                                    : 'N/A'
                            }
                        </td>

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
                            <button class="btn btn-sm btn-primary" onclick="retrieveProduct(${product.product_id})">
                                <i class="fas fa-eye"></i> View
                            </button>
                        
                            <button class="btn btn-sm btn-secondary" onclick="updateProfitMargin(${product.product_id}, '${product.converted_price}')">
                                <i class="fas fa-edit"></i> Edit Margin
                            </button>
                         
                        </td>   
                        `;
                    tableBody.appendChild(row);
                });


            }
        }).send();
    };

    function updateProfitMargin(productId, amount) {
        const modal = new bootstrap.Modal(document.getElementById('updateProfitMargin'));

        // Set product ID and amount
        document.getElementById('product-id-for-margin').value = productId;
        document.getElementById('converted-currency').value = amount;
        document.getElementById('new-profit-margin').value = '';
        document.getElementById('profit-margin-amount').value = '';
        document.getElementById('selling-price').value = '';

        // Store base amount in a hidden attribute or global var if needed
        document.getElementById('new-profit-margin').setAttribute('data-amount', amount);

        modal.show();
    }

    // Listen to profit margin input
    document.addEventListener('DOMContentLoaded', () => {
        const marginInput = document.getElementById('new-profit-margin');

        marginInput.addEventListener('input', function() {
            const amount = parseFloat(this.getAttribute('data-amount'));
            const margin = parseFloat(this.value);

            if (!isNaN(amount) && !isNaN(margin)) {
                const profitAmount = amount * (margin / 100);
                const sellingPrice = amount + profitAmount;

                document.getElementById('profit-margin-amount').value = profitAmount.toFixed(2);
                document.getElementById('selling-price').value = sellingPrice.toFixed(2);
            } else {
                document.getElementById('profit-margin-amount').value = '';
                document.getElementById('selling-price').value = '';
            }
        });
    });

    // event listener for auto computation of profit margin


    function retrieveProduct(pid) {
        new GetRequest({
            getUrl: "controller/user/product-import?action=single-product",
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



    onload = () => {
        const keyword = document.getElementById('inv-keyword').value;


        const status = document.getElementById('inv-status').value;


        window.viewProduct(keyword, status);
    };
</script>