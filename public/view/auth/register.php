  <?php

  $client = new Google\Client();
  $client->setClientId('408096805493-cfatjhsa5q0aubs53d6862d2ccdjs76u.apps.googleusercontent.com');
  $client->setClientSecret('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');
  $client->setRedirectUri('http://localhost/dropshipping/redirect');
  $client->setState('register');
  $client->addScope('email');
  $client->addScope('profile');

  $url = $client->createAuthUrl();


  ?>

  <main class="main">
    <div class="title">
      <img src="assets/img/logo.png" alt="LuzViMinDrop Logo" class="img-fluid" style="max-width: 550px;" />
      <p class="mt-3">Your AI-powered dropshipping partner across Luzon, Visayas, and Mindanao.</p>
    </div>

    <section class="p-1">

      <div class="container d-flex justify-content-center align-align-items-start" style="min-height: 60vh;">
        <div class="auth-container" style="width: 100%; max-width: 500px;">
          <div class="card-body">
            <h5 class="card-title text-center mb-4">Register</h5>
            <form action="controller/auth/index.php?action=register" method="POST" id="auth-form">

              <!-- First Name Field -->
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <i class="fas fa-user"></i>
                </div>
                <input type="text" name="first_name" id="firstName" placeholder="First Name" required />
              </div>

              <!-- Last Name Field -->
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <i class="fas fa-user"></i>
                </div>
                <input type="text" name="last_name" id="lastName" placeholder="Last Name" required />
              </div>

              <!-- Email Field -->
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <i class="fas fa-envelope"></i>
                </div>
                <input type="email" name="email" id="email" placeholder="Email" required />
              </div>

              <!-- Phone Number Field -->
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <i class="fas fa-phone"></i>
                </div>
                <input type="text" name="phone_number" id="phoneNumber" placeholder="Phone Number" required />
                
              </div>

              <!-- User type -->

              <div class="input-group mb-3">

                <select name="role" id="role" required class="form-select">
                  <option value="" disabled selected>Select User Type</option>
                  <option value="user">Business Owner</option>
                  <option value="supplier">Supplier</option>

                  <option value="courier">Courier</option>
                </select>
              </div>
              <!-- Password Field -->
              <div class="input-group mb-3">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="pass" placeholder="Password" required />
                <i class="fas fa-eye-slash" id="togglePass"></i>
              </div>

              <!-- Confirm Password Field -->
              <div class="input-group mb-3">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" id="confirmPass" placeholder="Confirm Password" required />
                <i class="fas fa-eye-slash" id="toggleConfirmPass"></i>
              </div>


              <input type="submit" class="btn btn-login w-100 mb-3" value="Register" name="Register" id="submit-btn" />
              
              <div class="alert alert-info mt-3" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <small>After registration, you'll receive an email to verify your account.</small>
              </div>
              
              <div class="text-center mb-3">
                <p class="or">or</p>
              </div>
              <div class="text-center mb-3">
                <a href="<?php echo $url ?>" class="btn btn-outline-danger w-100">
                  <i class="fab fa-google me-2"></i>Register with Google
                </a>
              </div>
              <div class="text-center links">
                <p class="mb-1">Already have an account?</p>
                <a href="login" class="btn btn-link p-0">Log In</a>
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
      redirectUrl: "login"

    });
  </script>