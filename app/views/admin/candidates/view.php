<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<div class="container py-5">
    <?php echo \App\Core\Session::flash('success'); ?>
    <?php echo \App\Core\Session::flash('error'); ?>

    <div class="row g-4">
        <!-- Sidebar Navigation Card -->
        <div class="col-lg-3">
            <?php 
                $activeTab = 'candidates';
                require APP_ROOT . '/views/layouts/admin_sidebar.php'; 
            ?>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="fw-extrabold mb-1">Job Seeker Profile Details</h2>
                    <p class="text-muted small mb-0">Detailed view of candidate credentials, resume, and experience</p>
                </div>
                <a href="<?php echo URL_ROOT; ?>/admin/candidates" class="btn btn-outline-secondary px-4 py-2 rounded-3 fw-semibold">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Job Seekers
                </a>
            </div>

            <!-- Profile Info Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <div class="d-flex align-items-center flex-wrap gap-4 border-bottom pb-4 mb-4">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-2" style="width: 80px; height: 80px;">
                        <?php echo strtoupper(substr($candidate->name, 0, 1)); ?>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1"><?php echo htmlspecialchars($candidate->name); ?></h3>
                        <p class="text-muted mb-2"><i class="fa-regular fa-envelope me-1"></i> <?php echo htmlspecialchars($candidate->email); ?></p>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="badge <?php echo $candidate->status === 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?> px-3 py-1.5 rounded-pill">
                                <?php echo ucfirst($candidate->status); ?>
                            </span>
                            <span class="badge bg-secondary-subtle text-secondary px-3 py-1.5 rounded-pill">
                                Joined <?php echo date('M d, Y', strtotime($candidate->created_at)); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-uppercase text-muted small mb-2">Phone Number</h6>
                        <p class="fw-semibold mb-3"><?php echo htmlspecialchars($candidate->phone ?: 'Not provided'); ?></p>
                    </div>

                    <div class="col-md-6">
                        <h6 class="fw-bold text-uppercase text-muted small mb-2">Education Background</h6>
                        <p class="fw-semibold mb-3"><?php echo htmlspecialchars($candidate->education ?: 'Not specified'); ?></p>
                    </div>

                    <div class="col-md-12">
                        <h6 class="fw-bold text-uppercase text-muted small mb-2">Key Skills</h6>
                        <p class="mb-3">
                            <?php if (!empty($candidate->skills)): ?>
                                <?php foreach (explode(',', $candidate->skills) as $skill): ?>
                                    <span class="badge bg-primary-subtle text-primary px-3 py-2 me-1 mb-1 rounded-3 fw-medium">
                                        <i class="fa-solid fa-check me-1"></i> <?php echo htmlspecialchars(trim($skill)); ?>
                                    </span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-muted fs-6">No skills listed yet.</span>
                            <?php endif; ?>
                        </p>
                    </div>

                    <div class="col-md-12">
                        <h6 class="fw-bold text-uppercase text-muted small mb-2">Professional Experience</h6>
                        <p class="text-secondary mb-3"><?php echo nl2br(htmlspecialchars($candidate->experience ?: 'No experience record added.')); ?></p>
                    </div>

                    <div class="col-md-12">
                        <h6 class="fw-bold text-uppercase text-muted small mb-2">Personal Bio</h6>
                        <p class="text-secondary mb-3"><?php echo nl2br(htmlspecialchars($candidate->bio ?: 'No bio provided.')); ?></p>
                    </div>

                    <?php if (!empty($candidate->resume_path)): ?>
                        <div class="col-md-12">
                            <h6 class="fw-bold text-uppercase text-muted small mb-2">Resume / CV Document</h6>
                            <a href="<?php echo URL_ROOT . '/' . $candidate->resume_path . htmlspecialchars($candidate->resume_path); ?>" target="_blank" class="btn btn-outline-danger px-4 py-2 rounded-3 fw-bold">
                                <i class="fa-regular fa-file-pdf me-2"></i> Download / View Resume
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Profile Actions Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <h5 class="fw-bold mb-3">Administrative Controls</h5>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="<?php echo URL_ROOT; ?>/admin/candidates/status/<?php echo $candidate->id; ?>" class="btn <?php echo $candidate->status === 'active' ? 'btn-warning' : 'btn-success'; ?> px-4 py-2.5 rounded-3 fw-bold">
                        <i class="fa-solid <?php echo $candidate->status === 'active' ? 'fa-ban' : 'fa-check'; ?> me-2"></i>
                        <?php echo $candidate->status === 'active' ? 'Deactivate Account' : 'Activate Account'; ?>
                    </a>
                    
                    <form action="<?php echo URL_ROOT; ?>/admin/candidates/delete/<?php echo $candidate->id; ?>" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this candidate and all associated data?');">
                        <button type="submit" class="btn btn-danger px-4 py-2.5 rounded-3 fw-bold">
                            <i class="fa-solid fa-trash me-2"></i> Delete Candidate Account
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require APP_ROOT . '/views/layouts/footer.php'; ?>
