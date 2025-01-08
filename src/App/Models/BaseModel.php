<?php

namespace App\Models;

use Core\Database;
use PDOException;
use PDO;

class BaseModel
{
    // Database    
    protected $db;
    // Tên bảng
    protected $table;
    // Khóa chính
    protected $primaryKey = 'id';

    // Khởi tạo kết nối database
    public function __construct($db = null)
    {
        if ($db instanceof PDO) {
            $this->db = $db;
        } else {
            $this->db = Database::getInstance()->getConnection();
        }
    }

    // Lấy tất cả các bản ghi
    public function findAll()
    {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy bản ghi theo ID
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tạo bản ghi mới
    public function create($data)
    {
        $columns = implode(', ', array_keys($data));
        $values = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($values)";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($data);
    }

    // Get database connection
    public function getConnection()
    {
        return $this->db;
    }

    // Set database connection
    public function setConnection($connection)
    {
        if ($connection instanceof PDO) {
            $this->db = $connection;
        }
    }

    // Cập nhật bản ghi
    public function update($id, $data)
    {
        try {
            $this->db->beginTransaction();
            $setClause = '';
            foreach ($data as $key => $value) {
                $setClause .= "$key = :$key, ";
            }
            $setClause = rtrim($setClause, ', ');

            $sql = "UPDATE {$this->table} SET $setClause WHERE {$this->primaryKey} = :id";
            $stmt = $this->db->prepare($sql);

            $data['id'] = $id;
            if (!$stmt->execute($data)) {
                throw new \PDOException("Error updating record");
            }
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log("Error updating record: " . $e->getMessage());
            throw new \Exception("Không thể cập nhật dữ liệu");
        }
    }

    // Xóa bản ghi
    public function delete($id)
    {
        try {
            $this->db->beginTransaction();
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
            $stmt = $this->db->prepare($sql);
            if (!$stmt->execute(['id' => $id])) {
                throw new \PDOException("Error deleting record");
            }
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log("Error deleting record: " . $e->getMessage());
            throw new \Exception("Không thể xóa dữ liệu");
        }
    }

    public function getById($id)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting record by ID: " . $e->getMessage());
            return false;
        }
    }
}
