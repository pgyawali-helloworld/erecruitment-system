<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;

/**
 * CandidateController Class
 * Handles workflows and dashboards restricted to Candidate / Job Seeker role.
 */
class CandidateController extends Controller {
    private $userModel;
    private $applicationModel;

    public function __construct() {
        Session::authorize('candidate');
        $this->userModel = $this->model('User');
        $this->applicationModel = $this->model('Application');
    }

    /**
     * Get Candidate Details
     */
    private function getCandidateData() {
        $candidate = $this->userModel->getUserDetails(Session::get('user_id'), 'candidate');
        return $candidate;
    }

    /**
     * Show Candidate Dashboard
     */
    public function dashboard() {
        $candidate = $this->getCandidateData();
        $applications = [];

        if ($candidate && isset($candidate->candidate_id)) {
            $applications = $this->applicationModel->getCandidateApplications($candidate->candidate_id);
        }

        $data = [
            'title' => 'Candidate Workspace',
            'candidate' => $candidate,
            'applications' => $applications,
            'activeTab' => 'dashboard'
        ];

        $this->view('candidate/dashboard', $data);
    }

    /**
     * View Applied Jobs
     */
    public function applications() {
        $candidate = $this->getCandidateData();
        $applications = [];

        if ($candidate && isset($candidate->candidate_id)) {
            $applications = $this->applicationModel->getCandidateApplications($candidate->candidate_id);
        }

        $data = [
            'title' => 'My Applied Vacancies',
            'candidate' => $candidate,
            'applications' => $applications,
            'activeTab' => 'applications'
        ];

        $this->view('candidate/applications', $data);
    }

    /**
     * Show & Edit Candidate Profile
     */
    public function profile() {
        $candidate = $this->getCandidateData();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $phone = trim($_POST['phone']);
            $skills = trim($_POST['skills']);
            $education = trim($_POST['education']);
            $experience = trim($_POST['experience']);
            $bio = trim($_POST['bio']);

            $resumePath = $candidate ? $candidate->resume_path : '';

            // File upload for resume
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
                    }
                }
            }

            // Update candidate profile database
            $db = new \App\Core\Database();
            $db->query("
                UPDATE candidates 
                SET phone = :phone, skills = :skills, education = :education, experience = :experience, bio = :bio, resume_path = :resume_path
                WHERE user_id = :user_id
            ");
            $db->bind(':phone', $phone);
            $db->bind(':skills', $skills);
            $db->bind(':education', $education);
            $db->bind(':experience', $experience);
            $db->bind(':bio', $bio);
            $db->bind(':resume_path', $resumePath);
            $db->bind(':user_id', Session::get('user_id'));

            if ($db->execute()) {
                // Also update user name if provided
                if (!empty($_POST['name'])) {
                    $db->query("UPDATE users SET name = :name WHERE id = :user_id");
                    $db->bind(':name', trim($_POST['name']));
                    $db->bind(':user_id', Session::get('user_id'));
                    $db->execute();
                    Session::set('user_name', trim($_POST['name']));
                }

                Session::setFlash('success', 'Profile details updated successfully.', 'alert-success');
                $this->redirect('candidate/profile');
            } else {
                Session::setFlash('error', 'Failed to update profile.');
            }
        }

        $data = [
            'title' => 'My Candidate Profile',
            'candidate' => $candidate,
            'activeTab' => 'profile'
        ];

        $this->view('candidate/profile', $data);
    }

    /**
     * Show Candidate Resume Dashboard
     */
    public function resume() {
        $candidate = $this->getCandidateData();
        $resume = null;
        if ($candidate && isset($candidate->candidate_id)) {
            $resumeModel = $this->model('Resume');
            $resume = $resumeModel->getByCandidate($candidate->candidate_id);
        }
        $data = [
            'title' => 'My Resume',
            'candidate' => $candidate,
            'resume' => $resume,
            'activeTab' => 'resume'
        ];
        // Use the combined upload/dashboard view (resume_upload.php) which already handles display and upload form
        $this->view('candidate/resume', $data);
    }
}
