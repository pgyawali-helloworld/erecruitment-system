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
                        <i class="fa-solid fa-user-graduate fs-2"></i>
                    </div>
                    <h5 class="fw-bold mb-0"><?php echo htmlspecialchars(\App\Core\Session::get('user_name')); ?></h5>
                    <span class="badge bg-secondary mt-2 px-3 py-2 rounded-pill small">Job Candidate</span>
                </div>
                <div class="nav flex-column nav-pills gap-1">
                    <a class="nav-link active py-2.5 rounded-3 fw-semibold" href="<?php echo URL_ROOT; ?>/candidate/dashboard">
                        <i class="fa-solid fa-gauge me-2"></i>Dashboard
                    </a>
                    <a class="nav-link text-secondary py-2.5 rounded-3 fw-semibold" href="<?php echo URL_ROOT; ?>/candidate/profile">
                        <i class="fa-solid fa-user me-2"></i>My Profile
                    </a>
                    <a class="nav-link text-secondary py-2.5 rounded-3 fw-semibold" href="<?php echo URL_ROOT; ?>/candidate/applications">
                        <i class="fa-solid fa-paper-plane me-2"></i>Applied Vacancies
                    </a>
                    <a class="nav-link text-secondary py-2.5 rounded-3 fw-semibold" href="<?php echo URL_ROOT; ?>/candidate/resume">
                        <i class="fa-solid fa-file-lines me-2"></i>My Resume
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="fw-extrabold mb-1">Candidate Dashboard</h2>
                    <p class="text-muted small mb-0">Track submitted job applications, update profile, and view status</p>
                </div>
                <a href="<?php echo URL_ROOT; ?>/jobs" class="btn btn-primary px-4 py-2.5 rounded-3 fw-bold shadow-sm">
                    <i class="fa-solid fa-magnifying-glass me-2"></i> Browse More Vacancies
                </a>
            </div>

            <!-- Stats Metric Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small fw-bold text-uppercase">Applications Sent</span>
                            <span class="bg-primary-subtle text-primary rounded-3 px-2.5 py-1.5"><i class="fa-solid fa-paper-plane"></i></span>
                        </div>
                        <h3 class="fw-extrabold"><?php echo count($applications); ?></h3>
                        <p class="text-muted small mb-0">Job positions applied</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small fw-bold text-uppercase">Shortlisted / Accepted</span>
                            <span class="bg-success-subtle text-success rounded-3 px-2.5 py-1.5"><i class="fa-solid fa-circle-check"></i></span>
                        </div>
                        <?php 
                            $acceptedCount = 0;
                            foreach ($applications as $app) {
                                if ($app->status === 'accepted') $acceptedCount++;
                            }
                        ?>
                        <h3 class="fw-extrabold"><?php echo $acceptedCount; ?></h3>
                        <p class="text-success small mb-0"><?php echo $acceptedCount; ?> Accepted applications</p>
                    </div>
                </div>
            </div>

            <!-- Application Progress Table -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
                    <h5 class="fw-bold mb-0">Recent Applications Status</h5>
                    <a href="<?php echo URL_ROOT; ?>/candidate/applications" class="btn btn-outline-primary btn-sm px-3 rounded-2">View History</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th>Job Designation</th>
                                <th>Company</th>
                                <th>Location</th>
                                <th>Applied Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if (empty($applications)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">You haven't submitted any job applications yet. <a href="<?php echo URL_ROOT; ?>/jobs" class="fw-bold">Browse Jobs</a> to apply!</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach (array_slice($applications, 0, 5) as $app): ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo URL_ROOT; ?>/jobs/view/<?php echo $app->job_id; ?>" class="fw-bold text-dark text-decoration-none">
                                                <?php echo htmlspecialchars($app->job_title); ?>
                                            </a>
                                        </td>
                                        <td><span class="fw-semibold text-secondary"><?php echo htmlspecialchars($app->company_name); ?></span></td>
                                        <td><?php echo htmlspecialchars($app->location); ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($app->applied_at)); ?></td>
                                        <td>
                                            <?php 
                                                $badgeClass = 'bg-secondary-subtle text-secondary';
                                                if ($app->status === 'pending') $badgeClass = 'bg-warning-subtle text-warning';
                                                elseif ($app->status === 'under_review') $badgeClass = 'bg-info-subtle text-info';
                                                elseif ($app->status === 'accepted') $badgeClass = 'bg-success-subtle text-success';
                                                elseif ($app->status === 'rejected') $badgeClass = 'bg-danger-subtle text-danger';
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?> px-2.5 py-1.5 fw-bold">
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
