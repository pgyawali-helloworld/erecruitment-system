<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<div class="container py-5">
    <?php echo \App\Core\Session::flash('success'); ?>
    <?php echo \App\Core\Session::flash('error'); ?>

    <div class="mb-4">
        <a href="<?php echo URL_ROOT; ?>/jobs" class="text-decoration-none text-muted fw-semibold">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to All Vacancies
        </a>
    </div>

    <div class="row g-4">
        <!-- Main Job Description -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-md-5 mb-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4 border-bottom pb-4">
                    <div>
                        <span class="badge bg-primary-subtle text-primary px-3 py-1.5 rounded-pill mb-2 fw-bold">
                            <?php echo htmlspecialchars($job->category_name ?: 'General Vacancy'); ?>
                        </span>
                        <h1 class="fw-extrabold mb-2 display-6"><?php echo htmlspecialchars($job->title); ?></h1>
                        <p class="text-primary fs-5 fw-bold mb-2">
                            <i class="fa-solid fa-building me-1"></i> <?php echo htmlspecialchars($job->company_name); ?>
                        </p>
                        <div class="d-flex flex-wrap gap-3 text-muted small">
    <span>
        <i class="fa-solid fa-location-dot text-danger me-1"></i>
        <?php echo htmlspecialchars($job->location); ?>
    </span>

    <span>
        <i class="fa-solid fa-clock text-info me-1"></i>
        <?php echo htmlspecialchars($job->job_type); ?>
    </span>

    <span>
        <i class="fa-solid fa-money-bill-wave text-success me-1"></i>
        <?php echo htmlspecialchars($job->salary ?: 'Negotiable'); ?>
    </span>
   

    <?php if (isset($job->required_experience) && $job->required_experience !== '' && $job->required_experience !== null): ?>
        <span>
            <i class="fa-solid fa-briefcase text-primary me-1"></i>
            <strong>Experience:</strong>
            <?php echo htmlspecialchars($job->required_experience); ?>
        </span>
    <?php endif; ?>
    
</div>
                    </div>

                    <div>
                        <?php if ($hasApplied): ?>
                            <span class="badge bg-success px-4 py-3 rounded-3 fs-6">
                                <i class="fa-solid fa-circle-check me-1"></i> Application Submitted
                            </span>
                        <?php elseif ($job->status !== 'active'): ?>
                            <span class="badge bg-secondary px-4 py-3 rounded-3 fs-6">
                                Position Closed
                            </span>
                        <?php elseif (\App\Core\Session::isLoggedIn() && \App\Core\Session::get('user_role') === 'candidate'): ?>
                            <button type="button" class="btn btn-primary btn-lg px-4 py-3 rounded-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#applyModal">
                                <i class="fa-solid fa-paper-plane me-2"></i> Apply For Position
                            </button>
                        <?php elseif (!\App\Core\Session::isLoggedIn()): ?>
                            <a href="<?php echo URL_ROOT; ?>/login" class="btn btn-primary btn-lg px-4 py-3 rounded-3 fw-bold shadow-sm">
                                <i class="fa-solid fa-right-to-bracket me-2"></i> Login to Apply
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <h5 class="fw-bold mb-3 border-start border-4 border-primary ps-3">Job Overview & Description</h5>
                    <div class="text-secondary leading-relaxed fs-6">
                        <?php echo nl2br(htmlspecialchars($job->description)); ?>
                    </div>
                </div>

                <?php if (!empty($job->requirements)): ?>
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3 border-start border-4 border-primary ps-3">Candidate Requirements & Qualifications</h5>
                        <div class="text-secondary leading-relaxed fs-6">
                            <?php echo nl2br(htmlspecialchars($job->requirements)); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar Company Details Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 sticky-lg-top" style="top: 90px;">
                <h5 class="fw-bold mb-3">About Hiring Company</h5>
                <div class="border-bottom pb-3 mb-3">
                    <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($job->company_name); ?></h6>
                    <span class="badge bg-light text-dark px-2.5 py-1 rounded-2 mb-2"><?php echo htmlspecialchars($job->industry ?: 'Technology'); ?></span>
                    <?php if (!empty($job->website)): ?>
                        <div class="small">
                            <a href="<?php echo htmlspecialchars($job->website); ?>" target="_blank" class="text-decoration-none">
                                <i class="fa-solid fa-globe me-1"></i> Visit Website
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="small text-muted mb-4">
                    <div class="mb-2"><strong>Size:</strong> <?php echo htmlspecialchars($job->company_size ?: '10-50 employees'); ?></div>
                    <div class="mb-2"><strong>Location:</strong> <?php echo htmlspecialchars($job->company_address ?: $job->location); ?></div>
                    <?php if (!empty($job->company_description)): ?>
                        <div class="mt-3 text-secondary"><?php echo htmlspecialchars($job->company_description); ?></div>
                    <?php endif; ?>
                </div>

                <?php if ($job->status === 'active' && \App\Core\Session::isLoggedIn() && \App\Core\Session::get('user_role') === 'candidate' && !$hasApplied): ?>
                    <button type="button" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#applyModal">
                        <i class="fa-solid fa-paper-plane me-2"></i> Apply Now
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Apply Modal Form -->
<?php if ($job->status === 'active' && \App\Core\Session::isLoggedIn() && \App\Core\Session::get('user_role') === 'candidate' && !$hasApplied): ?>
<div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="applyModalLabel">Submit Application</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URL_ROOT; ?>/jobs/apply/<?php echo $job->id; ?>" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">You are applying for <strong><?php echo htmlspecialchars($job->title); ?></strong> at <strong><?php echo htmlspecialchars($job->company_name); ?></strong>.</p>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Upload Resume / CV (PDF, DOC, DOCX)</label>
                        <input type="file" name="resume" class="form-control py-2.5 rounded-3" accept=".pdf,.doc,.docx">
                        <div class="form-text">Leave blank to use your existing uploaded profile resume.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cover Letter / Note to Hiring Manager</label>
                        <textarea name="cover_letter" class="form-control rounded-3" rows="4" placeholder="Briefly introduce yourself and why you're a great fit for this position..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light px-4 py-2 rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-bold">Submit Resume</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require APP_ROOT . '/views/layouts/footer.php'; ?>
