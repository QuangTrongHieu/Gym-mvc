<?php

namespace App\Models;

use App\Models\BaseModel;
use PDO;

class Schedule extends BaseModel
{
    protected $table = 'schedules';

    public function getAllSchedulesWithNames($month = null, $year = null)
    {
        $conditions = [];
        $params = [];

        if ($month && $year) {
            $conditions[] = "MONTH(s.training_date) = :month AND YEAR(s.training_date) = :year";
            $params[':month'] = $month;
            $params[':year'] = $year;
        }

        $whereClause = !empty($conditions) ? "WHERE " . implode(' AND ', $conditions) : "";

        $sql = "SELECT s.*, u.fullName as user_name, t.fullName as trainer_name,
                       u.email as user_email, t.email as trainer_email,
                       u.phone as user_phone, t.phone as trainer_phone
                FROM {$this->table} s
                LEFT JOIN users u ON s.user_id = u.id
                LEFT JOIN trainers t ON s.trainer_id = t.id
                {$whereClause}
                ORDER BY s.training_date ASC, s.start_time ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSchedulesByUser($userId, $month = null, $year = null)
    {
        $conditions = ["s.user_id = :user_id"];
        $params = [':user_id' => $userId];

        if ($month && $year) {
            $conditions[] = "MONTH(s.training_date) = :month AND YEAR(s.training_date) = :year";
            $params[':month'] = $month;
            $params[':year'] = $year;
        }

        $whereClause = implode(' AND ', $conditions);

        $sql = "SELECT s.*, u.fullName as user_name, t.fullName as trainer_name,
                       u.email as user_email, t.email as trainer_email,
                       u.phone as user_phone, t.phone as trainer_phone
                FROM {$this->table} s
                LEFT JOIN users u ON s.user_id = u.id
                LEFT JOIN trainers t ON s.trainer_id = t.id
                WHERE {$whereClause}
                ORDER BY s.training_date ASC, s.start_time ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSchedulesByTrainer($trainerId, $month = null, $year = null)
    {
        $conditions = ["s.trainer_id = :trainer_id"];
        $params = [':trainer_id' => $trainerId];

        if ($month && $year) {
            $conditions[] = "MONTH(s.training_date) = :month AND YEAR(s.training_date) = :year";
            $params[':month'] = $month;
            $params[':year'] = $year;
        }

        $whereClause = implode(' AND ', $conditions);

        $sql = "SELECT s.*, u.fullName as user_name, t.fullName as trainer_name,
                       u.email as user_email, t.email as trainer_email,
                       u.phone as user_phone, t.phone as trainer_phone
                FROM {$this->table} s
                LEFT JOIN users u ON s.user_id = u.id
                LEFT JOIN trainers t ON s.trainer_id = t.id
                WHERE {$whereClause}
                ORDER BY s.training_date ASC, s.start_time ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkScheduleConflict($trainerId, $date, $startTime, $endTime, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as count
                FROM {$this->table}
                WHERE trainer_id = :trainer_id
                AND training_date = :date
                AND (
                    (start_time <= :start_time AND end_time > :start_time)
                    OR (start_time < :end_time AND end_time >= :end_time)
                    OR (start_time >= :start_time AND end_time <= :end_time)
                )";

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
        }

        $stmt = $this->db->prepare($sql);
        $params = [
            ':trainer_id' => $trainerId,
            ':date' => $date,
            ':start_time' => $startTime,
            ':end_time' => $endTime
        ];

        if ($excludeId) {
            $params[':exclude_id'] = $excludeId;
        }

        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] > 0;
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (user_id, trainer_id, training_date, start_time, end_time, notes, status) 
                VALUES (:user_id, :trainer_id, :training_date, :start_time, :end_time, :notes, :status)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':user_id' => $data['user_id'],
            ':trainer_id' => $data['trainer_id'],
            ':training_date' => $data['training_date'],
            ':start_time' => $data['start_time'],
            ':end_time' => $data['end_time'],
            ':notes' => $data['notes'],
            ':status' => $data['status']
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} 
                SET user_id = :user_id,
                    trainer_id = :trainer_id,
                    training_date = :training_date,
                    start_time = :start_time,
                    end_time = :end_time,
                    notes = :notes,
                    status = :status
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':user_id' => $data['user_id'],
            ':trainer_id' => $data['trainer_id'],
            ':training_date' => $data['training_date'],
            ':start_time' => $data['start_time'],
            ':end_time' => $data['end_time'],
            ':notes' => $data['notes'],
            ':status' => $data['status']
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
