<?php

namespace App\Models;

use PDO;
use PDOException;
use Core\Helpers\FileUploader;

class User extends BaseModel
{
    protected $table = 'users';
    protected const UPLOAD_DIR = 'public/uploads/members/avatars';
    protected const DEFAULT_AVATAR = 'default.jpg';

    public function __construct($db = null)
    {
        parent::__construct($db);

        // Ensure upload directory exists
        $uploadPath = ROOT_PATH . '/' . self::UPLOAD_DIR;
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
    }

    public function findByUsername($username)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE username = :username";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['username' => $username]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error finding user by username: " . $e->getMessage());
            return false;
        }
    }

    public function findByEmail($email)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['email' => $email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error finding user by email: " . $e->getMessage());
            return false;
        }
    }

    public function findByPhone($phone)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE phone = :phone";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['phone' => $phone]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error finding user by phone: " . $e->getMessage());
            return false;
        }
    }

    public function findByRole($role)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE eRole = :role ORDER BY createdAt DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['role' => $role]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error finding users by role: " . $e->getMessage());
            return [];
        }
    }

    public function create($data)
    {
        try {
            // Đảm bảo các trường bắt buộc
            $requiredFields = ['username', 'fullName', 'dateOfBirth', 'sex', 'phone', 'email', 'password'];

            // Xóa role khỏi required fields và set mặc định
            $data['eRole'] = 'USER'; // Sửa từ 'role' thành 'eRole' để khớp với tên cột trong DB

            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    throw new \Exception("Missing required field: {$field}");
                }
            }

            // Thêm avatar mặc định nếu không có
            if (!isset($data['avatar'])) {
                $data['avatar'] = '/assets/images/default-avatar.png';
            }

            // Thêm timestamp
            $data['createdAt'] = date('Y-m-d H:i:s');
            $data['updatedAt'] = date('Y-m-d H:i:s');

            // Hash password nếu chưa được hash
            if (strlen($data['password']) < 60) { // Kiểm tra nếu password chưa được hash
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            return parent::create($data);
        } catch (PDOException $e) {
            error_log("Error creating user: " . $e->getMessage());
            // Log chi tiết lỗi để debug
            error_log("SQL Error: " . print_r($e, true));
            throw new \Exception("Không thể tạo tài khoản. Vui lòng thử lại sau.");
        }
    }

    public function update($id, $data)
    {
        try {
            $setClause = [];
            $params = [];
            
            foreach ($data as $key => $value) {
                $setClause[] = "`$key` = ?";
                $params[] = $value;
            }
            
            $params[] = $id;
            
            $sql = "UPDATE " . $this->table . " SET " . implode(', ', $setClause) . " WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (\PDOException $e) {
            error_log("Error updating user: " . $e->getMessage());
            return false;
        }
    }

    public function updatePassword($id, $newPassword)
    {
        try {
            $sql = "UPDATE {$this->table} SET password = :password, updatedAt = :updatedAt WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'id' => $id,
                'password' => $newPassword,
                'updatedAt' => date('Y-m-d H:i:s')
            ]);
        } catch (PDOException $e) {
            error_log("Error updating password: " . $e->getMessage());
            throw new \Exception("Không thể cập nhật mật khẩu. Vui lòng thử lại sau.");
        }
    }

    public function getAddresses($userId)
    {
        try {
            $sql = "SELECT * FROM addresses WHERE userId = :userId ORDER BY isDefault DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['userId' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting user addresses: " . $e->getMessage());
            return [];
        }
    }

    public function getDefaultAddress($userId)
    {
        try {
            $sql = "SELECT * FROM addresses WHERE userId = :userId AND isDefault = true LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['userId' => $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting default address: " . $e->getMessage());
            return null;
        }
    }

    public function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function validatePhone($phone)
    {
        return preg_match('/^[0-9]{10,11}$/', $phone) === 1;
    }

    public function findAllMembers()
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE eRole = 'USER'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error finding members: " . $e->getMessage());
            return [];
        }
    }

    public function uploadAvatar($file)
    {
        try {
            $uploader = new FileUploader();
            return $uploader->handleProfileImage($file);
        } catch (\Exception $e) {
            throw new \RuntimeException('Không thể tải lên ảnh: ' . $e->getMessage());
        }
    }

    public function updateAvatar($userId, $avatarFileName)
    {
        try {
            // Get current avatar
            $currentUser = $this->getById($userId);
            if (!$currentUser) {
                throw new \Exception('User not found');
            }

            // Delete old avatar if it exists and is not the default
            if (!empty($currentUser['avatar']) && $currentUser['avatar'] !== self::DEFAULT_AVATAR) {
                $oldAvatarPath = ROOT_PATH . '/' . self::UPLOAD_DIR . '/' . $currentUser['avatar'];
                if (file_exists($oldAvatarPath) && is_file($oldAvatarPath)) {
                    unlink($oldAvatarPath);
                }
            }

            // Update avatar in database
            $sql = "UPDATE {$this->table} SET avatar = :avatar, updatedAt = :updatedAt WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([
                'id' => $userId,
                'avatar' => $avatarFileName,
                'updatedAt' => date('Y-m-d H:i:s')
            ]);

            if (!$success) {
                throw new \Exception('Failed to update avatar in database');
            }

            return $avatarFileName;
        } catch (\Exception $e) {
            error_log("Error updating avatar: " . $e->getMessage());
            throw new \RuntimeException('Không thể cập nhật ảnh đại diện: ' . $e->getMessage());
        }
    }

    public function getAvatarUrl($avatar)
    {
        if (empty($avatar)) {
            return '/gym/public/assets/images/default-avatar.png';
        }
        return '/gym/public/uploads/members/avatars/' . $avatar;
    }

    public function getById($id)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting user by ID: " . $e->getMessage());
            return false;
        }
    }

    public function getStatsByDateRange($startDate, $endDate, $role = null)
    {
        try {
            $sql = "SELECT DATE(createdAt) as date, COUNT(*) as count 
                    FROM {$this->table}
                    WHERE createdAt BETWEEN ? AND ?";
            
            $params = [$startDate, $endDate];

            if ($role) {
                $sql .= " AND eRole = ?";
                $params[] = $role;
            }

            $sql .= " GROUP BY DATE(createdAt)
                      ORDER BY date ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Error getting user stats: " . $e->getMessage());
            return [];
        }
    }

    public function getCountByRole($role)
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE eRole = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$role]);
            $result = $stmt->fetch();
            return (int)$result['count'];
        } catch (\PDOException $e) {
            error_log("Error getting count by role: " . $e->getMessage());
            return 0;
        }
    }
}
