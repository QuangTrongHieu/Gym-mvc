<?php

namespace App\Models;

class TrainingSession extends BaseModel
{
    protected $table = 'training_sessions';

    public function getByTrainerId($trainerId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE trainer_id = :trainer_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['trainer_id' => $trainerId]);
        return $stmt->fetchAll();
    }

    public function updateStatus($sessionId, $status, $notes)
    {
        $sql = "UPDATE {$this->table} SET status = :status, notes = :notes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $sessionId,
            'status' => $status,
            'notes' => $notes
        ]);
    }

    public function getUpcomingByTrainerId($trainerId)
    {
        $sql = "SELECT ts.* FROM {$this->table} ts
                JOIN pt_registrations pr ON ts.registration_id = pr.id
                WHERE pr.trainer_id = :trainer_id 
                AND ts.session_date >= CURRENT_DATE
                AND ts.status = 'scheduled'
                ORDER BY ts.session_date ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['trainer_id' => $trainerId]);
        return $stmt->fetchAll();
    }
}
