<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<div class="container py-5">
    <?php echo \App\Core\Session::flash('success'); ?>
    <?php echo \App\Core\Session::flash('error'); ?>

    <div class="row g-4">
        <!-- Sidebar Navigation Card -->
        <div class="col-lg-3">
            <?php 
                $activeTab = 'jobs';
                require APP_ROOT . '/views/layouts/admin_sidebar.php'; 
            ?>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="fw-extrabold mb-1">Manage Job Vacancies</h2>
                    <p class="text-muted small mb-0">Overview of all registered job postings across the portal</p>
                </div>
            </div>

            <!-- Job Listings Table Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th>Job Title</th>
                                <th>Company</th>
                                <th>Type</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Posted Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if (empty($jobs)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No job vacancies found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($jobs as $job): ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo URL_ROOT; ?>/jobs/view/<?php echo $job->id; ?>" target="_blank" class="fw-bold text-dark text-decoration-none">
                                                <?php echo htmlspecialchars($job->title); ?>
                                            </a>
                                            <div class="text-muted small"><?php echo htmlspecialchars($job->category_name ?: 'General'); ?></div>
                                        </td>
                                        <td><span class="fw-semibold text-secondary"><?php echo htmlspecialchars($job->company_name); ?></span></td>
                                        <td><span class="badge bg-info-subtle text-info px-2.5 py-1.5"><?php echo htmlspecialchars($job->job_type); ?></span></td>
                                        <td><?php echo htmlspecialchars($job->location); ?></td>
                                        <td>
                                            <span class="badge <?php echo $job->status === 'active' ? 'bg-success-subtle text-success' : ($job->status === 'inactive' ? 'bg-warning-subtle text-warning' : 'bg-secondary-subtle text-secondary'); ?> px-2.5 py-1.5">
                                                <?php echo ucfirst($job->status); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('Y-m-d', strtotime($job->created_at)); ?></td>
                                        <td class="text-end">
                                            <a href="<?php echo URL_ROOT; ?>/admin/jobs/status/<?php echo $job->id; ?>" class="btn btn-sm <?php echo $job->status === 'active' ? 'btn-outline-warning' : 'btn-outline-success'; ?> me-1" title="Toggle Status">
                                                <i class="fa-solid <?php echo $job->status === 'active' ? 'fa-pause' : 'fa-play'; ?>"></i>
                                            </a>
                                            <form action="<?php echo URL_ROOT; ?>/admin/jobs/delete/<?php echo $job->id; ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this job vacancy?');">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Job">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
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
