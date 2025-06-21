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



<!-- Modal -->
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

<script>
    function cardBuildProduct(data) {
        const container = document.getElementById('product-results');
        container.innerHTML = ''; // Clear previous results

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
                            <img src="${product.productImage}" 
                                 class="product-image w-100" 
                                 alt="${product.productNameEn}">
                        </div>
                        <div class="col-7">
                            <h6 class="text-primary mb-1">${product.productSku}</h6>
                            <h5 class="card-title">${productNames[0]}</h5>
                            <p class="text-muted small">${product.productNameEn}</p>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="price-tag">$${product.sellPrice}</span>
                                <span class="badge badge-custom">${product.categoryName || 'Uncategorized'}</span>
                            </div>

                            <div class="mt-3">
                                <span class="badge bg-light text-dark me-1">
                                    <i class="fas fa-weight me-1"></i> ${product.productWeight}g
                                </span>
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-warehouse me-1"></i> Listed ${product.listingCount}x
                                </span>
                            </div>

                            <div class="mt-3 d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary flex-fill" onclick="retrieveProduct('${product.pid}')">
                                    <i class="fas fa-info-circle me-1"></i> Details
                                </button>
                                <button class="btn btn-sm btn-success flex-fill" onclick="importProduct('${product.pid}')">
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

    function importProduct(pid) {
        // display sweetalert 

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
                        "controller/product-import/index.php?action=import-product",
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
            getUrl: "controller/product-import?action=search-product",
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
        // Initialize the product search with default values
        const keyword = document.getElementById('keyword').value || '';
        const totalProduct = document.getElementById('totalProduct').value || 10;
        window.viewProduct(keyword, totalProduct);
    };
</script>