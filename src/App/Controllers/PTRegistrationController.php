<?php

namespace App\Controllers;

use App\Models\PTRegistration;
use App\Models\User;

class PTRegistrationController
{
    protected $model;
    protected $userModel;

    public function __construct()
    {
        $this->model = new PTRegistration();
        $this->userModel = new User();
    }

    public function getAll()
    {
        return $this->model->getAll();
    }

    public function create($data)
    {
        // Kiểm tra dữ liệu đầu vào
        if (empty($data['user_id']) || empty($data['pt_id'])) {
            throw new \Exception('Thiếu thông tin cần thiết để đăng ký PT.');
        }

        // Xác thực người dùng
        $user = $this->userModel->findById($data['user_id']);
        if (!$user) {
            throw new \Exception('Người dùng không tồn tại.');
        }

        // Kiểm tra xem người dùng đã đăng ký PT này chưa
        $existingRegistration = $this->model->findByUserIdAndPTId($data['user_id'], $data['pt_id']);
        if ($existingRegistration) {
            throw new \Exception('Người dùng đã đăng ký PT này.');
        }

        // Logic để tạo đăng ký PT mới
        return $this->model->create($data);
    }

    public function update($id, $data)
    {
        // Logic để cập nhật đăng ký PT
        return $this->model->update($id, $data);
    }

    public function delete($id)
    {
        // Logic để xóa đăng ký PT
        return $this->model->delete($id);
    }

    public function find($id)
    {
        // Logic để lấy thông tin đăng ký PT
        return $this->model->findById($id);
    }
}
