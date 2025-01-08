<?php

namespace App\Controllers;

use App\Models\Trainer;
use Core\Helpers\FileUploader;

class TrainerController extends BaseController
{
    private $trainerModel;
    private $uploader;
    private const UPLOAD_DIR = 'public/uploads/trainers';
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    private const ALLOWED_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->trainerModel = new Trainer();
        $this->uploader = new FileUploader();

        // Ensure upload directory exists
        $uploadPath = ROOT_PATH . '/' . self::UPLOAD_DIR;
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
    }

    private function handleImageUpload($file, $oldAvatar = null)
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            if ($file['error'] === UPLOAD_ERR_NO_FILE) {
                return false; // No file uploaded, not an error
            }
            throw new \Exception($this->getFileErrorMessage($file['error']));
        }

        // Validate file size
        if ($file['size'] > self::MAX_FILE_SIZE) {
            throw new \Exception('Kích thước file quá lớn. Giới hạn ' . (self::MAX_FILE_SIZE / 1024 / 1024) . 'MB');
        }

        // Kiểm tra và tạo thư mục nếu chưa tồn tại
        $uploadDir = ROOT_PATH . '/' . self::UPLOAD_DIR;
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new \Exception('Không thể tạo thư mục upload');
            }
        }

        // Validate MIME type
        if (!file_exists($file['tmp_name'])) {
            throw new \Exception('File tạm không tồn tại hoặc upload thất bại');
        }

        if (!is_readable($file['tmp_name'])) {
            throw new \Exception('Không thể đọc file tạm');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            throw new \Exception('Không thể kiểm tra loại file');
        }

        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!array_key_exists($mimeType, self::ALLOWED_TYPES)) {
            throw new \Exception('Loại file không được hỗ trợ. Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP)');
        }

        // Delete old avatar if exists
        if ($oldAvatar && $oldAvatar !== 'default.jpg') {
            $oldAvatarPath = $uploadDir . '/' . $oldAvatar;
            if (file_exists($oldAvatarPath) && is_file($oldAvatarPath)) {
                unlink($oldAvatarPath);
            }
        }

        // Generate safe filename
        $extension = self::ALLOWED_TYPES[$mimeType];
        $fileName = 'trainer_' . uniqid() . '_' . time() . '.' . $extension;
        $targetPath = $uploadDir . '/' . $fileName;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new \Exception('Không thể lưu file. Vui lòng kiểm tra quyền thư mục');
        }

        // Set secure permissions
        chmod($targetPath, 0644);

        return $fileName;
    }

    private function getFileErrorMessage($error)
    {
        switch ($error) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'File vượt quá kích thước cho phép (' . (self::MAX_FILE_SIZE / 1024 / 1024) . 'MB)';
            case UPLOAD_ERR_PARTIAL:
                return 'File chỉ được tải lên một phần. Vui lòng thử lại';
            case UPLOAD_ERR_NO_FILE:
                return 'Không có file nào được tải lên';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Thiếu thư mục tạm. Vui lòng liên hệ quản trị viên';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Không thể ghi file. Vui lòng kiểm tra quyền thư mục';
            case UPLOAD_ERR_EXTENSION:
                return 'Upload bị chặn bởi extension';
            default:
                return 'Lỗi không xác định (' . $error . ')';
        }
    }

    public function index()
    {
        $trainer = $this->trainerModel->getAllTrainers();
        if (empty($trainer)) {
            $_SESSION['error'] = 'Không có huấn luyện viên nào được tìm thấy.';
        }
        $this->view('admin/trainer/index', [
            'title' => 'Quản lý huấn luyện viên',
            'trainer' => $trainer
        ]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Debug log
                error_log("POST data received: " . print_r($_POST, true));

                // Validate data
                $this->validateTrainerData($_POST);

                // Prepare data
                $data = [
                    'username' => trim($_POST['username']),
                    'fullName' => trim($_POST['fullName']),
                    'email' => trim($_POST['email']),
                    'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                    'dateOfBirth' => $_POST['dateOfBirth'],
                    'sex' => $_POST['sex'],
                    'phone' => trim($_POST['phone']),
                    'specialization' => trim($_POST['specialization'] ?? ''),
                    'experience' => trim($_POST['experience'] ?? ''),
                    'certification' => trim($_POST['certification'] ?? ''),
                    'salary' => floatval($_POST['salary'] ?? 0),
                    'eRole' => 'TRAINER',
                    'status' => 'ACTIVE',
                    'avatar' => 'default.jpg'
                ];

                error_log("Prepared data: " . print_r($data, true));

                // Create trainer
                $trainerId = $this->trainerModel->create($data);

                if (!$trainerId) {
                    throw new \Exception("Không thể tạo huấn luyện viên mới");
                }

                // Handle avatar if exists
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $fileName = $this->handleImageUpload($_FILES['avatar']);
                    if ($fileName) {
                        $this->trainerModel->update($trainerId, ['avatar' => $fileName]);
                    }
                }

                $_SESSION['success'] = 'Thêm huấn luyện viên thành công';
                $this->redirect('admin/trainer');

            } catch (\Exception $e) {
                error_log("Error creating trainer: " . $e->getMessage());
                $_SESSION['error'] = $e->getMessage();
                $this->redirect('admin/trainer');
            }
            return;
        }

        $this->view('admin/Trainer/create', ['title' => 'Thêm huấn luyện viên mới']);
    }

    // Hàm kiểm tra dữ liệu
    private function validateTrainerData($data) 
    {
        // Kiểm tra các trường bắt buộc
        $requiredFields = ['username', 'fullName', 'email', 'password'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \Exception("Vui lòng điền đầy đủ thông tin: $field");
            }
        }

        // Kiểm tra email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Email không hợp lệ');
        }

        // Kiểm tra số điện thoại
        if (!preg_match('/^(0|\+84)[3|5|7|8|9][0-9]{8}$/', $data['phone'])) {
            throw new \Exception('Số điện thoại không hợp lệ');
        }

        // Kiểm tra username đã tồn tại
        if ($this->trainerModel->findByUsername($data['username'])) {
            throw new \Exception('Tên đăng nhập đã tồn tại');
        }

        // Kiểm tra email đã tồn tại
        if ($this->trainerModel->findByEmail($data['email'])) {
            throw new \Exception('Email đã được sử dụng');
        }
    }

    public function edit($id)
    {
        try {
            // Get trainer data for both GET and POST requests
            $trainer = $this->trainerModel->findById($id);
            if (!$trainer) {
                throw new \Exception('Không tìm thấy huấn luyện viên');
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Validate required fields
                $requiredFields = ['username', 'fullName', 'email', 'dateOfBirth', 
                                 'sex', 'phone', 'experience', 'certification', 'salary'];
                foreach ($requiredFields as $field) {
                    if (empty($_POST[$field])) {
                        throw new \Exception("Vui lòng điền đầy đủ thông tin: {$field}");
                    }
                }

                // Validate email format
                if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception('Email không hợp lệ');
                }

                // Validate phone number (Vietnamese format)
                if (!preg_match('/^(0|\+84)[3|5|7|8|9][0-9]{8}$/', $_POST['phone'])) {
                    throw new \Exception('Số điện thoại không hợp lệ');
                }

                // Validate date of birth
                $dob = new \DateTime($_POST['dateOfBirth']);
                $today = new \DateTime();
                $age = $dob->diff($today)->y;
                if ($age < 18) {
                    throw new \Exception('Huấn luyện viên phải từ 18 tuổi trở lên');
                }

                // Validate salary
                if (!is_numeric($_POST['salary']) || $_POST['salary'] < 0) {
                    throw new \Exception('Mức lương không hợp lệ');
                }

                // Check if email exists for other trainers
                $existingTrainer = $this->trainerModel->findByEmail($_POST['email']);
                if ($existingTrainer && $existingTrainer['id'] != $id) {
                    throw new \Exception('Email đã được sử dụng bởi huấn luyện viên khác');
                }

                // Prepare trainer data
                $data = [
                    'username' => trim($_POST['username']),
                    'fullName' => trim($_POST['fullName']),
                    'email' => trim($_POST['email']),
                    'dateOfBirth' => $_POST['dateOfBirth'],
                    'sex' => $_POST['sex'],
                    'phone' => trim($_POST['phone']),
                    'experience' => trim($_POST['experience']),
                    'certification' => trim($_POST['certification']),
                    'salary' => floatval($_POST['salary'])
                ];

                // Handle password update if provided
                if (!empty($_POST['password'])) {
                    if (strlen($_POST['password']) < 6) {
                        throw new \Exception('Mật khẩu phải có ít nhất 6 ký tự');
                    }
                    $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                }

                $this->trainerModel->beginTransaction();

                try {
                    // Handle avatar upload if provided
                    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
                        $fileName = $this->handleImageUpload($_FILES['avatar'], $trainer['avatar'] ?? null);
                        if ($fileName) {
                            $data['avatar'] = $fileName;
                        }
                    }

                    // Update trainer record
                    if (!$this->trainerModel->update($id, $data)) {
                        throw new \Exception('Không thể cập nhật thông tin huấn luyện viên');
                    }

                    $this->trainerModel->commit();
                    $_SESSION['success'] = 'Cập nhật thông tin huấn luyện viên thành công';

                } catch (\Exception $e) {
                    if ($this->trainerModel->inTransaction()) {
                        $this->trainerModel->rollBack();
                    }

                    // Delete uploaded avatar if exists and not default
                    if (isset($fileName) && $fileName !== 'default.jpg') {
                        $avatarPath = ROOT_PATH . '/' . self::UPLOAD_DIR . '/' . $fileName;
                        if (file_exists($avatarPath)) {
                            unlink($avatarPath);
                        }
                    }

                    throw $e;
                }

                $this->redirect('admin/trainer');
            }

            // Show edit form for GET request
            $this->view('admin/Trainer/edit', [
                'title' => 'Chỉnh sửa thông tin huấn luyện viên',
                'trainer' => [$trainer] // Wrap in array to match view expectations
            ]);

        } catch (\Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            $this->redirect('admin/trainer');
        }
    }

    public function delete($id)
    {
        try {
            if (empty($id)) {
                throw new \Exception('ID huấn luyện viên không hợp lệ');
            }

            // Fetch trainer with validation
            $trainer = $this->trainerModel->findById($id);
            if (!$trainer) {
                throw new \Exception('Không tìm thấy huấn luyện viên');
            }

            // Handle AJAX POST request for deletion
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Check if it's an AJAX request
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    
                    // Start transaction
                    $this->trainerModel->beginTransaction();

                    try {
                        // Delete trainer's avatar if it exists and is not the default
                        if (!empty($trainer['avatar']) && $trainer['avatar'] !== 'default.jpg') {
                            $avatarPath = ROOT_PATH . '/' . self::UPLOAD_DIR . '/' . $trainer['avatar'];
                            if (file_exists($avatarPath)) {
                                unlink($avatarPath);
                            }
                        }

                        // Delete trainer record
                        if (!$this->trainerModel->delete($id)) {
                            throw new \Exception('Không thể xóa thông tin huấn luyện viên');
                        }

                        $this->trainerModel->commit();
                        
                        // Return JSON success response
                        echo json_encode([
                            'success' => true,
                            'message' => 'Xóa huấn luyện viên thành công'
                        ]);
                        return;
                    } catch (\Exception $e) {
                        if ($this->trainerModel->inTransaction()) {
                            $this->trainerModel->rollBack();
                        }
                        throw $e;
                    }
                }
                
                // Handle non-AJAX POST request
                $this->trainerModel->delete($id);
                $_SESSION['success'] = 'Xóa huấn luyện viên thành công';
                $this->redirect('admin/trainer');
            }

            // Show confirmation page for GET request
            $this->view('admin/Trainer/delete', [
                'title' => 'Xóa huấn luyện viên',
                'trainer' => $trainer
            ]);
            return;

        } catch (\Exception $e) {
            // Handle AJAX request errors
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
                return;
            }
            
            // Handle regular request errors
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            $this->redirect('admin/trainer');
        }
    }

    public function list()
    {
        $trainers = $this->trainerModel->findActiveTrainers();
        $this->view('RegisTrainer/list-trainers', [
            'title' => 'Đội ngũ Huấn luyện viên',
            'trainers' => $trainers
        ]);
    }

    public function editProfile()
    {
        // Get current trainer's information
        $trainerId = $_SESSION['trainer_id'];
        $trainer = $this->trainerModel->getTrainerById($trainerId);

        if (!$trainer) {
            $_SESSION['error'] = 'Không tìm thấy thông tin huấn luyện viên.';
            $this->redirect('trainer/dashboard');
        }

        $this->view('Trainer/Profile/edit', [
            'title' => 'Chỉnh sửa thông tin cá nhân',
            'trainer' => $trainer
        ]);
    }

    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('trainer/profile/edit');
        }

        $trainerId = $_SESSION['trainer_id'];
        $trainer = $this->trainerModel->getTrainerById($trainerId);

        $data = [
            'fullName' => $_POST['fullName'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'specialization' => $_POST['specialization'],
            'experience' => $_POST['experience']
        ];

        try {
            // Handle avatar upload
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
                $fileName = $this->handleImageUpload($_FILES['avatar'], $trainer['avatar'] ?? null);
                if ($fileName) {
                    $data['avatar'] = $fileName;
                }
            }

            // Handle password update
            if (!empty($_POST['password'])) {
                if ($_POST['password'] !== $_POST['password_confirm']) {
                    throw new \Exception('Mật khẩu xác nhận không khớp.');
                }
                $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            if ($this->trainerModel->updateTrainer($trainerId, $data)) {
                $_SESSION['success'] = 'Cập nhật thông tin thành công.';
                $this->redirect('trainer/dashboard');
            } else {
                throw new \Exception('Có lỗi xảy ra khi cập nhật thông tin.');
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('trainer/profile/edit');
        }
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $trainer = $this->trainerModel->getTrainerByUsername($username);

            if ($trainer && password_verify($password, $trainer['password'])) {
                $_SESSION['trainer_id'] = $trainer['id'];
                $_SESSION['trainer_name'] = $trainer['fullName'];
                $_SESSION['trainer_role'] = 'TRAINER';
                $_SESSION['trainer_avatar'] = $trainer['avatar'];

                $this->redirect('trainer/dashboard');
            } else {
                $this->view('Trainer/login', [
                    'error' => 'Tên đăng nhập hoặc mật khẩu không đúng'
                ], 'auth_layout');
            }
        } else {
            $this->view('Trainer/login', [], 'auth_layout');
        }
    }

    public function dashboard()
    {
        $this->redirect('trainer/dashboard');
    }
    public function logout()
    {
        // Clear all session variables
        session_unset();
        // Redirect to login page
        $this->redirect('trainer/login');
    }

    // Hàm mới để lấy danh sách huấn luyện viên
    public function getTrainers()
    {
        // Lấy dữ liệu từ cơ sở dữ liệu
        $trainers = $this->trainerModel->getAllTrainers();

        // Trả về dữ liệu dướidạng JSON
        echo json_encode($trainers);
    }
}
