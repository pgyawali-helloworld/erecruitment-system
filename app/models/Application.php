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
     * Submit a new job application.
     *
     * Status is received from JobController based on
     * the final match percentage.
     */
    public function apply(
        $jobId,
        $candidateId,
        $resumePath,
        $coverLetter = '',
        $matchPercentage = null,
        $status = 'pending'
    ) {

        /*
         * Make sure match percentage is numeric.
         */
        if ($matchPercentage === null || !is_numeric($matchPercentage)) {
            $matchPercentage = 0;
        }

        $matchPercentage = (float) $matchPercentage;

        /*
         * Keep match percentage between 0 and 100.
         */
        $matchPercentage = max(
            0,
            min(
                100,
                $matchPercentage
            )
        );

       /*
     * Automatically determine application status.
     */
    if ($matchPercentage >= 100) {

        $status = 'shortlisted';

    } elseif ($matchPercentage < 50) {

        $status = 'rejected';

    } else {

        $status = 'pending';
    }


        /*
         * Insert application.
         */
        $this->db->query("
            INSERT INTO applications (
                job_id,
                candidate_id,
                resume_path,
                cover_letter,
                status,
                match_percentage
            )
            VALUES (
                :job_id,
                :candidate_id,
                :resume_path,
                :cover_letter,
                :status,
                :match_percentage
            )
        ");

        $this->db->bind(
            ':job_id',
            $jobId
        );

        $this->db->bind(
            ':candidate_id',
            $candidateId
        );

        $this->db->bind(
            ':resume_path',
            $resumePath
        );

        $this->db->bind(
            ':cover_letter',
            trim($coverLetter)
        );

        $this->db->bind(
            ':status',
            $status
        );

        $this->db->bind(
            ':match_percentage',
            $matchPercentage
        );

        return $this->db->execute();
    }

    /**
     * Check if a candidate has already applied
     * to a specific job.
     */
    public function hasApplied($jobId, $candidateId) {

        $this->db->query("
            SELECT id
            FROM applications
            WHERE job_id = :job_id
            AND candidate_id = :candidate_id
        ");

        $this->db->bind(
            ':job_id',
            $jobId
        );

        $this->db->bind(
            ':candidate_id',
            $candidateId
        );

        $this->db->single();

        return $this->db->rowCount() > 0;
    }

    /**
     * Get all applications submitted by a candidate.
     */
    public function getCandidateApplications($candidateId) {

        $this->db->query("
            SELECT
                a.*,
                j.title AS job_title,
                j.location,
                j.job_type,
                j.salary,
                c.company_name,
                c.logo
            FROM applications a
            INNER JOIN jobs j
                ON a.job_id = j.id
            INNER JOIN companies c
                ON j.company_id = c.id
            WHERE a.candidate_id = :candidate_id
            ORDER BY a.applied_at DESC
        ");

        $this->db->bind(
            ':candidate_id',
            $candidateId
        );

        return $this->db->resultSet();
    }

    /**
     * Get all applications submitted for
     * an employer's company jobs.
     */
    public function getEmployerApplications($companyId) {

        $this->db->query("
            SELECT
                a.*,

                j.title AS job_title,
                j.required_experience,

                u.name AS candidate_name,
                u.email AS candidate_email,

                cand.phone AS candidate_phone,
                cand.skills AS candidate_skills,
                cand.education AS candidate_education,
                cand.experience AS profile_experience,

                r.parsed_json AS resume_parsed_json

            FROM applications a

            INNER JOIN jobs j
                ON a.job_id = j.id

            INNER JOIN candidates cand
                ON a.candidate_id = cand.id

            INNER JOIN users u
                ON cand.user_id = u.id

            LEFT JOIN resumes r
                ON r.candidate_id = cand.id
                AND r.file_path = a.resume_path

            WHERE j.company_id = :company_id

            ORDER BY a.applied_at DESC
        ");

        $this->db->bind(
            ':company_id',
            $companyId
        );

        return $this->db->resultSet();
    }

    /**
     * Get all applications for Admin.
     */
    public function getAllApplications() {

        $this->db->query("
            SELECT
                a.*,
                j.title AS job_title,
                c.company_name,
                u.name AS candidate_name,
                u.email AS candidate_email
            FROM applications a
            INNER JOIN jobs j
                ON a.job_id = j.id
            INNER JOIN companies c
                ON j.company_id = c.id
            INNER JOIN candidates cand
                ON a.candidate_id = cand.id
            INNER JOIN users u
                ON cand.user_id = u.id
            ORDER BY a.applied_at DESC
        ");

        return $this->db->resultSet();
    }

    /**
     * Manually update application status.
     */
    public function updateStatus($applicationId, $status) {

        $status = strtolower(trim((string) $status));

        $allowedStatuses = [
            'pending',
            'under_review',
            'shortlisted',
            'rejected'
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            return false;
        }

        $this->db->query("
            UPDATE applications
            SET status = :status
            WHERE id = :id
        ");

        $this->db->bind(
            ':id',
            $applicationId
        );

        $this->db->bind(
            ':status',
            $status
        );

        return $this->db->execute();
    }
}
?>