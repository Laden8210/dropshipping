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
                    <div class="col-md-4">
                        <label for="avatar_url" class="form-label">Avatar URL</label>
                        <input type="url" class="form-control" id="avatar_url" name="avatar_url">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>

                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
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