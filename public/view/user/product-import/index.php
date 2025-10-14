<div class="main-container" id="main-container">
    <div class="header-section text-center">
        <p class="lead">Manage your store inventory and import products from our catalog to expand your offerings.</p>
    </div>


    <div class="row">


        <!-- Search Form -->
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-search me-2"></i>Search Products</h5>
                </div>
                <div class="card-body">
                    <form id="import-form">
                        <div class="row g-3">
                            <!-- Keyword input -->
                            <div class="col-md-4">
                                <label for="keyword" class="form-label">Product Name or Keyword</label>
                                <input type="text" class="form-control" id="keyword" name="keyword" placeholder="Enter product name, SKU, or keyword" required>
                            </div>

                            <!-- Total Product input -->
                            <div class="col-md-4">
                                <label for="totalProduct" class="form-label">Total Product</label>
                                <input type="number" class="form-control" id="totalProduct" name="totalProduct" placeholder="Enter total products to display" required>
                            </div>

                            <!-- Buttons -->
                            <div class="col-md-4 d-flex align-items-end justify-content-start">
                                <div class="d-flex gap-2 w-100 justify-content-end">
                                    <button type="reset" class="btn btn-outline-secondary px-4">
                                        <i class="fas fa-redo me-2"></i>Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-search me-2"></i>Search Products
                                    </button>

                                </div>
                            </div>
                        </div>
                    </form>


                </div>
            </div>

            <!-- Product Results -->
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Search Results</h5>
                    <span class="badge bg-primary rounded-pill" id="counter">3 products found</span>
                </div>
                <div class="card-body">
                    <div class="row mt-4" id="product-results"></div>


                </div>
            </div>
        </div>
    </div>

</div>

<!-- Button to trigger modal -->

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


<script>
    function cardBuildProduct(data) {
        const container = document.getElementById('product-results');
        container.innerHTML = '';

        if (!data || data.length === 0) {
            container.innerHTML = `
            <div class="col-12">
                <div class="alert alert-warning">No products found.</div>
            </div>
        `;
            return;
        }

        data.forEach(product => {
            // Parse productName (which is a stringified array)
            let productNames = [];
            try {
                productNames = JSON.parse(product.productName);
            } catch (e) {
                productNames = [product.productNameEn || 'Unknown Product'];
            }

            const col = document.createElement('div');
            col.className = 'col-md-6 mb-4';

            col.innerHTML = `
            <div class="card product-card h-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <img src="public/images/products/${product.primary_image}" 
                                 class="product-image w-100" 
                                 alt="${product.product_sku}">
                        </div>
                        <div class="col-7">

                            <h5 class="card-title">${product.product_name}</h5>
                            <p class="text-muted small">${product.product_sku}</p>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="price-tag">${product.currency} ${product.price}</span>
                                <span class="badge badge-custom">${product.category_name || 'Uncategorized'}</span>
                            </div>

                            <div class="mt-3">
               
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-warehouse me-1"></i> ${product.warehouse_name}
                                </span>
                            
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-map-marker-alt me-1"></i> ${product.warehouse_address}
                                </span>
                            </div>

                            <div class="mt-3 d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary flex-fill" onclick="retrieveProduct('${product.product_id}')">
                                    <i class="fas fa-info-circle me-1"></i> Details
                                </button>
                                <button class="btn btn-sm btn-success flex-fill" onclick="importProduct('${product.product_id}')">
                                    <i class="fas fa-cart-plus me-1"></i> Import
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
            container.appendChild(col);
        });
    }

    function retrieveProduct(pid) {
        new GetRequest({
            getUrl: "controller/user/product-import?action=single-product",
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

    function importProduct(pid) {

        Swal.fire({
            title: 'Import Product',
            text: "Are you sure you want to import this product?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, import it!',
            cancelButtonText: 'No, cancel!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Importing...',
                    text: 'Please wait while the product is being imported.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                axios.post(
                        "controller/user/product-import/index.php?action=import-product",
                        JSON.stringify({
                            pid: pid
                        }), {
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        }
                    )
                    .then(response => {
                        Swal.close();
                        console.log("Product import response:", response.data.status);
                        console.log("Product import response data:", response.data.message);

                        if (response.data.status === "success") {
                            Swal.fire(
                                    'Imported!',
                                    'The product has been imported successfully.',
                                    'success'
                                )
                                .then(() => {
                                    // Optionally, you can refresh the product list or redirect
                                    window.location.reload();
                                });
                        } else {
                            Swal.fire(
                                'Error!',
                                response.data.message || 'Failed to import the product.',
                                'error'
                            );
                        }
                    })
                    .catch(error => {
                        console.error("Error importing product:", error.message);
                        Swal.close();
                        let errorMsg = 'An error occurred while importing the product.';
                        if (error.response && error.response.data && error.response.data.message) {
                            errorMsg = error.response.data.message;
                        } else if (error.response && error.response.data && typeof error.response.data === 'string') {
                            try {
                                const errObj = JSON.parse(error.response.data);
                                if (errObj.message) errorMsg = errObj.message;
                            } catch (e) {}
                        }
                        Swal.fire(
                            'Error!',
                            errorMsg,
                            'error'
                        );
                    });
            }
        });

    }

    // Add CSS for image enlargement
    document.head.insertAdjacentHTML('beforeend', `
<style>
.img-enlarged {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    max-height: 90vh;
    max-width: 90vw;
    z-index: 1060;
    box-shadow: 0 0 50px rgba(0,0,0,0.5);
    cursor: zoom-out;
}
</style>
`);
    window.viewProduct = (keyword, totalProduct) => {
        new GetRequest({
            getUrl: "controller/user/product-import?action=search-product",
            params: {
                keyword,
                totalProduct
            },
            callback: (err, data) => {
                if (err) {

                    console.error("Error fetching user data:", err);
                    const submitBtn = document.querySelector('button[type="submit"]');
                    submitBtn.innerHTML = '<i class="fas fa-search me-2"></i> Search Products';
                    submitBtn.disabled = false;
                    return;
                }
                console.log("User data retrieved:", data);

                console.log(data);
                // Update the counter badge
                const counter = document.getElementById('counter');
                counter.textContent = `${data.length} products found`;

                cardBuildProduct(data);

                const submitBtn = document.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="fas fa-search me-2"></i> Search Products';
                submitBtn.disabled = false;

            }
        }).send();
    };



    document.addEventListener('DOMContentLoaded', function() {
        // Form submission handling
        const importForm = document.getElementById('import-form');
        importForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const keyword = document.getElementById('keyword').value;
            const totalProduct = document.getElementById('totalProduct').value;


            const submitBtn = document.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Searching...';
            submitBtn.disabled = true;

            window.viewProduct(keyword, totalProduct);

        });
    });

    onload = () => {

        const keyword = document.getElementById('keyword').value || '';
        const totalProduct = document.getElementById('totalProduct').value || 10;
        window.viewProduct(keyword, totalProduct);
    };
</script>