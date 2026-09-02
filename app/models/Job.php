<?php
namespace App\Models;

use App\Core\Database;

/**
 * Job Model Class
 * Handles job vacancy database operations, searches, and management.
 */
class Job {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Get all active jobs with optional filtering
     * (keyword, category, job_type, location, status)
     */
    public function getAllJobs($filters = []) {
        $sql = "
            SELECT j.*,
                   c.company_name,
                   c.logo,
                   c.industry,
                   cat.name AS category_name,
                   cat.icon AS category_icon
            FROM jobs j
            INNER JOIN companies c ON j.company_id = c.id
            LEFT JOIN categories cat ON j.category_id = cat.id
            WHERE 1=1
        ";

        $params = [];

        /*
         * Status Filter
         */
        if (isset($filters['status']) && !empty($filters['status'])) {
            $sql .= " AND j.status = :status";
            $params[':status'] = $filters['status'];
        } else {
            $sql .= " AND j.status = 'active'";
        }

        /*
         * Keyword Filter
         */
        if (!empty($filters['keyword'])) {
            $sql .= "
                AND (
                    j.title LIKE :keyword
                    OR j.description LIKE :keyword
                    OR c.company_name LIKE :keyword
                )
            ";

            $params[':keyword'] =
                '%' . trim($filters['keyword']) . '%';
        }

        /*
         * Category Filter
         */
        if (!empty($filters['category_id'])) {
            $sql .= " AND j.category_id = :category_id";
            $params[':category_id'] =
                $filters['category_id'];
        }

        /*
         * Job Type Filter
         */
        if (!empty($filters['job_type'])) {
            $sql .= " AND j.job_type = :job_type";
            $params[':job_type'] =
                $filters['job_type'];
        }

        /*
         * Location Filter
         */
        if (!empty($filters['location'])) {
            $sql .= " AND j.location LIKE :location";
            $params[':location'] =
                '%' . trim($filters['location']) . '%';
        }

        $sql .= " ORDER BY j.created_at DESC";

        $this->db->query($sql);

        foreach ($params as $param => $value) {
            $this->db->bind($param, $value);
        }

        return $this->db->resultSet();
    }

    /**
     * Get job by ID with detailed company and category information
     */
    public function getJobById($id) {
        $this->db->query("
            SELECT j.*,
                   c.company_name,
                   c.website,
                   c.industry,
                   c.company_size,
                   c.description AS company_description,
                   c.logo,
                   c.address AS company_address,
                   c.user_id AS employer_user_id,
                   cat.name AS category_name,
                   cat.icon AS category_icon
            FROM jobs j
            INNER JOIN companies c
                ON j.company_id = c.id
            LEFT JOIN categories cat
                ON j.category_id = cat.id
            WHERE j.id = :id
        ");

        $this->db->bind(':id', $id);

        return $this->db->single();
    }

    /**
     * Get all jobs posted by an employer (company_id)
     */
    public function getJobsByCompanyId($companyId) {
        $this->db->query("
            SELECT j.*,
                   cat.name AS category_name,
                   (
                       SELECT COUNT(*)
                       FROM applications a
                       WHERE a.job_id = j.id
                   ) AS applications_count
            FROM jobs j
            LEFT JOIN categories cat
                ON j.category_id = cat.id
            WHERE j.company_id = :company_id
            ORDER BY j.created_at DESC
        ");

        $this->db->bind(':company_id', $companyId);

        return $this->db->resultSet();
    }

    /**
     * Create a new job listing
     */
    public function createJob($data) {
        $this->db->query("
            INSERT INTO jobs (
                company_id,
                category_id,
                title,
                description,
                requirements,
                location,
                salary,
                job_type,
                status,
                expiry_date,
                required_experience
            )
            VALUES (
                :company_id,
                :category_id,
                :title,
                :description,
                :requirements,
                :location,
                :salary,
                :job_type,
                :status,
                :expiry_date,
                :required_experience
            )
        ");

        $this->db->bind(
            ':company_id',
            $data['company_id']
        );

        $this->db->bind(
            ':category_id',
            !empty($data['category_id'])
                ? $data['category_id']
                : null
        );

        $this->db->bind(
            ':title',
            trim($data['title'])
        );

        $this->db->bind(
            ':description',
            trim($data['description'])
        );

        $this->db->bind(
            ':requirements',
            trim($data['requirements'])
        );

        $this->db->bind(
            ':location',
            trim($data['location'])
        );

        $this->db->bind(
            ':salary',
            trim($data['salary'])
        );

        $this->db->bind(
            ':job_type',
            $data['job_type']
        );

        $this->db->bind(
            ':status',
            !empty($data['status'])
                ? $data['status']
                : 'active'
        );

        $this->db->bind(
            ':expiry_date',
            !empty($data['expiry_date'])
                ? $data['expiry_date']
                : null
        );

        /*
         * Required Experience
         */
        $this->db->bind(
            ':required_experience',
            isset($data['required_experience'])
                ? (float)$data['required_experience']
                : 0
        );

        return $this->db->execute();
    }

    /**
     * Update an existing job listing
     */
    public function updateJob($id, $data) {
        $this->db->query("
            UPDATE jobs
            SET category_id = :category_id,
                title = :title,
                description = :description,
                requirements = :requirements,
                location = :location,
                salary = :salary,
                job_type = :job_type,
                status = :status,
                expiry_date = :expiry_date,
                required_experience = :required_experience
            WHERE id = :id
        ");

        $this->db->bind(
            ':id',
            $id
        );

        $this->db->bind(
            ':category_id',
            !empty($data['category_id'])
                ? $data['category_id']
                : null
        );

        $this->db->bind(
            ':title',
            trim($data['title'])
        );

        $this->db->bind(
            ':description',
            trim($data['description'])
        );

        $this->db->bind(
            ':requirements',
            trim($data['requirements'])
        );

        $this->db->bind(
            ':location',
            trim($data['location'])
        );

        $this->db->bind(
            ':salary',
            trim($data['salary'])
        );

        $this->db->bind(
            ':job_type',
            $data['job_type']
        );

        $this->db->bind(
            ':status',
            $data['status']
        );

        $this->db->bind(
            ':expiry_date',
            !empty($data['expiry_date'])
                ? $data['expiry_date']
                : null
        );

        /*
         * Required Experience
         */
        $this->db->bind(
            ':required_experience',
            isset($data['required_experience'])
                ? (float)$data['required_experience']
                : 0
        );

        return $this->db->execute();
    }

    /**
     * Delete a job vacancy
     */
    public function deleteJob($id) {
        $this->db->query("
            DELETE FROM jobs
            WHERE id = :id
        ");

        $this->db->bind(
            ':id',
            $id
        );

        return $this->db->execute();
    }

    /**
     * Toggle job status
     * (active, inactive, closed)
     */
    public function toggleJobStatus($id, $status) {
        $this->db->query("
            UPDATE jobs
            SET status = :status
            WHERE id = :id
        ");

        $this->db->bind(
            ':id',
            $id
        );

        $this->db->bind(
            ':status',
            $status
        );

        return $this->db->execute();
    }
}