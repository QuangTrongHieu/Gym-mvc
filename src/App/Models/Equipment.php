<?php

namespace App\Models;

use App\Models\BaseModel;
use PDOException;

class Equipment extends BaseModel
{
    protected $table = 'equipment';

    public function __construct()
    {
        parent::__construct();
    }

    public function create($data)
    {
        try {
            $sql = "INSERT INTO equipment (
                name, 
                description, 
                image_path, 
                purchaseDate, 
                price, 
                status, 
                lastMaintenanceDate, 
                nextMaintenanceDate
            ) VALUES (
                :name, 
                :description, 
                :image_path, 
                :purchaseDate, 
                :price, 
                :status, 
                :lastMaintenanceDate, 
                :nextMaintenanceDate
            )";

            $stmt = $this->db->prepare($sql);
            
            $stmt->execute([
                ':name' => $data['name'],
                ':description' => $data['description'],
                ':image_path' => $data['image_path'] ?? null,
                ':purchaseDate' => $data['purchaseDate'],
                ':price' => $data['price'],
                ':status' => $data['status'],
                ':lastMaintenanceDate' => $data['lastMaintenanceDate'] ?? null,
                ':nextMaintenanceDate' => $data['nextMaintenanceDate'] ?? null
            ]);

            return $this->db->lastInsertId();

        } catch (\PDOException $e) {
            error_log("Equipment creation error: " . $e->getMessage());
            throw new \Exception("Không thể tạo thiết bị: " . $e->getMessage());
        }
    }

    public function update($id, $data)
    {
        try {
            $sql = "UPDATE {$this->table} 
                    SET name = :name,
                        description = :description,
                        purchaseDate = :purchaseDate,
                        price = :price,
                        status = :status,
                        lastMaintenanceDate = :lastMaintenanceDate,
                        nextMaintenanceDate = :nextMaintenanceDate,
                        updated_at = NOW()";

            if (isset($data['image_path']) && !empty($data['image_path'])) {
                $sql .= ", image_path = :image_path";
            }

            $sql .= " WHERE id = :id";

            $stmt = $this->db->prepare($sql);

            $params = [
                ':id' => $id,
                ':name' => trim($data['name']),
                ':description' => trim($data['description']),
                ':purchaseDate' => $data['purchaseDate'],
                ':price' => floatval($data['price']),
                ':status' => $data['status'],
                ':lastMaintenanceDate' => !empty($data['lastMaintenanceDate']) ? $data['lastMaintenanceDate'] : null,
                ':nextMaintenanceDate' => !empty($data['nextMaintenanceDate']) ? $data['nextMaintenanceDate'] : null
            ];

            if (isset($data['image_path']) && !empty($data['image_path'])) {
                $params[':image_path'] = trim($data['image_path']);
            }

            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error updating equipment: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $equipment = $this->findById($id);
            if (!$equipment) {
                return false;
            }

            $sql = "DELETE FROM {$this->table} WHERE id = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([':id' => $id]);

            if ($result && !empty($equipment['image_path'])) {
                $imagePath = ROOT_PATH . $equipment['image_path'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Error deleting equipment: " . $e->getMessage());
            return false;
        }
    }

    public function findById($id)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => (int)$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error finding equipment: " . $e->getMessage());
            return false;
        }
    }

    public function findAll()
    {
        try {
            $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC, id DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting all equipment: " . $e->getMessage());
            return [];
        }
    }

    public function findActiveEquipment()
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching active equipment: " . $e->getMessage());
            return [];
        }
    }

    public function getAllActiveEquipment()
    {
        try {
            // Temporarily remove status filter to check all equipment
            $sql = "SELECT * FROM {$this->table} ORDER BY name ASC";
            error_log("SQL Query: " . $sql);
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();
            error_log("Result count: " . count($result));
            return $result;
        } catch (PDOException $e) {
            error_log("Error fetching active equipment: " . $e->getMessage());
            return [];
        }
    }
}
