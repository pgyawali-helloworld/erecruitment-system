<?php
namespace App\Models;

use App\Core\Database;

/**
 * Category Model Class
 * Handles database operations for Job Categories.
 */
class Category {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Get all categories
     */
    public function getAllCategories() {
        $this->db->query("SELECT * FROM categories ORDER BY name ASC");
        return $this->db->resultSet();
    }

    /**
     * Get category by ID
     */
    public function getCategoryById($id) {
        $this->db->query("SELECT * FROM categories WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Validate duplicate category names
     * Returns true if name is already taken, false otherwise.
     */
    public function validateDuplicateName($name, $excludeId = null) {
        if ($excludeId) {
            $this->db->query("SELECT * FROM categories WHERE LOWER(name) = LOWER(:name) AND id != :exclude_id");
            $this->db->bind(':exclude_id', $excludeId);
        } else {
            $this->db->query("SELECT * FROM categories WHERE LOWER(name) = LOWER(:name)");
        }
        $this->db->bind(':name', trim($name));
        $this->db->single();
        return $this->db->rowCount() > 0;
    }

    /**
     * Add category
     */
    public function addCategory($data) {
        $this->db->query("INSERT INTO categories (name, icon) VALUES (:name, :icon)");
        $this->db->bind(':name', trim($data['name']));
        $this->db->bind(':icon', trim($data['icon'] ?: 'fa-briefcase'));
        return $this->db->execute();
    }

    /**
     * Update category
     */
    public function updateCategory($id, $data) {
        $this->db->query("UPDATE categories SET name = :name, icon = :icon WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':name', trim($data['name']));
        $this->db->bind(':icon', trim($data['icon'] ?: 'fa-briefcase'));
        return $this->db->execute();
    }

    /**
     * Delete category
     */
    public function deleteCategory($id) {
        $this->db->query("DELETE FROM categories WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
