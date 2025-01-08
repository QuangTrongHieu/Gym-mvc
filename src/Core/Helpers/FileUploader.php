<?php

namespace Core\Helpers;

class FileUploader {
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private $maxSize = 5242880; // 5MB
    private $uploadPath;

    public function __construct($uploadPath = null) {
        // Nếu không set path thì lấy thư mục uploads trong public
        $this->uploadPath = $uploadPath ?? ROOT_PATH . '/public/uploads/';
    }

    public function upload($file, $folder = '') {
        try {
            $this->validateFile($file);
            
            // Tạo thư mục nếu chưa tồn tại
            $targetDir = $this->uploadPath . $folder;
            if (!file_exists($targetDir)) {
                if (!@mkdir($targetDir, 0777, true)) {
                    throw new \Exception('Không thể tạo thư mục upload. Vui lòng kiểm tra quyền truy cập.');
                }
                // Set permissions after creation
                @chmod($targetDir, 0777);
            }

            // Tạo tên file ngẫu nhiên để tránh trùng
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '_' . time() . '.' . $extension;
            $targetFile = $targetDir . '/' . $fileName;

            // Di chuyển file tạm sang thư mục đích
            if (!@move_uploaded_file($file['tmp_name'], $targetFile)) {
                $error = error_get_last();
                throw new \Exception('Không thể upload file: ' . ($error['message'] ?? 'Unknown error'));
            }

            // Set permissions for uploaded file
            @chmod($targetFile, 0644);

            // Trả về đường dẫn relative để lưu vào DB
            return '/uploads/' . ($folder ? $folder . '/' : '') . $fileName;

        } catch (\Exception $e) {
            throw new \Exception('Upload thất bại: ' . $e->getMessage());
        }
    }

    private function validateFile($file) {
        // Kiểm tra các lỗi upload
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('File upload bị lỗi');
        }

        // Kiểm tra mime type
        if (!in_array($file['type'], $this->allowedTypes)) {
            throw new \Exception('Loại file không được hỗ trợ. Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP)');
        }

        // Kiểm tra kích thước
        if ($file['size'] > $this->maxSize) {
            throw new \Exception('Kích thước file không được vượt quá 5MB');
        }
    }

    public function delete($filePath) {
        $fullPath = ROOT_PATH . '/public' . $filePath;
        if (file_exists($fullPath)) {
            unlink($fullPath);
            return true;
        }
        return false;
    }

    public function handleProfileImage($file, $oldAvatar = null)
    {
        try {
            // Sử dụng validation có sẵn
            $this->validateFile($file);
            
            // Upload ảnh mới
            $newAvatar = $this->upload($file, 'profiles');
            
            // Xóa ảnh cũ nếu có và không phải ảnh mặc định
            if ($oldAvatar && $oldAvatar !== 'default.jpg') {
                $this->delete('/uploads/profiles/' . $oldAvatar);
            }
            
            // Trả về tên file (bỏ /uploads/profiles/)
            return basename($newAvatar);
            
        } catch (\Exception $e) {
            throw new \Exception('Không thể tải lên ảnh: ' . $e->getMessage());
        }
    }
} 
