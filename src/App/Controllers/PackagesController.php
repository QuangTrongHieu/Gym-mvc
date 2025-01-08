<?php

namespace App\Controllers;

use App\Models\Package;
use App\Models\Membership;

class PackagesController extends BaseController
{
    private $packageModel;
    private $membershipModel;

    public function __construct()
    {
        parent::__construct();
        $this->packageModel = new Package();
        $this->membershipModel = new Membership();
    }

    public function index()
    {
        $packages = $this->packageModel->findAll();
        $this->view('admin/packages/index', [
            'title' => 'Quản lý Gói tập',
            'packages' => $packages
        ]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'duration' => $_POST['duration'],
                'price' => $_POST['price'],
                'status' => $_POST['status']
            ];

            if ($this->packageModel->create($data)) {
                $_SESSION['success'] = 'Thêm gói tập thành công';
                $this->redirect('admin/packages');
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi thêm gói tập';
                $this->redirect('admin/packages/create');
            }
        }

        $this->view('admin/packages/create', [
            'title' => 'Thêm gói tập mới'
        ]);
    }

    public function edit($id)
    {
        $package = $this->packageModel->findById($id);

        if (!$package) {
            $_SESSION['error'] = 'Không tìm thấy gói tập';
            $this->redirect('admin/packages');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'duration' => $_POST['duration'],
                'price' => $_POST['price'],
                'status' => $_POST['status']
            ];

            if ($this->packageModel->update($id, $data)) {
                $_SESSION['success'] = 'Cập nhật gói tập thành công';
                $this->redirect('admin/packages');
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật gói tập';
            }
        }

        $this->view('admin/packages/edit', [
            'title' => 'Sửa gói tập',
            'package' => $package
        ]);
    }

    public function delete($id)
    {
        if ($this->packageModel->delete($id)) {
            $_SESSION['success'] = 'Xóa gói tập thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa gói tập';
        }
        $this->redirect('admin/packages');
    }

    public function listpackages()
    {
        $packages = $this->packageModel->findActivePackages();
        $this->view('packages/list', [
            'title' => 'Danh sách gói tập',
            'packages' => $packages
        ]);
    }

    public function register($id)
    {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để đăng ký gói tập';
            $this->redirect('login');
            return;
        }

        // Lấy thông tin gói tập
        $package = $this->packageModel->findById($id);
        if (!$package) {
            $_SESSION['error'] = 'Không tìm thấy gói tập';
            $this->redirect('packages');
            return;
        }

        // Kiểm tra trạng thái gói tập
        if ($package['status'] !== 'ACTIVE') {
            $_SESSION['error'] = 'Gói tập này hiện không khả dụng';
            $this->redirect('packages');
            return;
        }

        // Kiểm tra người dùng đã có gói tập active hoặc pending
        if ($this->membershipModel->hasActiveOrPendingMembership($_SESSION['user']['id'])) {
            $_SESSION['error'] = 'Bạn đã có gói tập đang hoạt động hoặc đang chờ xác nhận';
            $this->redirect('packages');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate dates
            $startDate = strtotime($_POST['start_date']);
            $endDate = strtotime($_POST['end_date']);
            $today = strtotime(date('Y-m-d'));

            if ($startDate < $today) {
                $_SESSION['error'] = 'Ngày bắt đầu không thể là ngày trong quá khứ';
                $this->redirect("membership/register/$id");
                return;
            }

            if ($endDate <= $startDate) {
                $_SESSION['error'] = 'Ngày kết thúc phải sau ngày bắt đầu';
                $this->redirect("membership/register/$id");
                return;
            }

            $data = [
                'user_id' => $_SESSION['user']['id'],
                'package_id' => $id,
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'payment_method' => $_POST['payment_method'],
                'status' => 'PENDING',
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->membershipModel->create($data)) {
                $_SESSION['success'] = 'Đăng ký gói tập thành công. Vui lòng đợi xác nhận từ admin.';
                $this->redirect('packages');
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi đăng ký gói tập';
                $this->redirect("membership/register/$id");
            }
        }

        $this->view('membership/register', [
            'package' => $package,
            'title' => 'Đăng ký gói tập'
        ]);
    }
}
