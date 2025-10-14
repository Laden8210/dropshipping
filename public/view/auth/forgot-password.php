
<main class="main">
    <div class="title">
        <img src="assets/img/logo.png" alt="LuzViMinDrop Logo" class="img-fluid" style="max-width: 550px;" />
        <p class="mt-3">Your AI-powered dropshipping partner across Luzon, Visayas, and Mindanao.</p>
    </div>

    <section class="p-1">
        <div class="container d-flex justify-content-center align-items-start" style="min-height: 60vh;">
            <div class="auth-container" style="width: 100%; max-width: 500px;">
                <div class="card-body">
                    <h5 class="card-title text-center mb-4">Forgot Password</h5>
                    <p class="text-center text-muted mb-4">Enter your email address and we'll send you a link to reset your password.</p>
                    
                    <form id="forgot-password-form" action="controller/auth/index.php?action=forgot-password" method="post">
                        <!-- Email Field -->
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <input type="email" name="email" id="email" placeholder="Email Address" required />
                        </div>

                        <input type="submit" class="btn btn-login w-100 mb-3" value="Send Reset Link" id="submit-btn" />
                        
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
const forgotPasswordForm = new CreateRequest({
    formSelector: "#forgot-password-form",
    submitButtonSelector: "#submit-btn",
    callback: (err, res) => {

    },
    confirmationRequired: false,

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
    const form = document.getElementById('forgot-password-form');
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
