<?php

namespace App\Models;

class PTRegistration extends BaseModel
{
    protected $table = 'pt_registrations';

    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $data['createdAt'] = date('Y-m-d H:i:s');
        return parent::create($data);
    }

    public function update($id, $data)
    {
        $data['updatedAt'] = date('Y-m-d H:i:s');
        return parent::update($id, $data);
    }

    public function delete($id)
    {
        return parent::delete($id);
    }

    public function getAll()
    {
        $sql = "SELECT * FROM pt_registrations";
        return $this->db->query($sql)->fetchAll();
    }

    public function findByUserIdAndPTId($userId, $ptId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id AND pt_id = :pt_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':pt_id' => $ptId
        ]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getClientsByTrainerId($trainerId)
    {
        $sql = "SELECT u.* FROM users u 
                JOIN pt_registrations pr ON u.id = pr.user_id 
                WHERE pr.pt_id = :trainer_id AND pr.status = 'active'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['trainer_id' => $trainerId]);
        return $stmt->fetchAll();
    }
}
