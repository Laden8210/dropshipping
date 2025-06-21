    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="logo-container">
            <img src="assets/img/logo.png" alt="LuzViMinDrop Logo" class="logo">
        </div>

        <div class="user-profile">
            <div class="user-icon">
                <?php echo strtoupper(substr($name, 0, 1)); ?>
            </div>
            <div class="profile-actions">
                <span class="user-name"><?php echo $name; ?></span>
                <a href="profile.php" class="edit-icon" title="Edit Profile">
                    <i class="ri-edit-line"></i>
                </a>
            </div>
            <?php if ($role == 'user') : ?>
                <span style="font-size: 14px; margin-right:20px;">Business Owner</span>
            <?php elseif ($role == 'supplier') : ?>
                <span style="font-size: 14px; margin-right:20px;">Supplier</span>
            <?php elseif ($role == 'admin') : ?>
                <span style="font-size: 14px; margin-right:20px;">Administrator</span>
            <?php endif; ?>
        </div>

        <?php if ($role == 'user') : ?>
            <nav class="nav-links">
                <a href="dashboard" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard' ? 'active' : ''; ?>">
                    <i class="ri-dashboard-line"></i> <span>Dashboard</span>
                </a>
                <a href="product-import" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'product-import' ? 'active' : ''; ?>">
                    <i class="ri-download-line"></i> <span>Product Import</span>
                </a>
                <a href="inventory" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'inventory' ? 'active' : ''; ?>">
                    <i class="ri-archive-line"></i> <span>Inventory</span>
                </a>
                <a href="orders" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'orders' ? 'active' : ''; ?>">
                    <i class="ri-shopping-cart-line"></i> <span>Orders</span>
                </a>
                <a href="store" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'store' ? 'active' : ''; ?>">
                    <i class="ri-shopping-cart-line"></i> <span>Store Profile</span>
                </a>

                <a href="reports" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reports' ? 'active' : ''; ?>">
                    <i class="ri-bar-chart-line"></i> <span>Reports</span>
                </a>

                <a href="support" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'support' ? 'active' : ''; ?>">
                    <i class="ri-customer-service-2-line"></i> <span>Support</span>
                </a>
                <a href="feedback" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'feedback' ? 'active' : ''; ?>">
                    <i class="ri-star-line"></i> <span>Feedback</span>
                </a>

                <a href="settings" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings' ? 'active' : ''; ?>">
                    <i class="ri-settings-2-line"></i> <span>Settings</span>
                </a>
            </nav>

        <?php elseif ($role == 'supplier') : ?>

            <nav class="nav-links">
                <a href="dashboard" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard' ? 'active' : ''; ?>">
                    <i class="ri-dashboard-line"></i> <span>Dashboard</span>
                </a>

                <a href="inventory" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'inventory' ? 'active' : ''; ?>">
                    <i class="ri-archive-line"></i> <span>Inventory</span>
                </a>


                <a href="category" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'category' ? 'active' : ''; ?>">
                    <i class="ri-list-check-2-line"></i> <span>Categories</span>
                </a>
                <a href="orders" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'orders' ? 'active' : ''; ?>">
                    <i class="ri-shopping-cart-line"></i> <span>Orders</span>
                </a>

                <a href="settings" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings' ? 'active' : ''; ?>">
                    <i class="ri-settings-2-line"></i> <span>Settings</span>
                </a>
            </nav>

        <?php elseif ($role == 'admin') : ?>
            <nav class="nav-links">
                <a href="dashboard" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard' ? 'active' : ''; ?>">
                    <i class="ri-dashboard-line"></i> <span>Dashboard</span>
                </a>
                <a href="users" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users' ? 'active' : ''; ?>">
                    <i class="ri-user-3-line"></i> <span>Users</span>
                </a>
                <a href="products" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products' ? 'active' : ''; ?>">
                    <i class="ri-product-hunt-line"></i> <span>Products</span>
                </a>
                <a href="orders" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'orders' ? 'active' : ''; ?>">
                    <i class="ri-shopping-cart-line"></i> <span>Orders</span>
                </a>


                <a href="reports" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reports' ? 'active' : ''; ?>">
                    <i class="ri-bar-chart-line"></i> <span>Reports</span>
                </a>
                <a href="support" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'support' ? 'active' : ''; ?>">
                    <i class="ri-customer-service-2-line"></i> <span>Support</span>
                </a>
                <a href="settings" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings' ? 'active' : ''; ?>">
                    <i class="ri-settings-2-line"></i> <span>Settings</span>
                </a>
            </nav>
        <?php endif; ?>




        <div class="logout-section">
            <a href="logout" class="logoutLink" id="logoutLink">
                <i class="ri-logout-box-r-line"></i> <span>Logout</span>
            </a>
        </div>

        <!-- Toggle Button -->
        <button class="toggle-btn" id="toggle-btn">
            <i class="ri-menu-line"></i>
        </button>
    </aside>