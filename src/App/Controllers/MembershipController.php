<?php

namespace App\Controllers;

use App\Models\Membership;
use App\Models\MembershipRegistration;
use App\Models\Package;
use App\Models\Payment;

class MembershipController extends BaseController
{
    private $membershipModel;
    private $packageModel;
    private $paymentModel;
    private $trainerModel;

    public function __construct()
    {
        parent::__construct();
        $this->membershipModel = new Membership();
        $this->packageModel = new Package();
        $this->paymentModel = new Payment();
        $this->trainerModel = new Trainer();
    }

    public function index()
    {
        $memberships = $this->membershipModel->findAll();
        $this->view('membership/index', [
            'title' => 'Quản lý Hội viên',
            'memberships' => $memberships
        ]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'userId' => $_POST['userId'],
                'packageId' => $_POST['packageId'],
                'startDate' => $_POST['startDate'],
                'endDate' => $_POST['endDate'],
                'status' => $_POST['status'],
                'paymentId' => $_POST['paymentId']
            ];
            $this->membershipModel->create($data);
            // Có thể thêm logic điều hướng hoặc thông báo sau khi tạo
        }
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'userId' => $_POST['userId'],
                'packageId' => $_POST['packageId'],
                'startDate' => $_POST['startDate'],
                'endDate' => $_POST['endDate'],
                'status' => $_POST['status'],
                'paymentId' => $_POST['paymentId']
            ];
            $this->membershipModel->update($id, $data);
            // Có thể thêm logic điều hướng hoặc thông báo sau khi cập nhật
        }
        // Có thể thêm logic để lấy thông tin hội viên hiện tại và hiển thị
    }

    public function delete($id)
    {
        try {
            // Kiểm tra xem hội viên có tồn tại không
            $membership = $this->membershipModel->find($id);
            if (!$membership) {
                $_SESSION['error'] = 'Không tìm thấy hội viên này';
                header('Location: /membership');
                return;
            }

            // Thực hiện xóa hội viên
            $this->membershipModel->delete($id);
            
            // Thêm thông báo thành công vào session
            $_SESSION['success'] = 'Đã xóa hội viên thành công';
            
            // Chuyển hướng về trang danh sách
            header('Location: /membership');
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa hội viên: ' . $e->getMessage();
            header('Location: /membership');
        }
    }

    public function show($id)
    {
        $membershipRegistrationModel = new MembershipRegistration();
        $membership = $membershipRegistrationModel->find($id);
        
        if (!$membership) {
            $_SESSION['error'] = 'Không tìm thấy hội viên này';
            header('Location: /membership');
            return;
        }

        $this->view('membership/show', [
            'title' => 'Chi tiết hội viên',
            'membership' => $membership
        ]);
    }

    // Hiển thị form đăng ký gói tập
    public function register($id)
    {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để đăng ký gói tập';
            header('Location: /gym/login');
            exit;
        }

        // Lấy thông tin gói tập
        $package = $this->packageModel->findById($id);
        if (!$package) {
            $_SESSION['error'] = 'Không tìm thấy gói tập';
            header('Location: /gym/packages');
            exit;
        }

        // Kiểm tra xem user đã có gói tập active chưa
        $activeMembership = $this->membershipModel->findActiveByUserId($_SESSION['user_id']);
        if ($activeMembership) {
            $_SESSION['error'] = 'Bạn đã có gói tập đang hoạt động';
            header('Location: /gym/packages');
            exit;
        }

        // Lấy danh sách huấn luyện viên
        $trainers = $this->trainerModel->findAll();

        // Lấy thông tin user
        $user = [
            'id' => $_SESSION['user_id'],
            'fullName' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'phone' => $_SESSION['user_phone']
        ];
        
        $this->view('membership/register', [
            'title' => 'Đăng ký gói tập',
            'package' => $package,
            'user' => $user,
            'trainers' => $trainers
        ]);
    }

    // Xử lý đăng ký gói tập
    public function processRegistration()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /gym/packages');
            exit;
        }

        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để đăng ký gói tập';
            header('Location: /gym/login');
            exit;
        }

        $packageId = $_POST['package_id'] ?? null;
        $trainerId = $_POST['trainer_id'] ?? null;

        if (!$packageId || !$trainerId) {
            $_SESSION['error'] = 'Thiếu thông tin đăng ký';
            header('Location: /gym/membership/register/' . $packageId);
            exit;
        }

        $package = $this->packageModel->findById($packageId);
        if (!$package) {
            $_SESSION['error'] = 'Không tìm thấy gói tập';
            header('Location: /gym/packages');
            exit;
        }

        // Tạo đăng ký mới
        $registrationData = [
            'user_id' => $_SESSION['user']['id'],
            'package_id' => $packageId,
            'trainer_id' => $trainerId,
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime('+' . $package['duration'] . ' months')),
            'status' => 'PENDING',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $registrationId = $this->membershipModel->create($registrationData);
        if ($registrationId) {
            $_SESSION['success'] = 'Đăng ký gói tập thành công. Vui lòng chờ xác nhận.';
            header('Location: /gym/membership/my-memberships');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra. Vui lòng thử lại.';
            header('Location: /gym/membership/register/' . $packageId);
        }
        exit;
    }

    // Xem danh sách đăng ký của người dùng
    public function myMemberships()
    {
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để xem gói tập';
            header('Location: /gym/login');
            exit;
        }

        $memberships = $this->membershipModel->findByUserId($_SESSION['user']['id']);
        
        $this->view('membership/my-memberships', [
            'title' => 'Gói tập của tôi',
            'memberships' => $memberships
        ]);
    }

    // Hủy đăng ký gói tập
    public function cancel($id)
    {
        try {
            $membership = $this->membershipModel->find($id);
            
            if (!$membership || $membership['userId'] != $_SESSION['user']['id']) {
                throw new \Exception('Không tìm thấy đăng ký này');
            }

            if ($membership['status'] !== 'PENDING') {
                throw new \Exception('Chỉ có thể hủy đăng ký đang chờ xác nhận');
            }

            $this->membershipModel->update($id, ['status' => 'CANCELLED']);
            $_SESSION['success'] = 'Hủy đăng ký thành công';

        } catch (\Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }

        header('Location: /gym/membership/my-memberships');
    }

    // Admin: Xem danh sách đăng ký
    public function adminIndex()
    {
        if (!isset($_SESSION['admin'])) {
            header('Location: /gym/admin-login');
            return;
        }

        $memberships = $this->membershipModel->findAllWithDetails();
        $this->view('admin/membership/index', [
            'title' => 'Quản lý đăng ký gói tập',
            'memberships' => $memberships
        ]);
    }

    // Admin: Xem chi tiết đăng ký
    public function view($id)
    {
        if (!isset($_SESSION['admin'])) {
            header('Location: /gym/admin-login');
            return;
        }

        $membership = $this->membershipModel->findWithDetails($id);
        if (!$membership) {
            $_SESSION['error'] = 'Không tìm thấy đăng ký này';
            header('Location: /gym/admin/memberships');
            return;
        }

        $this->view('admin/membership/view', [
            'title' => 'Chi tiết đăng ký',
            'membership' => $membership
        ]);
    }

    // Admin: Phê duyệt đăng ký
    public function approve($id)
    {
        if (!isset($_SESSION['admin'])) {
            header('Location: /gym/admin-login');
            return;
        }

        try {
            $membership = $this->membershipModel->find($id);
            if (!$membership) {
                throw new \Exception('Không tìm thấy đăng ký này');
            }

            $this->membershipModel->update($id, ['status' => 'ACTIVE']);
            $this->paymentModel->update($membership['paymentId'], ['paymentStatus' => 'COMPLETED']);
            
            $_SESSION['success'] = 'Phê duyệt đăng ký thành công';

        } catch (\Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }

        header('Location: /gym/admin/memberships');
    }

    // Admin: Từ chối đăng ký
    public function reject($id)
    {
        if (!isset($_SESSION['admin'])) {
            header('Location: /gym/admin-login');
            return;
        }

        try {
            $membership = $this->membershipModel->find($id);
            if (!$membership) {
                throw new \Exception('Không tìm thấy đăng ký này');
            }

            $this->membershipModel->update($id, ['status' => 'REJECTED']);
            $this->paymentModel->update($membership['paymentId'], ['paymentStatus' => 'CANCELLED']);
            
            $_SESSION['success'] = 'Từ chối đăng ký thành công';

        } catch (\Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }

        header('Location: /gym/admin/memberships');
    }
}