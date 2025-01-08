<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Address;
use App\Models\Membership;
use App\Models\Package;
use Core\Database;
use Core\Helpers\FileUploader;
use Exception;

class UserController extends BaseController
{
    private $userModel;
    private $addressModel;
    private $membershipModel;
    private $packageModel;
    private $db;
    private $uploader;
    private const UPLOAD_CONFIG = [
        'dir' => 'public/uploads/members/avatars',
        'max_size' => 5 * 1024 * 1024,
        'allowed_types' => [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp'
        ]
    ];

    private const DEFAULT_AVATAR = '/gym/public/assets/images/default-avatar.png';

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance()->getConnection();
        $this->userModel = new User($this->db);
        $this->addressModel = new Address($this->db);
        $this->membershipModel = new Membership($this->db);
        $this->packageModel = new Package($this->db);
        $this->uploader = new FileUploader();

        // Ensure upload directory exists
        $uploadPath = ROOT_PATH . '/' . self::UPLOAD_CONFIG['dir'];
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
    }

    public function index()
    {
        if ($this->auth->isLoggedIn()) {
            $userId = $this->auth->getUserId();
            $user = $this->userModel->findById($userId);

            if (!$user) {
                $_SESSION['error'] = 'Không tìm thấy thông tin người dùng';
                $this->redirect('/login');
            }

            // Get user's active membership if exists
            $memberships = $this->membershipModel->findByUserId($userId);
            $activeMembership = !empty($memberships) ? $memberships[0] : null;

            // Get package details if membership exists
            if ($activeMembership) {
                $package = $this->packageModel->findById($activeMembership['packageId']);
                $user['package'] = $package;
                $user['membership'] = $activeMembership;
            }

            $this->view('user/account/profile', [
                'title' => 'Hồ sơ của tôi',
                'user' => $user,
                'username' => $user['username'],
                'fullName' => $user['fullName'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'sex' => $user['sex'],
                'dateOfBirth' => $user['dateOfBirth'],
                'avatarUrl' => $this->getAvatarUrl($user['avatar'])
            ]);
        } else {
            $this->redirect('login');
        }
    }

    public function adminIndex()
    {
        try {
            $this->checkRole(['ADMIN']);
            $this->ensureTransactionClosed();

            // Get all members with role USER
            $members = $this->userModel->findByRole('USER');

            // Get active memberships and packages for each member
            foreach ($members as &$member) {
                // Get member's active membership
                $memberships = $this->membershipModel->findByUserId($member['id']);
                $member['membership'] = !empty($memberships) ? $memberships[0] : null;

                // Get package details if membership exists
                if ($member['membership']) {
                    $package = $this->packageModel->findById($member['membership']['packageId']);
                    $member['package'] = $package;
                }

                // Get avatar URL
                $member['avatarUrl'] = $this->getAvatarUrl($member['avatar']);
            }

            // Get all packages for the create form
            $packages = $this->packageModel->findAll();

            $this->view('admin/member/index', [
                'title' => 'Quản lý Hội viên',
                'members' => $members,
                'packages' => $packages,
                'defaultAvatarBase64' => self::DEFAULT_AVATAR
            ], 'admin_layout');
        } catch (\Exception $e) {
            $this->handleError($e, 'admin/member');
        }
    }

    private function handleImageUpload($file, $oldAvatar = null)
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('Lỗi khi tải file lên');
        }

        // Validate file size
        if ($file['size'] > self::UPLOAD_CONFIG['max_size']) {
            throw new \Exception('File quá lớn. Giới hạn ' . (self::UPLOAD_CONFIG['max_size'] / 1024 / 1024) . 'MB');
        }

        // Get file extension from MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!array_key_exists($mimeType, self::UPLOAD_CONFIG['allowed_types'])) {
            throw new \Exception('Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP)');
        }

        // Create upload directory if not exists
        $uploadDir = ROOT_PATH . '/' . self::UPLOAD_CONFIG['dir'];
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Delete old avatar
        if ($oldAvatar && $oldAvatar !== basename(self::DEFAULT_AVATAR)) {
            $oldAvatarPath = $uploadDir . '/' . $oldAvatar;
            if (file_exists($oldAvatarPath)) {
                unlink($oldAvatarPath);
            }
        }

        // Generate new filename
        $extension = self::UPLOAD_CONFIG['allowed_types'][$mimeType];
        $fileName = 'avatar_' . uniqid() . '.' . $extension;
        $targetPath = $uploadDir . '/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new \Exception('Không thể lưu file. Vui lòng kiểm tra quyền thư mục');
        }

        chmod($targetPath, 0644);
        return $fileName;
    }

    private function getAvatarUrl($avatar)
    {
        if (empty($avatar)) {
            return '/gym/public/assets/images/default-avatar.png';
        }
        return '/gym/public/uploads/members/avatars/' . $avatar;
    }

    // User Profile Methods
    public function profile()
    {
        $this->requireLogin();

        $userId = $this->auth->getUserId();
        $user = $this->userModel->findById($userId);

        if (!$user) {
            $_SESSION['error'] = 'Không tìm thấy thông tin người dùng';
            $this->redirect('/login');
        }

        $this->view('user/account/profile', [
            'title' => 'Hồ sơ của tôi',
            'user' => $user,
            'username' => $user['username'],
            'fullName' => $user['fullName'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'sex' => $user['sex'],
            'dateOfBirth' => $user['dateOfBirth'],
            'avatarUrl' => $this->getAvatarUrl($user['avatar'])
        ]);
    }

    public function updateProfile()
    {
        try {
            if (!$this->isPost()) {
                throw new \Exception('Phương thức không được hỗ trợ');
            }

            $userId = $_POST['id'] ?? null;
            if (!$userId) {
                throw new \Exception('ID không hợp lệ');
            }

            // Lấy thông tin user hiện tại
            $user = $this->userModel->findById($userId);
            if (!$user) {
                throw new \Exception('Không tìm thấy thông tin người dùng');
            }

            // Chuẩn bị dữ liệu cập nhật
            $data = [
                'id' => $userId,
                'fullName' => $_POST['fullName'] ?? $user['fullName'],
                'email' => $_POST['email'] ?? $user['email'],
                'phone' => $_POST['phone'] ?? $user['phone'],
                'sex' => $_POST['sex'] ?? $user['sex'],
                'dateOfBirth' => $_POST['dateOfBirth'] ?? $user['dateOfBirth']
            ];

            // Xử lý upload ảnh nếu có
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
                try {
                    $avatarFileName = $this->handleImageUpload($_FILES['avatar'], $user['avatar'] ?? null);
                    if ($avatarFileName !== false) {
                        $data['avatar'] = $avatarFileName;
                    }
                } catch (\Exception $e) {
                    $_SESSION['error'] = 'Lỗi upload ảnh: ' . $e->getMessage();
                    $this->redirect('/profile');
                    return;
                }
            }

            // Xử lý mật khẩu nếu được cung cấp
            if (!empty($_POST['password'])) {
                $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            // Cập nhật thông tin
            if ($this->userModel->update($data)) {
                $_SESSION['success'] = 'Cập nhật thông tin thành công';
                $this->redirect('/profile');
            } else {
                throw new \Exception('Cập nhật thông tin thất bại');
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/profile');
        }
    }

    public function adminMembers()
    {
        $this->checkRole(['ADMIN']);

        try {
            $this->ensureTransactionClosed();

            $members = $this->membershipModel->findAll();
            $packages = $this->packageModel->findAll();

            $this->view('admin/member/index', [
                'title' => 'Quản lý Hội viên',
                'members' => $members,
                'packages' => $packages
            ]);
        } catch (\Exception $e) {
            $this->handleError($e, 'admin/member');
        }
    }

    public function createMember()
    {
        $this->checkRole(['ADMIN']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->ensureTransactionClosed();
                $this->db->beginTransaction();

                $data = $this->validateMemberData($_POST, true);

                // Handle avatar upload
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $data['avatar'] = $this->handleImageUpload($_FILES['avatar']);
                }

                // Create user
                $userId = $this->userModel->create($data);
                if (!$userId) {
                    throw new \Exception('Không thể tạo tài khoản');
                }

                // Create membership if package selected
                if (!empty($_POST['package_id'])) {
                    $this->createMembership($userId, $_POST['package_id']);
                }

                $this->db->commit();
                $_SESSION['success'] = 'Thêm thành viên thành công';
            } catch (\Exception $e) {
                $this->handleError($e, 'admin/member');
            }

            $this->redirect('admin/member');
        }
    }

    public function editMember($id)
    {
        $this->checkRole(['ADMIN']);

        try {
            $this->ensureTransactionClosed();

            $member = $this->userModel->findById($id);
            if (!$member) {
                throw new \Exception('Không tìm thấy thông tin hội viên');
            }

            // Lấy thông tin gói tập và membership hiện tại
            $memberships = $this->membershipModel->findByUserId($id);
            $activeMembership = !empty($memberships) ? $memberships[0] : null;
            $packages = $this->packageModel->findAll();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->db->beginTransaction();

                $data = $this->validateMemberData($_POST, false);

                // Handle avatar upload
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $data['avatar'] = $this->handleImageUpload($_FILES['avatar'], $member['avatar']);
                }

                // Cập nhật thông tin hội viên
                if (!$this->userModel->update($id, $data)) {
                    throw new \Exception('Không thể cập nhật thông tin hội viên');
                }

                // Cập nhật hoặc tạo mới membership nếu có chọn gói tập
                if (!empty($_POST['package_id'])) {
                    if ($activeMembership) {
                        // Cập nhật membership hiện tại
                        $membershipData = [
                            'packageId' => $_POST['package_id'],
                            'startDate' => $_POST['startDate'] ?? date('Y-m-d'),
                            'endDate' => $_POST['endDate'] ?? null,
                            'status' => $_POST['membership_status'] ?? 'ACTIVE'
                        ];
                        if (!$this->membershipModel->update($activeMembership['id'], $membershipData)) {
                            throw new \Exception('Không thể cập nhật thông tin gói tập');
                        }
                    } else {
                        // Tạo membership mới
                        $this->createMembership($id, $_POST['package_id']);
                    }
                }

                $this->db->commit();
                $_SESSION['success'] = 'Cập nhật thông tin thành công';
                $this->redirect('admin/member');
                return;
            }

            // Chuẩn bị dữ liệu cho form
            $member['package_id'] = $activeMembership['packageId'] ?? null;
            $member['membership_status'] = $activeMembership['status'] ?? null;
            $member['startDate'] = $activeMembership ? date('Y-m-d', strtotime($activeMembership['startDate'])) : null;
            $member['endDate'] = $activeMembership ? date('Y-m-d', strtotime($activeMembership['endDate'])) : null;

            $this->view('admin/member/edit', [
                'title' => 'Chỉnh sửa thông tin hội viên',
                'member' => $member,
                'packages' => $packages,
                'membership' => $activeMembership
            ]);

        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('admin/member');
        }
    }

    public function deleteMember($id)
    {
        $this->checkRole(['ADMIN']);

        try {
            $this->ensureTransactionClosed();

            $member = $this->userModel->findById($id);
            if (!$member) {
                throw new \Exception('Không tìm thấy hội viên');
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->db->beginTransaction();

                // Delete related memberships
                $memberships = $this->membershipModel->findByUserId($id);
                foreach ($memberships as $membership) {
                    if (!$this->membershipModel->delete($membership['id'])) {
                        throw new \Exception('Không thể xóa thông tin gói tập');
                    }
                }

                // Delete avatar file
                $this->deleteAvatarFile($member['avatar']);

                // Delete user
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
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('admin/member');
        }
    }

    protected function requireLogin()
    {
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('/login');
        }
    }

    protected function requirePostMethod()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('user/profile');
        }
    }

    protected function ensureTransactionClosed()
    {
        if ($this->db->inTransaction()) {
            $this->db->rollback();
        }
    }

    protected function handleError(\Exception $e, $redirectPath = null)
    {
        if ($this->db->inTransaction()) {
            $this->db->rollback();
        }
        $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        if ($redirectPath) {
            $this->redirect($redirectPath);
        }
    }

    private function handleTransaction(callable $callback)
    {
        try {
            $this->ensureTransactionClosed();
            $this->db->beginTransaction();

            $result = $callback();

            $this->db->commit();
            return $result;
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
            throw $e;
        }
    }

    private function validateMemberData(array $data, bool $isNew = false): array
    {
        $validatedData = [
            'fullName' => $data['fullName'] ?? '',
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? '',
            'dateOfBirth' => $data['dateOfBirth'] ?? null,
            'sex' => $data['sex'] ?? null,
            'status' => $data['status'] ?? 'ACTIVE'
        ];

        if ($isNew) {
            $validatedData['username'] = $data['username'] ?? '';
            $validatedData['password'] = isset($data['password']) ?
                password_hash($data['password'], PASSWORD_DEFAULT) : '';
            $validatedData['eRole'] = 'USER';
        }

        // Validate required fields
        $requiredFields = ['fullName', 'email', 'phone'];
        if ($isNew) {
            $requiredFields[] = 'username';
            $requiredFields[] = 'password';
        }

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \Exception("Trường {$field} là bắt buộc");
            }
            if (isset($data[$field])) {
                $validatedData[$field] = trim($data[$field]);
            }
        }

        // Validate email
        if (!filter_var($validatedData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Email không hợp lệ');
        }

        // Validate phone
        if (!preg_match('/^[0-9]{10}$/', $validatedData['phone'])) {
            throw new \Exception('Số điện thoại không hợp lệ');
        }

        return $validatedData;
    }

    protected function createMembership($userId, $packageId)
    {
        $package = $this->packageModel->findById($packageId);
        if (!$package) {
            throw new \Exception('Không tìm thấy gói tập');
        }

        $membershipData = [
            'userId' => $userId,
            'packageId' => $packageId,
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

    protected function deleteAvatarFile($avatar)
    {
        if (!empty($avatar) && $avatar !== 'default.jpg') {
            $avatarPath = ROOT_PATH . '/' . self::UPLOAD_CONFIG['dir'] . '/' . $avatar;
            if (file_exists($avatarPath)) {
                unlink($avatarPath);
            }
        }
    }

    protected function maskEmail($email)
    {
        if (!$email) return '';

        $parts = explode('@', $email);
        if (count($parts) !== 2) return $email;

        $name = $parts[0];
        $domain = $parts[1];
        $maskedName = substr($name, 0, 2) . str_repeat('*', max(strlen($name) - 2, 0));
        return $maskedName . '@' . $domain;
    }

    protected function maskPhone($phone)
    {
        if (!$phone) return '';
        return substr($phone, 0, 3) . str_repeat('*', max(strlen($phone) - 5, 0)) . substr($phone, -2);
    }

    private function sanitizeOutput(array $data): array
    {
        $safe = [];
        foreach ($data as $key => $value) {
            if (in_array($key, ['email', 'phone'])) {
                $safe[$key] = $this->maskSensitiveData($value);
                continue;
            }
            $safe[$key] = htmlspecialchars($value);
        }
        return $safe;
    }

    private function maskSensitiveData(string $value): string
    {
        $length = strlen($value);
        if ($length <= 4) return str_repeat('*', $length);
        return substr($value, 0, 2) . str_repeat('*', $length - 4) . substr($value, -2);
    }

    public function updateAvatar()
    {
        try {
            if (!isset($_FILES['avatar'])) {
                throw new \Exception('Không tìm thấy file ảnh');
            }

            $file = $_FILES['avatar'];
            $userId = $_SESSION['user']['id'];

            // Kiểm tra và tạo thư mục nếu chưa tồn tại
            $uploadDir = dirname(dirname(dirname(__DIR__))) . '/public/uploads/members/avatars/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Kiểm tra loại file
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowedTypes)) {
                throw new \Exception('Chỉ chấp nhận file ảnh định dạng JPG, PNG hoặc GIF');
            }

            // Kiểm tra kích thước file (5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                throw new \Exception('Kích thước file không được vượt quá 5MB');
            }

            // Tạo tên file mới
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = 'avatar_' . $userId . '_' . time() . '.' . $extension;
            $targetPath = $uploadDir . $newFileName;

            // Di chuyển file
            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                throw new \Exception('Không thể lưu file ảnh');
            }

            // Cập nhật đường dẫn ảnh trong database
            $user = $this->userModel->findById($userId);
            if ($user) {
                // Xóa ảnh cũ nếu có
                if (!empty($user['avatar'])) {
                    $oldAvatarPath = $uploadDir . $user['avatar'];
                    if (file_exists($oldAvatarPath)) {
                        unlink($oldAvatarPath);
                    }
                }

                // Cập nhật avatar mới
                $this->userModel->update($userId, ['avatar' => $newFileName]);
            }

            echo json_encode([
                'success' => true,
                'avatarUrl' => $this->getAvatarUrl($newFileName)
            ]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
