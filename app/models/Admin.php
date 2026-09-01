<?php
namespace App\Models;

use App\Core\Database;

/**
 * Admin Model Class
 * Handles queries for Admin Dashboard stats, reports, and administrative tasks.
 */
class Admin {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Get counts for dashboard summary
     */
    public function getDashboardStats() {
        $stats = [];

        // Total Employers
        $this->db->query("SELECT COUNT(*) AS total FROM users WHERE role = 'employer'");
        $stats['total_employers'] = $this->db->single()->total;

        // Total Job Seekers (candidates)
        $this->db->query("SELECT COUNT(*) AS total FROM users WHERE role = 'candidate'");
        $stats['total_candidates'] = $this->db->single()->total;

        // Total Jobs
        $this->db->query("SELECT COUNT(*) AS total FROM jobs");
        $stats['total_jobs'] = $this->db->single()->total;

        // Total Applications
        $this->db->query("SELECT COUNT(*) AS total FROM applications");
        $stats['total_applications'] = $this->db->single()->total;

        return $stats;
    }

    /**
     * Get recent users
     */
    public function getRecentUsers($limit = 5) {
        $this->db->query("SELECT id, name, email, role, status, created_at FROM users ORDER BY created_at DESC LIMIT :limit");
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    /**
     * Get recent job postings
     */
    public function getRecentJobs($limit = 5) {
        $this->db->query("
            SELECT j.id, j.title, j.job_type, j.status, j.created_at, c.company_name 
            FROM jobs j 
            LEFT JOIN companies c ON j.company_id = c.id 
            ORDER BY j.created_at DESC 
            LIMIT :limit
        ");
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    /**
     * Get Jobs count grouped by Category
     */
    public function getJobsByCategory() {
        $this->db->query("
            SELECT c.name AS category_name, c.icon, COUNT(j.id) AS job_count 
            FROM categories c 
            LEFT JOIN jobs j ON c.id = j.category_id 
            GROUP BY c.id, c.name, c.icon 
            ORDER BY job_count DESC
        ");
        return $this->db->resultSet();
    }

    /**
     * Get recent activities across users, jobs, and applications
     */
    public function getRecentActivities($limit = 10) {
        // Fetch recent user registrations
        $this->db->query("SELECT 'user_registered' AS event_type, name AS detail, created_at AS event_time, role AS extra FROM users ORDER BY created_at DESC LIMIT :limit");
        $this->db->bind(':limit', $limit);
        $users = $this->db->resultSet();

        // Fetch recent job postings
        $this->db->query("SELECT 'job_posted' AS event_type, title AS detail, created_at AS event_time, job_type AS extra FROM jobs ORDER BY created_at DESC LIMIT :limit");
        $this->db->bind(':limit', $limit);
        $jobs = $this->db->resultSet();

        // Fetch recent applications
        $this->db->query("SELECT 'application_submitted' AS event_type, CAST(job_id AS CHAR) AS detail, applied_at AS event_time, status AS extra FROM applications ORDER BY applied_at DESC LIMIT :limit");
        $this->db->bind(':limit', $limit);
        $apps = $this->db->resultSet();

        // Merge results
        $activities = array_merge($users, $jobs, $apps);

        // Sort by event_time DESC
        usort($activities, function($a, $b) {
            return strcmp($b->event_time, $a->event_time);
        });

        // Slice to the requested limit
        return array_slice($activities, 0, $limit);
    }
}
