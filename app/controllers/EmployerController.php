<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;

/**
 * EmployerController Class
 * Handles operations and dashboards restricted to Employer role.
 */
class EmployerController extends Controller {
    private $userModel;
    private $jobModel;
    private $categoryModel;
    private $applicationModel;

    public function __construct() {
        Session::authorize('employer');
        $this->userModel = $this->model('User');
        $this->jobModel = $this->model('Job');
        $this->categoryModel = $this->model('Category');
        $this->applicationModel = $this->model('Application');
    }

    /**
     * Get Employer Company Details
     */
    private function getEmployerCompany() {
        $employer = $this->userModel->getUserDetails(
            Session::get('user_id'),
            'employer'
        );

        if (!$employer || empty($employer->company_id)) {
            Session::setFlash(
                'error',
                'Company profile is incomplete. Please contact support.'
            );
            return null;
        }

        return $employer;
    }

    /**
     * Show Employer Dashboard
     */
    public function dashboard() {
        $employer = $this->getEmployerCompany();
        $jobs = [];
        $applications = [];

        if ($employer) {
            $jobs = $this->jobModel->getJobsByCompanyId(
                $employer->company_id
            );

            $applications = $this->applicationModel->getEmployerApplications(
                $employer->company_id
            );
        }

        $data = [
            'title' => 'Employer Workspace',
            'employer' => $employer,
            'jobs' => $jobs,
            'applications' => $applications,
            'activeTab' => 'dashboard'
        ];

        $this->view('employer/dashboard', $data);
    }

    /**
     * List all Job Postings for Employer
     */
    public function jobs() {
        $employer = $this->getEmployerCompany();
        $jobs = [];

        if ($employer) {
            $jobs = $this->jobModel->getJobsByCompanyId(
                $employer->company_id
            );
        }

        $data = [
            'title' => 'My Job Vacancies',
            'jobs' => $jobs,
            'activeTab' => 'jobs'
        ];

        $this->view('employer/jobs/index', $data);
    }

    /**
     * Show Create Job Form
     */
    public function showCreateJob() {
        $categories = $this->categoryModel->getAllCategories();

        $data = [
            'title' => 'Post a New Job Vacancy',
            'categories' => $categories,
            'activeTab' => 'jobs',
            'errors' => [],
            'old' => []
        ];

        $this->view('employer/jobs/create', $data);
    }

    /**
     * Action to Save New Job
     */
    public function createJob() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $employer = $this->getEmployerCompany();

            if (!$employer) {
                Session::setFlash(
                    'error',
                    'Company profile not found.'
                );
                $this->redirect('employer/dashboard');
                return;
            }

            $title = trim($_POST['title'] ?? '');
            $category_id = trim($_POST['category_id'] ?? '');
            $job_type = trim($_POST['job_type'] ?? '');
            $location = trim($_POST['location'] ?? '');
            $salary = trim($_POST['salary'] ?? '');
            $expiry_date = trim($_POST['expiry_date'] ?? '');

            /*
             * Required Experience
             */
            $required_experience = trim(
                $_POST['required_experience'] ?? '0'
            );

            $description = trim($_POST['description'] ?? '');
            $requirements = trim($_POST['requirements'] ?? '');

            $errors = [];

            /*
             * Job Title Validation
             */
            if (empty($title)) {
                $errors['title'] = 'Job title is required.';
            }

            /*
             * Category Validation
             */
            if (empty($category_id)) {
                $errors['category_id'] = 'Please select a job category.';
            }

            /*
             * Employment Type Validation
             */
            if (empty($job_type)) {
                $errors['job_type'] = 'Please select employment type.';
            }

            /*
             * Location Validation
             */
            if (empty($location)) {
                $errors['location'] = 'Job location is required.';
            }

            /*
             * Description Validation
             */
            if (empty($description)) {
                $errors['description'] = 'Job description is required.';
            }

            /*
             * Salary Validation
             */
            if ($salary === '') {
                $errors['salary'] = 'Salary is required.';
            } elseif (!is_numeric($salary)) {
                $errors['salary'] = 'Salary must be a valid number.';
            } elseif ((float)$salary < 0) {
                $errors['salary'] = 'Salary cannot be negative.';
            }

            /*
             * Expiry Date Validation
             */
            if (empty($expiry_date)) {
                $errors['expiry_date'] =
                    'Application expiry date is required.';
            } elseif (
                strtotime($expiry_date) <
                strtotime(date('Y-m-d'))
            ) {
                $errors['expiry_date'] =
                    'Expiry date cannot be in the past.';
            }

            /*
             * Required Experience Validation
             */
            if ($required_experience === '') {

                $errors['required_experience'] =
                    'Required experience is required.';

            } elseif (!is_numeric($required_experience)) {

                $errors['required_experience'] =
                    'Required experience must be a valid number.';

            } elseif ((float)$required_experience < 0) {

                $errors['required_experience'] =
                    'Required experience cannot be negative.';

            } elseif ((float)$required_experience > 50) {

                $errors['required_experience'] =
                    'Required experience cannot exceed 50 years.';
            }

            /*
             * Create Job
             */
            if (empty($errors)) {

                $jobData = [
                    'company_id' => $employer->company_id,
                    'category_id' => $category_id,
                    'title' => $title,
                    'job_type' => $job_type,
                    'location' => $location,
                    'salary' => $salary,
                    'expiry_date' => $expiry_date,

                    // Required Experience
                    'required_experience' =>
                        (float)$required_experience,

                    'description' => $description,
                    'requirements' => $requirements,
                    'status' => 'active'
                ];

                if ($this->jobModel->createJob($jobData)) {

                    Session::setFlash(
                        'success',
                        'Job vacancy posted successfully!',
                        'alert-success'
                    );

                    $this->redirect('employer/jobs');
                    return;

                } else {

                    Session::setFlash(
                        'error',
                        'Something went wrong. Failed to create job.'
                    );
                }
            }

            /*
             * Reload Create Form With Errors
             */
            $categories =
                $this->categoryModel->getAllCategories();

            $data = [
                'title' => 'Post a New Job Vacancy',
                'categories' => $categories,
                'errors' => $errors,
                'old' => $_POST,
                'activeTab' => 'jobs'
            ];

            $this->view(
                'employer/jobs/create',
                $data
            );

        } else {

            $this->redirect('employer/jobs/create');
        }
    }

    /**
     * Show Edit Job Form
     */
    public function showEditJob($id) {

        $employer = $this->getEmployerCompany();
        $job = $this->jobModel->getJobById($id);

        if (
            !$employer ||
            !$job ||
            $job->company_id != $employer->company_id
        ) {

            Session::setFlash(
                'error',
                'Job vacancy not found or access unauthorized.'
            );

            $this->redirect('employer/jobs');
            return;
        }

        $categories =
            $this->categoryModel->getAllCategories();

        $data = [
            'title' => 'Edit Job Vacancy - ' . $job->title,
            'job' => $job,
            'categories' => $categories,
            'errors' => [],
            'activeTab' => 'jobs'
        ];

        $this->view(
            'employer/jobs/edit',
            $data
        );
    }

    /**
     * Action to Update Job
     */
    public function editJob($id) {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $employer = $this->getEmployerCompany();
            $job = $this->jobModel->getJobById($id);

            /*
             * Authorization Check
             */
            if (
                !$employer ||
                !$job ||
                $job->company_id != $employer->company_id
            ) {

                Session::setFlash(
                    'error',
                    'Job vacancy not found or access unauthorized.'
                );

                $this->redirect('employer/jobs');
                return;
            }

            /*
             * Get Form Data
             */
            $title = trim($_POST['title'] ?? '');
            $category_id = trim($_POST['category_id'] ?? '');
            $job_type = trim($_POST['job_type'] ?? '');
            $location = trim($_POST['location'] ?? '');
            $salary = trim($_POST['salary'] ?? '');
            $status = trim($_POST['status'] ?? '');
            $expiry_date = trim($_POST['expiry_date'] ?? '');

            /*
             * Required Experience
             */
            $required_experience = trim(
                $_POST['required_experience'] ?? '0'
            );

            $description = trim($_POST['description'] ?? '');
            $requirements = trim($_POST['requirements'] ?? '');

            $errors = [];

            /*
             * Job Title Validation
             */
            if (empty($title)) {
                $errors['title'] = 'Job title is required.';
            }

            /*
             * Category Validation
             */
            if (empty($category_id)) {
                $errors['category_id'] =
                    'Please select a job category.';
            }

            /*
             * Employment Type Validation
             */
            if (empty($job_type)) {
                $errors['job_type'] =
                    'Job type is required.';
            }

            /*
             * Location Validation
             */
            if (empty($location)) {
                $errors['location'] =
                    'Job location is required.';
            }

            /*
             * Description Validation
             */
            if (empty($description)) {
                $errors['description'] =
                    'Job description is required.';
            }

            /*
             * Salary Validation
             */
            if (
                $salary !== '' &&
                (
                    !is_numeric($salary) ||
                    (float)$salary < 0
                )
            ) {

                $errors['salary'] =
                    'Salary must be a valid non-negative number.';
            }

            /*
             * Required Experience Validation
             */
            if ($required_experience === '') {

                $errors['required_experience'] =
                    'Required experience is required.';

            } elseif (!is_numeric($required_experience)) {

                $errors['required_experience'] =
                    'Required experience must be a valid number.';

            } elseif ((float)$required_experience < 0) {

                $errors['required_experience'] =
                    'Required experience cannot be negative.';

            } elseif ((float)$required_experience > 50) {

                $errors['required_experience'] =
                    'Required experience cannot exceed 50 years.';
            }

            /*
             * Expiry Date Validation
             */
            if (
                !empty($expiry_date) &&
                strtotime($expiry_date) <
                strtotime(date('Y-m-d'))
            ) {

                $errors['expiry_date'] =
                    'Expiry date cannot be in the past.';
            }

            /*
             * Update Job
             */
            if (empty($errors)) {

                $jobData = [
                    'category_id' => $category_id,
                    'title' => $title,
                    'job_type' => $job_type,
                    'location' => $location,
                    'salary' => $salary,
                    'status' => $status,
                    'expiry_date' => $expiry_date,

                    // Required Experience
                    'required_experience' =>
                        (float)$required_experience,

                    'description' => $description,
                    'requirements' => $requirements
                ];

                if (
                    $this->jobModel->updateJob(
                        $id,
                        $jobData
                    )
                ) {

                    Session::setFlash(
                        'success',
                        'Job vacancy updated successfully!',
                        'alert-success'
                    );

                    $this->redirect('employer/jobs');
                    return;

                } else {

                    Session::setFlash(
                        'error',
                        'Failed to update job vacancy.'
                    );
                }
            }

            /*
             * Reload Edit Form With Errors
             */
            $categories =
                $this->categoryModel->getAllCategories();

            $data = [
                'title' => 'Edit Job Vacancy',
                'job' => (object)array_merge(
                    (array)$job,
                    $_POST
                ),
                'categories' => $categories,
                'errors' => $errors,
                'activeTab' => 'jobs'
            ];

            $this->view(
                'employer/jobs/edit',
                $data
            );

        } else {

            $this->redirect(
                'employer/jobs/edit/' . $id
            );
        }
    }

    /**
     * Delete Employer Job
     */
    public function deleteJob($id) {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $employer = $this->getEmployerCompany();
            $job = $this->jobModel->getJobById($id);

            if (
                $employer &&
                $job &&
                $job->company_id == $employer->company_id
            ) {

                if ($this->jobModel->deleteJob($id)) {

                    Session::setFlash(
                        'success',
                        'Job vacancy deleted successfully.',
                        'alert-success'
                    );

                } else {

                    Session::setFlash(
                        'error',
                        'Failed to delete job vacancy.'
                    );
                }

            } else {

                Session::setFlash(
                    'error',
                    'Unauthorized operation or job not found.'
                );
            }
        }

        $this->redirect('employer/jobs');
    }

    /**
     * Employer Candidate Applications Review
     */
    public function applications() {

        $employer = $this->getEmployerCompany();
        $applications = [];

        if ($employer) {

            $applications =
                $this->applicationModel
                    ->getEmployerApplications(
                        $employer->company_id
                    );
        }

        $data = [
            'title' => 'Candidate Applications',
            'applications' => $applications,
            'activeTab' => 'applications'
        ];

        $this->view(
            'employer/applications/index',
            $data
        );
    }

    /**
     * Update Candidate Application Status
     * pending, under_review, accepted, rejected
     */
    public function updateApplicationStatus($id) {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $status = isset($_POST['status'])
                ? trim($_POST['status'])
                : 'pending';

            $validStatuses = [
                'pending',
                'under_review',
                'shortlisted',
                'rejected'
            ];

            if (in_array($status, $validStatuses)) {

                if (
                    $this->applicationModel
                        ->updateStatus($id, $status)
                ) {

                    Session::setFlash(
                        'success',
                        'Application status updated to ' .
                        str_replace('_', ' ', $status) . '.',
                        'alert-success'
                    );

                } else {

                    Session::setFlash(
                        'error',
                        'Failed to update application status.'
                    );
                }
            }
        }

        $this->redirect('employer/applications');
    }
}