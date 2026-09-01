<?php
namespace App\Models;

use App\Core\Database;

/**
 * Resume Model
 * Handles CRUD operations for the `resumes` table.
 */
class Resume {
    protected $db;
    protected $table = 'resumes';

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Save a new resume record.
     * @param int $candidateId
     * @param string $filePath
     * @param array|null $parsedData
     * @return int|false Inserted ID or false on failure
     */
    public function create(int $candidateId, string $filePath, ?string $extractedText = null, ?array $parsedData = null) {
        $sql = "INSERT INTO {$this->table} (candidate_id, file_path, extracted_text, parsed_json) VALUES (:candidate_id, :file_path, :extracted_text, :parsed_json)";
        $this->db->query($sql);
        $this->db->bind(':candidate_id', $candidateId);
        $this->db->bind(':file_path', $filePath);
        $this->db->bind(':extracted_text', $extractedText);
        $this->db->bind(':parsed_json', $parsedData ? json_encode($parsedData) : null);
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Get latest resume for a candidate.
     */
    public function getByCandidate(int $candidateId) {
        $sql = "SELECT * FROM {$this->table} WHERE candidate_id = :candidate_id ORDER BY created_at DESC LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':candidate_id', $candidateId);
        return $this->db->single();
    }

    /**
     * Update parsed JSON for an existing resume.
     */
    public function updateParsed(int $resumeId, array $parsedData) {
        $sql = "UPDATE {$this->table} SET parsed_json = :parsed_json WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':parsed_json', json_encode($parsedData));
        $this->db->bind(':id', $resumeId);
        return $this->db->execute();
    }

    /**
     * Get resume by its ID.
     */
    public function getById(int $resumeId) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $resumeId);
        return $this->db->single();
    }

    /**
     * Update extracted_text and parsed_json for a resume.
     */
    public function updateExtractedAndParsed(int $resumeId, ?string $extractedText, array $parsedData) {
        $sql = "UPDATE {$this->table} SET extracted_text = :extracted_text, parsed_json = :parsed_json WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':extracted_text', $extractedText);
        $this->db->bind(':parsed_json', json_encode($parsedData));
        $this->db->bind(':id', $resumeId);
        return $this->db->execute();
    }

    /**
     * Update only the file_path of a resume.
     */
    public function updateFilePath(int $resumeId, string $filePath) {
        $sql = "UPDATE {$this->table} SET file_path = :file_path WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':file_path', $filePath);
        $this->db->bind(':id', $resumeId);
        return $this->db->execute();
    }
}
?>
