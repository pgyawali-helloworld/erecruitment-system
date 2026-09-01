<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<div class="container py-5">
    <?php echo \App\Core\Session::flash('success'); ?>
    <?php echo \App\Core\Session::flash('error'); ?>

    <div class="row g-4">
        <!-- Sidebar Navigation Card -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-4">
                <div class="text-center py-3 border-bottom mb-3">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px;">
                        <i class="fa-solid fa-building fs-2"></i>
                    </div>
                    <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($employer->company_name ?: \App\Core\Session::get('user_name')); ?></h5>
                    <span class="badge bg-primary mt-2 px-3 py-2 rounded-pill small">Employer Account</span>
                </div>
                <div class="nav flex-column nav-pills gap-1">
                    <a class="nav-link active py-2.5 rounded-3 fw-semibold" href="<?php echo URL_ROOT; ?>/employer/dashboard">
                        <i class="fa-solid fa-gauge me-2"></i>Dashboard
                    </a>
                    <a class="nav-link text-secondary py-2.5 rounded-3 fw-semibold" href="<?php echo URL_ROOT; ?>/employer/jobs">
                        <i class="fa-solid fa-briefcase me-2"></i>My Job Openings
                    </a>
                    <a class="nav-link text-secondary py-2.5 rounded-3 fw-semibold" href="<?php echo URL_ROOT; ?>/employer/jobs/create">
                        <i class="fa-solid fa-plus-circle me-2"></i>Post New Job
                    </a>
                    <a class="nav-link text-secondary py-2.5 rounded-3 fw-semibold" href="<?php echo URL_ROOT; ?>/employer/applications">
                        <i class="fa-solid fa-users me-2"></i>Candidate Applications
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="fw-extrabold mb-1">Employer Workspace</h2>
                    <p class="text-muted small mb-0">Manage job postings, review applicant resumes, and update recruitment progress</p>
                </div>
                <a href="<?php echo URL_ROOT; ?>/employer/jobs/create" class="btn btn-primary px-4 py-2.5 rounded-3 fw-bold shadow-sm">
                    <i class="fa-solid fa-plus me-1"></i> Post New Vacancy
                </a>
            </div>

            <!-- Stats Metric Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small fw-bold text-uppercase">Total Vacancies Posted</span>
                            <span class="bg-primary-subtle text-primary rounded-3 px-2.5 py-1.5"><i class="fa-solid fa-briefcase"></i></span>
                        </div>
                        <h3 class="fw-extrabold"><?php echo count($jobs); ?></h3>
                        <p class="text-muted small mb-0">Job listings created</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small fw-bold text-uppercase">Applications Received</span>
                            <span class="bg-success-subtle text-success rounded-3 px-2.5 py-1.5"><i class="fa-solid fa-file-invoice"></i></span>
                        </div>
                        <h3 class="fw-extrabold"><?php echo count($applications); ?></h3>
                        <p class="text-muted small mb-0">Resumes submitted by job seekers</p>
                    </div>
                </div>
            </div>

            <!-- Recent Job Vacancies Table -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
                    <h5 class="fw-bold mb-0">My Recent Vacancies</h5>
                    <a href="<?php echo URL_ROOT; ?>/employer/jobs" class="btn btn-outline-primary btn-sm px-3 rounded-2">View All Vacancies</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th>Job Title</th>
                                <th>Type</th>
                                <th>Location</th>
                                <th>Applicants</th>
                                <th>Status</th>
                                <th>Posted Date</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if (empty($jobs)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">You haven't posted any job vacancies yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach (array_slice($jobs, 0, 5) as $job): ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo URL_ROOT; ?>/jobs/view/<?php echo $job->id; ?>" class="fw-bold text-dark text-decoration-none">
                                                <?php echo htmlspecialchars($job->title); ?>
                                            </a>
                                        </td>
                                        <td><span class="badge bg-info-subtle text-info px-2.5 py-1.5"><?php echo htmlspecialchars($job->job_type); ?></span></td>
                                        <td><?php echo htmlspecialchars($job->location); ?></td>
                                        <td><span class="fw-bold text-primary"><?php echo $job->applications_count; ?> candidates</span></td>
                                        <td>
                                            <span class="badge <?php echo $job->status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'; ?> px-2.5 py-1.5">
                                                <?php echo ucfirst($job->status); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('Y-m-d', strtotime($job->created_at)); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Applications Received -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
                    <h5 class="fw-bold mb-0">Recent Candidate Applications</h5>
                    <a href="<?php echo URL_ROOT; ?>/employer/applications" class="btn btn-outline-primary btn-sm px-3 rounded-2">Review All Resumes</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th>Candidate</th>
                                <th>Applied Vacancy</th>
                                <th>Date</th>
                                <th>Resume</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if (empty($applications)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No candidate applications received yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach (array_slice($applications, 0, 5) as $app): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($app->candidate_name); ?></div>
                                            <span class="text-muted small"><?php echo htmlspecialchars($app->candidate_email); ?></span>
                                        </td>
                                        <td><span class="fw-semibold text-primary"><?php echo htmlspecialchars($app->job_title); ?></span></td>
                                        <td><?php echo date('Y-m-d', strtotime($app->applied_at)); ?></td>
                                        <td>
                                            <?php if (!empty($app->resume_path)): ?>
                                                <a href="<?php echo URL_ROOT . '/' . htmlspecialchars($app->resume_path); ?>" target="_blank" class="badge bg-danger-subtle text-danger text-decoration-none px-2.5 py-1.5">
                                                    <i class="fa-regular fa-file-pdf me-1"></i> View Resume
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1.5">No Resume</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary px-2.5 py-1.5">
                                                <?php echo ucfirst(str_replace('_', ' ', $app->status)); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require APP_ROOT . '/views/layouts/footer.php'; ?>
