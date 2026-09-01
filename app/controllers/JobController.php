<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Helpers\MatchingHelper;
use App\Helpers\ResumeParser;

/**
 * JobController Class
 * Manages public job listings, search filters, job details, and application submission.
 */
class JobController extends Controller {
    private $jobModel;
    private $categoryModel;

    public function __construct() {
        $this->jobModel = $this->model('Job');
        $this->categoryModel = $this->model('Category');
    }

    /**
     * Display public job listing board with search and filters
     */
    public function index() {
        $filters = [
            'keyword' => isset($_GET['keyword']) ? trim($_GET['keyword']) : '',
            'category_id' => isset($_GET['category']) ? trim($_GET['category']) : '',
            'job_type' => isset($_GET['job_type']) ? trim($_GET['job_type']) : '',
            'location' => isset($_GET['location']) ? trim($_GET['location']) : '',
            'status' => 'active'
        ];

        $jobs = $this->jobModel->getAllJobs($filters);
        $categories = $this->categoryModel->getAllCategories();

        $data = [
            'title' => 'Browse Job Vacancies',
            'jobs' => $jobs,
            'categories' => $categories,
            'filters' => $filters
        ];

        $this->view('jobs/index', $data);
    }

    /**
     * Display detailed single job view
     */
    public function show($id) {
        $job = $this->jobModel->getJobById($id);


        if (!$job) {
            Session::setFlash('error', 'Job vacancy not found.');
            $this->redirect('jobs');
        }

        $hasApplied = false;
        if (Session::isLoggedIn() && Session::get('user_role') === 'candidate') {
            $userModel = $this->model('User');
            $candidate = $userModel->getUserDetails(Session::get('user_id'), 'candidate');
            if ($candidate && isset($candidate->candidate_id)) {
                $applicationModel = $this->model('Application');
                $hasApplied = $applicationModel->hasApplied($job->id, $candidate->candidate_id);
            }
        }

        $data = [
            'title' => $job->title . ' - ' . $job->company_name,
            'job' => $job,
            'hasApplied' => $hasApplied
        ];

        $this->view('jobs/view', $data);
    }

    /**
     * Process Job Application Submission
     */
    public function apply($id) {
        Session::authorize('candidate');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $job = $this->jobModel->getJobById($id);

            if (!$job || $job->status !== 'active') {
                Session::setFlash('error', 'Job vacancy is no longer accepting applications.');
                $this->redirect('jobs');
            }

            $userModel = $this->model('User');
            $candidate = $userModel->getUserDetails(Session::get('user_id'), 'candidate');

            if (!$candidate || !isset($candidate->candidate_id)) {
                Session::setFlash('error', 'Candidate profile not found. Please update your profile first.');
                $this->redirect('candidate/dashboard');
            }

            $applicationModel = $this->model('Application');

            if ($applicationModel->hasApplied($job->id, $candidate->candidate_id)) {
                Session::setFlash('error', 'You have already applied for this job.');
                $this->redirect('jobs/view/' . $id);
            }

            $coverLetter = isset($_POST['cover_letter']) ? trim($_POST['cover_letter']) : '';
            $resumePath = $candidate->resume_path ?: '';

                        // Handle file upload if a new resume is uploaded
            if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
                $fileTmp = $_FILES['resume']['tmp_name'];
                $fileName = $_FILES['resume']['name'];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $allowedExts = ['pdf', 'doc', 'docx'];
                if (in_array($fileExt, $allowedExts)) {
                    $uploadDir = APP_ROOT . '/../public/uploads/resumes/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $newFileName = 'resume_' . Session::get('user_id') . '_' . time() . '.' . $fileExt;
                    $targetFile = $uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmp, $targetFile)) {
                        $resumePath = 'uploads/resumes/' . $newFileName;

                        // Parse the uploaded resume
                        try {
                            $parser = new ResumeParser();
                            [$resumeText, $resumeSkills, $education, $experience, $email, $phone] =
                                $parser->parse($targetFile);

                            $parsedSkills = implode(',', $resumeSkills);
                            // Calculate match percentage based on parsed skills
                            $matchPercentage = MatchingHelper::calculateMatch(
                                $job->requirements ?? '',
                                $parsedSkills
                            );
                        } catch (\Exception $e) {
                             error_log('Resume parsing failed: ' . $e->getMessage());
                             // Fallback to 0 match percentage
                             $matchPercentage = 0;
                        }
                    } else {
                        Session::setFlash('error', 'Failed to move uploaded file.');
                        $this->redirect('jobs/view/' . $id);
                    }
                } else {
                    Session::setFlash('error', 'Invalid file type. Please upload a PDF or DOC document.');
                    $this->redirect('jobs/view/' . $id);
                }
            }
            // Ensure a resume is present
            if (empty($resumePath)) {
                Session::setFlash('error', 'Please upload a resume to apply.');
                $this->redirect('jobs/view/' . $id);
            }

            // Determine skill set for matching
            // Use parsed resume skills if available, otherwise fallback to stored candidate skills
            $skillString = '';
            if (isset($resumeSkills) && is_array($resumeSkills) && count($resumeSkills) > 0) {
                $skillString = implode(',', $resumeSkills);
            } elseif (!empty($candidate->skills)) {
                $skillString = $candidate->skills;
            }

            // Calculate match percentage based on parsed skills
            $matchPercentage = MatchingHelper::calculateMatch(
                $job->requirements ?? '',
                $parsedSkills
            );
            // DEBUG: Log parsing details (remove after verification)
            error_log('Resume text length: ' . (isset($resumeText) ? strlen($resumeText) : 0));
            error_log('Parsed skills: ' . (isset($resumeSkills) ? implode(",", $resumeSkills) : ''));
            error_log('Calculated match percentage: ' . $matchPercentage);

            if ($applicationModel->apply($job->id, $candidate->candidate_id, $resumePath, $coverLetter, $matchPercentage)) {
                Session::setFlash('success', 'Your application for ' . htmlspecialchars($job->title) . ' has been submitted successfully!', 'alert-success');
                $this->redirect('candidate/dashboard');
            } else {
                Session::setFlash('error', 'Failed to submit application. Please try again.');
                $this->redirect('jobs/view/' . $id);
            }
        } else {
            $this->redirect('jobs/view/' . $id);
        }
    }
}
