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
                    <a class="nav-link text-secondary py-2.5 rounded-3 fw-semibold" href="<?php echo URL_ROOT; ?>/candidate/dashboard">
                        <i class="fa-solid fa-gauge me-2"></i>Dashboard
                    </a>
                    <a class="nav-link active py-2.5 rounded-3 fw-semibold" href="<?php echo URL_ROOT; ?>/candidate/profile">
                        <i class="fa-solid fa-user me-2"></i>My Profile
                    </a>
                    <a class="nav-link text-secondary py-2.5 rounded-3 fw-semibold" href="<?php echo URL_ROOT; ?>/candidate/applications">
                        <i class="fa-solid fa-paper-plane me-2"></i>Applied Vacancies
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="fw-extrabold mb-1">My Candidate Profile</h2>
                    <p class="text-muted small mb-0">Update your resume, skill set, and contact info for employers</p>
                </div>
            </div>

            <!-- Profile Form Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-md-5">
                <form action="<?php echo URL_ROOT; ?>/candidate/profile" method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text" name="name" class="form-control py-2.5 rounded-3" value="<?php echo htmlspecialchars($candidate->name ?? \App\Core\Session::get('user_name')); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" class="form-control py-2.5 rounded-3 bg-light" value="<?php echo htmlspecialchars($candidate->email ?? \App\Core\Session::get('user_email')); ?>" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="text" name="phone" class="form-control py-2.5 rounded-3" placeholder="e.g. 9876543210" value="<?php echo htmlspecialchars($candidate->phone ?? ''); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Education Level</label>
                            <input type="text" name="education" class="form-control py-2.5 rounded-3" placeholder="e.g. Bachelor of Computer Science" value="<?php echo htmlspecialchars($candidate->education ?? ''); ?>">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Skills (Comma Separated)</label>
                            <input type="text" name="skills" class="form-control py-2.5 rounded-3" placeholder="e.g. PHP, HTML, CSS, JavaScript, React, MySQL" value="<?php echo htmlspecialchars($candidate->skills ?? ''); ?>">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Work Experience</label>
                            <textarea name="experience" class="form-control rounded-3" rows="3" placeholder="Describe your relevant work experience..."><?php echo htmlspecialchars($candidate->experience ?? ''); ?></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Personal Bio</label>
                            <textarea name="bio" class="form-control rounded-3" rows="3" placeholder="A short bio about yourself..."><?php echo htmlspecialchars($candidate->bio ?? ''); ?></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Default Resume / CV (PDF, DOC, DOCX)</label>
                            <input type="file" name="resume" class="form-control py-2.5 rounded-3" accept=".pdf,.doc,.docx">
                            <?php if (!empty($candidate->resume_path)): ?>
                                <div class="mt-2 small">
                                    Current Resume: <a href="<?php echo URL_ROOT . '/' . htmlspecialchars($candidate->resume_path); ?>" target="_blank" class="fw-bold text-danger"><i class="fa-regular fa-file-pdf me-1"></i> View Saved Resume</a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-12 pt-3">
                            <button type="submit" class="btn btn-primary px-5 py-3 rounded-3 fw-bold shadow-sm">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Save Profile Details
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/views/layouts/footer.php'; ?>
