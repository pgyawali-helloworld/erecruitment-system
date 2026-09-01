<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<div class="container py-5">
    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3">
            <?php 
                $activeTab = 'employers';
                require APP_ROOT . '/views/layouts/admin_sidebar.php'; 
            ?>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="fw-extrabold mb-1">Employer Profile Details</h2>
                    <p class="text-muted small mb-0">Detailed view of company profile and system logs</p>
                </div>
                <a href="<?php echo URL_ROOT; ?>/admin/employers" class="btn btn-outline-secondary btn-sm rounded-2 px-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to List
                </a>
            </div>

            <!-- Profile Info Cards -->
            <div class="row g-4">
                <!-- Basic Company Profile -->
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                <i class="fa-solid fa-building fs-3"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0"><?php echo htmlspecialchars($employer->company_name ?: 'No Company Profile Name'); ?></h4>
                                <span class="text-muted small"><i class="fa-solid fa-globe me-1"></i><?php echo htmlspecialchars($employer->website ?: 'Website not provided'); ?></span>
                            </div>
                        </div>
                        
                        <h5 class="fw-bold mt-4 mb-2 small text-secondary text-uppercase">Company Description</h5>
                        <p class="text-dark small leading-relaxed">
                            <?php echo nl2br(htmlspecialchars($employer->description ?: 'No description provided by the employer.')); ?>
                        </p>
                        
                        <div class="row g-3 mt-3 pt-3 border-top">
                            <div class="col-sm-6">
                                <span class="text-muted d-block small">Industry</span>
                                <span class="fw-semibold small text-dark"><?php echo htmlspecialchars($employer->industry ?: 'N/A'); ?></span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block small">Company Size</span>
                                <span class="fw-semibold small text-dark"><?php echo htmlspecialchars($employer->company_size ?: 'N/A'); ?></span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block small">Corporate Address</span>
                                <span class="fw-semibold small text-dark"><?php echo htmlspecialchars($employer->address ?: 'N/A'); ?></span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block small">Registered Account Name</span>
                                <span class="fw-semibold small text-dark"><?php echo htmlspecialchars($employer->name); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account & Control Settings -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4 h-100">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">Status & Actions</h5>
                        
                        <div class="mb-4">
                            <span class="text-muted d-block small">Account Status</span>
                            <span class="badge <?php echo $employer->status === 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?> px-3 py-2 rounded-pill mt-2">
                                <i class="fa-solid <?php echo $employer->status === 'active' ? 'fa-circle-check' : 'fa-circle-xmark'; ?> me-1"></i>
                                <?php echo ucfirst(htmlspecialchars($employer->status)); ?>
                            </span>
                        </div>
                        
                        <div class="mb-4">
                            <span class="text-muted d-block small">Registered Email</span>
                            <span class="fw-bold small text-dark d-block text-truncate"><?php echo htmlspecialchars($employer->email); ?></span>
                        </div>
                        
                        <div class="mb-4">
                            <span class="text-muted d-block small">Joined Platform At</span>
                            <span class="fw-semibold small text-dark"><?php echo date('F d, Y H:i', strtotime($employer->created_at)); ?></span>
                        </div>
                        
                        <div class="d-grid gap-2 border-top pt-3">
                            <a href="<?php echo URL_ROOT; ?>/admin/employers/toggle/<?php echo $employer->id; ?>" class="btn <?php echo $employer->status === 'active' ? 'btn-warning' : 'btn-success'; ?> rounded-3 py-2">
                                <i class="fa-solid <?php echo $employer->status === 'active' ? 'fa-user-slash' : 'fa-user-check'; ?> me-1"></i>
                                <?php echo $employer->status === 'active' ? 'Deactivate Employer' : 'Activate Employer'; ?>
                            </a>
                            
                            <form action="<?php echo URL_ROOT; ?>/admin/employers/delete/<?php echo $employer->id; ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this employer? This will permanently delete all company details, posted jobs, and job applications.');">
                                <button type="submit" class="btn btn-danger w-100 rounded-3 py-2">
                                    <i class="fa-solid fa-trash-can me-1"></i> Delete Employer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require APP_ROOT . '/views/layouts/footer.php'; ?>
