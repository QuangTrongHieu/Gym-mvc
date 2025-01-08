<?php

namespace App\Controllers;

use App\Models\MembershipRegistration;
use App\Models\Payment;

class MembershipRegistrationController
{
    protected $model;
    protected $paymentModel;

    public function __construct()
    {
        $this->model = new MembershipRegistration();
        $this->paymentModel = new Payment();
    }

    public function create($data)
    {
        try {
            // Validate dữ liệu đầu vào
            $this->validateRegistrationData($data);

            // Tạo payment record trước
            $paymentData = [
                'amount' => $data['amount'],
                'paymentMethod' => $data['paymentMethod'],
                'qrImage' => $data['qrImage'] ?? null,
                'refNo' => $data['refNo'] ?? null,
                'paymentStatus' => 'PENDING',
                'paymentDate' => date('Y-m-d H:i:s')
            ];
            
            $paymentId = $this->paymentModel->create($paymentData);

            // Tạo membership registration
            $registrationData = [
                'userId' => $data['userId'],
                'packageId' => $data['packageId'], 
                'startDate' => $data['startDate'],
                'endDate' => $data['endDate'],
                'freezeCount' => 0,
                'freezeDays' => 0,
                'status' => 'ACTIVE',
                'paymentId' => $paymentId
            ];

            return $this->model->create($registrationData);

        } catch (\Exception $e) {
            throw new \Exception("Lỗi khi tạo đăng ký: " . $e->getMessage());
        }
    }

    public function update($id, $data)
    {
        try {
            // Validate dữ liệu
            if (!$id || !$data) {
                throw new \Exception("Dữ liệu không hợp lệ");
            }

            // Kiểm tra xem đăng ký có tồn tại không
            $registration = $this->model->find($id);
            if (!$registration) {
                throw new \Exception("Không tìm thấy đăng ký");
            }

            return $this->model->update($id, $data);

        } catch (\Exception $e) {
            throw new \Exception("Lỗi khi cập nhật đăng ký: " . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            // Kiểm tra xem đăng ký có tồn tại không
            $registration = $this->model->find($id);
            if (!$registration) {
                throw new \Exception("Không tìm thấy đăng ký");
            }

            return $this->model->delete($id);

        } catch (\Exception $e) {
            throw new \Exception("Lỗi khi xóa đăng ký: " . $e->getMessage());
        }
    }

    public function find($id)
    {
        try {
            $registration = $this->model->find($id);
            if (!$registration) {
                throw new \Exception("Không tìm thấy đăng ký");
            }
            return $registration;

        } catch (\Exception $e) {
            throw new \Exception("Lỗi khi tìm đăng ký: " . $e->getMessage());
        }
    }

    private function validateRegistrationData($data)
    {
        if (!isset($data['userId']) || !isset($data['packageId']) || 
            !isset($data['startDate']) || !isset($data['endDate']) ||
            !isset($data['amount']) || !isset($data['paymentMethod'])) {
            throw new \Exception("Thiếu thông tin bắt buộc");
        }

        // Kiểm tra ngày bắt đầu phải nhỏ hơn ngày kết thúc
        if (strtotime($data['startDate']) >= strtotime($data['endDate'])) {
            throw new \Exception("Ngày bắt đầu phải nhỏ hơn ngày kết thúc");
        }

        // Có thể thêm các validation khác
    }
}