<!-- Sidebar -->
<aside class="sidebar" id="sidebar">

    <div class="logo-container text-center py-3">
        <img src="assets/img/logo.png" alt="LuzViMinDrop Logo" class="logo img-fluid" style="max-height: 40px;">
    </div>


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
            }elseif ($role == 'courier') {
                $badgeText = 'Courier';
                $badgeClass .= 'bg-warning';
            } else {
                $badgeText = 'Unknown Role';
                $badgeClass .= 'bg-secondary';
            }
            ?>
            <span class="<?php echo $badgeClass; ?>"><?php echo $badgeText; ?></span>
            <?php if ($role == 'user') : ?>
                <div class="small mt-1 text-dark">
                    Store:
                    <span class="fw-bold text-dark" id="store-name" onclick="updateStore()" style="cursor: pointer;">

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
            'deliveries' => ['Deliveries', 'ri-truck-line'],
            'inventory' => ['Inventory', 'ri-archive-line'],
            'product' => ['Products', 'ri-product-hunt-line'],
            'category' => ['Categories', 'ri-file-list-line'],
            'orders' => ['Orders', 'ri-shopping-cart-line'],
            'reports' => ['Reports', 'ri-bar-chart-line'],
            'support' => ['Support', 'ri-customer-service-2-line'],
            'feedback' => ['Feedback', 'ri-star-line'],
            'users' => ['User Management', 'ri-user-settings-line'],
            'activity' => ['System Activity', 'ri-line-chart-line'],
            'settings' => ['Settings', 'ri-settings-2-line']
        ];

        $allowedPages = [
            'user' => ['dashboard', 'product-import', 'inventory', 'orders', 'reports', 'support', 'feedback', 'settings'],
            'supplier' => ['dashboard', 'inventory', 'product', 'category', 'orders', 'settings'],
            'admin' => ['dashboard', 'users', 'activity', 'settings'],
            'courier' => ['dashboard', 'deliveries', 'settings']
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
        <form id="set-store" method="POST"
            action="controller/user/store-profile/index.php?action=set-current-store">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Store</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label for="storeName" class="form-label">Store Name</label>
                    <select class="form-select" id="storeName" name="store_id" required>
                        <option value="" disabled selected>Select your store</option>

                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="update-store-btn">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if ($role == 'user'): ?>

    <script>
        function updateStore() {
            const modal = new bootstrap.Modal(document.getElementById('storeNameModal'));
            modal.show();
        }

        function retrieveCurrentStore() {
            axios.get("controller/user/store-profile/index.php?action=get-current-store")
                .then(function(response) {
                    const res = response.data;
                    const storeName = document.getElementById('store-name');
                    storeName.textContent = res.store_name || 'Unnamed Store';
                    console.log("Current store fetched successfully:", res.store_name || 'Unnamed Store');
                })
                .catch(function(error) {
                    console.error("Error fetching current store:", error);
                });
        }


        new CreateRequest({
            formSelector: '#set-store',
            submitButtonSelector: '#update-store-btn',
            callback: (err, res) => err ? console.error("Form submission error:", err) : retrieveCurrentStore(),

        });


        new GetRequest({
            getUrl: "controller/user/store-profile/index.php?action=get-stores",
            params: {},
            showLoading: false,
            showSuccess: false,
            callback: (err, res) => {
                if (err) {
                    console.error("Error fetching store details:", err);
                    return;
                }

                const stores = res || [];
                const storeSelect = document.getElementById('storeName');
                storeSelect.innerHTML = '';
                stores.forEach(store => {
                    const option = document.createElement('option');
                    option.value = store.store_id;
                    option.textContent = store.store_name || 'Unnamed Store';
                    storeSelect.appendChild(option);
                });
                retrieveCurrentStore();

            }
        }).send();
    </script>
<?php endif; ?>