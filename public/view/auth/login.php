
  <main class="main">
    <div class="title">
      <img src="assets/img/logo.png" alt="LuzViMinDrop Logo" class="img-fluid" style="max-width: 550px;" />
      <p class="mt-3">Your AI-powered dropshipping partner across Luzon, Visayas, and Mindanao.</p>
    </div>

    <section class="p-1">

      <div class="container d-flex justify-content-center align-align-items-start" style="min-height: 60vh;">
        <div class="auth-container" style="width: 100%; max-width: 400px;">
          <div class="card-body">
            <h5 class="card-title text-center mb-4">Login</h5>
            <form action="controller/auth/index.php?action=login" method="POST" id="auth-form">
      
              <div class="input-group">
                <div class="input-group-prepend">
                  <i class="fas fa-phone"></i>
                </div>
                <input type="text" name="email" id="phoneNumber" placeholder="Phone Number or Email" required />
              </div>

              <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="pass" placeholder="Password" required />
                <i class="fas fa-eye-slash" id="togglePass"></i>
              </div>

              <div class="mb-3 text-end">
                <a href="forgot-password" class="recover">Recover Password</a>
              </div>
              <input type="submit" class="btn btn-login w-100 mb-3" value="Login" name="login" id="submit-btn" />
              <div class="text-center mb-3">
                <p class="or">or</p>
              </div>
        
              <div class="text-center links">
                <p class="mb-1">Don't have an account yet?</p>
                <a href="register" class="btn btn-link p-0">Register</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>

  </main>

  <script>
    const createRequest = new CreateRequest({
      formSelector: "#auth-form",
      submitButtonSelector: "#submit-btn",
      callback: (err, res) => err ? console.error("Form submission error:", err) : console.log(
        "Form submitted successfully:", res),
      confirmationRequired: false,
      redirectUrl: "dashboard"
    });
  </script>