<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<div class="container py-5">
    <!-- Flash Messages -->
    <?php echo \App\Core\Session::flash('success'); ?>
    <?php echo \App\Core\Session::flash('error'); ?>

    <div class="row g-4">
        <!-- Sidebar Navigation Card -->
        <div class="col-lg-3">
            <?php 
                $activeTab = 'dashboard';
                require APP_ROOT . '/views/layouts/admin_sidebar.php'; 
            ?>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="fw-extrabold mb-1">System Control Center</h2>
                    <p class="text-muted small mb-0">Global metrics and management overview</p>
                </div>
                <span class="text-muted small"><i class="fa-regular fa-clock me-1"></i>Session: Active</span>
            </div>

            <!-- Metric Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small fw-bold text-uppercase">Employers</span>
                            <span class="bg-primary-subtle text-primary rounded-3 px-2 py-1.5"><i class="fa-solid fa-building"></i></span>
                        </div>
                        <h3 class="fw-extrabold"><?php echo $stats['total_employers']; ?></h3>
                        <p class="text-muted small mb-0">Registered accounts</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small fw-bold text-uppercase">Job Seekers</span>
                            <span class="bg-info-subtle text-info rounded-3 px-2 py-1.5"><i class="fa-solid fa-users"></i></span>
                        </div>
                        <h3 class="fw-extrabold"><?php echo $stats['total_candidates']; ?></h3>
                        <p class="text-muted small mb-0">Active candidates</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small fw-bold text-uppercase">Total Jobs</span>
                            <span class="bg-success-subtle text-success rounded-3 px-2 py-1.5"><i class="fa-solid fa-briefcase"></i></span>
                        </div>
                        <h3 class="fw-extrabold"><?php echo $stats['total_jobs']; ?></h3>
                        <p class="text-muted small mb-0">Vacancies posted</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small fw-bold text-uppercase">Applications</span>
                            <span class="bg-warning-subtle text-warning rounded-3 px-2 py-1.5"><i class="fa-solid fa-file-invoice"></i></span>
                        </div>
                        <h3 class="fw-extrabold"><?php echo $stats['total_applications']; ?></h3>
                        <p class="text-muted small mb-0">Submitted resumes</p>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <!-- Recent Registrations -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
                            <h5 class="fw-bold mb-0">Recent Users</h5>
                            <span class="badge bg-primary px-2.5 py-1.5 rounded-pill small">Newest</span>
                        </div>
                        <?php if (empty($recentUsers)): ?>
                            <p class="text-muted text-center py-4 my-0">No users registered yet.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0 table-sm">
                                    <tbody class="small">
                                        <?php foreach ($recentUsers as $user): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold"><?php echo htmlspecialchars($user->name); ?></div>
                                                    <span class="text-muted small"><?php echo htmlspecialchars($user->email); ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary-subtle text-secondary text-uppercase px-2 py-0.5" style="font-size: 0.75rem;">
                                                        <?php echo htmlspecialchars($user->role); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $user->status === 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?> px-2 py-0.5" style="font-size: 0.75rem;">
                                                        <?php echo htmlspecialchars($user->status); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Jobs -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
                            <h5 class="fw-bold mb-0">Recent Job Posts</h5>
                            <span class="badge bg-success px-2.5 py-1.5 rounded-pill small">Latest</span>
                        </div>
                        <?php if (empty($recentJobs)): ?>
                            <p class="text-muted text-center py-4 my-0">No job postings found.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0 table-sm">
                                    <tbody class="small">
                                        <?php foreach ($recentJobs as $job): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold"><?php echo htmlspecialchars($job->title); ?></div>
                                                    <span class="text-muted small"><?php echo htmlspecialchars($job->company_name); ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info-subtle text-info px-2 py-0.5" style="font-size: 0.75rem;">
                                                        <?php echo htmlspecialchars($job->job_type); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $job->status === 'active' ? 'bg-success-subtle text-success' : ($job->status === 'inactive' ? 'bg-warning-subtle text-warning' : 'bg-secondary-subtle text-secondary'); ?> px-2 py-0.5" style="font-size: 0.75rem;">
                                                        <?php echo htmlspecialchars($job->status); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Table -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
                    <h5 class="fw-bold mb-0">System Activity Stream</h5>
                    <a href="<?php echo URL_ROOT; ?>/admin/reports" class="btn btn-outline-primary btn-sm px-3 rounded-2">View Reports</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th>Activity Time</th>
                                <th>Activity Type</th>
                                <th>Description / Detail</th>
                                <th>Status / Extra</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if (empty($recentActivities)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">No recent activities logged in system.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentActivities as $act): ?>
                                    <tr>
                                        <td><?php echo date('Y-m-d H:i', strtotime($act->event_time)); ?></td>
                                        <td>
                                            <?php if ($act->event_type === 'user_registered'): ?>
                                                <span class="badge bg-primary-subtle text-primary"><i class="fa-solid fa-user-plus me-1"></i> User Registered</span>
                                            <?php elseif ($act->event_type === 'job_posted'): ?>
                                                <span class="badge bg-success-subtle text-success"><i class="fa-solid fa-briefcase me-1"></i> Job Posted</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-warning"><i class="fa-solid fa-file-invoice me-1"></i> Application Submitted</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($act->event_type === 'user_registered'): ?>
                                                New user <strong><?php echo htmlspecialchars($act->detail); ?></strong> joined.
                                            <?php elseif ($act->event_type === 'job_posted'): ?>
                                                New vacancy <strong><?php echo htmlspecialchars($act->detail); ?></strong> published.
                                            <?php else: ?>
                                                Application sent for job ID #<?php echo htmlspecialchars($act->detail); ?>.
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="text-uppercase small fw-bold text-secondary"><?php echo htmlspecialchars($act->extra); ?></span>
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
