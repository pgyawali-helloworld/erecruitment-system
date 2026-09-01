<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<section class="py-5 bg-light-subtle" style="min-height: 80vh; display: flex; align-items: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-8 col-sm-10">
                
                <!-- Display session flash alerts (e.g. registration success, logout success) -->
                <?php echo \App\Core\Session::flash('register_success'); ?>
                <?php echo \App\Core\Session::flash('logout_success'); ?>
                <?php echo \App\Core\Session::flash('auth_error'); ?>
                <?php echo \App\Core\Session::flash('login_error'); ?>

                <div class="card border-0 shadow-lg p-4 p-md-5 rounded-4 bg-white">
                    <div class="text-center mb-4">
                        <span class="brand-logo-icon mb-3 mx-auto">
                            <i class="fa-solid fa-briefcase"></i>
                        </span>
                        <h2 class="fw-extrabold mb-1">Welcome Back</h2>
                        <p class="text-muted small">Login to access your dashboard</p>
                    </div>

                    <!-- HTML5 Form with Client-side validation -->
                    <form action="<?php echo URL_ROOT; ?>/login" method="POST" class="needs-validation" novalidate id="loginForm">
                        
                        <!-- Email Input -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold small">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-regular fa-envelope"></i></span>
                                <input type="email" name="email" id="email" 
                                       class="form-control bg-light border-start-0 <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" 
                                       placeholder="you@example.com" 
                                       value="<?php echo htmlspecialchars($data['email']); ?>" 
                                       required>
                                <div class="invalid-feedback">
                                    Please enter a valid email address.
                                </div>
                                <?php if (!empty($data['email_err'])) : ?>
                                    <div class="invalid-feedback d-block"><?php echo $data['email_err']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <label for="password" class="form-label fw-semibold small mb-0">Password</label>
                                <a href="#" class="text-decoration-none small fw-semibold text-primary">Forgot Password?</a>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" name="password" id="password" 
                                       class="form-control bg-light border-start-0 <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" 
                                       placeholder="&bull;&bull;&bull;&bull;&bull;&bull;" 
                                       required>
                                <div class="invalid-feedback">
                                    Please enter your password.
                                </div>
                                <?php if (!empty($data['password_err'])) : ?>
                                    <div class="invalid-feedback d-block"><?php echo $data['password_err']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold mb-3">
                            <i class="fa-solid fa-right-to-bracket me-2"></i>Sign In
                        </button>

                        <div class="text-center mt-3">
                            <p class="mb-0 small text-muted">Don't have an account? 
                                <a href="<?php echo URL_ROOT; ?>/register" class="text-decoration-none fw-bold text-primary">Register Here</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Client-side Bootstrap 5 form validation
(function () {
  'use strict'
  var forms = document.querySelectorAll('.needs-validation')
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        form.classList.add('was-validated')
      }, false)
    })
})()
</script>

<?php require APP_ROOT . '/views/layouts/footer.php'; ?>
