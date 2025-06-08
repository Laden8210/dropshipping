  <?php

  $client = new Google\Client();
  $client->setClientId('408096805493-cfatjhsa5q0aubs53d6862d2ccdjs76u.apps.googleusercontent.com');
  $client->setClientSecret('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');
  $client->setRedirectUri('http://localhost/dropshipping/redirect');
  $client->setState('login');
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
        <div class="auth-container" style="width: 100%; max-width: 400px;">
          <div class="card-body">
            <h5 class="card-title text-center mb-4">Login</h5>
            <form action="controller/auth/index.php?action=forgot-password" method="POST" id="auth-form">

              <div class="input-group">
                <div class="input-group-prepend">
                  <i class="fas fa-phone"></i>
                </div>
                <input type="text" name="email" id="phoneNumber" placeholder="Phone Number or Email" required />
              </div>

              <input type="submit" class="btn btn-login w-100 mb-3" value="Recover Password" name="login" id="submit-btn" />
         

              <div class="text-center links">
            
                <a href="register" class="btn btn-link p-0">Back to Login</a>
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

    });
  </script>