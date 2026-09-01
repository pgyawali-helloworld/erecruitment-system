<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<div class="container py-5">
    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3">
            <?php 
                $activeTab = 'reports';
                require APP_ROOT . '/views/layouts/admin_sidebar.php'; 
            ?>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="fw-extrabold mb-1">System Reports & Analytics</h2>
                    <p class="text-muted small mb-0">Live statistics and activity audits</p>
                </div>
                <button onclick="window.print()" class="btn btn-outline-dark btn-sm rounded-2 px-3">
                    <i class="fa-solid fa-print me-1"></i> Print Report
                </button>
            </div>

            <!-- Summary Statistics Cards -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Key Metrics</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="border rounded-4 p-3 bg-light text-center">
                            <span class="text-secondary small fw-bold uppercase">Total Registered Users</span>
                            <h2 class="fw-extrabold text-primary mt-2">
                                <?php echo $stats['total_employers'] + $stats['total_candidates'] + 1; // including admin ?>
                            </h2>
                            <div class="small text-muted mt-1">
                                <?php echo $stats['total_employers']; ?> Employers | <?php echo $stats['total_candidates']; ?> Candidates
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded-4 p-3 bg-light text-center">
                            <span class="text-secondary small fw-bold uppercase">Total Job Vacancies</span>
                            <h2 class="fw-extrabold text-success mt-2">
                                <?php echo $stats['total_jobs']; ?>
                            </h2>
                            <div class="small text-muted mt-1">
                                Open and active jobs on the platform
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded-4 p-3 bg-light text-center">
                            <span class="text-secondary small fw-bold uppercase">Total Job Applications</span>
                            <h2 class="fw-extrabold text-warning mt-2">
                                <?php echo $stats['total_applications']; ?>
                            </h2>
                            <div class="small text-muted mt-1">
                                Resumes submitted for active openings
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jobs by Category -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Jobs by Category</h5>
                <?php if (empty($jobsByCategory)): ?>
                    <p class="text-muted text-center py-4 my-0">No job categories defined.</p>
                <?php else: ?>
                    <div class="row g-4">
                        <?php 
                        $maxJobs = 0;
                        foreach ($jobsByCategory as $c) {
                            if ($c->job_count > $maxJobs) $maxJobs = $c->job_count;
                        }
                        // Avoid division by zero
                        $maxJobs = $maxJobs ?: 1;
                        ?>
                        <?php foreach ($jobsByCategory as $c): ?>
                            <?php 
                            $percentage = round(($c->job_count / $maxJobs) * 100); 
                            ?>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 text-primary" style="width: 24px; text-align: center;">
                                        <i class="fa-solid <?php echo htmlspecialchars($c->icon ?: 'fa-briefcase'); ?>"></i>
                                    </span>
                                    <span class="fw-semibold small flex-grow-1"><?php echo htmlspecialchars($c->name); ?></span>
                                    <span class="badge bg-secondary-subtle text-secondary small"><?php echo $c->job_count; ?> jobs</span>
                                </div>
                                <div class="progress rounded-pill mb-3" style="height: 8px;">
                                    <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: <?php echo $percentage; ?>%;" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Detailed Activity Log -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Detailed System Activity Audit</h5>
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th>Timestamp</th>
                                <th>Event Type</th>
                                <th>Affected Asset / Description</th>
                                <th>Attribute / Status</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if (empty($recentActivities)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No activities recorded.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentActivities as $act): ?>
                                    <tr>
                                        <td><?php echo date('Y-m-d H:i:s', strtotime($act->event_time)); ?></td>
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
                                                New account registered: <strong><?php echo htmlspecialchars($act->detail); ?></strong>
                                            <?php elseif ($act->event_type === 'job_posted'): ?>
                                                New job opening listed: <strong><?php echo htmlspecialchars($act->detail); ?></strong>
                                            <?php else: ?>
                                                Application sent for job ID #<?php echo htmlspecialchars($act->detail); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="text-uppercase font-monospace small px-2 py-0.5 border rounded bg-light"><?php echo htmlspecialchars($act->extra); ?></span>
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
