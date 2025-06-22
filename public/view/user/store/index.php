<div class="main-container" id="main-container">

    <!-- 🏬 Store Profile Section -->
    <div class="store-profile card shadow-sm mb-4">
        <div class="card-body d-flex align-items-center gap-3">
            <img src="store-logo.png" alt="Store Logo" class="rounded-circle border" style="width: 60px; height: 60px; object-fit: cover;">
            <div>
                <h5 class="mb-1 fw-bold" id="store-name">My Store Name</h5>
                <p class="mb-0 small text-muted" id="store-desc">Manage your store inventory and import products from our catalog.</p>
            </div>
        </div>
    </div>

    <div class="header-section text-center mb-4">
        <p class="lead text-muted">Expand your offerings by searching and importing products below.</p>
    </div>

    <div class="row">

        <!-- 🔍 Search Form -->
        <div class="col-lg-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-search me-2"></i>Search Products</h5>
                </div>
                <div class="card-body">
                    <form id="import-form">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="keyword" class="form-label">Product Name or Keyword</label>
                                <input type="text" class="form-control" id="keyword" name="keyword" placeholder="Enter product name, SKU, or keyword" required>
                            </div>

                            <div class="col-md-4">
                                <label for="totalProduct" class="form-label">Total Product</label>
                                <input type="number" class="form-control" id="totalProduct" name="totalProduct" placeholder="Enter total products to display" required>
                            </div>

                            <div class="col-md-4 d-flex align-items-end justify-content-end">
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

            <!-- 📦 Search Results -->
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Search Results</h5>
                    <span class="badge bg-primary rounded-pill" id="counter">3 products found</span>
                </div>
                <div class="card-body">
                    <div class="row mt-3" id="product-results">
                        <!-- Results injected here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
