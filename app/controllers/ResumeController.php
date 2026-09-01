<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Candidate;
use App\Models\Resume;
use App\Models\Skill;
use App\Helpers\ResumeParser;

/**
 * ResumeController handles resume upload and display for candidates.
 */
class ResumeController extends Controller {
    public function __construct() {
        // Ensure candidate is logged in
        Session::authorize('candidate');
    }

    /**
     * Show the upload form.
     */
    public function uploadForm() {
        $data = [
            'title' => 'Upload Resume',
            'activeTab' => 'resume'
        ];
        $this->view('candidate/resume', $data);
    }

    /**
     * Process uploaded resume.
     */
    public function upload() {
        // Get candidate ID via linked user
        $candidateModel = $this->model('Candidate');
        $candidate = $candidateModel->getByUserId(Session::get('user_id'));
        if (!$candidate) {
            Session::setFlash('error', 'Candidate record not found.', 'alert-danger');
            $this->redirect('candidate/profile');
        }
        $candidateId = $candidate->id;

        // Validate upload
        if (!isset($_FILES['resume']) || $_FILES['resume']['error'] !== UPLOAD_ERR_OK) {
            Session::setFlash('error', 'No file uploaded or upload error.', 'alert-danger');
            $this->redirect('candidate/resume');
        }
        $fileTmp = $_FILES['resume']['tmp_name'];
        $fileName = $_FILES['resume']['name'];
        $fileSize = $_FILES['resume']['size'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['pdf', 'doc', 'docx'];
        if (!in_array($fileExt, $allowedExts)) {
            Session::setFlash('error', 'Invalid file type. Allowed: PDF, DOC, DOCX.', 'alert-danger');
            $this->redirect('candidate/resume');
        }
        if ($fileSize > 5 * 1024 * 1024) { // 5MB
            Session::setFlash('error', 'File exceeds maximum size of 5MB.', 'alert-danger');
            $this->redirect('candidate/resume');
        }

        // Store file
        $uploadDir = APP_ROOT . '/../public/uploads/resumes/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $newFileName = 'resume_' . $candidateId . '_' . time() . '.' . $fileExt;
        $targetPath = $uploadDir . $newFileName;
        if (!move_uploaded_file($fileTmp, $targetPath)) {
            Session::setFlash('error', 'Failed to move uploaded file.', 'alert-danger');
            $this->redirect('candidate/resume');
        }
        $relativePath = 'uploads/resumes/' . $newFileName;

        // Parse resume using an instance of ResumeParser
        $parser = new ResumeParser();
        $parsed = $parser->parse($targetPath);
        $extractedText = $parsed[0] ?? '';
        $skills = $parsed[1] ?? [];
        $education = $parsed[2] ?? '';
        $experience = $parsed[3] ?? '';
        $email = $parsed[4] ?? '';
        $phone = $parsed[5] ?? '';
        $parsedJson = [
            'skills' => $skills,
            'education' => $education,
            'experience' => $experience,
            'email' => $email,
            'phone' => $phone
        ];

        // Save resume record
        $resumeModel = $this->model('Resume');
        $resumeId = $resumeModel->create($candidateId, $relativePath, $extractedText, $parsedJson);
        if (!$resumeId) {
            Session::setFlash('error', 'Failed to save resume information.', 'alert-danger');
            $this->redirect('candidate/resume');
        }

        // Store skills linking
        $skillModel = $this->model('Skill');
        foreach ($skills as $skillName) {
            $skillId = $skillModel->findOrCreate($skillName);
            if ($skillId) {
                $skillModel->attachToResume($resumeId, $skillId);
            }
        }

        Session::setFlash('success', 'Resume uploaded and processed successfully.', 'alert-success');
        $this->redirect('candidate/resume');
    }
}
?>
