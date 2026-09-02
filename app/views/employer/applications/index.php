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
                        <?php echo htmlspecialchars(
                            \App\Core\Session::get('user_name')
                        ); ?>
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


                    <a class="nav-link text-secondary py-2.5 rounded-3 fw-semibold"
                       href="<?php echo URL_ROOT; ?>/employer/jobs">

                        <i class="fa-solid fa-briefcase me-2"></i>
                        My Job Openings

                    </a>


                    <a class="nav-link text-secondary py-2.5 rounded-3 fw-semibold"
                       href="<?php echo URL_ROOT; ?>/employer/jobs/create">

                        <i class="fa-solid fa-plus-circle me-2"></i>
                        Post New Job

                    </a>


                    <a class="nav-link active py-2.5 rounded-3 fw-semibold"
                       href="<?php echo URL_ROOT; ?>/employer/applications">

                        <i class="fa-solid fa-users me-2"></i>
                        Candidate Applications

                    </a>

                </div>

            </div>

        </div>


        <!-- Main Content Area -->
        <div class="col-lg-9">

            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

                <div>

                    <h2 class="fw-extrabold mb-1">
                        Candidate Resumes & Applications
                    </h2>

                    <p class="text-muted small mb-0">
                        Review candidate submissions and update recruitment stage
                    </p>

                </div>

            </div>


            <!-- Applications Review Table Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">

                <div class="table-responsive">

                    <table class="table align-middle table-hover mb-0">

                        <thead class="table-light text-muted small">

                            <tr>

                                <th>
                                    Candidate Info
                                </th>

                                <th>
                                    Applied Vacancy
                                </th>

                                <th>
                                    Submitted Date
                                </th>

                                <th>
                                    Resume / CV
                                </th>

                                <th>
                                    Match %
                                </th>

                                <th>
                                    Status
                                </th>

                                <th class="text-end">
                                    Update Status
                                </th>

                            </tr>

                        </thead>


                        <tbody class="small">

                            <?php if (empty($applications)): ?>

                                <tr>

                                    <td colspan="7"
                                        class="text-center py-4 text-muted">

                                        No candidate applications received yet.

                                    </td>

                                </tr>

                            <?php else: ?>


                                <?php foreach ($applications as $app): ?>

                                    <tr>

                                        <!-- Candidate Information -->
                                        <td>

                                            <div class="fw-bold text-dark">

                                                <?php echo htmlspecialchars(
                                                    $app->candidate_name
                                                ); ?>

                                            </div>


                                            <div class="text-muted small">

                                                <?php echo htmlspecialchars(
                                                    $app->candidate_email
                                                ); ?>

                                            </div>


                                            <div class="text-muted small">

                                                <i class="fa-solid fa-phone me-1"></i>

                                                <?php echo htmlspecialchars(
                                                    $app->candidate_phone ?: 'No phone'
                                                ); ?>

                                            </div>

                                        </td>


                                        <!-- Job -->
                                        <td>

                                            <span class="fw-semibold text-primary">

                                                <?php echo htmlspecialchars(
                                                    $app->job_title
                                                ); ?>

                                            </span>

                                        </td>


                                        <!-- Applied Date -->
                                        <td>

                                            <?php echo date(
                                                'Y-m-d H:i',
                                                strtotime(
                                                    $app->applied_at
                                                )
                                            ); ?>

                                        </td>


                                        <!-- Resume -->
                                        <td>

                                            <?php if (!empty($app->resume_path)): ?>

                                                <a href="<?php echo URL_ROOT . '/' . $app->resume_path; ?>"
                                                   target="_blank"
                                                   class="badge bg-danger-subtle text-danger text-decoration-none px-2 py-1">

                                                    <i class="fa-solid fa-file-pdf me-1"></i>

                                                    View Resume

                                                </a>

                                            <?php else: ?>

                                                <span class="badge bg-secondary">

                                                    No Resume

                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- MATCH INFORMATION -->
                                        <td>

                                            <?php if (
                                                isset($app->match_percentage) &&
                                                $app->match_percentage !== null
                                            ): ?>

                                                <?php

                                                /*
                                                 * Overall Match Percentage
                                                 */
                                                $matchPercentage =
                                                    (float) $app->match_percentage;


                                                /*
                                                 * Required experience from Job
                                                 */
                                                $requiredExperience =
                                                    (float) (
                                                        $app->required_experience ?? 0
                                                    );


                                                /*
                                                 * Resume experience
                                                 *
                                                 * ResumeParser stores experience
                                                 * inside resumes.parsed_json.
                                                 */
                                                $resumeExperience = 0;


                                                if (
                                                    !empty(
                                                        $app->resume_parsed_json
                                                    )
                                                ) {

                                                    $parsedResume =
                                                        json_decode(
                                                            $app->resume_parsed_json,
                                                            true
                                                        );


                                                    if (
                                                        is_array(
                                                            $parsedResume
                                                        ) &&
                                                        isset(
                                                            $parsedResume['experience']
                                                        )
                                                    ) {

                                                        /*
                                                         * Structured experience format:
                                                         *
                                                         * experience:
                                                         * {
                                                         *     total_years: 2.5,
                                                         *     text: "..."
                                                         * }
                                                         */
                                                        if (
                                                            is_array(
                                                                $parsedResume['experience']
                                                            ) &&
                                                            isset(
                                                                $parsedResume['experience']['total_years']
                                                            )
                                                        ) {

                                                            $resumeExperience =
                                                                (float) (
                                                                    $parsedResume['experience']['total_years']
                                                                );

                                                        }


                                                        /*
                                                         * Backward compatibility:
                                                         * If experience was stored as a number.
                                                         */
                                                        elseif (
                                                            is_numeric(
                                                                $parsedResume['experience']
                                                            )
                                                        ) {

                                                            $resumeExperience =
                                                                (float) (
                                                                    $parsedResume['experience']
                                                                );

                                                        }

                                                    }

                                                }

                                                ?>


                                                <!-- Overall Match -->
                                                <div class="fw-bold text-primary fs-6">

                                                    <?php echo htmlspecialchars(
                                                        number_format(
                                                            $matchPercentage,
                                                            0
                                                        )
                                                    ); ?>%

                                                </div>


                                                <!-- Experience Comparison -->
                                                <?php if ($requiredExperience > 0): ?>

                                                    <div class="mt-1">

                                                        <small class="text-muted d-block">

                                                            <i class="fa-solid fa-briefcase me-1"></i>

                                                            Required:

                                                            <strong>

                                                                <?php echo number_format(
                                                                    $requiredExperience,
                                                                    1
                                                                ); ?>

                                                            </strong>

                                                            yrs

                                                        </small>


                                                        <small class="text-muted d-block">

                                                            <i class="fa-solid fa-user-clock me-1"></i>

                                                            Resume:

                                                            <strong>

                                                                <?php echo number_format(
                                                                    $resumeExperience,
                                                                    1
                                                                ); ?>

                                                            </strong>

                                                            yrs

                                                        </small>

                                                    </div>

                                                <?php endif; ?>


                                            <?php else: ?>

                                                <span class="text-muted">
                                                    —
                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- Status -->
                                        <td>

                                            <?php

                                            /*
                                             * Default status style
                                             */
                                            $statusClass =
                                                'bg-secondary-subtle text-secondary';


                                            /*
                                             * Normalize status to lowercase
                                             * so database values are handled
                                             * consistently.
                                             */
                                            $currentStatus =
                                                strtolower(
                                                    trim(
                                                        $app->status ?? ''
                                                    )
                                                );


                                            if (
                                                $currentStatus === 'pending'
                                            ) {

                                                $statusClass =
                                                    'bg-warning-subtle text-warning';

                                            }

                                            elseif (
                                                $currentStatus === 'under_review'
                                            ) {

                                                $statusClass =
                                                    'bg-info-subtle text-info';

                                            }

                                            elseif (
                                                $currentStatus === 'shortlisted'
                                            ) {

                                                $statusClass =
                                                    'bg-success-subtle text-success';

                                            }

                                            elseif (
                                                $currentStatus === 'rejected'
                                            ) {

                                                $statusClass =
                                                    'bg-danger-subtle text-danger';

                                            }

                                            ?>


                                            <span class="badge <?php echo $statusClass; ?> px-2.5 py-1.5 fw-bold">

                                                <?php echo ucfirst(
                                                    str_replace(
                                                        '_',
                                                        ' ',
                                                        $currentStatus
                                                    )
                                                ); ?>

                                            </span>

                                        </td>


                                        <!-- Update Status -->
                                        <td class="text-end">

                                            <form
                                                action="<?php echo URL_ROOT; ?>/employer/applications/status/<?php echo $app->id; ?>"
                                                method="POST"
                                                class="d-inline-flex gap-1 align-items-center"
                                            >

                                                <select
                                                    name="status"
                                                    class="form-select form-select-sm rounded-2"
                                                    style="width: 130px;"
                                                >

                                                    <option
                                                        value="pending"
                                                        <?php echo $currentStatus === 'pending'
                                                            ? 'selected'
                                                            : ''; ?>
                                                    >
                                                        Pending
                                                    </option>


                                                    <option
                                                        value="under_review"
                                                        <?php echo $currentStatus === 'under_review'
                                                            ? 'selected'
                                                            : ''; ?>
                                                    >
                                                        Reviewing
                                                    </option>


                                                    <option
                                                        value="shortlisted"
                                                        <?php echo $currentStatus === 'shortlisted'
                                                            ? 'selected'
                                                            : ''; ?>
                                                    >
                                                        Shortlisted
                                                    </option>


                                                    <option
                                                        value="rejected"
                                                        <?php echo $currentStatus === 'rejected'
                                                            ? 'selected'
                                                            : ''; ?>
                                                    >
                                                        Rejected
                                                    </option>

                                                </select>


                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-primary px-2 py-1 rounded-2"
                                                >

                                                    <i class="fa-solid fa-check"></i>

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