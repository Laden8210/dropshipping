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
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <!-- Main Product Info -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h4 id="productNameEn"></h4>
                        <p class="mb-1"><strong>Description:</strong></p>
                        <div id="productDescription" class="mb-3"></div>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>SKU:</strong> <span id="productSku"></span></p>
                                <p><strong>Category:</strong> <span id="productCategory"></span></p>
                                <p><strong>Material:</strong> <span id="productMaterial"></span></p>
                                <p><strong>Product Type:</strong> <span id="productType"></span></p>
                                <p><strong>Supplier:</strong> <span id="supplierName"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Weight:</strong> <span id="productWeight"></span>g</p>
                                <p><strong>Packing Weight:</strong> <span id="packingWeight"></span>g</p>
                                <p><strong>Price:</strong> $<span id="sellPrice"></span></p>
                                <p><strong>Suggested Price:</strong> $<span id="suggestSellPrice"></span></p>
                                <p><strong>Status:</strong> <span id="status"></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <img id="primaryImage" src="" class="img-fluid rounded mb-3" style="max-height: 300px;">
                        <p class="text-muted"><small>Primary Variant Image</small></p>
                    </div>
                </div>

                <!-- Product Images Gallery -->
                <div class="mb-4">
                    <h5>Product Images</h5>
                    <div class="d-flex flex-wrap gap-2" id="productImages">
                        <!-- Images will be injected here -->
                    </div>
                </div>

                <!-- Variants Table -->
                <div class="mb-4">
                    <h5>Available Variants</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Variant Key</th>
                                    <th>Image</th>
                                    <th>SKU</th>
                                    <th>Dimensions</th>
                                    <th>Weight</th>
                                    <th>Price</th>
                                    <th>Suggested Price</th>
                                </tr>
                            </thead>
                            <tbody id="variantTableBody">
                                <!-- Variants will be injected here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Additional Details -->
                <div class="mb-4">
                    <h5>Additional Details</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Materials:</strong> <span id="materialDetails"></span></p>
                            <p><strong>Packing:</strong> <span id="packingDetails"></span></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Product Properties:</strong> <span id="productProperties"></span></p>
                            <p><strong>Created Time:</strong> <span id="createrTime"></span></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Listed Count:</strong> <span id="listedNum"></span></p>
                            <p><strong>Supplier ID:</strong> <span id="supplierId"></span></p>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Simple gallery functionality
    document.querySelectorAll('.gallery-thumb').forEach(thumb => {
        thumb.addEventListener('click', function() {
            document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // In a real app, this would change the main image
            const newSrc = this.querySelector('img').src.replace('100', '400');
            document.getElementById('primaryImage').src = newSrc;
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const searchForm = document.getElementById('search-form');
        searchForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent form submission

            const keyword = document.getElementById('inv-keyword').value;

            const status = document.getElementById('inv-status').value;


            window.viewProduct(keyword, sort_by);
        });
    });

    window.viewProduct = (keyword, sort_by) => {
        new GetRequest({
            getUrl: "controller/inventory?action=search-product",
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
                stats[0].textContent = totalProducts; // Total Products
                stats[1].textContent = totalActive; // Active Products


                stats[2].textContent = totalInactive; // Inactive Products
                stats[3].textContent = totalLowStock; // Low Stock Items



                // table 
                const tableBody = document.querySelector('.inventory-table tbody');
                tableBody.innerHTML = ''; // Clear existing rows

                data.forEach(product => {
                    const row = document.createElement('tr');
                    let images = [];

                    try {
                        images = JSON.parse(product.productImage);
                    } catch (e) {
                        console.error("Failed to parse productImage:", e);
                    }



                    const firstImage = Array.isArray(images) && images.length > 0 ?
                        images[0] :
                        'https://via.placeholder.com/40/000000/ffffff?text=No+Image';

                    row.innerHTML = `
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="${firstImage}" class="rounded me-3" alt="Product" width="40">
                                <div>${product.productNameEn}</div>
                            </div>
                        </td>
                        <td>${product.productSku}</td>
                        <td>${product.categoryName}</td>
                        <td>
                            <div class="d-flex align-items-center">
                         
                                <span>${product.totalInventory}</span>
                            </div>
                        </td>
                        <td>$${product.suggestSellPrice}</td>
                        <td>₱${product.exchangeRate}</td>
                        <td><span class="status-badge status-${product.status_db.toLowerCase()}">${product.status_db}</span></td>
                        <td>

                            <button class="btn btn-sm btn-outline-info action-btn"  onclick="retrieveProduct('${product.pid}')"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-sm btn-outline-danger action-btn"><i class="fas fa-trash-alt"></i></button>
                        </td>`;
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


    onload = () => {
        const keyword = document.getElementById('inv-keyword').value;


        const status = document.getElementById('inv-status').value;


        window.viewProduct(keyword, status);
    };
</script>