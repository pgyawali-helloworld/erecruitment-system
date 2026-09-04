<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<div class="container py-5">

    <?php echo \App\Core\Session::flash('success'); ?>
    <?php echo \App\Core\Session::flash('error'); ?>

    <div class="row g-4">

        <!-- Sidebar Navigation Card -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-4">

                <div class="text-center py-3 border-bottom mb-3">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width: 70px; height: 70px;">
                        <i class="fa-solid fa-building fs-2"></i>
                    </div>

                    <h5 class="fw-bold mb-0">
                        <?php echo htmlspecialchars(\App\Core\Session::get('user_name')); ?>
                    </h5>

                    <span class="badge bg-primary mt-2 px-3 py-2 rounded-pill small">
                        Employer Account
                    </span>
                </div>

                <div class="nav flex-column nav-pills gap-1">

                    <a class="nav-link text-secondary py-2.5 rounded-3 fw-semibold"
                       href="<?php echo URL_ROOT; ?>/employer/dashboard">
                        <i class="fa-solid fa-gauge me-2"></i>
                        Dashboard
                    </a>

                    <a class="nav-link active py-2.5 rounded-3 fw-semibold"
                       href="<?php echo URL_ROOT; ?>/employer/jobs">
                        <i class="fa-solid fa-briefcase me-2"></i>
                        My Job Openings
                    </a>

                    <a class="nav-link text-secondary py-2.5 rounded-3 fw-semibold"
                       href="<?php echo URL_ROOT; ?>/employer/jobs/create">
                        <i class="fa-solid fa-plus-circle me-2"></i>
                        Post New Job
                    </a>

                    <a class="nav-link text-secondary py-2.5 rounded-3 fw-semibold"
                       href="<?php echo URL_ROOT; ?>/employer/applications">
                        <i class="fa-solid fa-users me-2"></i>
                        Candidate Applications
                    </a>

                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9">

            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

                <div>
                    <h2 class="fw-extrabold mb-1">
                        Edit Job Vacancy
                    </h2>

                    <p class="text-muted small mb-0">
                        Update the details of your job posting
                    </p>
                </div>

                <a href="<?php echo URL_ROOT; ?>/employer/jobs"
                   class="btn btn-outline-secondary px-4 py-2.5 rounded-3 fw-semibold">
                    <i class="fa-solid fa-arrow-left me-1"></i>
                    Back to Jobs
                </a>

            </div>

            <!-- Edit Job Form Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-md-5">

                <form action="<?php echo URL_ROOT; ?>/employer/jobs/edit/<?php echo $job->id; ?>"
                      method="POST"
                      novalidate>

                    <div class="row g-3">

                        <!-- Job Title -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold">
                                Job Title <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="title"
                                minlength="5"
                                maxlength="100"
                                required
                                class="form-control py-2.5 rounded-3 <?php echo !empty($errors['title']) ? 'is-invalid' : ''; ?>"
                                placeholder="e.g. Senior PHP Web Developer"
                                value="<?php echo htmlspecialchars($job->title ?? ''); ?>">

                            <?php if (!empty($errors['title'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errors['title']); ?>
                                </div>
                            <?php endif; ?>
                        </div>


                        <!-- Job Category -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                Job Category <span class="text-danger">*</span>
                            </label>

                            <select
                                name="category_id"
                                required
                                class="form-select py-2.5 rounded-3 <?php echo !empty($errors['category_id']) ? 'is-invalid' : ''; ?>">

                                <option value="">
                                    Select Category
                                </option>

                                <?php foreach ($categories as $cat): ?>

                                    <option
                                        value="<?php echo htmlspecialchars($cat->id); ?>"
                                        <?php echo ((string)($job->category_id ?? '') === (string)$cat->id) ? 'selected' : ''; ?>>

                                        <?php echo htmlspecialchars($cat->name); ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                            <?php if (!empty($errors['category_id'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errors['category_id']); ?>
                                </div>
                            <?php endif; ?>

                        </div>


                        <!-- Employment Type -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                Employment Type <span class="text-danger">*</span>
                            </label>

                            <select
                                name="job_type"
                                required
                                class="form-select py-2.5 rounded-3 <?php echo !empty($errors['job_type']) ? 'is-invalid' : ''; ?>">

                                <option value="">
                                    Select Employment Type
                                </option>

                                <option value="Full-time"
                                    <?php echo (($job->job_type ?? '') === 'Full-time') ? 'selected' : ''; ?>>
                                    Full-time
                                </option>

                                <option value="Part-time"
                                    <?php echo (($job->job_type ?? '') === 'Part-time') ? 'selected' : ''; ?>>
                                    Part-time
                                </option>

                                <option value="Contract"
                                    <?php echo (($job->job_type ?? '') === 'Contract') ? 'selected' : ''; ?>>
                                    Contract
                                </option>

                                <option value="Remote"
                                    <?php echo (($job->job_type ?? '') === 'Remote') ? 'selected' : ''; ?>>
                                    Remote
                                </option>

                                <option value="Internship"
                                    <?php echo (($job->job_type ?? '') === 'Internship') ? 'selected' : ''; ?>>
                                    Internship
                                </option>

                            </select>

                            <?php if (!empty($errors['job_type'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errors['job_type']); ?>
                                </div>
                            <?php endif; ?>

                        </div>


                        <!-- Job Location -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                Job Location <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="location"
                                minlength="3"
                                maxlength="100"
                                required
                                class="form-control py-2.5 rounded-3 <?php echo !empty($errors['location']) ? 'is-invalid' : ''; ?>"
                                placeholder="e.g. Kathmandu, Nepal or Remote"
                                value="<?php echo htmlspecialchars($job->location ?? ''); ?>">

                            <?php if (!empty($errors['location'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errors['location']); ?>
                                </div>
                            <?php endif; ?>

                        </div>


                        <!-- Salary -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                Salary (NPR) <span class="text-danger">*</span>
                            </label>

                            <input
                                type="number"
                                name="salary"
                                min="0"
                                step="0.01"
                                required
                                class="form-control py-2.5 rounded-3 <?php echo !empty($errors['salary']) ? 'is-invalid' : ''; ?>"
                                placeholder="e.g. 80000"
                                value="<?php echo htmlspecialchars($job->salary ?? ''); ?>">

                            <?php if (!empty($errors['salary'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errors['salary']); ?>
                                </div>
                            <?php endif; ?>

                        </div>


                        <!-- Application Expiry Date -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                Application Expiry Date <span class="text-danger">*</span>
                            </label>

                            <input
                                type="date"
                                name="expiry_date"
                                min="<?php echo date('Y-m-d'); ?>"
                                required
                                class="form-control py-2.5 rounded-3 <?php echo !empty($errors['expiry_date']) ? 'is-invalid' : ''; ?>"
                                value="<?php echo htmlspecialchars($job->expiry_date ?? ''); ?>">

                            <?php if (!empty($errors['expiry_date'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errors['expiry_date']); ?>
                                </div>
                            <?php endif; ?>

                        </div>


                        <!-- Required Experience -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                Required Experience <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">

                                <input
                                    type="number"
                                    name="required_experience"
                                    id="required_experience"
                                    min="0"
                                    max="50"
                                    step="0.5"
                                    required
                                    class="form-control py-2.5 rounded-start-3 <?php echo !empty($errors['required_experience']) ? 'is-invalid' : ''; ?>"
                                    placeholder="e.g. 2"
                                    value="<?php echo htmlspecialchars($job->required_experience ?? '0'); ?>">

                                <span class="input-group-text">
                                    Years
                                </span>

                            </div>

                            <?php if (!empty($errors['required_experience'])): ?>
                                <div class="invalid-feedback d-block">
                                    <?php echo htmlspecialchars($errors['required_experience']); ?>
                                </div>
                            <?php endif; ?>

                            <small class="text-muted">
                                Enter 0 for freshers. You can use 0.5 for 6 months.
                            </small>

                        </div>


                        <!-- Job Status -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                Job Status <span class="text-danger">*</span>
                            </label>

                            <select
                                name="status"
                                required
                                class="form-select py-2.5 rounded-3 <?php echo !empty($errors['status']) ? 'is-invalid' : ''; ?>">

                                <option value="active"
                                    <?php echo (($job->status ?? '') === 'active') ? 'selected' : ''; ?>>
                                    Active
                                </option>

                                <option value="inactive"
                                    <?php echo (($job->status ?? '') === 'inactive') ? 'selected' : ''; ?>>
                                    Inactive
                                </option>

                                <option value="closed"
                                    <?php echo (($job->status ?? '') === 'closed') ? 'selected' : ''; ?>>
                                    Closed
                                </option>

                            </select>

                            <?php if (!empty($errors['status'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errors['status']); ?>
                                </div>
                            <?php endif; ?>

                        </div>


                        <!-- Job Description -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold">
                                Job Overview & Responsibilities
                                <span class="text-danger">*</span>
                            </label>

                            <textarea
                                name="description"
                                rows="5"
                                minlength="20"
                                required
                                class="form-control rounded-3 <?php echo !empty($errors['description']) ? 'is-invalid' : ''; ?>"
                                placeholder="Detailed description of the role..."><?php echo htmlspecialchars($job->description ?? ''); ?></textarea>

                            <?php if (!empty($errors['description'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($errors['description']); ?>
                                </div>
                            <?php endif; ?>

                        </div>


                        <!-- Candidate Requirements -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold">
                                Candidate Requirements & Qualifications
                            </label>

                            <textarea
                                name="requirements"
                                rows="4"
                                minlength="10"
                                class="form-control rounded-3"
                                placeholder="Skills, qualifications, and experience required..."><?php echo htmlspecialchars($job->requirements ?? ''); ?></textarea>

                        </div>


                        <!-- Submit Button -->
                        <div class="col-md-12 pt-3">

                            <button
                                type="submit"
                                class="btn btn-primary px-5 py-3 rounded-3 fw-bold shadow-sm">

                                <i class="fa-solid fa-save me-2"></i>
                                Update Job Vacancy

                            </button>

                        </div>

                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/views/layouts/footer.php'; ?>