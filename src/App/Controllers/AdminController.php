<?php

namespace App\Controllers;

use App\Models\Admin;
use App\Models\Package;
use App\Models\User;
use App\Models\Trainer;
use App\Models\Schedule;
use Core\View;

class AdminController extends BaseController
{
    private $model;
    private $packageModel;
    private $userModel;
    private $trainerModel;
    private $scheduleModel;

    public function __construct()
    {
        parent::__construct();
        $this->checkRole(['ADMIN']);
        $this->model = new Admin();
        $this->packageModel = new Package();
        $this->userModel = new User();
        $this->trainerModel = new Trainer();
        $this->scheduleModel = new Schedule();
    }

    public function index()
    {
        // Điều hướng từ /admin sang /admin/dashboard
        $this->redirect('admin/dashboard');
    }

    public function dashboard()
    {
        $packageStatistics = $this->getRevenueData();
        $monthlyRevenue = $this->packageModel->getMonthlyRevenue();

        // Format data for the chart
        $chartData = [
            'labels' => ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 
                        'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
            'revenue' => array_map(function($item) { return $item['total_revenue']; }, $monthlyRevenue),
            'users' => array_map(function($item) { return $item['total_users']; }, $monthlyRevenue)
        ];

        $content = '<div class="container mt-4">
            <h1>Chào mừng đến với Trang quản trị</h1>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Quản lý Admin</h5>
                            <p class="card-text">Quản lý tài khoản admin của hệ thống</p>
                            <a href="/admin/admin-management" class="btn btn-primary">Truy cập</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Quản lý Nhân viên</h5>
                            <p class="card-text">Quản lý thông tin nhân viên</p>
                            <a href="/gym/admin/Trainer" class="btn btn-primary">Truy cập</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Theo dõi Doanh thu</h5>
                            <p class="card-text">Theo dõi doanh thu từ các gói tập</p>
                            <a href="/admin/revenue" class="btn btn-primary">Truy cập</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

        // Render view với layout dashboard
        $this->view('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'content' => $content,
            'revenueData' => $packageStatistics,
            'monthlyRevenue' => $chartData
        ]);
    }

    private function getRevenueData()
    {
        return $this->packageModel->getPackageStatistics();
    }

    public function adminManagement()
    {
        $admins = $this->model->findAll();
        $this->view('admin/AdminManagement/index', [
            'title' => 'Quản lý Admin',
            'admins' => $admins
        ]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                    'eRole' => 'ADMIN'
                ];

                $this->model->create($data);
                $_SESSION['success'] = 'Thêm admin thành công';
            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            header('Location: /gym/admin/admin-management');
            exit;
        }
        header('Location: /gym/admin/admin-management');
        exit;
    }

    public function edit($id)
    {
        $admin = $this->model->findById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'username' => $_POST['username'],
                    'email' => $_POST['email']
                ];

                if (!empty($_POST['password'])) {
                    $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                }

                $this->model->update($id, $data);
                $_SESSION['success'] = 'Cập nhật admin thành công';
            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            header('Location: /gym/admin/admin-management');
            exit;
        }
        header('Location: /gym/admin/admin-management');
        exit;
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->model->delete($id);
                $_SESSION['success'] = 'Xóa admin thành công';
            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            header('Location: /gym/admin/admin-management');
            exit;
        }
    }

    public function getUserStats()
    {
        try {
            $memberCount = $this->userModel->getCountByRole('member');
            $trainerCount = $this->userModel->getCountByRole('trainer');
            $userCount = $this->userModel->getCountByRole('user');

            header('Content-Type: application/json');
            echo json_encode([
                'members' => [$memberCount],
                'trainers' => [$trainerCount],
                'users' => [$userCount]
            ]);
        } catch (\Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
