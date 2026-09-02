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
                        <i class="fa-solid fa-user-graduate fs-2"></i>
                    </div>

                    <h5 class="fw-bold mb-0">
                        <?php echo htmlspecialchars(\App\Core\Session::get('user_name')); ?>
                    </h5>

                    <span class="badge bg-secondary mt-2 px-3 py-2 rounded-pill small">
                        Job Candidate
                    </span>
                </div>

                <div class="nav flex-column nav-pills gap-1">

                    <a class="nav-link text-secondary py-2.5 rounded-3 fw-semibold"
                       href="<?php echo URL_ROOT; ?>/candidate/dashboard">
                        <i class="fa-solid fa-gauge me-2"></i>
                        Dashboard
                    </a>

                    <a class="nav-link text-secondary py-2.5 rounded-3 fw-semibold"
                       href="<?php echo URL_ROOT; ?>/candidate/profile">
                        <i class="fa-solid fa-user me-2"></i>
                        My Profile
                    </a>

                    <a class="nav-link active py-2.5 rounded-3 fw-semibold"
                       href="<?php echo URL_ROOT; ?>/candidate/applications">
                        <i class="fa-solid fa-paper-plane me-2"></i>
                        Applied Vacancies
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
                        My Applied Vacancies
                    </h2>

                    <p class="text-muted small mb-0">
                        History and live updates for all your submitted job applications
                    </p>
                </div>

                <a href="<?php echo URL_ROOT; ?>/jobs"
                   class="btn btn-outline-primary px-4 py-2.5 rounded-3 fw-bold">

                    <i class="fa-solid fa-magnifying-glass me-2"></i>
                    Find Vacancies

                </a>

            </div>


            <!-- Applications History Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">

                <div class="table-responsive">

                    <table class="table align-middle table-hover mb-0">

                        <thead class="table-light text-muted small">
                            <tr>
                                <th>Job Designation</th>
                                <th>Company</th>
                                <th>Location & Type</th>
                                <th>Applied Date</th>
                                <th>Resume Sent</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody class="small">

                            <?php if (empty($applications)): ?>

                                <tr>
                                    <td colspan="6"
                                        class="text-center py-4 text-muted">

                                        You haven't applied for any job vacancies yet.

                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php foreach ($applications as $app): ?>

                                    <?php
                                    /*
                                     * -------------------------------------------------
                                     * APPLICATION STATUS
                                     * -------------------------------------------------
                                     *
                                     * If status is NULL/empty from database,
                                     * treat the application as Pending.
                                     */
                                    $status = !empty($app->status)
                                        ? strtolower(trim($app->status))
                                        : 'pending';


                                    /*
                                     * -------------------------------------------------
                                     * STATUS BADGE
                                     * -------------------------------------------------
                                     */
                                    $badgeClass = 'bg-secondary-subtle text-secondary';

                                    switch ($status) {

                                        case 'pending':
                                            $badgeClass = 'bg-warning-subtle text-warning';
                                            break;

                                        case 'under_review':
                                            $badgeClass = 'bg-info-subtle text-info';
                                            break;

                                        case 'shortlisted':
                                            $badgeClass = 'bg-success-subtle text-success';
                                            break;

                                        case 'rejected':
                                            $badgeClass = 'bg-danger-subtle text-danger';
                                            break;

                                        case 'accepted':
                                            $badgeClass = 'bg-success-subtle text-success';
                                            break;

                                        default:
                                            $badgeClass = 'bg-secondary-subtle text-secondary';
                                            break;
                                    }


                                    /*
                                     * -------------------------------------------------
                                     * DISPLAY STATUS
                                     * -------------------------------------------------
                                     */
                                    $displayStatus = ucfirst(
                                        str_replace('_', ' ', $status)
                                    );


                                    /*
                                     * -------------------------------------------------
                                     * APPLIED DATE
                                     * -------------------------------------------------
                                     */
                                    $appliedDate = !empty($app->applied_at)
                                        ? date('Y-m-d H:i', strtotime($app->applied_at))
                                        : 'N/A';
                                    ?>

                                    <tr>

                                        <!-- Job -->
                                        <td>

                                            <a href="<?php echo URL_ROOT; ?>/jobs/view/<?php echo (int)$app->job_id; ?>"
                                               class="fw-bold text-dark text-decoration-none">

                                                <?php
                                                echo htmlspecialchars(
                                                    $app->job_title ?? 'Unknown Job'
                                                );
                                                ?>

                                            </a>

                                        </td>


                                        <!-- Company -->
                                        <td>

                                            <span class="fw-semibold text-secondary">

                                                <?php
                                                echo htmlspecialchars(
                                                    $app->company_name ?? 'Unknown Company'
                                                );
                                                ?>

                                            </span>

                                        </td>


                                        <!-- Location & Job Type -->
                                        <td>

                                            <div>
                                                <?php
                                                echo htmlspecialchars(
                                                    $app->location ?? 'Not specified'
                                                );
                                                ?>
                                            </div>

                                            <?php if (!empty($app->job_type)): ?>

                                                <span class="badge bg-light text-muted small">
                                                    <?php
                                                    echo htmlspecialchars($app->job_type);
                                                    ?>
                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- Applied Date -->
                                        <td>
                                            <?php echo $appliedDate; ?>
                                        </td>


                                        <!-- Resume Sent -->
                                        <td>

                                            <?php if (!empty($app->resume_path)): ?>

                                                <a href="<?php echo URL_ROOT . '/' . ltrim($app->resume_path, '/'); ?>"
                                                   target="_blank"
                                                   rel="noopener noreferrer"
                                                   class="badge bg-danger-subtle text-danger text-decoration-none px-2.5 py-1.5">

                                                    <i class="fa-regular fa-file-pdf me-1"></i>
                                                    View Resume

                                                </a>

                                            <?php else: ?>

                                                <span class="text-muted">
                                                    Not available
                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- Status -->
                                        <td>

                                            <span class="badge <?php echo $badgeClass; ?> px-2.5 py-1.5 fw-bold">

                                                <?php echo htmlspecialchars($displayStatus); ?>

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

