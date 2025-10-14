<?php

$token = isset($_GET['token']) ? $_GET['token'] : '';
if (empty($token)) {
    echo '<script>window.location.href = "login";</script>';
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
                    <h5 class="card-title text-center mb-4">Verify Email</h5>
                    <p class="text-center text-muted mb-4">Click the button below to verify your email address.</p>
                    
                    <div id="verification-status" class="text-center">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Verifying...</span>
                        </div>
                        <p>Verifying your email address...</p>
                    </div>
                    
                    <div class="text-center links mt-4">
                        <a href="login" class="btn btn-link p-0">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
// Auto-verify email when page loads
document.addEventListener('DOMContentLoaded', function() {
    const token = '<?php echo htmlspecialchars($token); ?>';
    const statusDiv = document.getElementById('verification-status');
    
    // Send verification request
    fetch('controller/auth/index.php?action=verify-email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            token: token
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            statusDiv.innerHTML = `
                <div class="text-success mb-3">
                    <i class="fas fa-check-circle fa-3x"></i>
                </div>
                <h5 class="text-success">Email Verified Successfully!</h5>
                <p>${data.message}</p>
                <div class="mt-3">
                    <a href="login" class="btn btn-primary">Go to Login</a>
                </div>
            `;
        } else {
            statusDiv.innerHTML = `
                <div class="text-danger mb-3">
                    <i class="fas fa-exclamation-circle fa-3x"></i>
                </div>
                <h5 class="text-danger">Verification Failed</h5>
                <p>${data.message}</p>
                <div class="mt-3">
                    <a href="register" class="btn btn-primary">Register Again</a>
                    <a href="login" class="btn btn-secondary ms-2">Back to Login</a>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Verification error:', error);
        statusDiv.innerHTML = `
            <div class="text-danger mb-3">
                <i class="fas fa-exclamation-triangle fa-3x"></i>
            </div>
            <h5 class="text-danger">Verification Error</h5>
            <p>An error occurred while verifying your email. Please try again.</p>
            <div class="mt-3">
                <a href="register" class="btn btn-primary">Register Again</a>
                <a href="login" class="btn btn-secondary ms-2">Back to Login</a>
            </div>
        `;
    });
});
</script>
