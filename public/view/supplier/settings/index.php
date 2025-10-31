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
                Warehouse
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
           
                </div>

      

                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>


        <div class="tab-pane fade" id="warehouse" role="tabpanel">
            <form id="create-warehouse" action="controller/supplier/setting/index.php?action=create-warehouse" method="POST">
                <div class="mb-3">
                    <label for="warehouse_name" class="form-label">Warehouse Name</label>
                    <input type="text" class="form-control" id="warehouse_name" name="warehouse_name" required>
                </div>
                <div class="mb-3">
                    <label for="warehouse_address" class="form-label">Warehouse Address</label>
                    <textarea class="form-control" id="warehouse_address" name="warehouse_address" rows="3" required></textarea>
                </div>

                <button type="submit" class="btn btn-success" id="create-warehouse-btn">Save Warehouse</button>
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
    new GetRequest({
        getUrl: "controller/supplier/setting?action=warehouse-details",
        params: {

        },
        showLoading: false,
        showSuccess: false,
        callback: (err, data) => {
            if (err) {
                console.error("Error fetching warehouse details:", err);
                return;
            }
            if (Array.isArray(data) && data.length > 0) {
                const warehouseDetails = data[0];
                document.getElementById('warehouse_name').value = warehouseDetails.warehouse_name;
                document.getElementById('warehouse_address').value = warehouseDetails.warehouse_address;
            } else {
                console.warn("No warehouse details found for the user.");
            }


        }
    }).send();

    function loadUserProfile() {
        new GetRequest({
            getUrl: 'controller/supplier/setting?action=profile',
            params: {},
            showLoading: false,
            showSuccess: false,
            callback: (err, data) => {
                if (err) {
                    console.error('Error loading profile:', err);
                    return;
                }
                document.getElementById('first_name').value = data.first_name || '';
                document.getElementById('last_name').value = data.last_name || '';
                document.getElementById('email').value = data.email || '';
                document.getElementById('phone_number').value = data.phone_number || '';
                // format date to YYYY-MM-DD
                if (data.birth_date) {
                    const birthDate = new Date(data.birth_date);
                    const year = birthDate.getFullYear();
                    const month = String(birthDate.getMonth() + 1).padStart(2, '0');
                    const day = String(birthDate.getDate()).padStart(2, '0');
                    document.getElementById('birth_date').value = `${year}-${month}-${day}`;
                } else {
                    document.getElementById('birth_date').value = '';
                }
                document.getElementById('gender').value = data.gender || '';

            }
        }).send();
    }
    loadUserProfile();

    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const params = {};
        for (let [key, value] of formData.entries()) {
            params[key] = value;
        }

        new PostRequest({
            postUrl: 'controller/supplier/setting/update-profile.php',
            params: params,
            callback: (err, data) => {
                if (!err) {
                    loadUserProfile(); 
                }
            }
        }).send();
    });


    const createExamRequest = new CreateRequest({
        formSelector: '#create-warehouse',
        submitButtonSelector: '#create-warehouse-btn',
        callback: (err, res) => err ? console.error("Form submission error:", err) : console.log("Form submitted successfully:", res),
        redirectUrl: 'category',
    });
</script>