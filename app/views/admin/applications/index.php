<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<div class="container py-5">
    <?php echo \App\Core\Session::flash('success'); ?>
    <?php echo \App\Core\Session::flash('error'); ?>

    <div class="row g-4">
        <!-- Sidebar Navigation Card -->
        <div class="col-lg-3">
            <?php 
                $activeTab = 'applications';
                require APP_ROOT . '/views/layouts/admin_sidebar.php'; 
            ?>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="fw-extrabold mb-1">Global Applications Audit</h2>
                    <p class="text-muted small mb-0">System-wide candidate job submissions and status tracking</p>
                </div>
            </div>

            <!-- Applications Table Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th>Candidate</th>
                                <th>Job Title</th>
                                <th>Employer / Company</th>
                                <th>Applied Date</th>
                                <th>Resume</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if (empty($applications)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No applications found in the system.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($applications as $app): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($app->candidate_name); ?></div>
                                            <span class="text-muted small"><?php echo htmlspecialchars($app->candidate_email); ?></span>
                                        </td>
                                        <td><span class="fw-semibold text-primary"><?php echo htmlspecialchars($app->job_title); ?></span></td>
                                        <td><?php echo htmlspecialchars($app->company_name); ?></td>
                                        <td><?php echo date('Y-m-d H:i', strtotime($app->applied_at)); ?></td>
                                        <td>
                                            <?php if (!empty($app->resume_path)): ?>
                                                <a href="<?php echo URL_ROOT . '/' . $candidate->resume_path . htmlspecialchars($app->resume_path); ?>" target="_blank" class="badge bg-danger-subtle text-danger text-decoration-none px-2.5 py-1.5">
                                                    <i class="fa-regular fa-file-pdf me-1"></i> View Resume
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">None</span>
                                            <?php endif; ?>
                                        </td>
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
