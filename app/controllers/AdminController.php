<?php
namespace App\Controllers;

use App\Core\Controller; 
use App\Core\Session;

/**
 * AdminController Class
 * Manages admin actions and restricts access to authorized administrators.
 */
class AdminController extends Controller {
    
    public function __construct() {
        // Enforce admin-only access
        Session::authorize('admin');
    }

    /**
     * Show Admin Dashboard
     */
    public function dashboard() {
        $adminModel = $this->model('Admin');
        
        $stats = $adminModel->getDashboardStats();
        $recentUsers = $adminModel->getRecentUsers(5);
        $recentJobs = $adminModel->getRecentJobs(5);
        $recentActivities = $adminModel->getRecentActivities(8);
        
        $data = [
            'title' => 'Administrator Control Dashboard',
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'recentJobs' => $recentJobs,
            'recentActivities' => $recentActivities
        ];
        
        $this->view('admin/dashboard', $data);
    }

    /**
     * Show Reports Page
     */
    public function reports() {
        $adminModel = $this->model('Admin');
        
        $stats = $adminModel->getDashboardStats();
        $jobsByCategory = $adminModel->getJobsByCategory();
        $recentActivities = $adminModel->getRecentActivities(20);
        
        $data = [
            'title' => 'System Reports & Analytics',
            'stats' => $stats,
            'jobsByCategory' => $jobsByCategory,
            'recentActivities' => $recentActivities
        ];
        
        $this->view('admin/reports', $data);
    }

    /**
     * List Categories
     */
    public function categories() {
        $categoryModel = $this->model('Category');
        
        $categories = $categoryModel->getAllCategories();
        
        $data = [
            'title' => 'Manage Categories',
            'categories' => $categories,
            'activeTab' => 'categories'
        ];
        
        $this->view('admin/categories/index', $data);
    }

    /**
     * Add Category Action
     */
    public function addCategory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoryModel = $this->model('Category');
            
            $name = trim($_POST['name']);
            $icon = trim($_POST['icon'] ?: 'fa-briefcase');
            
            // Server side validation
            $errors = [];
            if (empty($name)) {
                $errors['name'] = 'Category name is required.';
            } elseif (strlen($name) < 3) {
                $errors['name'] = 'Category name must be at least 3 characters.';
            } elseif ($categoryModel->validateDuplicateName($name)) {
                $errors['name'] = 'Category name already exists.';
            }
            
            if (empty($errors)) {
                if ($categoryModel->addCategory(['name' => $name, 'icon' => $icon])) {
                    Session::setFlash('success', 'Category added successfully.', 'alert-success');
                } else {
                    Session::setFlash('error', 'Something went wrong. Please try again.');
                }
                $this->redirect('admin/categories');
            } else {
                // If errors, pass them back
                $categories = $categoryModel->getAllCategories();
                $data = [
                    'title' => 'Manage Categories',
                    'categories' => $categories,
                    'errors' => $errors,
                    'old' => $_POST,
                    'activeTab' => 'categories'
                ];
                $this->view('admin/categories/index', $data);
            }
        } else {
            $this->redirect('admin/categories');
        }
    }

    /**
     * Show Edit Category Page
     */
    public function showEditCategory($id) {
        $categoryModel = $this->model('Category');
        $category = $categoryModel->getCategoryById($id);
        
        if (!$category) {
            Session::setFlash('error', 'Category not found.');
            $this->redirect('admin/categories');
        }
        
        $data = [
            'title' => 'Edit Category',
            'category' => $category,
            'activeTab' => 'categories'
        ];
        
        $this->view('admin/categories/edit', $data);
    }

    /**
     * Edit Category Action
     */
    public function editCategory($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoryModel = $this->model('Category');
            $category = $categoryModel->getCategoryById($id);
            
            if (!$category) {
                Session::setFlash('error', 'Category not found.');
                $this->redirect('admin/categories');
            }
            
            $name = trim($_POST['name']);
            $icon = trim($_POST['icon'] ?: 'fa-briefcase');
            
            $errors = [];
            if (empty($name)) {
                $errors['name'] = 'Category name is required.';
            } elseif (strlen($name) < 3) {
                $errors['name'] = 'Category name must be at least 3 characters.';
            } elseif ($categoryModel->validateDuplicateName($name, $id)) {
                $errors['name'] = 'Category name already exists.';
            }
            
            if (empty($errors)) {
                if ($categoryModel->updateCategory($id, ['name' => $name, 'icon' => $icon])) {
                    Session::setFlash('success', 'Category updated successfully.', 'alert-success');
                    $this->redirect('admin/categories');
                } else {
                    Session::setFlash('error', 'Something went wrong. Please try again.');
                }
            }
            
            $data = [
                'title' => 'Edit Category',
                'category' => (object)['id' => $id, 'name' => $name, 'icon' => $icon],
                'errors' => $errors,
                'activeTab' => 'categories'
            ];
            $this->view('admin/categories/edit', $data);
        } else {
            $this->redirect('admin/categories/edit/' . $id);
        }
    }

    /**
     * Delete Category Action
     */
    public function deleteCategory($id) {
        $categoryModel = $this->model('Category');
        $category = $categoryModel->getCategoryById($id);
        
        if (!$category) {
            Session::setFlash('error', 'Category not found.');
        } else {
            try {
                if ($categoryModel->deleteCategory($id)) {
                    Session::setFlash('success', 'Category deleted successfully.', 'alert-success');
                } else {
                    Session::setFlash('error', 'Cannot delete category. Jobs might be associated with it.');
                }
            } catch (\PDOException $e) {
                Session::setFlash('error', 'Cannot delete category. Jobs are currently assigned to this category.');
            }
        }
        $this->redirect('admin/categories');
    }

    /**
     * View all Employers
     */
    public function employers() {
        $userModel = $this->model('User');
        $employers = $userModel->getUsersByRole('employer');
        
        $data = [
            'title' => 'Manage Employers',
            'employers' => $employers,
            'activeTab' => 'employers'
        ];
        
        $this->view('admin/employers/index', $data);
    }

    /**
     * View Employer Details
     */
    public function employerDetails($id) {
        $userModel = $this->model('User');
        $employer = $userModel->getUserDetails($id, 'employer');
        
        if (!$employer) {
            Session::setFlash('error', 'Employer not found.');
            $this->redirect('admin/employers');
        }
        
        $data = [
            'title' => 'Employer Details - ' . $employer->company_name,
            'employer' => $employer,
            'activeTab' => 'employers'
        ];
        
        $this->view('admin/employers/view', $data);
    }

    /**
     * Toggle Employer Status (Activate/Deactivate)
     */
    public function toggleEmployerStatus($id) {
        $userModel = $this->model('User');
        $employer = $userModel->getUserDetails($id, 'employer');
        
        if (!$employer) {
            Session::setFlash('error', 'Employer not found.');
        } else {
            $newStatus = ($employer->status === 'active') ? 'inactive' : 'active';
            if ($userModel->toggleUserStatus($id, $newStatus)) {
                $statusMsg = ($newStatus === 'active') ? 'activated' : 'deactivated';
                Session::setFlash('success', "Employer '{$employer->company_name}' has been successfully {$statusMsg}.", 'alert-success');
            } else {
                Session::setFlash('error', 'Failed to toggle employer status.');
            }
        }
        $this->redirect('admin/employers');
    }

    /**
     * Delete Employer Action
     */
    public function deleteEmployer($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = $this->model('User');
            $employer = $userModel->getUserDetails($id, 'employer');
            
            if (!$employer) {
                Session::setFlash('error', 'Employer not found.');
            } else {
                if ($userModel->deleteUser($id)) {
                    Session::setFlash('success', "Employer '{$employer->company_name}' and all associated postings have been deleted.", 'alert-success');
                } else {
                    Session::setFlash('error', 'Failed to delete employer.');
                }
            }
        }
        $this->redirect('admin/employers');
    }

    /**
     * View all Job Seekers (Candidates)
     */
    public function candidates() {
        $userModel = $this->model('User');
        $candidates = $userModel->getUsersByRole('candidate');
        
        $data = [
            'title' => 'Manage Job Seekers',
            'candidates' => $candidates,
            'activeTab' => 'candidates'
        ];
        
        $this->view('admin/candidates/index', $data);
    }

    /**
     * View Job Seeker Details
     */
    public function candidateDetails($id) {
        $userModel = $this->model('User');
        $candidate = $userModel->getUserDetails($id, 'candidate');
        
        if (!$candidate) {
            Session::setFlash('error', 'Job Seeker not found.');
            $this->redirect('admin/candidates');
        }
        
        $data = [
            'title' => 'Job Seeker Details - ' . $candidate->name,
            'candidate' => $candidate,
            'activeTab' => 'candidates'
        ];
        
        $this->view('admin/candidates/view', $data);
    }

    /**
     * Toggle Candidate Status (Activate/Deactivate)
     */
    public function toggleCandidateStatus($id) {
        $userModel = $this->model('User');
        $candidate = $userModel->getUserDetails($id, 'candidate');
        
        if (!$candidate) {
            Session::setFlash('error', 'Job Seeker not found.');
        } else {
            $newStatus = ($candidate->status === 'active') ? 'inactive' : 'active';
            if ($userModel->toggleUserStatus($id, $newStatus)) {
                $statusMsg = ($newStatus === 'active') ? 'activated' : 'deactivated';
                Session::setFlash('success', "Job Seeker '{$candidate->name}' has been successfully {$statusMsg}.", 'alert-success');
            } else {
                Session::setFlash('error', 'Failed to toggle job seeker status.');
            }
        }
        $this->redirect('admin/candidates');
    }

    /**
     * Delete Candidate Action
     */
    public function deleteCandidate($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = $this->model('User');
            $candidate = $userModel->getUserDetails($id, 'candidate');
            
            if (!$candidate) {
                Session::setFlash('error', 'Job Seeker not found.');
            } else {
                if ($userModel->deleteUser($id)) {
                    Session::setFlash('success', "Job Seeker '{$candidate->name}' and all associated applications have been deleted.", 'alert-success');
                } else {
                    Session::setFlash('error', 'Failed to delete job seeker.');
                }
            }
        }
        $this->redirect('admin/candidates');
    }

    /**
     * View all Jobs (Admin)
     */
    public function jobs() {
        $jobModel = $this->model('Job');
        $jobs = $jobModel->getAllJobs(['status' => '']); // Fetch all statuses
        
        $data = [
            'title' => 'Manage Vacancies & Postings',
            'jobs' => $jobs,
            'activeTab' => 'jobs'
        ];
        
        $this->view('admin/jobs/index', $data);
    }

    /**
     * Toggle Job Status (active, inactive, closed)
     */
    public function toggleJobStatus($id) {
        $jobModel = $this->model('Job');
        $job = $jobModel->getJobById($id);

        if (!$job) {
            Session::setFlash('error', 'Job vacancy not found.');
        } else {
            $newStatus = ($job->status === 'active') ? 'inactive' : 'active';
            if ($jobModel->toggleJobStatus($id, $newStatus)) {
                Session::setFlash('success', "Job status for '{$job->title}' updated to {$newStatus}.", 'alert-success');
            } else {
                Session::setFlash('error', 'Failed to update job status.');
            }
        }
        $this->redirect('admin/jobs');
    }

    /**
     * Delete Job Action (Admin)
     */
    public function deleteJob($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $jobModel = $this->model('Job');
            $job = $jobModel->getJobById($id);

            if (!$job) {
                Session::setFlash('error', 'Job vacancy not found.');
            } else {
                if ($jobModel->deleteJob($id)) {
                    Session::setFlash('success', "Job posting '{$job->title}' has been deleted.", 'alert-success');
                } else {
                    Session::setFlash('error', 'Failed to delete job posting.');
                }
            }
        }
        $this->redirect('admin/jobs');
    }

    /**
     * View all Applications (Admin)
     */
    public function applications() {
        $applicationModel = $this->model('Application');
        $applications = $applicationModel->getAllApplications();

        $data = [
            'title' => 'Manage Applications Overview',
            'applications' => $applications,
            'activeTab' => 'applications'
        ];

        $this->view('admin/applications/index', $data);
    }
}

