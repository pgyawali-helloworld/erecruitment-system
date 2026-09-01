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
                    <h5 class="fw-bold mb-0"><?php echo htmlspecialchars(\App\Core\Session::get('user_name')); ?></h5>
                    <span class="badge bg-primary mt-2 px-3 py-2 rounded-pill small">Employer Account</span>
                </div>
                <div class="nav flex-column nav-pills gap-1">
                    <a class="nav-link text-secondary py-2.5 rounded-3 fw-semibold" href="<?php echo URL_ROOT; ?>/employer/dashboard">
                        <i class="fa-solid fa-gauge me-2"></i>Dashboard
                    </a>
                    <a class="nav-link active py-2.5 rounded-3 fw-semibold" href="<?php echo URL_ROOT; ?>/employer/jobs">
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
                    <h2 class="fw-extrabold mb-1">My Job Vacancies</h2>
                    <p class="text-muted small mb-0">Manage active job postings and create new vacancies</p>
                </div>
                <a href="<?php echo URL_ROOT; ?>/employer/jobs/create" class="btn btn-primary px-4 py-2.5 rounded-3 fw-bold shadow-sm">
                    <i class="fa-solid fa-plus me-1"></i> Post New Job
                </a>
            </div>

            <!-- Job Listings Table -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th>Job Title</th>
                                <th>Category</th>
                                <th>Type</th>
                                <th>Location</th>
                                <th>Applicants</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if (empty($jobs)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No vacancies posted yet. Click "Post New Job" to get started!</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($jobs as $job): ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo URL_ROOT; ?>/jobs/view/<?php echo $job->id; ?>" target="_blank" class="fw-bold text-dark text-decoration-none">
                                                <?php echo htmlspecialchars($job->title); ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars($job->category_name ?: 'General'); ?></td>
                                        <td><span class="badge bg-info-subtle text-info px-2.5 py-1.5"><?php echo htmlspecialchars($job->job_type); ?></span></td>
                                        <td><?php echo htmlspecialchars($job->location); ?></td>
                                        <td><span class="fw-bold text-primary"><?php echo $job->applications_count; ?> applied</span></td>
                                        <td>
                                            <span class="badge <?php echo $job->status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'; ?> px-2.5 py-1.5">
                                                <?php echo ucfirst($job->status); ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?php echo URL_ROOT; ?>/employer/jobs/edit/<?php echo $job->id; ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit Vacancy">
                                                <i class="fa-solid fa-pen"></i> Edit
                                            </a>
                                            <form action="<?php echo URL_ROOT; ?>/employer/jobs/delete/<?php echo $job->id; ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this job vacancy?');">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Vacancy">
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
