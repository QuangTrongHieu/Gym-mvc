<?php

namespace App\Models;

// Lớp MembershipRegistration kế thừa từ BaseModel
class MembershipRegistration extends BaseModel
{
    // Khai báo tên bảng trong cơ sở dữ liệu
    protected $table = 'membership_registrations';

    // Phương thức tạo bản ghi mới
    public function create($data)
    {
        // Gọi phương thức create của lớp cha
        return parent::create($data);
    }

    // Phương thức cập nhật bản ghi theo id
    public function update($id, $data)
    {
        // Gọi phương thức update của lớp cha
        return parent::update($id, $data);
    }

    // Phương thức tìm bản ghi theo id
    public function find($id)
    {
        // Gọi phương thức findById của lớp hiện tại
        return $this->findById($id);
    }

    // Phương thức xóa bản ghi theo id
    public function delete($id)
    {
        // Gọi phương thức delete của lớp cha
        return parent::delete($id);
    }

    // Phương thức cập nhật trạng thái của bản ghi
    public function updateStatus($id, $status)
    {
        // Gọi phương thức update của lớp hiện tại để cập nhật trường 'status'
        return $this->update($id, ['status' => $status]);
    }

    // Phương thức tìm bản ghi theo userId
    public function findByUserId($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE userId = :userId ORDER BY id DESC LIMIT 1");
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetch();
    }
}