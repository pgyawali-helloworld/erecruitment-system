<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 hero-content">
                <span class="hero-badge"><i class="fa-solid fa-sparkles me-2 text-primary"></i>Shape Your Career Path</span>
                <h1 class="hero-title">Find the Perfect <span class="text-gradient">Job Match</span> for You</h1>
                <p class="hero-subtitle">Discover thousands of job opportunities from top companies around the world. Secure your dream role, or find exceptional talents to scale your business.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?php echo URL_ROOT; ?>/jobs" class="btn btn-primary btn-lg px-4 py-3"><i class="fa-solid fa-magnifying-glass me-2"></i>Find Jobs</a>
                    <a href="<?php echo URL_ROOT; ?>/login" class="btn btn-outline-primary btn-lg px-4 py-3"><i class="fa-solid fa-circle-plus me-2"></i>Post a Job</a>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <!-- Visual graphic or vector layout using styled HTML structure -->
                <div class="position-relative">
                    <div class="card border-0 bg-white p-4 shadow-lg rounded-4 position-relative" style="z-index: 2;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-3">Developer Category</span>
                            <span class="text-muted small"><i class="fa-regular fa-clock me-1"></i>Just Posted</span>
                        </div>
                        <h4 class="mb-2">Senior Full Stack PHP Developer</h4>
                        <p class="text-muted mb-3"><i class="fa-solid fa-building me-1"></i>TechSphere Solutions &bull; Kathmandu</p>
                        <div class="d-flex gap-2 mb-4">
                            <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill">Full-Time</span>
                            <span class="badge bg-info-subtle text-info px-3 py-1 rounded-pill">Remote Option</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                            <span class="fw-bold text-primary fs-5">$60k - $80k <span class="text-muted fs-6 fw-normal">/ year</span></span>
                            <a href="#" class="btn btn-primary btn-sm px-3">Apply Now</a>
                        </div>
                    </div>
                    <!-- Background aesthetic blob or shapes -->
                    <div class="position-absolute top-100 start-0 translate-middle bg-primary opacity-10 rounded-circle" style="width: 180px; height: 180px; filter: blur(40px); z-index: 1;"></div>
                    <div class="position-absolute top-0 start-100 translate-middle bg-secondary opacity-15 rounded-circle" style="width: 250px; height: 250px; filter: blur(50px); z-index: 1;"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Search Section -->
<section class="container mb-5">
    <div class="hero-search-card">
        <form action="<?php echo URL_ROOT; ?>/jobs" method="GET">
            <div class="row g-3">
                <div class="col-lg-5 col-md-6">
                    <div class="search-input-group">
                        <span class="search-icon-wrapper"><i class="fa-solid fa-search"></i></span>
                        <input type="text" name="keywords" class="form-control" placeholder="Job title, keywords, or company...">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="search-input-group">
                        <span class="search-icon-wrapper"><i class="fa-solid fa-location-dot"></i></span>
                        <select name="location" class="form-select">
                            <option value="">Select Location</option>
                            <option value="Kathmandu">Kathmandu</option>
                            <option value="Lalitpur">Lalitpur</option>
                            <option value="Bhaktapur">Bhaktapur</option>
                            <option value="Pokhara">Pokhara</option>
                            <option value="Remote">Remote</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3">
                    <button type="submit" class="btn btn-primary w-100 h-100 py-3"><i class="fa-solid fa-magnifying-glass me-2"></i>Search Jobs</button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Stats Section -->
<!-- <section class="py-5">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon primary mx-auto">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <div class="stat-number">1,250+</div>
                    <div class="stat-label">Live Job Openings</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon success mx-auto">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <div class="stat-number">450+</div>
                    <div class="stat-label">Verified Companies</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon secondary mx-auto">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div class="stat-number">8,900+</div>
                    <div class="stat-label">Talented Candidates</div>
                </div>
            </div>
        </div>
    </div>
</section> -->

<!-- Job Categories or Features Section -->
<section class="py-5 bg-light-subtle rounded-4 my-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h1 mb-2">Why Choose <span class="text-gradient">E-Recruit</span></h2>
            <p class="text-muted col-lg-6 mx-auto">A seamless platform built with advanced search tools, profile management, and direct communication to simplify the recruitment process.</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-4">
                    <div class="bg-primary-subtle text-primary rounded-3 p-3 d-inline-block mb-4" style="width: fit-content;">
                        <i class="fa-solid fa-circle-check fs-4"></i>
                    </div>
                    <h5 class="card-title mb-3">Easy Application Process</h5>
                    <p class="card-text text-muted">Upload your resume once and apply to multiple matching jobs with a single click. Keep track of application progress in real-time.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-4">
                    <div class="bg-info-subtle text-info rounded-3 p-3 d-inline-block mb-4" style="width: fit-content;">
                        <i class="fa-solid fa-sliders fs-4"></i>
                    </div>
                    <h5 class="card-title mb-3">Advanced Job Filtering</h5>
                    <p class="card-text text-muted">Filter jobs by role type, location, experience level, salary range, and company reviews to find exact matches.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-4">
                    <div class="bg-success-subtle text-success rounded-3 p-3 d-inline-block mb-4" style="width: fit-content;">
                        <i class="fa-solid fa-user-tie fs-4"></i>
                    </div>
                    <h5 class="card-title mb-3">Employer Dashboard</h5>
                    <p class="card-text text-muted">Employers can post vacancies, filter candidates by skills, schedule interviews, and manage selections from a unified dashboard.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call To Action Banner -->
<section class="container my-5 py-3">
    <div class="cta-banner p-5 text-white">
        <div class="row align-items-center g-4 cta-content">
            <div class="col-lg-8 text-center text-lg-start">
                <h2 class="text-white h1 mb-3">Ready to find your next great hire?</h2>
                <p class="mb-0 text-white-50 fs-5">Or looking to kickstart your career? Join thousands of professionals and employers today.</p>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
                <a href="<?php echo URL_ROOT; ?>/register" class="btn btn-light btn-lg px-4 py-3 fw-bold text-primary"><i class="fa-solid fa-rocket me-2"></i>Get Started Now</a>
            </div>
        </div>
    </div>
</section>

<?php require APP_ROOT . '/views/layouts/footer.php'; ?>
