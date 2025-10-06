<?php
// Admin settings page - consistent with other user settings
?>

<div class="main-container" id="main-container">
    <div class="header-section">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-2">Admin Settings</h2>
                <p class="text-muted mb-0">Manage your admin account settings</p>
            </div>
        </div>
    </div>

    <div class="main-content">
        <!-- Profile Settings -->
        <div class="settings-section mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Profile Information</h5>
                </div>
                <div class="card-body">
                    <form id="profile-form">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($_SESSION['auth']['name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($_SESSION['auth']['name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['auth']['email'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone_number" name="phone_number">
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary" id="update-profile-btn">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Password Settings -->
        <div class="settings-section mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Change Password</h5>
                </div>
                <div class="card-body">
                    <form id="password-form">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            <div class="col-md-6">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-warning" id="update-password-btn">
                                <i class="fas fa-key"></i> Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="settings-section mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>System Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">PHP Version</label>
                            <p class="text-muted"><?php echo PHP_VERSION; ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">MySQL Version</label>
                            <p class="text-muted" id="mysql-version">Loading...</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Server Software</label>
                            <p class="text-muted"><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Admin Role</label>
                            <p class="text-muted"><span class="badge bg-danger">Administrator</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Database Management -->
        <div class="settings-section mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-database me-2"></i>Database Management</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <button class="btn btn-outline-primary w-100" onclick="backupDatabase()">
                                <i class="fas fa-download"></i> Backup Database
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-warning w-100" onclick="optimizeDatabase()">
                                <i class="fas fa-tools"></i> Optimize Database
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-danger w-100" onclick="clearCache()">
                                <i class="fas fa-trash"></i> Clear Cache
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadSystemInfo();
});

function loadSystemInfo() {
    // Load MySQL version
    new GetRequest({
        getUrl: 'controller/admin/settings/get-info.php',
        params: {},
        showLoading: false,
        showSuccess: false,
        callback: (err, data) => {
            if (!err && data) {
                document.getElementById('mysql-version').textContent = data.mysql_version || 'Unknown';
            }
        }
    }).send();
}

// Handle profile form submission
new CreateRequest({
    formSelector: '#profile-form',
    submitButtonSelector: '#update-profile-btn',
    callback: (err, data) => {
        if (!err) {
            Swal.fire('Success', 'Profile updated successfully!', 'success');
        }
    }
});

// Handle password form submission
new CreateRequest({
    formSelector: '#password-form',
    submitButtonSelector: '#update-password-btn',
    callback: (err, data) => {
        if (!err) {
            Swal.fire('Success', 'Password changed successfully!', 'success');
            document.getElementById('password-form').reset();
        }
    }
});

function backupDatabase() {
    Swal.fire({
        title: 'Backup Database?',
        text: 'This will create a backup of the entire database.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, backup!'
    }).then((result) => {
        if (result.isConfirmed) {
            new PostRequest({
                postUrl: 'controller/admin/settings/backup-database.php',
                params: {},
                showLoading: true,
                showSuccess: true,
                callback: (err, data) => {
                    if (!err) {
                        Swal.fire('Success', 'Database backup completed!', 'success');
                    }
                }
            }).send();
        }
    });
}

function optimizeDatabase() {
    Swal.fire({
        title: 'Optimize Database?',
        text: 'This will optimize database tables for better performance.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, optimize!'
    }).then((result) => {
        if (result.isConfirmed) {
            new PostRequest({
                postUrl: 'controller/admin/settings/optimize-database.php',
                params: {},
                showLoading: true,
                showSuccess: true,
                callback: (err, data) => {
                    if (!err) {
                        Swal.fire('Success', 'Database optimization completed!', 'success');
                    }
                }
            }).send();
        }
    });
}

function clearCache() {
    Swal.fire({
        title: 'Clear Cache?',
        text: 'This will clear all cached data.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, clear!'
    }).then((result) => {
        if (result.isConfirmed) {
            new PostRequest({
                postUrl: 'controller/admin/settings/clear-cache.php',
                params: {},
                showLoading: true,
                showSuccess: true,
                callback: (err, data) => {
                    if (!err) {
                        Swal.fire('Success', 'Cache cleared successfully!', 'success');
                    }
                }
            }).send();
        }
    });
}
</script>