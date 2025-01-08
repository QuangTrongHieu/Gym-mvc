<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Trainer;
use Exception;

class AuthController extends BaseController
{
    private $userModel;
    private $adminModel;
    private $trainerModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->adminModel = new Admin();
        $this->trainerModel = new Trainer();
    }

    // Login cho user thường
    public function login()
    {
        if ($this->auth->isLoggedIn()) {
            $this->redirect('');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';

                $user = $this->userModel->findByUsername($username);

                if (!$user || !password_verify($password, $user['password'])) {
                    throw new Exception('Thông tin đăng nhập không đúng');
                }

                if ($user['eRole'] !== 'USER') {
                    throw new Exception('Tài khoản không hợp lệ');
                }

                // Thực hiện login và kiểm tra kết quả
                $loginSuccess = $this->auth->login($user['id'], $user['username'], $user['avatar'], 'USER');



                // Đảm bảo session được ghi trước khi redirect
                session_write_close();

                // Thêm độ trễ nhỏ để đảm bảo session được ghi
                usleep(100000); // 0.1 giây

                $this->redirect('');
            } catch (Exception $e) {
                $this->view('login', ['error' => $e->getMessage()]);
                return;
            }
        }

        $this->view('login');
    }

    // Login cho admin
    public function adminLogin()
    {
        if ($this->auth->isLoggedIn()) {
            $this->redirect('admin');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';

                $admin = $this->adminModel->findByUsername($username);

                if (!$admin) {
                    throw new Exception('Thông tin đăng nhập không đúng');
                }

                if (!password_verify($password, $admin['password'])) {
                    throw new Exception('Thông tin đăng nhập không đúng');
                }

                if ($admin['eRole'] !== 'ADMIN') {
                    throw new Exception('Bạn không có quyền truy cập');
                }

                $this->auth->login($admin['id'], $admin['username'], null, 'ADMIN');
                $this->redirect('admin');
            } catch (Exception $e) {
                $this->view('admin/login', ['error' => $e->getMessage()], 'default_layout');
                return;
            }
        }

        $this->view('admin/login', [], 'default_layout');
    }

    // Login cho Trainer 
    public function trainerLogin()
    {
        if ($this->auth->isLoggedIn()) {
            $this->redirect('trainer/dashboard');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';

                $trainer = $this->trainerModel->findByUsername($username);

                if (!$trainer) {
                    throw new Exception('Thông tin đăng nhập không đúng');
                }

                if (!password_verify($password, $trainer['password'])) {
                    throw new Exception('Thông tin đăng nhập không đúng');
                }

                // Set session data
                $_SESSION['trainer_id'] = $trainer['id'];
                $_SESSION['trainer_name'] = $trainer['fullName'];
                $_SESSION['trainer_role'] = 'TRAINER';
                $_SESSION['trainer_avatar'] = $trainer['avatar'];

                $this->redirect('trainer/dashboard');
            } catch (Exception $e) {
                $this->view('Trainer/login', [
                    'error' => $e->getMessage()
                ]);
                return;
            }
        }

        // Show login form
        $this->view('Trainer/login', [], 'default_layout');
    }

    // Đăng ký chỉ dành cho user thường
    public function register()
    {
        if ($this->auth->isLoggedIn()) {
            $this->redirect('');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'username' => $_POST['username'] ?? '',
                    'fullName' => $_POST['fullName'] ?? '',
                    'email' => $_POST['email'] ?? '',
                    'phone' => $_POST['phone'] ?? '',
                    'dateOfBirth' => $_POST['dateOfBirth'] ?? '',
                    'sex' => $_POST['sex'] ?? '',
                    'password' => $_POST['password'] ?? '',
                    'confirmPassword' => $_POST['confirmPassword'] ?? '',
                    'eRole' => 'USER'
                ];

                $this->validateRegistrationData($data);

                // Kiểm tra username đã tồn tại
                if ($this->userModel->findByUsername($data['username'])) {
                    throw new Exception('Tên đăng nhập đã tồn tại');
                }

                // Kiểm tra email đã tồn tại
                if ($this->userModel->findByEmail($data['email'])) {
                    throw new Exception('Email đã được sử dụng');
                }

                // Kiểm tra số điện thoại đã tồn tại
                if ($this->userModel->findByPhone($data['phone'])) {
                    throw new Exception('Số điện thoại đã được sử dụng');
                }

                // Hash password và tạo user
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                // Bỏ confirmPassword
                unset($data['confirmPassword']);

                // Tạo tài khoản
                $userId = $this->userModel->create($data);
                $this->auth->login($userId, $data['username'], null, 'USER');
                $this->redirect('');
            } catch (Exception $e) {
                // Hiển thị form với thông báo lỗi và giữ lại dữ liệu cũ
                $this->view('register', [
                    'error' => $e->getMessage(),
                    'old' => $_POST
                ]);
                return;
            }
        }

        // Hiển thị form đăng ký
        $this->view('register');
    }

    // Logout chung cho tất cả role
    public function logout()
    {
        $userRole = $this->auth->getUserRole();
        $this->auth->logout();

        switch ($userRole) {
            case 'ADMIN':
                $this->redirect('');
                break;
            case 'TRAINNER':
                $this->redirect('');
                break;
            default:
                $this->redirect('');
        }
    }

    // Validation helper
    private function validateRegistrationData($data)
    {
        // Validate các trường bắt buộc
        $requiredFields = ['username', 'fullName', 'email', 'phone', 'dateOfBirth', 'sex', 'password', 'confirmPassword'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception('Vui lòng điền đầy đủ thông tin');
            }
        }

        // Validate username
        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $data['username'])) {
            throw new Exception('Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới, độ dài 3-20 ký tự');
        }

        // Validate email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email không hợp lệ');
        }

        // Validate phone
        if (!preg_match('/^[0-9]{10,11}$/', $data['phone'])) {
            throw new Exception('Số điện thoại không hợp lệ');
        }

        // Validate password
        if (strlen($data['password']) < 6) {
            throw new Exception('Mật khẩu phải có ít nhất 6 ký tự');
        }

        // Validate confirm password
        if ($data['password'] !== $data['confirmPassword']) {
            throw new Exception('Xác nhận mật khẩu không khớp');
        }

        // Validate date of birth
        $dob = strtotime($data['dateOfBirth']);
        if (!$dob) {
            throw new Exception('Ngày sinh không hợp lệ');
        }

        // Validate sex
        if (!in_array($data['sex'], ['Male', 'Female', 'Other'])) {
            throw new Exception('Giới tính không hợp lệ');
        }
    }
}
