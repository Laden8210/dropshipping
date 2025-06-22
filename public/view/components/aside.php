<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <!-- Logo -->
    <div class="logo-container text-center py-3">
        <img src="assets/img/logo.png" alt="LuzViMinDrop Logo" class="logo img-fluid" style="max-height: 40px;">
    </div>

    <!-- Store Profile Section -->
    <div class="store-profile-card bg-white rounded shadow-sm mx-1 p-3 mb-4 d-flex align-items-center">
        <div class="avatar-circle me-3">
            <span class="initial"><?php echo strtoupper(substr($name, 0, 1)); ?></span>
        </div>
        <div>
            <div class="fw-semibold fs-6 mb-1 text-dark"><?php echo htmlspecialchars($name); ?></div>
            <?php
            $badgeText = '';
            $badgeClass = 'badge ';
            if ($role == 'user') {
                $badgeText = 'Business Owner';
                $badgeClass .= 'bg-primary';
            } elseif ($role == 'supplier') {
                $badgeText = 'Supplier';
                $badgeClass .= 'bg-success';
            } elseif ($role == 'admin') {
                $badgeText = 'Administrator';
                $badgeClass .= 'bg-danger';
            }
            ?>
            <span class="<?php echo $badgeClass; ?>"><?php echo $badgeText; ?></span>
            <?php if ($role == 'user') : ?>
                <div class="small mt-1 text-dark">
                    Store:
                    <span class="fw-bold text-dark" id="store-name" onclick="updateStore()" style="cursor: pointer;">
                        <?php echo isset($store_name) ? htmlspecialchars($store_name) : 'Default Store Name'; ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>



    <nav class="nav-links px-1">
        <?php
        $navItems = [
            'dashboard' => ['Dashboard', 'ri-dashboard-line'],
            'product-import' => ['Product Import', 'ri-download-line'],
            'inventory' => ['Inventory', 'ri-archive-line'],
            'product' => ['Products', 'ri-product-hunt-line'],
            'category' => ['Categories', 'ri-list-check-2-line'],
            'orders' => ['Orders', 'ri-shopping-cart-line'],
            'store' => ['Store Profile', 'ri-store-line'],
            'reports' => ['Reports', 'ri-bar-chart-line'],
            'support' => ['Support', 'ri-customer-service-2-line'],
            'feedback' => ['Feedback', 'ri-star-line'],
            'settings' => ['Settings', 'ri-settings-2-line']
        ];

        $allowedPages = [
            'user' => ['dashboard', 'product-import', 'inventory', 'orders', 'store', 'reports', 'support', 'feedback', 'settings'],
            'supplier' => ['dashboard', 'inventory', 'product', 'category', 'orders', 'settings'],
            'admin' => ['dashboard', 'users', 'products', 'orders', 'reports', 'support', 'settings']
        ];

        foreach ($navItems as $key => [$label, $icon]) {
            if (in_array($key, $allowedPages[$role])) {
                $isActive = basename($_SERVER['PHP_SELF']) == "$key.php" ? 'active' : '';
                echo "<a href=\"$key\" class=\"nav-link $isActive\"><i class=\"$icon\"></i> <span>$label</span></a>";
            }
        }
        ?>
    </nav>


    <div class="logout-section px-1 mt-4">
        <a href="logout" class="nav-link text-danger">
            <i class="ri-logout-box-r-line"></i> <span>Logout</span>
        </a>
    </div>


</aside>

<div class="modal fade" id="storeNameModal" tabindex="-1" aria-labelledby="storeNameModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="storeNameForm" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Store Name</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label for="storeName" class="form-label">Store Name</label>
                    <select class="form-select" id="storeName" name="storeName" required>
                        <option value="" disabled selected>Select your store</option>
                        <?php foreach ($stores as $store): ?>
                            <option value="<?php echo htmlspecialchars($store['name']); ?>">
                                <?php echo htmlspecialchars($store['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function updateStore() {
        const modal = new bootstrap.Modal(document.getElementById('storeNameModal'));
        modal.show();
    }
</script>