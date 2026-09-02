<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Helpers\MatchingHelper;
use App\Helpers\ResumeParser;

/**
 * JobController Class
 * Manages public job listings, search filters,
 * job details, and application submission.
 */
class JobController extends Controller {

    private $jobModel;
    private $categoryModel;

    public function __construct() {
        $this->jobModel = $this->model('Job');
        $this->categoryModel = $this->model('Category');
    }

    /**
     * Display public job listing board
     * with search and filters.
     */
    public function index() {

        $filters = [
            'keyword' => isset($_GET['keyword'])
                ? trim($_GET['keyword'])
                : '',

            'category_id' => isset($_GET['category'])
                ? trim($_GET['category'])
                : '',

            'job_type' => isset($_GET['job_type'])
                ? trim($_GET['job_type'])
                : '',

            'location' => isset($_GET['location'])
                ? trim($_GET['location'])
                : '',

            'status' => 'active'
        ];

        $jobs = $this->jobModel->getAllJobs($filters);

        $categories =
            $this->categoryModel->getAllCategories();

        $data = [
            'title' => 'Browse Job Vacancies',
            'jobs' => $jobs,
            'categories' => $categories,
            'filters' => $filters
        ];

        $this->view(
            'jobs/index',
            $data
        );
    }

    /**
     * Display detailed single job view.
     */
    public function show($id) {

        $job =
            $this->jobModel->getJobById($id);

        if (!$job) {

            Session::setFlash(
                'error',
                'Job vacancy not found.'
            );

            $this->redirect('jobs');

            return;
        }

        $hasApplied = false;

        if (
            Session::isLoggedIn() &&
            Session::get('user_role') === 'candidate'
        ) {

            $userModel =
                $this->model('User');

            $candidate =
                $userModel->getUserDetails(
                    Session::get('user_id'),
                    'candidate'
                );

            if (
                $candidate &&
                isset($candidate->candidate_id)
            ) {

                $applicationModel =
                    $this->model('Application');

                $hasApplied =
                    $applicationModel->hasApplied(
                        $job->id,
                        $candidate->candidate_id
                    );
            }
        }

        $data = [
            'title' =>
                $job->title .
                ' - ' .
                $job->company_name,

            'job' => $job,
            'hasApplied' => $hasApplied
        ];

        $this->view(
            'jobs/view',
            $data
        );
    }

    /**
     * Process Job Application Submission.
     *
     * Matching:
     *
     * Skills only
     *      -> Skill match
     *
     * Experience only
     *      -> Experience match
     *
     * Skills + Experience
     *      -> Average of both
     *
     * Neither
     *      -> 0%
     *
     * Automatic status:
     *
     * 100%       -> shortlisted
     * 50%-99%    -> pending
     * Below 50%  -> rejected
     */
    public function apply($id) {

        Session::authorize('candidate');

        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST'
        ) {

            $this->redirect(
                'jobs/view/' . $id
            );

            return;
        }

        $job =
            $this->jobModel->getJobById($id);

        if (
            !$job ||
            $job->status !== 'active'
        ) {

            Session::setFlash(
                'error',
                'Job vacancy is no longer accepting applications.'
            );

            $this->redirect('jobs');

            return;
        }

        $userModel =
            $this->model('User');

        $candidate =
            $userModel->getUserDetails(
                Session::get('user_id'),
                'candidate'
            );

        if (
            !$candidate ||
            !isset($candidate->candidate_id)
        ) {

            Session::setFlash(
                'error',
                'Candidate profile not found. Please update your profile first.'
            );

            $this->redirect(
                'candidate/dashboard'
            );

            return;
        }

        $applicationModel =
            $this->model('Application');

        if (
            $applicationModel->hasApplied(
                $job->id,
                $candidate->candidate_id
            )
        ) {

            Session::setFlash(
                'error',
                'You have already applied for this job.'
            );

            $this->redirect(
                'jobs/view/' . $id
            );

            return;
        }

        $coverLetter =
            isset($_POST['cover_letter'])
                ? trim($_POST['cover_letter'])
                : '';

        /*
         * Existing candidate resume.
         */
        $resumePath =
            $candidate->resume_path ?: '';

        $resumeText = '';

        $resumeSkills = [];

        $education = '';

        $experience = [
            'section' => '',
            'total_years' => 0,
            'total_months' => 0,
            'formatted' => 'No experience found'
        ];

        $email = '';

        $phone = '';

        /*
         * =====================================================
         * RESUME UPLOAD
         * =====================================================
         */
        if (
            isset($_FILES['resume']) &&
            $_FILES['resume']['error'] ===
                UPLOAD_ERR_OK
        ) {

            $fileTmp =
                $_FILES['resume']['tmp_name'];

            $fileName =
                $_FILES['resume']['name'];

            $fileExt =
                strtolower(
                    pathinfo(
                        $fileName,
                        PATHINFO_EXTENSION
                    )
                );

            $allowedExts = [
                'pdf',
                'docx'
            ];

            if (
                !in_array(
                    $fileExt,
                    $allowedExts,
                    true
                )
            ) {

                Session::setFlash(
                    'error',
                    'Invalid file type. Please upload a PDF or DOCX document.'
                );

                $this->redirect(
                    'jobs/view/' . $id
                );

                return;
            }

            $uploadDir =
                APP_ROOT .
                '/../public/uploads/resumes/';

            if (!is_dir($uploadDir)) {

                mkdir(
                    $uploadDir,
                    0777,
                    true
                );
            }

            $newFileName =
                'resume_' .
                Session::get('user_id') .
                '_' .
                time() .
                '.' .
                $fileExt;

            $targetFile =
                $uploadDir .
                $newFileName;

            if (
                !move_uploaded_file(
                    $fileTmp,
                    $targetFile
                )
            ) {

                Session::setFlash(
                    'error',
                    'Failed to move uploaded file.'
                );

                $this->redirect(
                    'jobs/view/' . $id
                );

                return;
            }

            $resumePath =
                'uploads/resumes/' .
                $newFileName;

            /*
             * Parse uploaded resume.
             */
            try {

                $parser =
                    new ResumeParser();

                [
                    $resumeText,
                    $resumeSkills,
                    $education,
                    $experience,
                    $email,
                    $phone
                ] =
                    $parser->parse(
                        $targetFile
                    );

            } catch (\Exception $e) {

                error_log(
                    'Resume parsing failed: ' .
                    $e->getMessage()
                );

                Session::setFlash(
                    'error',
                    'Unable to process the uploaded resume.'
                );

                $this->redirect(
                    'jobs/view/' . $id
                );

                return;
            }

        } else {

            /*
             * =================================================
             * USE EXISTING RESUME
             * =================================================
             */

            if (
                empty($resumePath)
            ) {

                Session::setFlash(
                    'error',
                    'Please upload a resume to apply.'
                );

                $this->redirect(
                    'jobs/view/' . $id
                );

                return;
            }

            $existingFile =
                APP_ROOT .
                '/../public/' .
                $resumePath;

            if (
                file_exists($existingFile)
            ) {

                try {

                    $parser =
                        new ResumeParser();

                    [
                        $resumeText,
                        $resumeSkills,
                        $education,
                        $experience,
                        $email,
                        $phone
                    ] =
                        $parser->parse(
                            $existingFile
                        );

                } catch (\Exception $e) {

                    error_log(
                        'Existing resume parsing failed: ' .
                        $e->getMessage()
                    );

                    /*
                     * Fallback to profile skills.
                     */
                    $resumeSkills =
                        !empty($candidate->skills)
                            ? array_map(
                                'trim',
                                explode(
                                    ',',
                                    $candidate->skills
                                )
                            )
                            : [];

                }

            } else {

                /*
                 * Resume file doesn't exist.
                 * Use profile skills.
                 */
                $resumeSkills =
                    !empty($candidate->skills)
                        ? array_map(
                            'trim',
                            explode(
                                ',',
                                $candidate->skills
                            )
                        )
                        : [];
            }
        }

        /*
         * =====================================================
         * FALLBACK SKILLS
         * =====================================================
         */
        if (
            empty($resumeSkills) &&
            !empty($candidate->skills)
        ) {

            $resumeSkills =
                array_map(
                    'trim',
                    explode(
                        ',',
                        $candidate->skills
                    )
                );
        }

        $skillString =
            implode(
                ',',
                $resumeSkills
            );

        /*
         * =====================================================
         * SKILL MATCH
         * =====================================================
         */

        $skillRequirements =
            trim(
                $job->requirements ?? ''
            );

        $hasSkillRequirement =
            $skillRequirements !== '';

        $skillMatch = 0;

        if ($hasSkillRequirement) {

            $skillMatch =
                MatchingHelper::calculateMatch(
                    $skillRequirements,
                    $skillString
                );
        }

        /*
         * =====================================================
         * CANDIDATE EXPERIENCE
         * =====================================================
         */

        $candidateExperience = 0;

        if (
            is_array($experience)
        ) {

            $candidateExperience =
                (float) (
                    $experience['total_years']
                    ?? 0
                );

        } elseif (
            is_numeric($experience)
        ) {

            $candidateExperience =
                (float) $experience;
        }

        /*
         * =====================================================
         * REQUIRED EXPERIENCE
         * =====================================================
         */

        $requiredExperience =
            isset(
                $job->required_experience
            )
                ? (float)
                    $job->required_experience
                : 0;

        $hasExperienceRequirement =
            $requiredExperience > 0;

        /*
         * =====================================================
         * EXPERIENCE MATCH
         * =====================================================
         */

        $experienceMatch = 0;

        if (
            $hasExperienceRequirement
        ) {

            $experienceMatch =
                MatchingHelper::calculateExperienceMatch(
                    $requiredExperience,
                    $candidateExperience
                );
        }

        /*
         * =====================================================
         * FINAL MATCH PERCENTAGE
         * =====================================================
         */

        $matchPercentage =
            MatchingHelper::calculateOverallMatch(
                $skillMatch,
                $experienceMatch,
                $hasSkillRequirement,
                $hasExperienceRequirement
            );

        /*
         * Make absolutely sure final percentage
         * is between 0 and 100.
         */
        $matchPercentage = max(
            0,
            min(
                100,
                (int) round($matchPercentage)
            )
        );

        /*
         * =====================================================
         * AUTOMATIC APPLICATION STATUS
         * =====================================================
         *
         * 100%       = shortlisted
         * 50%-99%    = pending
         * Below 50%  = rejected
         */

        if ($matchPercentage >= 100) {

            $applicationStatus = 'shortlisted';

        } elseif ($matchPercentage < 50) {

            $applicationStatus = 'rejected';

        } else {

            $applicationStatus = 'pending';
        }

        /*
         * =====================================================
         * DEBUG LOG
         * =====================================================
         */

        error_log(
            'APPLICATION MATCH RESULT: ' .
            'Skill=' . $skillMatch . '%, ' .
            'Experience=' . $experienceMatch . '%, ' .
            'Overall=' . $matchPercentage . '%, ' .
            'Status=' . $applicationStatus
        );

        /*
         * =====================================================
         * SAVE APPLICATION
         * =====================================================
         */

        if (
            $applicationModel->apply(
                $job->id,
                $candidate->candidate_id,
                $resumePath,
                $coverLetter,
                $matchPercentage,
                $applicationStatus
            )
        ) {

            Session::setFlash(
                'success',
                'Your application for ' .
                htmlspecialchars(
                    $job->title
                ) .
                ' has been submitted successfully!',
                'alert-success'
            );

            $this->redirect(
                'candidate/dashboard'
            );

            return;

        } else {

            Session::setFlash(
                'error',
                'Failed to submit application. Please try again.'
            );

            $this->redirect(
                'jobs/view/' . $id
            );

            return;
        }
    }
}
?>