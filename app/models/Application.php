<?php
namespace App\Models;

use App\Core\Database;

/**
 * Application Model Class
 * Handles database operations for candidate job applications.
 */
class Application {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Submit a new job application
     */
    public function apply($jobId, $candidateId, $resumePath, $coverLetter = '', $matchPercentage = null) {
        $this->db->query(
            "
                INSERT INTO applications (job_id, candidate_id, resume_path, cover_letter, status, match_percentage)
                VALUES (:job_id, :candidate_id, :resume_path, :cover_letter, 'pending', :match_percentage)
            "
        );
        $this->db->bind(':job_id', $jobId);
        $this->db->bind(':candidate_id', $candidateId);
        $this->db->bind(':resume_path', $resumePath);
        $this->db->bind(':cover_letter', trim($coverLetter));
        $this->db->bind(':match_percentage', $matchPercentage);

        return $this->db->execute();
    }

    /**
     * Check if a candidate has already applied to a specific job
     */
    public function hasApplied($jobId, $candidateId) {
        $this->db->query("SELECT id FROM applications WHERE job_id = :job_id AND candidate_id = :candidate_id");
        $this->db->bind(':job_id', $jobId);
        $this->db->bind(':candidate_id', $candidateId);
        $this->db->single();
        return $this->db->rowCount() > 0;
    }

    /**
     * Get all applications submitted by a specific candidate
     */
    public function getCandidateApplications($candidateId) {
        $this->db->query("
            SELECT a.*, j.title AS job_title, j.location, j.job_type, j.salary,
                   c.company_name, c.logo
            FROM applications a
            INNER JOIN jobs j ON a.job_id = j.id
            INNER JOIN companies c ON j.company_id = c.id
            WHERE a.candidate_id = :candidate_id
            ORDER BY a.applied_at DESC
        ");
        $this->db->bind(':candidate_id', $candidateId);
        return $this->db->resultSet();
    }

    /**
     * Get all applications submitted for an employer's company jobs
     */
    public function getEmployerApplications($companyId) {
        $this->db->query("
            SELECT a.*, j.title AS job_title, u.name AS candidate_name, u.email AS candidate_email,
                   cand.phone AS candidate_phone, cand.skills AS candidate_skills, cand.education AS candidate_education
            FROM applications a
            INNER JOIN jobs j ON a.job_id = j.id
            INNER JOIN candidates cand ON a.candidate_id = cand.id
            INNER JOIN users u ON cand.user_id = u.id
            WHERE j.company_id = :company_id
            ORDER BY a.applied_at DESC
        ");
        $this->db->bind(':company_id', $companyId);
        return $this->db->resultSet();
    }

    /**
     * Get all applications for system overview (Admin)
     */
    public function getAllApplications() {
        $this->db->query("
            SELECT a.*, j.title AS job_title, c.company_name, u.name AS candidate_name, u.email AS candidate_email
            FROM applications a
            INNER JOIN jobs j ON a.job_id = j.id
            INNER JOIN companies c ON j.company_id = c.id
            INNER JOIN candidates cand ON a.candidate_id = cand.id
            INNER JOIN users u ON cand.user_id = u.id
            ORDER BY a.applied_at DESC
        ");
        return $this->db->resultSet();
    }

    /**
     * Update application status (pending, under_review, accepted, rejected)
     */
    public function updateStatus($applicationId, $status) {
        $this->db->query("UPDATE applications SET status = :status WHERE id = :id");
        $this->db->bind(':id', $applicationId);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }
}
