<div class="main-container" id="main-container">
    <div class="header-section text-center">
        <p class="lead">Manage your account settings, update user information, configure your warehouse, and more.</p>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs" id="settingsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">
                Profile
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="warehouse-tab" data-bs-toggle="tab" data-bs-target="#warehouse" type="button" role="tab">
                Store Profile
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab">
                Notifications
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                Security
            </button>
        </li>

    </ul>

    <!-- Tab Content -->
    <div class="tab-content mt-3" id="settingsTabContent">

        <!-- Profile Tab -->
        <div class="tab-pane fade show active" id="profile" role="tabpanel">
            <!-- Profile Display -->
            <div class="card mb-4" id="profile-display">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="ri-user-line me-2"></i>Profile Information</h5>
                    <button class="btn btn-outline-primary" onclick="toggleProfileEdit()">
                        <i class="ri-edit-line me-1"></i>Edit Profile
                    </button>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">First Name</label>
                            <p class="text-muted" id="display-first-name">Loading...</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Last Name</label>
                            <p class="text-muted" id="display-last-name">Loading...</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address</label>
                            <p class="text-muted" id="display-email">Loading...</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <p class="text-muted" id="display-phone">Loading...</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Birth Date</label>
                            <p class="text-muted" id="display-birth-date">Loading...</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Gender</label>
                            <p class="text-muted" id="display-gender">Loading...</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Account Status</label>
                            <p class="text-muted" id="display-status">Loading...</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Verified</label>
                            <p class="text-muted" id="display-email-verified">Loading...</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Member Since</label>
                            <p class="text-muted" id="display-created-at">Loading...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Edit Form -->
            <div class="card mb-4" id="profile-edit" style="display: none;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="ri-edit-line me-2"></i>Edit Profile</h5>
                    <button class="btn btn-outline-secondary" onclick="toggleProfileEdit()">
                        <i class="ri-close-line me-1"></i>Cancel
                    </button>
                </div>
                <div class="card-body">
                    <form id="profileForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="birth_date" class="form-label">Birth Date</label>
                                <input type="date" class="form-control" id="birth_date" name="birth_date">
                            </div>
                            <div class="col-md-4">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <!-- <div class="col-md-4">
                                <label for="avatar_url" class="form-label">Avatar URL</label>
                                <input type="url" class="form-control" id="avatar_url" name="avatar_url">
                            </div> -->
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i>Save Changes
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="toggleProfileEdit()">
                                <i class="ri-close-line me-1"></i>Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Change Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ri-lock-line me-2"></i>Change Password</h5>
                </div>
                <div class="card-body">
                    <form id="passwordForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            <div class="col-md-6">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning">
                            <i class="ri-key-line me-1"></i>Change Password
                        </button>
                    </form>
                </div>
            </div>
        </div>


        <div class="tab-pane fade" id="warehouse" role="tabpanel">

            <!-- 🏬 Store List with Toggle -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="ri-store-2-line me-2"></i>Your Stores</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Store Name</th>
                                <th>Address</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>

            <form id="create-store-profile" action="controller/user/store-profile?action=create-store" method="POST" enctype="multipart/form-data" class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="ri-store-2-line me-2"></i>Create Store Profile</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Store Name -->
                        <div class="col-md-6">
                            <label for="store_name" class="form-label">Store Name</label>
                            <input type="text" class="form-control" id="store_name" name="store_name" placeholder="e.g., John's Electronics" required>
                        </div>

                        <!-- Store Email -->
                        <div class="col-md-6">
                            <label for="store_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="store_email" name="store_email" placeholder="store@example.com">
                        </div>

                        <!-- Store Phone -->
                        <div class="col-md-6">
                            <label for="store_phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="store_phone" name="store_phone" placeholder="+63 912 345 6789">
                        </div>

                        <!-- Store Logo Upload -->
                        <div class="col-md-6">
                            <label for="store_logo_url" class="form-label">Store Logo</label>
                            <input type="file" class="form-control" id="store_logo_url" name="store_logo_url" accept="image/*">
                        </div>

                        <!-- Store Address -->
                        <div class="col-md-12">
                            <label for="store_address" class="form-label">Store Address</label>
                            <input type="text" class="form-control" id="store_address" name="store_address" placeholder="e.g., 123 Main St, City, Province">
                        </div>

                        <!-- Store Description -->
                        <div class="col-md-12">
                            <label for="store_description" class="form-label">Store Description</label>
                            <textarea class="form-control" id="store_description" name="store_description" rows="3" placeholder="Brief description about your store..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white text-end">
                    <button type="submit" class="btn btn-success px-4" id="create-store-btn">
                        <i class="ri-save-line me-2"></i>Save Store Profile
                    </button>
                </div>
            </form>


        </div>


        <!-- Notifications Tab -->
        <div class="tab-pane fade" id="notifications" role="tabpanel">
            <form id="notificationsForm">
                <div class="mb-3">
                    <label for="email_notifications" class="form-label">Email Notifications</label>
                    <select class="form-select" id="email_notifications" name="email_notifications">
                        <option value="enabled">Enabled</option>
                        <option value="disabled">Disabled</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="sms_notifications" class="form-label">SMS Notifications</label>
                    <select class="form-select" id="sms_notifications" name="sms_notifications">
                        <option value="enabled">Enabled</option>
                        <option value="disabled">Disabled</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-info">Update Notifications</button>
            </form>

        </div>
        <!-- Security Tab -->
        <div class="tab-pane fade" id="security" role="tabpanel">
            <form id="securityForm">
                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>

                <button type="submit" class="btn btn-danger">Change Password</button>
            </form>
        </div>

    </div>

</div>

<script>
    const request = new GetRequest({
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


  
            const tbody = document.querySelector('#warehouse .table tbody');
            tbody.innerHTML = '';

            stores.forEach(store => {
                const row = document.createElement('tr');
                row.innerHTML = `
                <td>
                    ${store.store_logo_url ? `<img src="public/images/store/${store.store_logo_url}" alt="Logo" style="width:40px;height:40px;object-fit:cover;border-radius:4px;margin-right:8px;">` : ''}
                    ${store.store_name || ''}
                </td>
                <td>${store.store_address || ''}</td>
                <td>${store.store_email || ''}</td>
                <td>${store.store_phone || ''}</td>
                <td>
                    <span class="badge ${store.status === 'active' ? 'bg-success' : 'bg-secondary'}">
                        ${store.status === 'active' ? 'Active' : 'Inactive'}
                    </span>
                </td>
            `;
                tbody.appendChild(row);
            });
        }
    });

    request.send();

    // Load user profile data
    document.addEventListener('DOMContentLoaded', function() {
        loadUserProfile();
    });

    function loadUserProfile() {
        new GetRequest({
            getUrl: 'controller/user/settings/get-profile.php',
            params: {},
            showLoading: false,
            showSuccess: false,
            callback: (err, data) => {
                if (err) {
                    console.error('Error loading profile:', err);
                    return;
                }
                
                if (data) {
                    // Display profile information
                    document.getElementById('display-first-name').textContent = data.first_name || 'Not provided';
                    document.getElementById('display-last-name').textContent = data.last_name || 'Not provided';
                    document.getElementById('display-email').textContent = data.email || 'Not provided';
                    document.getElementById('display-phone').textContent = data.phone_number || 'Not provided';
                    document.getElementById('display-birth-date').textContent = data.birth_date || 'Not provided';
                    document.getElementById('display-gender').textContent = data.gender ? data.gender.charAt(0).toUpperCase() + data.gender.slice(1) : 'Not provided';
                    document.getElementById('display-status').innerHTML = data.is_active ? 
                        '<span class="badge bg-success">Active</span>' : 
                        '<span class="badge bg-danger">Inactive</span>';
                    document.getElementById('display-email-verified').innerHTML = data.is_email_verified ? 
                        '<span class="badge bg-success">Verified</span>' : 
                        '<span class="badge bg-warning">Unverified</span>';
                    document.getElementById('display-created-at').textContent = data.created_at ? 
                        new Date(data.created_at).toLocaleDateString() : 'Not available';

                    // Populate edit form
                    document.getElementById('first_name').value = data.first_name || '';
                    document.getElementById('last_name').value = data.last_name || '';
                    document.getElementById('email').value = data.email || '';
                    document.getElementById('phone_number').value = data.phone_number || '';
                    document.getElementById('birth_date').value = data.birth_date || '';
                    document.getElementById('gender').value = data.gender || 'male';
                 //   document.getElementById('avatar_url').value = data.avatar_url || '';
                }
            }
        }).send();
    }

    function toggleProfileEdit() {
        const displayCard = document.getElementById('profile-display');
        const editCard = document.getElementById('profile-edit');
        
        if (editCard.style.display === 'none') {
            displayCard.style.display = 'none';
            editCard.style.display = 'block';
        } else {
            displayCard.style.display = 'block';
            editCard.style.display = 'none';
        }
    }

    // Handle profile form submission
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const params = {};
        for (let [key, value] of formData.entries()) {
            params[key] = value;
        }
        
        new PostRequest({
            postUrl: 'controller/user/settings/update-profile.php',
            params: params,
            showLoading: true,
            showSuccess: true,
            callback: (err, data) => {
                if (!err) {
                    loadUserProfile(); // Reload profile data
                    toggleProfileEdit(); // Switch back to display mode
                }
            }
        }).send();
    });

    // Handle password form submission
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const params = {};
        for (let [key, value] of formData.entries()) {
            params[key] = value;
        }
        
        new PostRequest({
            postUrl: 'controller/user/settings/change-password.php',
            params: params,
            showLoading: true,
            showSuccess: true,
            callback: (err, data) => {
                if (!err) {
                    document.getElementById('passwordForm').reset();
                }
            }
        }).send();
    });

    document.getElementById('create-store-profile').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        axios.post('controller/user/store-profile/index.php?action=create-store', formData)
            .then(response => {
                if (response.data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Store Created',
                        text: response.data.message,
                    });
                    this.reset();
                    request.send();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.data.message,
                    });
                }
            })
            .catch(error => {
                console.error("Error creating store:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while creating the store.',
                });
            });
    });
</script>