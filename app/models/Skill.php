<?php
namespace App\Models;

use App\Core\Database;

/**
 * Skill Model
 * Handles CRUD for `skills` and linking `resume_skills`.
 */
class Skill {
    protected $db;
    protected $skillTable = 'skills';
    protected $resumeSkillTable = 'resume_skills';

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Find a skill by name or create it if missing.
     * @param string $skillName
     * @return int|false Skill ID or false on failure
     */
    public function findOrCreate(string $skillName) {
        // Look for existing skill
        $sql = "SELECT id FROM {$this->skillTable} WHERE name = :name LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':name', $skillName);
        $result = $this->db->single();
        if ($result && isset($result->id)) {
            return (int)$result->id;
        }
        // Insert new skill
        $sql = "INSERT INTO {$this->skillTable} (name) VALUES (:name)";
        $this->db->query($sql);
        $this->db->bind(':name', $skillName);
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Attach a skill to a resume.
     */
    public function attachToResume(int $resumeId, int $skillId) {
        $sql = "INSERT INTO {$this->resumeSkillTable} (resume_id, skill_id) VALUES (:resume_id, :skill_id)";
        $this->db->query($sql);
        $this->db->bind(':resume_id', $resumeId);
        $this->db->bind(':skill_id', $skillId);
        return $this->db->execute();
    }

    /**
     * Get all skills linked to a resume.
     */
    public function getSkillsByResume(int $resumeId) {
        $sql = "SELECT s.* FROM {$this->skillTable} s \n                INNER JOIN {$this->resumeSkillTable} rs ON s.id = rs.skill_id \n                WHERE rs.resume_id = :resume_id";
        $this->db->query($sql);
        $this->db->bind(':resume_id', $resumeId);
        return $this->db->resultSet();
    }

    /**
     * Remove all skill mappings for a resume.
     */
    public function removeResumeSkills(int $resumeId) {
        $sql = "DELETE FROM {$this->resumeSkillTable} WHERE resume_id = :resume_id";
        $this->db->query($sql);
        $this->db->bind(':resume_id', $resumeId);
        return $this->db->execute();
    }
}
?>
