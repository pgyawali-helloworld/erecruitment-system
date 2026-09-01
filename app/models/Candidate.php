<?php
namespace App\Models;

use App\Core\Database;

/**
 * Candidate Model
 * Provides simple data retrieval for the `candidates` table.
 */
class Candidate {
    protected $db;
    protected $table = 'candidates';

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Retrieve candidate record by the linked user_id.
     * @param int $userId
     * @return object|false Candidate object with at least the `id` field, or false if not found.
     */
    public function getByUserId(int $userId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        return $this->db->single();
    }
}
?>
