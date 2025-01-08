<?php

namespace App\Models;

class Trainer extends BaseModel
{
    protected $table = 'trainers';

    public function __construct()
    {
        parent::__construct();
        // Set default fetch mode to FETCH_ASSOC
        $this->db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }

    // Transaction methods
    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }

    public function commit()
    {
        return $this->db->commit();
    }

    public function rollBack()
    {
        return $this->db->rollBack();
    }

    public function inTransaction()
    {
        return $this->db->inTransaction();
    }

    public function getAllTrainers()
    {
        $sql = "SELECT * FROM trainers WHERE status = 'active'";
        return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM trainers WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        try {
            // Validate required fields
            $requiredFields = ['username', 'password', 'fullName', 'dateOfBirth', 'sex', 'phone', 'email', 
                             'specialization', 'experience', 'certification', 'salary'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new \Exception("Trường {$field} không được để trống");
                }
            }

            // Validate email format
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Email không hợp lệ');
            }

            // Check if username already exists
            $existingUser = $this->findByUsername($data['username']);
            if ($existingUser) {
                throw new \Exception('Tên đăng nhập đã tồn tại');
            }

            // Check if email already exists
            $sql = "SELECT id FROM {$this->table} WHERE email = :email AND status = 'ACTIVE'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['email' => $data['email']]);
            if ($stmt->fetch()) {
                throw new \Exception('Email đã được sử dụng');
            }

            // Check if phone exists
            $sql = "SELECT id FROM {$this->table} WHERE phone = :phone AND status = 'ACTIVE'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['phone' => $data['phone']]);
            if ($stmt->fetch()) {
                throw new \Exception('Số điện thoại đã được sử dụng');
            }

            // Set default values
            $data['eRole'] = 'TRAINER';
            $data['status'] = 'ACTIVE';

            $sql = "INSERT INTO {$this->table} (username, password, fullName, dateOfBirth, 
                    sex, phone, email, specialization, experience, certification, salary, 
                    eRole, status, avatar, createdAt, updatedAt) 
                    VALUES (:username, :password, :fullName, :dateOfBirth, :sex, :phone, 
                    :email, :specialization, :experience, :certification, :salary, 
                    :eRole, :status, :avatar, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";

            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute($data);

            if (!$success) {
                throw new \Exception('Không thể thêm huấn luyện viên');
            }

            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            error_log("Database error in create trainer: " . $e->getMessage());
            throw new \Exception('Lỗi hệ thống, vui lòng thử lại sau');
        }
    }

    public function update($id, $data)
    {
        try {
            // Check username uniqueness if it's being updated
            if (isset($data['username'])) {
                $sql = "SELECT id FROM {$this->table} WHERE username = :username AND id != :id AND status = 'ACTIVE'";
                $stmt = $this->db->prepare($sql);
                $stmt->execute(['username' => $data['username'], 'id' => $id]);
                if ($stmt->fetch()) {
                    throw new \Exception('Tên đăng nhập đã được sử dụng bởi huấn luyện viên khác');
                }
            }

            // Check phone uniqueness if it's being updated
            if (isset($data['phone'])) {
                $sql = "SELECT id FROM {$this->table} WHERE phone = :phone AND id != :id AND status = 'ACTIVE'";
                $stmt = $this->db->prepare($sql);
                $stmt->execute(['phone' => $data['phone'], 'id' => $id]);
                if ($stmt->fetch()) {
                    throw new \Exception('Số điện thoại đã được sử dụng bởi huấn luyện viên khác');
                }
            }

            // Build update query
            $fields = [];
            $params = [];
            foreach ($data as $key => $value) {
                $fields[] = "`$key` = :$key";
                $params[$key] = $value;
            }

            // Add updatedAt timestamp
            $fields[] = "updatedAt = CURRENT_TIMESTAMP";

            // Add WHERE clause
            $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id AND status = 'ACTIVE'";
            $params['id'] = $id;

            // Execute update
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute($params);

            if (!$success) {
                throw new \Exception('Không thể cập nhật thông tin huấn luyện viên');
            }

            // Check if any rows were affected
            if ($stmt->rowCount() === 0) {
                throw new \Exception('Không tìm thấy huấn luyện viên hoặc không có thay đổi');
            }

            return true;
        } catch (\PDOException $e) {
            error_log("Database error in update trainer: " . $e->getMessage());
            throw new \Exception('Lỗi cơ sở dữ liệu khi cập nhật thông tin huấn luyện viên');
        }
    }

    public function delete($id)
    {
        try {
            // Check if trainer exists and is active
            $trainer = $this->findById($id);
            if (!$trainer) {
                throw new \Exception('Không tìm thấy huấn luyện viên');
            }

            // Check for active PT registrations
            $sql = "SELECT COUNT(*) FROM pt_registrations WHERE trainerId = :id AND status = 'ACTIVE'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            if ($stmt->fetchColumn() > 0) {
                throw new \Exception('Không thể xóa huấn luyện viên đang có học viên đăng ký');
            }

            // Check for active schedules
            if ($this->hasActiveSchedules($id)) {
                throw new \Exception('Không thể xóa huấn luyện viên đang có lịch huấn luyện');
            }

            // Soft delete trainer by setting status to INACTIVE
            $sql = "UPDATE {$this->table} SET 
                    status = 'INACTIVE',
                    updatedAt = CURRENT_TIMESTAMP 
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            if (!$stmt->execute(['id' => $id])) {
                throw new \Exception('Không thể xóa huấn luyện viên');
            }

            // Cancel future training sessions
            $sql = "UPDATE training_sessions ts
                    JOIN pt_registrations ptr ON ts.ptRegistrationId = ptr.id
                    SET ts.status = 'CANCELLED',
                        ts.updatedAt = CURRENT_TIMESTAMP 
                    WHERE ptr.trainerId = :trainerId 
                    AND ts.status = 'SCHEDULED' 
                    AND ts.sessionDate >= CURRENT_DATE";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['trainerId' => $id]);

            return true;
        } catch (\PDOException $e) {
            error_log("Database error in delete trainer: " . $e->getMessage());
            throw new \Exception('Lỗi hệ thống, vui lòng thử lại sau');
        }
    }

    public function getPerformanceStats($trainerId)
    {
        $sql = "SELECT 
                COUNT(DISTINCT pt.client_id) as total_clients,
                COUNT(ts.id) as total_sessions,
                COUNT(CASE WHEN ts.status = 'completed' THEN 1 END) as completed_sessions,
                COUNT(CASE WHEN ts.status = 'cancelled' THEN 1 END) as cancelled_sessions
                FROM trainers t
                LEFT JOIN pt_registrations pt ON t.id = pt.trainer_id
                LEFT JOIN training_sessions ts ON pt.id = ts.registration_id
                WHERE t.id = :trainerId
                GROUP BY t.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['trainerId' => $trainerId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function count($whereClause = '', $params = [])
    {
        $sql = "SELECT COUNT(*) as total FROM trainers t " . $whereClause;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }

    public function findWithFilters($conditions = [], $params = [], $limit = 10, $offset = 0)
    {
        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $sql = "SELECT * FROM trainers t {$whereClause} LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getSpecialties($trainerId)
    {
        $sql = "SELECT specialization FROM trainer_specialties 
                WHERE trainer_id = :trainer_id AND status = 'active'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['trainer_id' => $trainerId]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function findAll()
    {
        $stmt = $this->db->query("SELECT * FROM trainers");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        return $this->getById($id);
    }

    public function findByUsername($username)
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username AND status = 'ACTIVE'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email AND status = 'ACTIVE'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function findActiveTrainers()
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE status = 'ACTIVE'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error finding active trainers: " . $e->getMessage());
            return [];
        }
    }

    public function getTrainerByUsername($username)
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username AND status = 'ACTIVE'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getTrainerById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id AND status = 'ACTIVE'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function updateTrainer($id, $data)
    {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $data['id'] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function getTrainingSchedule($trainerId) {
        try {
            $sql = "SELECT ts.*, u.fullName as memberName 
                    FROM training_sessions ts 
                    JOIN pt_registrations ptr ON ts.ptRegistrationId = ptr.id
                    JOIN users u ON ptr.userId = u.id
                    WHERE ptr.trainerId = :trainerId
                    AND ts.sessionDate >= CURRENT_DATE 
                    AND ts.status = 'SCHEDULED'
                    ORDER BY ts.sessionDate";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['trainerId' => $trainerId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("Error getting training schedule: " . $e->getMessage());
            return []; 
        }
    }

    private function hasActiveSchedules($trainerId) {
        try {
            // Check for active training sessions
            $sql = "SELECT COUNT(*) FROM training_sessions ts
                    JOIN pt_registrations ptr ON ts.ptRegistrationId = ptr.id
                    WHERE ptr.trainerId = :trainerId
                    AND ts.status = 'SCHEDULED' 
                    AND ts.sessionDate >= CURRENT_DATE";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['trainerId' => $trainerId]);
            
            return $stmt->fetchColumn() > 0;
            
        } catch (\PDOException $e) {
            error_log("Error checking active schedules: " . $e->getMessage());
            return false;
        }
    }
}
