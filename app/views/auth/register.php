<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<section class="py-5 bg-light-subtle" style="min-height: 90vh; display: flex; align-items: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-9 col-sm-11">
                
                <?php echo \App\Core\Session::flash('register_error'); ?>

                <div class="card border-0 shadow-lg p-4 p-md-5 rounded-4 bg-white">
                    <div class="text-center mb-4">
                        <span class="brand-logo-icon mb-3 mx-auto">
                            <i class="fa-solid fa-briefcase"></i>
                        </span>
                        <h2 class="fw-extrabold mb-1">Create Account</h2>
                        <p class="text-muted small">Join E-Recruit and start your journey today</p>
                    </div>

                    <!-- Role Selector (Tabs) -->
                    <div class="row g-2 mb-4 text-center">
                        <div class="col-6">
                            <button type="button" id="tabCandidate" 
                                    class="btn w-100 py-2.5 fw-semibold border rounded-3 btn-role-tab <?php echo ($data['role'] === 'candidate') ? 'btn-primary border-primary' : 'bg-light text-muted'; ?>" 
                                    onclick="selectRole('candidate')">
                                <i class="fa-solid fa-user-tie me-2"></i>Job Seeker
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" id="tabEmployer" 
                                    class="btn w-100 py-2.5 fw-semibold border rounded-3 btn-role-tab <?php echo ($data['role'] === 'employer') ? 'btn-primary border-primary' : 'bg-light text-muted'; ?>" 
                                    onclick="selectRole('employer')">
                                <i class="fa-solid fa-building me-2"></i>Employer
                            </button>
                        </div>
                    </div>

                    <!-- Registration Form -->
                    <form action="<?php echo URL_ROOT; ?>/register" method="POST" class="needs-validation" novalidate id="registerForm">
                        
                        <!-- Hidden Role Input -->
                        <input type="hidden" name="role" id="roleInput" value="<?php echo htmlspecialchars($data['role']); ?>">

                        <!-- Full Name (Contact Person / Individual Name) -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold small">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-regular fa-user"></i></span>
                                <input type="text" name="name" id="name" 
                                       class="form-control bg-light border-start-0 <?php echo (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" 
                                       placeholder="John Doe" 
                                       value="<?php echo htmlspecialchars($data['name']); ?>" 
                                       required>
                                <div class="invalid-feedback">Full Name is required.</div>
                                <?php if (!empty($data['name_err'])) : ?>
                                    <div class="invalid-feedback d-block"><?php echo $data['name_err']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold small">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-regular fa-envelope"></i></span>
                                <input type="email" name="email" id="email" 
                                       class="form-control bg-light border-start-0 <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" 
                                       placeholder="johndoe@example.com" 
                                       value="<?php echo htmlspecialchars($data['email']); ?>" 
                                       required>
                                <div class="invalid-feedback">Please enter a valid email.</div>
                                <?php if (!empty($data['email_err'])) : ?>
                                    <div class="invalid-feedback d-block"><?php echo $data['email_err']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Phone Number (Job Seekers Only) -->
                        <div class="mb-3" id="phoneField" style="<?php echo ($data['role'] === 'candidate') ? '' : 'display: none;'; ?>">
                            <label for="phone" class="form-label fw-semibold small">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-phone"></i></span>
                                <input type="text" name="phone" id="phone" 
                                       class="form-control bg-light border-start-0 <?php echo (!empty($data['phone_err'])) ? 'is-invalid' : ''; ?>" 
                                       placeholder="+977-9876543210" 
                                       value="<?php echo htmlspecialchars($data['phone']); ?>"
                                       <?php echo ($data['role'] === 'candidate') ? 'required' : ''; ?>>
                                <div class="invalid-feedback">Valid phone number is required.</div>
                                <?php if (!empty($data['phone_err'])) : ?>
                                    <div class="invalid-feedback d-block"><?php echo $data['phone_err']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Company Name (Employers Only) -->
                        <div class="mb-3" id="companyField" style="<?php echo ($data['role'] === 'employer') ? '' : 'display: none;'; ?>">
                            <label for="company_name" class="form-label fw-semibold small">Company Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-briefcase"></i></span>
                                <input type="text" name="company_name" id="company_name" 
                                       class="form-control bg-light border-start-0 <?php echo (!empty($data['company_name_err'])) ? 'is-invalid' : ''; ?>" 
                                       placeholder="TechSphere solutions" 
                                       value="<?php echo htmlspecialchars($data['company_name']); ?>"
                                       <?php echo ($data['role'] === 'employer') ? 'required' : ''; ?>>
                                <div class="invalid-feedback">Company Name is required.</div>
                                <?php if (!empty($data['company_name_err'])) : ?>
                                    <div class="invalid-feedback d-block"><?php echo $data['company_name_err']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold small">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" name="password" id="password" 
                                       class="form-control bg-light border-start-0 <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" 
                                       placeholder="Minimum 6 characters" 
                                       required>
                                <div class="invalid-feedback">Password is required.</div>
                                <?php if (!empty($data['password_err'])) : ?>
                                    <div class="invalid-feedback d-block"><?php echo $data['password_err']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label fw-semibold small">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-shield-halved"></i></span>
                                <input type="password" name="confirm_password" id="confirm_password" 
                                       class="form-control bg-light border-start-0 <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" 
                                       placeholder="Re-enter password" 
                                       required>
                                <div class="invalid-feedback">Please confirm your password.</div>
                                <?php if (!empty($data['confirm_password_err'])) : ?>
                                    <div class="invalid-feedback d-block"><?php echo $data['confirm_password_err']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold mb-3">
                            <i class="fa-solid fa-user-plus me-2"></i>Sign Up
                        </button>

                        <div class="text-center mt-3">
                            <p class="mb-0 small text-muted">Already have an account? 
                                <a href="<?php echo URL_ROOT; ?>/login" class="text-decoration-none fw-bold text-primary">Login Here</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tab Toggling Scripts -->
<script>
function selectRole(role) {
    const roleInput = document.getElementById('roleInput');
    const phoneField = document.getElementById('phoneField');
    const companyField = document.getElementById('companyField');
    const phoneInput = document.getElementById('phone');
    const companyInput = document.getElementById('company_name');
    
    const tabCandidate = document.getElementById('tabCandidate');
    const tabEmployer = document.getElementById('tabEmployer');
    
    roleInput.value = role;

    if (role === 'candidate') {
        // Toggle tabs
        tabCandidate.classList.add('btn-primary', 'border-primary');
        tabCandidate.classList.remove('bg-light', 'text-muted');
        tabEmployer.classList.add('bg-light', 'text-muted');
        tabEmployer.classList.remove('btn-primary', 'border-primary');
        
        // Toggle input display
        phoneField.style.display = '';
        companyField.style.display = 'none';
        
        // Update required attributes
        phoneInput.setAttribute('required', 'required');
        companyInput.removeAttribute('required');
        companyInput.value = '';
    } else {
        // Toggle tabs
        tabEmployer.classList.add('btn-primary', 'border-primary');
        tabEmployer.classList.remove('bg-light', 'text-muted');
        tabCandidate.classList.add('bg-light', 'text-muted');
        tabCandidate.classList.remove('btn-primary', 'border-primary');
        
        // Toggle input display
        companyField.style.display = '';
        phoneField.style.display = 'none';
        
        // Update required attributes
        companyInput.setAttribute('required', 'required');
        phoneInput.removeAttribute('required');
        phoneInput.value = '';
    }
}

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
