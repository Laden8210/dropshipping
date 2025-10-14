<?php

$token = isset($_GET['token']) ? $_GET['token'] : '';
if (empty($token)) {
    echo '<script>window.location.href = "forgot-password";</script>';
    exit;
}
?>

<main class="main">
    <div class="title">
        <img src="assets/img/logo.png" alt="LuzViMinDrop Logo" class="img-fluid" style="max-width: 550px;" />
        <p class="mt-3">Your AI-powered dropshipping partner across Luzon, Visayas, and Mindanao.</p>
    </div>

    <section class="p-1">
        <div class="container d-flex justify-content-center align-items-start" style="min-height: 60vh;">
            <div class="auth-container" style="width: 100%; max-width: 500px;">
                <div class="card-body">
                    <h5 class="card-title text-center mb-4">Reset Password</h5>
                    <p class="text-center text-muted mb-4">Enter your new password below.</p>
                    
                    <form id="reset-password-form" method="post" action="controller/auth/index.php?action=reset-password">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>" />
                        
                        <!-- New Password Field -->
                        <div class="input-group mb-3">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" id="password" placeholder="New Password" required />
                            <i class="fas fa-eye-slash" id="togglePassword"></i>
                        </div>

                        <!-- Confirm Password Field -->
                        <div class="input-group mb-3">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="confirm_password" id="confirmPassword" placeholder="Confirm New Password" required />
                            <i class="fas fa-eye-slash" id="toggleConfirmPassword"></i>
                        </div>

                        <input type="submit" class="btn btn-login w-100 mb-3" value="Reset Password" id="submit-btn" />
                        
                        <div class="text-center links">
                            <p class="mb-1">Remember your password?</p>
                            <a href="login" class="btn btn-link p-0">Back to Login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
const resetPasswordForm = new CreateRequest({
    formSelector: "#reset-password-form",
    submitButtonSelector: "#submit-btn",
    callback: (err, res) => {
  
    },
    confirmationRequired: false,
    apiUrl: 'controller/auth/index.php?action=reset-password'
});

// Password visibility toggle
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordField = document.getElementById('password');
    const icon = this;
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        passwordField.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
});

document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
    const passwordField = document.getElementById('confirmPassword');
    const icon = this;
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        passwordField.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
});

// Custom alert function
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Insert alert at the top of the form
    const form = document.getElementById('reset-password-form');
    form.insertAdjacentHTML('beforebegin', alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
</script>

