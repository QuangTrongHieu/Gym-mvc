<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Membership;
use App\Models\Package;
use Core\Database;

class MemberController extends BaseController
{
    private $userModel;
    private $membershipModel;
    private $packageModel;
    private $db;

    public function __construct()
    {
        parent::__construct();
        $this->checkRole(['ADMIN']);
        $this->db = Database::getInstance()->getConnection();
        $this->userModel = new User($this->db);
        $this->membershipModel = new Membership($this->db);
        $this->packageModel = new Package($this->db);
    }

    public function index()
    {
        try {
            // Ensure no active transaction from previous operations
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }

            $members = $this->membershipModel->findAll();
            $packages = $this->packageModel->findAll();

            $this->view('admin/member/index', [
                'title' => 'Quản lý Hội viên',
                'members' => $members,
                'packages' => $packages
            ], 'admin_layout');
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            $this->redirect('admin/member');
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Ensure no active transaction
                if ($this->db->inTransaction()) {
                    $this->db->rollback();
                }

                $data = [
                    'username' => $_POST['username'],
                    'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                    'fullName' => $_POST['fullName'],
                    'email' => $_POST['email'],
                    'phone' => $_POST['phone'],
                    'dateOfBirth' => $_POST['dateOfBirth'],
                    'sex' => $_POST['sex'],
                    'eRole' => 'USER',
                    'status' => 'ACTIVE'
                ];

                // Validate input
                if (
                    empty($data['username']) || empty($_POST['password']) || empty($data['fullName']) ||
                    empty($data['email']) || empty($data['phone'])
                ) {
                    throw new \Exception('Vui lòng điền đầy đủ thông tin bắt buộc');
                }

                // Validate email format
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception('Email không hợp lệ');
                }

                // Validate phone number
                if (!preg_match('/^[0-9]{10}$/', $data['phone'])) {
                    throw new \Exception('Số điện thoại không hợp lệ');
                }

                $this->db->beginTransaction();

                // Create user
                $userId = $this->userModel->create($data);
                if (!$userId) {
                    throw new \Exception('Không thể tạo tài khoản');
                }

                // Create membership if package is selected
                if (!empty($_POST['package_id'])) {
                    $package = $this->packageModel->findById($_POST['package_id']);
                    if (!$package) {
                        throw new \Exception('Không tìm thấy gói tập');
                    }

                    $membershipData = [
                        'userId' => $userId,
                        'packageId' => $_POST['package_id'],
                        'startDate' => date('Y-m-d'),
                        'endDate' => date('Y-m-d', strtotime('+' . $package['duration'] . ' months')),
                        'status' => 'ACTIVE',
                        'paymentMethod' => 'CASH',
                        'amount' => $package['price'],
                        'paymentStatus' => 'PENDING'
                    ];

                    if (!$this->membershipModel->create($membershipData)) {
                        throw new \Exception('Không thể tạo gói tập');
                    }
                }

                $this->db->commit();
                $_SESSION['success'] = 'Thêm thành viên thành công';
            } catch (\Exception $e) {
                if ($this->db->inTransaction()) {
                    $this->db->rollback();
                }
                $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            }

            $this->redirect('admin/member');
        }
    }

    public function edit($id)
    {
        try {
            // Ensure no active transaction
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }

            // Fetch member with validation
            $member = $this->userModel->findById($id);
            if (!$member || $member['eRole'] !== 'USER') {
                throw new \Exception('Không tìm thấy thành viên hoặc không có quyền truy cập');
            }

            $packages = $this->packageModel->findAll();
            $memberships = $this->membershipModel->findByUserId($id);
            $activeMembership = !empty($memberships) ? $memberships[0] : null;

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Validate required fields
                $requiredFields = ['fullName', 'email', 'phone'];
                foreach ($requiredFields as $field) {
                    if (empty($_POST[$field])) {
                        throw new \Exception('Vui lòng điền đầy đủ thông tin bắt buộc');
                    }
                }

                // Validate email and phone
                if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception('Email không hợp lệ');
                }

                if (!preg_match('/^[0-9]{10}$/', $_POST['phone'])) {
                    throw new \Exception('Số điện thoại không hợp lệ');
                }

                $this->db->beginTransaction();

                // Update user data
                $userData = [
                    'fullName' => $_POST['fullName'],
                    'email' => $_POST['email'],
                    'phone' => $_POST['phone'],
                    'status' => $_POST['status'] ?? $member['status'],
                    'dateOfBirth' => $_POST['dateOfBirth'],
                    'sex' => $_POST['sex']
                ];

                if (!$this->userModel->update($id, $userData)) {
                    throw new \Exception('Không thể cập nhật thông tin hội viên');
                }

                $this->db->commit();
                $_SESSION['success'] = 'Cập nhật thông tin thành công';
                $this->redirect('admin/member');
            }

            // Prepare member data for display
            if ($activeMembership) {
                $member['package_id'] = $activeMembership['packageId'];
                $member['startDate'] = date('Y-m-d', strtotime($activeMembership['startDate']));
                $member['endDate'] = date('Y-m-d', strtotime($activeMembership['endDate']));
                $member['membership_id'] = $activeMembership['id'];
            }

            $this->view('admin/member/edit', [
                'title' => 'Chỉnh sửa thông tin hội viên',
                'member' => $member,
                'packages' => $packages
            ]);
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            $this->redirect('admin/member');
        }
    }

    public function delete($id)
    {
        try {
            // Ensure no active transaction
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }

            // Fetch and validate member
            $member = $this->userModel->findById($id);
            if (!$member || $member['eRole'] !== 'USER') {
                throw new \Exception('Không tìm thấy hội viên hoặc không có quyền xóa');
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->db->beginTransaction();

                // Delete related records first
                $memberships = $this->membershipModel->findByUserId($id);
                foreach ($memberships as $membership) {
                    if (!$this->membershipModel->delete($membership['id'])) {
                        throw new \Exception('Không thể xóa thông tin gói tập');
                    }
                }

                // Finally delete the user
                if (!$this->userModel->delete($id)) {
                    throw new \Exception('Không thể xóa thông tin hội viên');
                }

                $this->db->commit();
                $_SESSION['success'] = 'Xóa hội viên thành công';
                $this->redirect('admin/member');
            }

            $this->view('admin/member/delete', [
                'title' => 'Xóa hội viên',
                'member' => $member
            ]);
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            $this->redirect('admin/member');
        }
    }
}
