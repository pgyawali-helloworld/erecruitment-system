<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;

/**
 * EmployerController Class
 * Handles operations and dashboards restricted to Employer role.
 */
class EmployerController extends Controller
{
    private $userModel;
    private $jobModel;
    private $categoryModel;
    private $applicationModel;

    public function __construct()
    {
        Session::authorize('employer');

        $this->userModel = $this->model('User');
        $this->jobModel = $this->model('Job');
        $this->categoryModel = $this->model('Category');
        $this->applicationModel = $this->model('Application');
    }

    /**
     * Get Employer Company Details
     */
    private function getEmployerCompany()
    {
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
    public function dashboard()
    {
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
    public function jobs()
    {
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
    public function showCreateJob()
    {
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
    public function createJob()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('employer/jobs/create');
            return;
        }

        $employer = $this->getEmployerCompany();

        if (!$employer) {
            Session::setFlash('error', 'Company profile not found.');
            $this->redirect('employer/dashboard');
            return;
        }

        /*
         * Get POST data safely
         */
        $title = trim($_POST['title'] ?? '');
        $category_id = trim($_POST['category_id'] ?? '');
        $job_type = trim($_POST['job_type'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $salary = trim($_POST['salary'] ?? '');
        $expiry_date = trim($_POST['expiry_date'] ?? '');
        $required_experience = trim($_POST['required_experience'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $requirements = trim($_POST['requirements'] ?? '');

        $errors = [];

        /*
         * Job Title Validation
         */
        if (empty($title)) {
            $errors['title'] = 'Job title is required.';
        } elseif (strlen($title) < 5) {
            $errors['title'] = 'Job title must be at least 5 characters.';
        } elseif (strlen($title) > 100) {
            $errors['title'] = 'Job title cannot exceed 100 characters.';
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
         * Salary Validation
         */
        if ($salary === '') {
            $errors['salary'] = 'Salary is required.';
        } elseif (!is_numeric($salary)) {
            $errors['salary'] = 'Salary must be a valid number.';
        } elseif ((float) $salary < 0) {
            $errors['salary'] = 'Salary cannot be negative.';
        }

        /*
         * Expiry Date Validation
         */
        if (empty($expiry_date)) {
            $errors['expiry_date'] = 'Application expiry date is required.';
        } elseif (
            strtotime($expiry_date) === false ||
            strtotime($expiry_date) < strtotime(date('Y-m-d'))
        ) {
            $errors['expiry_date'] = 'Expiry date cannot be in the past.';
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
        } elseif ((float) $required_experience < 0) {
            $errors['required_experience'] =
                'Required experience cannot be negative.';
        } elseif ((float) $required_experience > 50) {
            $errors['required_experience'] =
                'Required experience cannot exceed 50 years.';
        }

        /*
         * Description Validation
         */
        if (empty($description)) {
            $errors['description'] = 'Job description is required.';
        } elseif (strlen($description) < 20) {
            $errors['description'] =
                'Job description must be at least 20 characters.';
        }

        /*
         * Save Job
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
                'required_experience' => (float) $required_experience,
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
         * Reload categories and show form with errors
         */
        $categories = $this->categoryModel->getAllCategories();

        $data = [
            'title' => 'Post a New Job Vacancy',
            'categories' => $categories,
            'errors' => $errors,
            'old' => $_POST,
            'activeTab' => 'jobs'
        ];

        $this->view('employer/jobs/create', $data);
    }

    /**
     * Show Edit Job Form
     */
    public function showEditJob($id)
    {
        $employer = $this->getEmployerCompany();

        if (!$employer) {
            $this->redirect('employer/dashboard');
            return;
        }

        $job = $this->jobModel->getJobById($id);

        /*
         * Verify job belongs to logged-in employer's company
         */
        if (!$job || $job->company_id != $employer->company_id) {

            Session::setFlash(
                'error',
                'Job vacancy not found or access unauthorized.'
            );

            $this->redirect('employer/jobs');
            return;
        }

        $categories = $this->categoryModel->getAllCategories();

        $data = [
            'title' => 'Edit Job Vacancy - ' . $job->title,
            'job' => $job,
            'categories' => $categories,
            'errors' => [],
            'activeTab' => 'jobs'
        ];

        $this->view('employer/jobs/edit', $data);
    }

    /**
     * Action to Update Job
     */
    public function editJob($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('employer/jobs/edit/' . $id);
            return;
        }

        $employer = $this->getEmployerCompany();

        if (!$employer) {
            $this->redirect('employer/dashboard');
            return;
        }

        $job = $this->jobModel->getJobById($id);

        /*
         * Security Check:
         * Employer can only edit their own company's jobs.
         */
        if (!$job || $job->company_id != $employer->company_id) {

            Session::setFlash(
                'error',
                'Job vacancy not found or access unauthorized.'
            );

            $this->redirect('employer/jobs');
            return;
        }

        /*
         * Get POST data safely
         */
        $title = trim($_POST['title'] ?? '');
        $category_id = trim($_POST['category_id'] ?? '');
        $job_type = trim($_POST['job_type'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $salary = trim($_POST['salary'] ?? '');
        $status = trim($_POST['status'] ?? '');
        $expiry_date = trim($_POST['expiry_date'] ?? '');
        $required_experience = trim($_POST['required_experience'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $requirements = trim($_POST['requirements'] ?? '');

        $errors = [];

        /*
         * Job Title Validation
         */
        if (empty($title)) {
            $errors['title'] = 'Job title is required.';
        } elseif (strlen($title) < 5) {
            $errors['title'] = 'Job title must be at least 5 characters.';
        } elseif (strlen($title) > 100) {
            $errors['title'] = 'Job title cannot exceed 100 characters.';
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
         * Salary Validation
         */
        if ($salary === '') {

            $errors['salary'] = 'Salary is required.';

        } elseif (!is_numeric($salary)) {

            $errors['salary'] = 'Salary must be a valid number.';

        } elseif ((float) $salary < 0) {

            $errors['salary'] = 'Salary cannot be negative.';
        }

        /*
         * Status Validation
         */
        $validStatuses = [
            'active',
            'inactive',
            'closed'
        ];

        if (!in_array($status, $validStatuses, true)) {
            $errors['status'] = 'Invalid job status selected.';
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

        } elseif ((float) $required_experience < 0) {

            $errors['required_experience'] =
                'Required experience cannot be negative.';

        } elseif ((float) $required_experience > 50) {

            $errors['required_experience'] =
                'Required experience cannot exceed 50 years.';
        }

        /*
         * Expiry Date Validation
         */
        if (empty($expiry_date)) {

            $errors['expiry_date'] =
                'Application expiry date is required.';

        } elseif (
            strtotime($expiry_date) === false ||
            strtotime($expiry_date) < strtotime(date('Y-m-d'))
        ) {

            $errors['expiry_date'] =
                'Expiry date cannot be in the past.';
        }

        /*
         * Description Validation
         */
        if (empty($description)) {

            $errors['description'] =
                'Job description is required.';

        } elseif (strlen($description) < 20) {

            $errors['description'] =
                'Job description must be at least 20 characters.';
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
                'required_experience' => (float) $required_experience,
                'description' => $description,
                'requirements' => $requirements
            ];

            if ($this->jobModel->updateJob($id, $jobData)) {

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
         * Reload form with validation errors
         */
        $categories = $this->categoryModel->getAllCategories();

        /*
         * Merge database job data with submitted POST data
         * so the user does not lose entered values.
         */
        $updatedJob = (object) array_merge(
            (array) $job,
            $_POST
        );

        $data = [
            'title' => 'Edit Job Vacancy',
            'job' => $updatedJob,
            'categories' => $categories,
            'errors' => $errors,
            'activeTab' => 'jobs'
        ];

        $this->view('employer/jobs/edit', $data);
    }

    /**
     * Delete Employer Job
     */
    public function deleteJob($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('employer/jobs');
            return;
        }

        $employer = $this->getEmployerCompany();

        if (!$employer) {
            $this->redirect('employer/dashboard');
            return;
        }

        $job = $this->jobModel->getJobById($id);

        /*
         * Security Check:
         * Employer can only delete their own company's jobs.
         */
        if ($job && $job->company_id == $employer->company_id) {

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

        $this->redirect('employer/jobs');
    }

    /**
     * Employer Candidate Applications Review
     */
    public function applications()
    {
        $employer = $this->getEmployerCompany();

        $applications = [];

        if ($employer) {
            $applications =
                $this->applicationModel->getEmployerApplications(
                    $employer->company_id
                );
        }

        $data = [
            'title' => 'Candidate Applications',
            'applications' => $applications,
            'activeTab' => 'applications'
        ];

        $this->view('employer/applications/index', $data);
    }

    /**
     * Update Candidate Application Status
     *
     * Valid statuses:
     * pending
     * under_review
     * accepted
     * rejected
     */
    public function updateApplicationStatus($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $status = trim($_POST['status'] ?? 'pending');

            $validStatuses = [
                'pending',
                'under_review',
                'shortlisted',
                'rejected'
            ];

            if (in_array($status, $validStatuses, true)) {

                if ($this->applicationModel->updateStatus($id, $status)) {

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
            } else {

                Session::setFlash(
                    'error',
                    'Invalid application status.'
                );
            }
        }

        $this->redirect('employer/applications');
    }
}

