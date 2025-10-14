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
                                    <th>Total Stock</th>
                                    <th>Price Range</th>
                                    <th>Profit Margin</th>
                                    <th>Warehouse</th>
                                    <th>Status</th>
                                    <th>Actions</th>

                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be populated here -->
                            </tbody>
                        </table>
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

                            </div>
                            <div class="col-md-6">
                                <p><strong>Total Variations:</strong> <span id="variationCount"></span></p>
                                <p><strong>Price Range:</strong> <span id="priceRange"></span></p>
                                <p><strong>Total Stock:</strong> <span id="totalStock"></span></p>
                                <p><strong>Last Updated:</strong> <span id="lastUpdated"></span></p>
                                <p><strong>Profit Margin:</strong> <span id="profitMargin"></span></p>
                            </div>
                        </div>

                        <!-- Variations Table -->
                        <div class="mt-4">
                            <h6>Product Variations</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Size</th>
                                            <th>Color</th>
                                            <th>Price</th>
                                            <th>Forex Conversion(PHP)</th>
                                           
                                            <th>Stock</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="variationsTable">
                                        <!-- Variations will be populated here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 text-center">
                        <img id="primaryImage" src="" class="img-fluid rounded mb-3" style="max-height: 300px;">
                        <p class="text-muted"><small>Primary Image</small></p>

                        <!-- Additional Images -->
                        <div id="additionalImages" class="mt-3">
                            <!-- Additional images will be populated here -->
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


<div class="modal fade" id="updateProfitMargin" tabindex="-1" aria-labelledby="updateProfitMarginLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateProfitMarginLabel">Update Profit Margin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="update-profit-margin-form" method="POST" action="controller/user/inventory/index.php?action=update-profit-margin">
                    <input type="hidden" id="product-id-for-margin" name="product_id">

                    <div class="mb-3">
                        <label for="new-profit-margin" class="form-label">Global Profit Margin (%)</label>
                        <input type="number" class="form-control" id="new-profit-margin" name="profit_margin"
                            step="0.01" min="0" max="1000" placeholder="Enter profit margin percentage">
                        <div class="form-text">This will apply to all variations. You can also set individual margins below.</div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Original Price</th>
                                    <th>Current Margin</th>
                                    <th>Selling Price</th>
                                    <th>Converted Price</th>
                                    <th>Selling Price in PHP</th>
                                    <th>New Margin %</th>
                                </tr>
                            </thead>
                            <tbody id="price-margin-table-body">
                                <!-- Data will be populated dynamically -->
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="update-profit-margin-btn" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Margins
                        </button>
                    </div>
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

                // Update card header with total products
                const totalProducts = data.length;
                const cardHeader = document.querySelector('.card-header h5');
                cardHeader.innerHTML = `<i class="fas fa-box me-2"></i>Inventory Items (${totalProducts})`;

                const statBadge = document.getElementById('stat');
                statBadge.textContent = `${data.filter(product => product.status === 'active').length} active items`;

                // Update inventory stats
                const totalActive = data.filter(product => product.status === 'active').length;
                const totalInactive = data.filter(product => product.status === 'inactive').length;
                const totalLowStock = data.filter(product => product.total_stock < 10).length;

                const stats = document.querySelectorAll('.stat-card h3');
                stats[0].textContent = totalProducts;
                stats[1].textContent = totalActive;
                stats[2].textContent = totalInactive;
                stats[3].textContent = totalLowStock;

                const tableBody = document.querySelector('.inventory-table tbody');
                tableBody.innerHTML = '';

                data.forEach(product => {
                    const row = document.createElement('tr');

                    // Calculate price display (show range if min and max are different)
                    const priceDisplay = product.min_price && product.max_price && product.min_price !== product.max_price ?
                        `${product.min_price} - ${product.max_price}` :
                        (product.min_price || product.max_price || 'N/A');

                    // Calculate selling price based on average price and profit margin
                    const avgPrice = product.min_price && product.max_price ?
                        (parseFloat(product.min_price) + parseFloat(product.max_price)) / 2 :
                        (parseFloat(product.min_price) || parseFloat(product.max_price) || 0);

                    const profitAmount = product.profit_margin ?
                        (avgPrice * parseFloat(product.profit_margin) / 100) :
                        0;

                    const sellingPrice = product.profit_margin ?
                        (avgPrice + profitAmount).toFixed(2) :
                        'N/A';



                    row.innerHTML = `
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="public/images/products/${product.primary_image}" 
                                 class="img-thumbnail me-2" 
                                 style="width: 40px; height: 40px; object-fit: cover;"
                                 onerror="this.src='public/images/placeholder.jpg'">
                            <div>
                                <div class="fw-semibold">${product.product_name}</div>
                                <small class="text-muted">${product.warehouse_name || 'No Warehouse'}</small>
                            </div>
                        </div>
                    </td>
                    <td>${product.product_sku}</td>
                    <td><span class="badge bg-info text-dark">${product.category_name}</span></td>
                    <td>
                        ${product.total_stock || 0}
                        ${product.total_stock < 10 ? '<span class="badge bg-danger ms-1">Low</span>' : ''}
                    </td>
                    <td>
                        ${priceDisplay}
                        ${product.min_price && product.max_price && product.min_price !== product.max_price 
                            ? `<br><small class="text-muted">Range: ${product.min_price} - ${product.max_price}</small>` 
                            : ''}
                    </td>
                                        <td>${product.profit_margin || 'No Profit Margin'}</td>
                 
                    <td>${product.warehouse_name || 'No Warehouse'}</td>
    

        
          
                    <td>
                        ${product.status === 'active' 
                            ? '<span class="badge bg-success">Active</span>' 
                            : product.status === 'inactive'
                                ? '<span class="badge bg-warning text-dark">Inactive</span>'
                                : product.status === 'archived'
                                    ? '<span class="badge bg-secondary">Archived</span>'
                                    : `<span class="badge bg-light text-dark">${product.status}</span>`
                        }
                        ${product.is_unlisted ? '<span class="badge bg-dark ms-1">Unlisted</span>' : ''}
                    </td>
                    <td>
                        <div class="">
                            <button class="btn btn-primary btn-sm" onclick="retrieveProduct(${product.product_id})">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn btn-secondary btn-sm" onclick="updateProfitMargin(${product.product_id})">
                                <i class="fas fa-edit"></i> Update Margin
                            </button>
                        </div>
                    </td>   
                `;
                    tableBody.appendChild(row);
                });
            }
        }).send();
    };

    function updateProfitMargin(pid) {
    new GetRequest({
        getUrl: "controller/user/inventory?action=single-product",
        params: { pid },
        callback: (err, data) => {
            if (err) return console.error("Error fetching product data:", err);

            const modal = new bootstrap.Modal(document.getElementById('updateProfitMargin'));
            document.getElementById('product-id-for-margin').value = pid;

            // Set the global margin input
            document.getElementById('new-profit-margin').value = data.profit_margin || 0;

            const tableBody = document.getElementById('price-margin-table-body');
            tableBody.innerHTML = '';

            if (data.variations && data.variations.length > 0) {
                data.variations.forEach(variation => {
                    const currentMargin = data.profit_margin || 0; // Use product-level margin
                    const originalPrice = parseFloat(variation.price) || 0;
                    const profitAmount = originalPrice * (currentMargin / 100);
                    const sellingPrice = originalPrice + profitAmount; // Calculate dynamically
                    const convertedPrice = parseFloat(variation.converted_price) || 0;

                    // Calculate profit amount in PHP (convert the profit amount, not the percentage)
                    const exchangeRate = convertedPrice > 0 ? convertedPrice / originalPrice : 0;
                    const profitAmountInPh = profitAmount * exchangeRate;
                    const sellingPriceInPh = convertedPrice + profitAmountInPh; // Calculate dynamically

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${originalPrice.toFixed(2)} ${variation.currency}</td>
                        <td>${currentMargin}% (${profitAmount.toFixed(2)} ${variation.currency})</td>
                        <td>${sellingPrice.toFixed(2)} ${variation.currency}</td>
                        <td>${convertedPrice > 0 ? convertedPrice.toFixed(2) : 'N/A'} PHP</td>
                        <td>${convertedPrice > 0 ? sellingPriceInPh.toFixed(2) : 'N/A'} PHP</td>
                        <td>
                            <input type="number" 
                                   class="form-control form-control-sm variation-margin" 
                                   data-variation-id="${variation.variation_id}"
                                   data-original-price="${originalPrice}"
                                   data-currency="${variation.currency}"
                                   data-converted-price="${convertedPrice}"
                                   value="${currentMargin}"
                                   step="0.01"
                                   min="0"
                                   max="1000"
                                   placeholder="Margin %">
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                // Product-level pricing (no variations)
                const currentMargin = data.profit_margin || 0;
                const minPrice = parseFloat(data.min_price) || 0;
                const maxPrice = parseFloat(data.max_price) || 0;
                const avgPrice = minPrice > 0 && maxPrice > 0 ? (minPrice + maxPrice) / 2 : minPrice || maxPrice || 0;
                const profitAmount = avgPrice * (currentMargin / 100);
                const sellingPrice = avgPrice + profitAmount; // Calculate dynamically
                const convertedPrice = parseFloat(data.converted_price) || 0;

                // Calculate profit amount in PHP
                const exchangeRate = convertedPrice > 0 ? convertedPrice / avgPrice : 0;
                const profitAmountInPh = profitAmount * exchangeRate;
                const sellingPriceInPh = convertedPrice + profitAmountInPh; // Calculate dynamically

                tableBody.innerHTML = `
                    <tr>
                        <td>${avgPrice.toFixed(2)} ${data.currency || 'USD'}</td>
                        <td>${currentMargin}% (${profitAmount.toFixed(2)} ${data.currency || 'USD'})</td>
                        <td>${sellingPrice.toFixed(2)} ${data.currency || 'USD'}</td>
                        <td>${convertedPrice > 0 ? convertedPrice.toFixed(2) : 'N/A'} PHP</td>
                        <td>${convertedPrice > 0 ? sellingPriceInPh.toFixed(2) : 'N/A'} PHP</td>
                        <td>
                            <input type="number" 
                                   class="form-control form-control-sm product-margin" 
                                   data-original-price="${avgPrice}"
                                   data-currency="${data.currency || 'USD'}"
                                   data-converted-price="${convertedPrice}"
                                   value="${currentMargin}"
                                   step="0.01"
                                   min="0"
                                   max="1000"
                                   placeholder="Margin %">
                        </td>
                    </tr>
                `;
            }

            // Re-initialize margin calculation after table is populated
            initializeMarginCalculation();
            modal.show();
        }
    }).send();
}

    function initializeMarginCalculation() {
        const marginInput = document.getElementById('new-profit-margin');
        const tableBody = document.getElementById('price-margin-table-body');

        marginInput.addEventListener('input', function() {
            const margin = parseFloat(this.value) || 0;
            console.log('Global margin changed to:', margin);

            // Update all variation margin inputs
            const marginInputs = tableBody.querySelectorAll('.variation-margin, .product-margin');
            marginInputs.forEach(input => {
                input.value = margin;

                const originalPrice = parseFloat(input.getAttribute('data-original-price'));
                const currency = input.getAttribute('data-currency');
                const convertedPrice = parseFloat(input.getAttribute('data-converted-price')) || 0;

                console.log('Processing variation:', {
                    originalPrice,
                    currency,
                    convertedPrice,
                    margin
                });

                // Calculate profit amount in original currency
                const profitAmount = originalPrice * (margin / 100);
                const sellingPrice = originalPrice + profitAmount;

                // Calculate profit amount in PHP
                const exchangeRate = convertedPrice > 0 ? convertedPrice / originalPrice : 0;
                const profitAmountInPh = profitAmount * exchangeRate;
                const sellingPriceInPh = convertedPrice + profitAmountInPh;

                console.log('Calculated values:', {
                    profitAmount,
                    sellingPrice,
                    profitAmountInPh,
                    sellingPriceInPh
                });

                // Update the table row - CORRECT CELL INDEXES
                const row = input.closest('tr');
                const cells = row.cells;

                // Cell indexes:
                // 0: Original Price
                // 1: Current Margin
                // 2: Selling Price  
                // 3: Converted Price
                // 4: Selling Price in PHP
                // 5: New Margin %

                cells[1].textContent = `${margin}% (${profitAmount.toFixed(2)} ${currency})`; // Current Margin
                cells[2].textContent = `${sellingPrice.toFixed(2)} ${currency}`; // Selling Price
                cells[4].textContent = `${convertedPrice > 0 ? sellingPriceInPh.toFixed(2) : 'N/A'} PHP`; // Selling Price in PHP

                console.log('Updated row:', {
                    margin: cells[1].textContent,
                    selling: cells[2].textContent,
                    sellingPHP: cells[4].textContent
                });
            });
        });

        // Add event listeners to individual margin inputs
        const marginInputs = tableBody.querySelectorAll('.variation-margin, .product-margin');
        marginInputs.forEach(input => {
            input.addEventListener('input', function() {
                const margin = parseFloat(this.value) || 0;
                const originalPrice = parseFloat(this.getAttribute('data-original-price'));
                const currency = this.getAttribute('data-currency');
                const convertedPrice = parseFloat(this.getAttribute('data-converted-price')) || 0;

                console.log('Individual margin changed:', {
                    originalPrice,
                    currency,
                    convertedPrice,
                    margin
                });

                // Calculate profit amount in original currency
                const profitAmount = originalPrice * (margin / 100);
                const sellingPrice = originalPrice + profitAmount;

                // Calculate profit amount in PHP
                const exchangeRate = convertedPrice > 0 ? convertedPrice / originalPrice : 0;
                const profitAmountInPh = profitAmount * exchangeRate;
                const sellingPriceInPh = convertedPrice + profitAmountInPh;

                // Update the table row
                const row = this.closest('tr');
                const cells = row.cells;

                cells[1].textContent = `${margin}% (${profitAmount.toFixed(2)} ${currency})`; // Current Margin
                cells[2].textContent = `${sellingPrice.toFixed(2)} ${currency}`; // Selling Price
                cells[4].textContent = `${convertedPrice > 0 ? sellingPriceInPh.toFixed(2) : 'N/A'} PHP`; // Selling Price in PHP

                updateMainMarginInput();
            });
        });
    }

    function updateMainMarginInput() {
        const marginInputs = document.querySelectorAll('.variation-margin');
        const mainInput = document.getElementById('new-profit-margin');

        if (marginInputs.length > 0) {
            const margins = Array.from(marginInputs).map(input => parseFloat(input.value) || 0);
            const allSame = margins.every(margin => margin === margins[0]);

            if (allSame) {
                mainInput.value = margins[0];
            } else {
                mainInput.value = '';
            }
        }
    }



    // event listener for auto computation of profit margin

    function retrieveProduct(pid) {
        new GetRequest({
            getUrl: "controller/user/inventory?action=single-product",
            params: {
                pid
            },
            callback: (err, data) => {
                if (err) return console.error("Error fetching product data:", err);

                const modal = new bootstrap.Modal(document.getElementById('productModal'));

                // Basic product info
                document.getElementById('productModalLabel').textContent = data.product_name || 'No Name';
                document.getElementById('productNameEn').textContent = data.product_name || '—';
                document.getElementById('productDescription').textContent = data.description || '—';
                document.getElementById('productSku').textContent = data.product_sku || '—';
                document.getElementById('productCategory').textContent = data.category_name || '—';
                document.getElementById('status').textContent = data.status || '—';
                document.getElementById('profitMargin').textContent = data.profit_margin + '%' || '—';

                // Handle variations
                const variations = data.variations || [];
                document.getElementById('variationCount').textContent = variations.length;

                // Calculate price range and total stock
                if (variations.length > 0) {
                    const prices = variations.map(v => parseFloat(v.price)).filter(p => !isNaN(p));
                    const minPrice = Math.min(...prices);
                    const maxPrice = Math.max(...prices);
                    const totalStock = variations.reduce((sum, v) => sum + (parseInt(v.stock_quantity) || 0), 0);

                    document.getElementById('priceRange').textContent =
                        minPrice === maxPrice ?
                        `${minPrice} ${variations[0].currency}` :
                        `${minPrice} - ${maxPrice} ${variations[0].currency}`;

                    document.getElementById('totalStock').textContent = totalStock;

                    // Find latest update date
                    const latestUpdate = variations.reduce((latest, v) => {
                        const updateDate = new Date(v.updated_at);
                        return updateDate > latest ? updateDate : latest;
                    }, new Date(0));

                    document.getElementById('lastUpdated').textContent =
                        latestUpdate > new Date(0) ? latestUpdate.toLocaleDateString() : '—';

                    // Populate variations table
                    populateVariationsTable(variations);
                } else {
                    document.getElementById('priceRange').textContent = '—';
                    document.getElementById('totalStock').textContent = '0';
                    document.getElementById('lastUpdated').textContent = '—';
                    document.getElementById('variationsTable').innerHTML = '<tr><td colspan="5" class="text-center">No variations found</td></tr>';
                }

                // Handle images
                const imagePath = `public/images/products/${data.primary_image}`;
                document.getElementById('primaryImage').src = imagePath;
                populateAdditionalImages(data.images || []);

                modal.show();
            }
        }).send();
    }

    function populateVariationsTable(variations) {
        const tbody = document.getElementById('variationsTable');
        tbody.innerHTML = '';

        variations.forEach(variation => {
            const row = document.createElement('tr');
            row.innerHTML = `
            <td>${variation.size || '—'}</td>
            <td>${variation.color || '—'}</td>

            <td>${variation.price} ${variation.currency}</td>
            <td>${variation.converted_currency} ${variation.converted_price}</td>

            <td>${variation.stock_quantity || 0}</td>
            <td>
                <span class="badge ${variation.is_active == 0 ? 'bg-success' : 'bg-secondary'}">
                    ${variation.is_active ==0 ? 'Active' : 'Inactive'}
                </span>
            </td>
        `;
            tbody.appendChild(row);
        });
    }

    function populateAdditionalImages(images) {
        const container = document.getElementById('additionalImages');
        container.innerHTML = '';

        if (images.length <= 1) return;

        const title = document.createElement('p');
        title.className = 'text-muted mb-2';
        title.innerHTML = '<small>Additional Images</small>';
        container.appendChild(title);

        images.forEach((image, index) => {
            if (index === 0) return; // Skip primary image

            const img = document.createElement('img');
            img.src = `public/images/products/${image}`;
            img.className = 'img-thumbnail me-2 mb-2';
            img.style = 'width: 80px; height: 80px; object-fit: cover;';
            img.alt = `Product image ${index + 1}`;
            container.appendChild(img);
        });
    }



    onload = () => {
        const keyword = document.getElementById('inv-keyword').value;


        const status = document.getElementById('inv-status').value;


        window.viewProduct(keyword, status);
    };
</script>