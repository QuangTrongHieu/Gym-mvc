<?php

namespace App\Controllers;

use App\Models\Trainer;
use App\Models\Equipment;
use App\Models\User;

class HomeController extends BaseController
{
    private $trainerModel;
    private $equipmentModel;
    private $userModel;

    public function __construct()
    {
        $this->trainerModel = new Trainer();
        $this->equipmentModel = new Equipment();
        $this->userModel = new User();
    }

    public function index()
    {
        try {
            // Khởi tạo dữ liệu mặc định
            $data = [];

            // Xử lý phân trang
            $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
            $limit = 12;
            $offset = ($page - 1) * $limit;

            // Lấy tổng số trainer
            $totalTrainers = $this->trainerModel->count();


            // Tính tổng số trang
            $totalPages = max(1, ceil($totalTrainers / $limit));
            $page = min($page, $totalPages);

            // Thêm thông tin người dùng vào data
            $data['user'] = [
                'isLoggedIn' => isset($_SESSION['user_id']),
                'name' => $_SESSION['user_name'] ?? null,
                'avatar' => $_SESSION['avatar'] ?? null
            ];

            // Render view với dữ liệu
            $this->view('home/index', $data);
        } catch (\Exception $e) {
            // Log lỗi chi tiết
            echo '<script>console.error("Error:", ' . json_encode($e->getMessage()) . ');</script>';
            error_log("Home page error: " . $e->getMessage());

            // Render view với thông báo lỗi
            $this->view('home/index', [
                'title' => 'Trang chủ',
                'error' => 'Có lỗi xảy ra khi tải dữ liệu'
            ]);
        }
    }
}
