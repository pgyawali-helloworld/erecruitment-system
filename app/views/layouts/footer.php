</div> <!-- End of .main-wrapper -->

<!-- Footer -->
<footer class="footer mt-auto py-5 bg-dark text-white">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <a class="navbar-brand d-flex align-items-center text-white mb-3" href="<?php echo URL_ROOT; ?>">
                    <span class="brand-logo-icon me-2">
                        <i class="fa-solid fa-briefcase"></i>
                    </span>
                    <span class="brand-text">E-Recruit</span>
                </a>
                <p class="text-secondary small">
                    A next-generation talent acquisition platform connecting job seekers with leading employers worldwide. Build your career with E-Recruit.
                </p>
                <div class="social-links mt-3">
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle me-2"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle me-2"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle me-2"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-6">
                <h6 class="text-uppercase mb-3 fw-bold small text-primary">For Candidates</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?php echo URL_ROOT; ?>/jobs" class="text-secondary text-decoration-none small">Browse Jobs</a></li>
                    <li><a href="<?php echo URL_ROOT; ?>/register" class="text-secondary text-decoration-none small">Candidate Register</a></li>
                    <li><a href="<?php echo URL_ROOT; ?>/login" class="text-secondary text-decoration-none small">Candidate Dashboard</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6">
                <h6 class="text-uppercase mb-3 fw-bold small text-primary">For Employers</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?php echo URL_ROOT; ?>/register" class="text-secondary text-decoration-none small">Employer Register</a></li>
                    <li><a href="<?php echo URL_ROOT; ?>/login" class="text-secondary text-decoration-none small">Post a Job</a></li>
                    <li><a href="<?php echo URL_ROOT; ?>/login" class="text-secondary text-decoration-none small">Talent Pool</a></li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-6">
                <h6 class="text-uppercase mb-3 fw-bold small text-primary">Contact Info</h6>
                <ul class="list-unstyled footer-contact text-secondary small">
                    <li class="mb-2"><i class="fas fa-map-marker-alt text-primary me-2"></i> Kathamandu, Nepal</li>
                    <li class="mb-2"><i class="fas fa-envelope text-primary me-2"></i> support@erecruit.com</li>
                    <li class="mb-2"><i class="fas fa-phone text-primary me-2"></i> +977-1-4444444</li>
                </ul>
            </div>
        </div>
        <hr class="my-4 border-secondary">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="text-secondary mb-0 small">&copy; <?php echo date('Y'); ?> E-Recruitment Portal. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap Bundle with Popper JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="<?php echo URL_ROOT; ?>/public/js/main.js"></script>
</body>
</html>
