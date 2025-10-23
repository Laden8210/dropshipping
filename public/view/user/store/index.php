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
                      <h5 class="card-title text-center mb-4">Setup Store Profile</h5>
                      <form  method="POST" id="create-store-profile">

                          <div class="col-md-12 my-3">
                              <label for="store_name" class="form-label">Store Name</label>
                              <input type="text" class="form-control" id="store_name" name="store_name" placeholder="e.g., John's Electronics" required>
                          </div>

                          <!-- Store Email -->
                          <div class="col-md-12 my-3">
                              <label for="store_email" class="form-label">Email</label>
                              <input type="email" class="form-control" id="store_email" name="store_email" placeholder="store@example.com">
                          </div>

                          <!-- Store Phone -->
                          <div class="col-md-12 my-3">
                              <label for="store_phone" class="form-label">Phone Number</label>
                              <input type="text" class="form-control" id="store_phone" name="store_phone" placeholder="+63 912 345 6789">
                          </div>

                          <!-- Store Logo Upload -->
                          <div class="col-md-12 my-3">
                              <label for="store_logo_url" class="form-label">Store Logo</label>
                              <input type="file" class="form-control" id="store_logo_url" name="store_logo_url" accept="image/*">
                          </div>

                          <!-- Store Address -->
                          <div class="col-md-12 my-3">
                              <label for="store_address" class="form-label">Store Address</label>
                              <input type="text" class="form-control" id="store_address" name="store_address" placeholder="e.g., 123 Main St, City, Province">
                          </div>

                          <!-- Store Description -->
                          <div class="col-md-12 my-3">
                              <label for="store_description" class="form-label">Store Description</label>
                              <textarea class="form-control" id="store_description" name="store_description" rows="3" placeholder="Brief description about your store..."></textarea>
                          </div>


                          <input type="submit" class="btn btn-login w-100 my-3" value="Setup" name="Setup" id="submit-btn" />

                          <div class="alert alert-info mt-3" role="alert">
                              <i class="fas fa-info-circle me-2"></i>
                              <small>Store profile setup required before accessing the dashboard.</small>

                          </div>

                      </form>
                  </div>
              </div>
          </div>
      </section>

  </main>

  <script>
      document.getElementById('create-store-profile').addEventListener('submit', function(e) {
          e.preventDefault();
          const formData = new FormData(this);
          axios.post('controller/auth/index.php?action=setup-store', formData)
              .then(response => {
                  if (response.data.status === 'success') {
                      Swal.fire({
                          icon: 'success',
                          title: 'Store Created',
                          text: response.data.message,
                      })
                      .then(() => {
                          window.location.href = 'dashboard';
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
                  console.error("Error creating store:", error.response.data.message);
                  Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: error.response.data.message,
                  });
              });
      });
  </script>