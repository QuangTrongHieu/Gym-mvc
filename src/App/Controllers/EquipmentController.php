<?php

namespace App\Controllers;

use App\Models\Equipment;

class EquipmentController extends BaseController
{
    private $equipmentModel;

    public function __construct()
    {
        $this->equipmentModel = new Equipment();
    }

    private function handleImageUpload($file)
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }

        $uploadDir = ROOT_PATH . '/public/uploads/equipment/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . '_' . basename($file['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return '/public/uploads/equipment/' . $fileName;
        }

        return false;
    }

    public function index()
    {
        $equipments = $this->equipmentModel->findAll();
        $this->view('admin/equipment/index', [
            'title' => 'Quản lý Thiết bị',
            'equipments' => $equipments
        ]);
    }

    public function listEquipment()
    {
        $equipments = $this->equipmentModel->getAllActiveEquipment();
        // Debug output
        echo "<!-- Debug: " . print_r($equipments, true) . " -->";

        $this->view('equipmentreviews/equipment', [
            'title' => 'Thiết Bị Phòng Gym',
            'equipments' => $equipments
        ]);
    }

    public function listEquipmentForUsers()
    {
        $equipment = $this->equipmentModel->findActiveEquipment();
        $this->view('EquipmentReviews/Equipment', [
            'title' => 'Các thiết bị tập của chúng tôi',
            'equipment' => $equipment
        ]);
    }

    public function create()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                error_log("Received POST data: " . print_r($_POST, true));

                // Validate data
                $this->validateEquipmentData($_POST);

                $data = [
                    'name' => trim($_POST['name']),
                    'description' => trim($_POST['description']),
                    'purchaseDate' => $_POST['purchaseDate'],
                    'price' => floatval($_POST['price']),
                    'status' => $_POST['status'],
                    'lastMaintenanceDate' => !empty($_POST['lastMaintenanceDate']) ? $_POST['lastMaintenanceDate'] : null,
                    'nextMaintenanceDate' => !empty($_POST['nextMaintenanceDate']) ? $_POST['nextMaintenanceDate'] : null
                ];

                // Handle image upload
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    error_log("Processing image upload");
                    $imagePath = $this->handleImageUpload($_FILES['image']);
                    if ($imagePath) {
                        $data['image_path'] = $imagePath;
                    }
                }

                error_log("Attempting to create equipment with data: " . print_r($data, true));

                $result = $this->equipmentModel->create($data);

                if (!$result) {
                    throw new \Exception("Không thể thêm thiết bị");
                }

                $_SESSION['success'] = 'Thêm thiết bị thành công';
                return $this->redirect('admin/equipment');
            }
            
            $this->view('admin/equipment/create');
            
        } catch (\Exception $e) {
            error_log("Error in equipment creation: " . $e->getMessage());
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            return $this->redirect('admin/equipment');
        }
    }

    private function validateEquipmentData($data) {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = 'Vui lòng nhập tên thiết bị';
        }

        if (empty($data['purchaseDate'])) {
            $errors[] = 'Vui lòng nhập ngày mua';
        }

        if (!isset($data['price']) || floatval($data['price']) <= 0) {
            $errors[] = 'Giá phải lớn hơn 0';
        }

        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors));
        }

        return true;
    }

    public function edit($id)
    {
        $equipment = $this->equipmentModel->findById($id);
        if (!$equipment) {
            $_SESSION['error'] = 'Không tìm thấy thiết bị';
            header('Location: /gym/admin/equipment');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate dữ liệu
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'purchaseDate' => $_POST['purchaseDate'],
                'price' => floatval($_POST['price']),
                'status' => $_POST['status'],
                'lastMaintenanceDate' => !empty($_POST['lastMaintenanceDate']) ? $_POST['lastMaintenanceDate'] : null,
                'nextMaintenanceDate' => !empty($_POST['nextMaintenanceDate']) ? $_POST['nextMaintenanceDate'] : null
            ];

            // Xử lý upload ảnh mới
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imagePath = $this->handleImageUpload($_FILES['image']);
                if ($imagePath) {
                    // Xóa ảnh cũ nếu có
                    if (!empty($equipment['image_path'])) {
                        $oldImagePath = ROOT_PATH . $equipment['image_path'];
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                    $data['image_path'] = $imagePath;
                }
            }

            // Kiểm tra validate
            $errors = [];
            if (empty($data['name'])) {
                $errors['name'] = 'Vui lòng nhập tên thiết bị';
            }
            if (empty($data['purchaseDate'])) {
                $errors['purchaseDate'] = 'Vui lòng nhập ngày mua';
            }
            if ($data['price'] <= 0) {
                $errors['price'] = 'Giá phải lớn hơn 0';
            }

            if (empty($errors)) {
                if ($this->equipmentModel->update($id, $data)) {
                    $_SESSION['success'] = 'Cập nhật thiết bị thành công';
                    header('Location: /gym/admin/equipment');
                    exit();
                } else {
                    $_SESSION['error'] = 'Có lỗi xảy ra';
                    header('Location: /gym/admin/equipment');
                    exit();
                }
            }

            $this->view('admin/equipment/edit', [
                'title' => 'Chỉnh sửa thiết bị',
                'equipment' => $equipment,
                'errors' => $errors
            ]);
        } else {
            $this->view('admin/equipment/edit', [
                'title' => 'Chỉnh sửa thiết bị',
                'equipment' => $equipment
            ]);
        }
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get equipment details before deletion
            $equipment = $this->equipmentModel->findById($id);

            if ($equipment && $this->equipmentModel->delete($id)) {
                // Delete associated image if it exists
                if (!empty($equipment['image_path'])) {
                    $imagePath = ROOT_PATH . $equipment['image_path'];
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                $_SESSION['success'] = 'Xóa thiết bị thành công';
                header('Location: /gym/admin/equipment');
                exit();
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra';
                header('Location: /gym/admin/equipment');
                exit();
            }
        }
        header('Location: /gym/admin/equipment');
        exit();
    }
}
