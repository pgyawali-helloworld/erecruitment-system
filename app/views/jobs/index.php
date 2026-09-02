<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<!-- Hero Search Banner -->
<div class="bg-primary text-white py-5 position-relative overflow-hidden" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
    <div class="container py-4 position-relative z-1">
        <div class="text-center max-w-700 mx-auto mb-4">
            <h1 class="fw-extrabold display-5 mb-2">Find Your Next Dream Career</h1>
            <p class="lead opacity-75">Explore hundreds of active job openings from top hiring companies</p>
        </div>

        <!-- Filter Form Box -->
        <div class="card border-0 shadow-lg rounded-4 p-3 bg-white text-dark">
            <form action="<?php echo URL_ROOT; ?>/jobs" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="keyword" class="form-control border-0 bg-transparent py-2.5 shadow-none" placeholder="Job title, skills, or company..." value="<?php echo htmlspecialchars($filters['keyword']); ?>">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="input-group border-start">
                        <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="fa-solid fa-list-check"></i></span>
                        <select name="category" class="form-select border-0 bg-transparent py-2.5 shadow-none">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat->id; ?>" <?php echo $filters['category_id'] == $cat->id ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="input-group border-start">
                        <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="fa-solid fa-briefcase"></i></span>
                        <select name="job_type" class="form-select border-0 bg-transparent py-2.5 shadow-none">
                            <option value="">All Job Types</option>
                            <option value="Full-time" <?php echo $filters['job_type'] === 'Full-time' ? 'selected' : ''; ?>>Full-time</option>
                            <option value="Part-time" <?php echo $filters['job_type'] === 'Part-time' ? 'selected' : ''; ?>>Part-time</option>
                            <option value="Contract" <?php echo $filters['job_type'] === 'Contract' ? 'selected' : ''; ?>>Contract</option>
                            <option value="Remote" <?php echo $filters['job_type'] === 'Remote' ? 'selected' : ''; ?>>Remote</option>
                            <option value="Internship" <?php echo $filters['job_type'] === 'Internship' ? 'selected' : ''; ?>>Internship</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold shadow-sm">
                        Search Jobs
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Main Job Listings Board -->
<div class="container py-5">
    <?php echo \App\Core\Session::flash('success'); ?>
    <?php echo \App\Core\Session::flash('error'); ?>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 border-bottom pb-3">
        <div>
            <h4 class="fw-bold mb-0">Open Vacancies</h4>
            <span class="text-muted small">Showing <?php echo count($jobs); ?> active positions</span>
        </div>
        
        <?php if (!empty($filters['keyword']) || !empty($filters['category_id']) || !empty($filters['job_type'])): ?>
            <a href="<?php echo URL_ROOT; ?>/jobs" class="btn btn-sm btn-outline-danger px-3 rounded-pill">
                <i class="fa-solid fa-xmark me-1"></i> Clear Filters
            </a>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <?php if (empty($jobs)): ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 text-center p-5 bg-white">
                    <div class="bg-light text-muted rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-briefcase fs-1"></i>
                    </div>
                    <h4 class="fw-bold mb-2">No Matching Job Openings Found</h4>
                    <p class="text-muted mb-4">Try tweaking your search keywords or resetting category filters to view available jobs.</p>
                    <a href="<?php echo URL_ROOT; ?>/jobs" class="btn btn-primary px-4 py-2 rounded-3 fw-bold">View All Open Jobs</a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($jobs as $job): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 bg-white h-100 transition-all card-hover p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="bg-primary-subtle text-primary rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                                    <i class="fa-solid <?php echo htmlspecialchars($job->category_icon ?: 'fa-building'); ?> fs-3"></i>
                                </div>
                                <span class="badge bg-primary-subtle text-primary px-3 py-1.5 rounded-pill font-monospace small">
                                    <?php echo htmlspecialchars($job->job_type); ?>
                                </span>
                            </div>

                            <h5 class="fw-bold mb-1">
                                <a href="<?php echo URL_ROOT; ?>/jobs/view/<?php echo $job->id; ?>" class="text-dark text-decoration-none hover-primary">
                                    <?php echo htmlspecialchars($job->title); ?>
                                </a>
                            </h5>

                            <p class="text-primary fw-semibold mb-2 fs-6">
                                <i class="fa-solid fa-building me-1 opacity-75"></i> <?php echo htmlspecialchars($job->company_name); ?>
                            </p>

                            <p class="text-muted small mb-3 line-clamp-2">
                                <?php echo htmlspecialchars(substr($job->description, 0, 120)) . '...'; ?>
                            </p>
                            <?php if (isset($job->required_experience) && $job->required_experience !== '' && $job->required_experience !== null): ?>
    <p class="text-muted small mb-2">
        <i class="fa-solid fa-briefcase me-1 text-primary"></i>
        <strong>Experience:</strong>
        <?php echo htmlspecialchars($job->required_experience); ?>
    </p>
<?php endif; ?>
                        </div>

                        <div class="pt-3 border-top mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-3 text-muted small">
                                <span><i class="fa-solid fa-location-dot me-1 text-danger"></i> <?php echo htmlspecialchars($job->location); ?></span>
                                <span><i class="fa-solid fa-money-bill-wave me-1 text-success"></i> <?php echo htmlspecialchars($job->salary ?: 'Negotiable'); ?></span>
                            </div>

                            <a href="<?php echo URL_ROOT; ?>/jobs/view/<?php echo $job->id; ?>" class="btn btn-outline-primary w-100 py-2 rounded-3 fw-bold">
                                View Job Details <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require APP_ROOT . '/views/layouts/footer.php'; ?>
