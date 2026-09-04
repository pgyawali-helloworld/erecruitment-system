<?php
namespace App\Models;

use App\Core\Database;

/**
 * User Model Class
 * Handles database operations related to User registration,
 * verification, and profiles.
 */
class User {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Find user by email
     * Used for duplicate check and login authentication.
     * 
     * @param string $email
     * @return object|false User record object or false if not found
     */
    public function findUserByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        
        $row = $this->db->single();
        
        if ($this->db->rowCount() > 0) {
            return $row;
        }
        return false;
    }

    /**
     * Register a new user
     * Wraps user creation and profile setup inside a transaction.
     * 
     * @param array $data Input data
     * @return bool True on success, False on failure
     */
    public function register($data) {
        try {
            $this->db->beginTransaction();

            // 1. Insert into users base table
            $this->db->query('INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)');
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':password', $data['password']); // Already hashed
            $this->db->bind(':role', $data['role']);
            $this->db->execute();

            $userId = $this->db->lastInsertId();

            // 2. Insert into profile table based on role
            if ($data['role'] === 'candidate') {
                $this->db->query('INSERT INTO candidates (user_id, phone) VALUES (:user_id, :phone)');
                $this->db->bind(':user_id', $userId);
                $this->db->bind(':phone', $data['phone']);
                $this->db->execute();
            } elseif ($data['role'] === 'employer') {
                $this->db->query('INSERT INTO companies (user_id, company_name) VALUES (:user_id, :company_name)');
                $this->db->bind(':user_id', $userId);
                $this->db->bind(':company_name', $data['company_name']);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            // System-level log can be written here. Returning false to controller
            return false;
        }
    }

    /**
     * Get users by their role (admin, employer, candidate) joined with profiles
     */
    public function getUsersByRole($role) {
        if ($role === 'employer') {
            $this->db->query("
                SELECT u.id, u.name, u.email, u.status, u.created_at, c.company_name, c.industry, c.address 
                FROM users u 
                LEFT JOIN companies c ON u.id = c.user_id 
                WHERE u.role = 'employer' 
                ORDER BY u.created_at DESC
            ");
        } else {
            $this->db->query("
                SELECT u.id, u.name, u.email, u.status, u.created_at, cand.phone, cand.skills, cand.education 
                FROM users u 
                LEFT JOIN candidates cand ON u.id = cand.user_id 
                WHERE u.role = 'candidate' 
                ORDER BY u.created_at DESC
            ");
        }
        return $this->db->resultSet();
    }

    /**
     * Get total count of users by role for pagination
     */
    public function getUserCountByRole($role) {
        if ($role === 'employer') {
            $this->db->query("SELECT COUNT(*) as cnt FROM users u WHERE u.role = 'employer'");
        } else {
            $this->db->query("SELECT COUNT(*) as cnt FROM users u WHERE u.role = 'candidate'");
        }
        $row = $this->db->single();
        return $row->cnt ?? 0;
    }

    /**
     * Get paginated users by role
     */
    public function getUsersByRolePaginated($role, $limit, $offset) {
        if ($role === 'employer') {
            $this->db->query("
                SELECT u.id, u.name, u.email, u.status, u.created_at, c.company_name, c.industry, c.address 
                FROM users u 
                LEFT JOIN companies c ON u.id = c.user_id 
                WHERE u.role = 'employer' 
                ORDER BY u.created_at DESC 
                LIMIT :limit OFFSET :offset
            ");
        } else {
            $this->db->query("
                SELECT u.id, u.name, u.email, u.status, u.created_at, cand.phone, cand.skills, cand.education 
                FROM users u 
                LEFT JOIN candidates cand ON u.id = cand.user_id 
                WHERE u.role = 'candidate' 
                ORDER BY u.created_at DESC 
                LIMIT :limit OFFSET :offset
            ");
        }
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }

    /**
     * Get full details of a user by ID and Role
     */
    public function getUserDetails($userId, $role) {
        if ($role === 'employer') {
            $this->db->query("
                SELECT u.id, u.name, u.email, u.status, u.created_at, 
                       c.id AS company_id, c.company_name, c.website, c.industry, c.company_size, c.description, c.logo, c.address 
                FROM users u 
                LEFT JOIN companies c ON u.id = c.user_id 
                WHERE u.id = :id AND u.role = 'employer'
            ");
        } else {
            $this->db->query("
                SELECT u.id, u.name, u.email, u.status, u.created_at, 
                       cand.id AS candidate_id, cand.phone, cand.skills, cand.experience, cand.education, cand.resume_path, cand.profile_pic, cand.bio 
                FROM users u 
                LEFT JOIN candidates cand ON u.id = cand.user_id 
                WHERE u.id = :id AND u.role = 'candidate'
            ");
        }
        $this->db->bind(':id', $userId);
        return $this->db->single();
    }

    /**
     * Toggle User status between active and inactive
     */
    public function toggleUserStatus($userId, $status) {
        $this->db->query("UPDATE users SET status = :status WHERE id = :id");
        $this->db->bind(':id', $userId);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }

    /**
     * Delete user and cascade delete profile, jobs, etc. (uses transactions)
     */
    public function deleteUser($userId) {
        try {
            $this->db->beginTransaction();
            $this->db->query("DELETE FROM users WHERE id = :id");
            $this->db->bind(':id', $userId);
            $this->db->execute();
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
