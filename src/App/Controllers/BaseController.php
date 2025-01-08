<?php

namespace App\Controllers;

use Core\Auth;

abstract class BaseController
{
    protected $auth;
    protected $route_params;

    public function __construct($route_params = [])
    {
        // Đảm bảo session được khởi tạo trước khi làm bất cứ điều gì
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->auth = new Auth();
        $this->route_params = $route_params;
    }

    public function view($view, $data = [], $layout = null)
    {
        // Đảm bảo session được khởi tạo trước khi làm bất cứ điều gì
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Thêm thông tin user vào data để view có thể sử dụng
        $data['user'] = [
            'isLoggedIn' => isset($_SESSION['user_id']),
            'id' => $_SESSION['user_id'] ?? null,
            'name' => $_SESSION['user_name'] ?? null,
            'avatar' => $_SESSION['avatar'] ?? null,
            'role' => $_SESSION['user_role'] ?? null
        ];

        // Extract data để sử dụng trong view
        extract($data);

        // Bắt đầu output buffering
        ob_start();

        // Load nội dung view
        $viewPath = ROOT_PATH . "/src/App/Views/{$view}.php";
        if (!file_exists($viewPath)) {
            throw new \Exception("View not found: {$view}");
        }
        require_once $viewPath;
        $content = ob_get_clean();

        // Nếu layout được cung cấp, sử dụng layout đó
        if ($layout) {
            // Load layout đã cung cấp
            require_once ROOT_PATH . "/src/App/Views/layouts/{$layout}.php";
        } else {
            // Nếu không có layout được cung cấp, sử dụng layout mặc định
            // Kiểm tra prefix của view để xác định layout
            if (strpos($view, 'admin/') === 0) {
                // Nếu view có prefix 'admin/', sử dụng layout admin
                $layoutPath = ROOT_PATH . "/src/App/Views/layouts/admin_layout.php";
            } else if (strpos($view, 'Trainer/') === 0) {
                // Nếu view có prefix 'Trainer/', sử dụng layout trainer
                $layoutPath = ROOT_PATH . "/src/App/Views/layouts/trainer_layout.php";
            } else if (strpos($view, 'user/') === 0) {
                // Nếu view có prefix 'user/', sử dụng layout user
                $layoutPath = ROOT_PATH . "/src/App/Views/layouts/user_layout.php";
            } else {
                // Nếu không có prefix nào, sử dụng layout mặc định
                $layoutPath = ROOT_PATH . "/src/App/Views/layouts/default_layout.php";
            }

            if (!file_exists($layoutPath)) {
                throw new \Exception("Layout not found: {$layoutPath}");
            }
            require_once $layoutPath;
        }

        return $content;
    }

    // Trả về dữ liệu dạng JSON
    protected function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Trả về dữ liệu dạng JSON với status code 400
    protected function error($message, $code = 400)
    {
        $this->json([
            'success' => false,
            'message' => $message
        ]);
    }

    // Trả về dữ liệu dạng JSON với status code 200
    protected function jsonResponse($data, $statusCode = 200)
    {
        ob_clean();
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    protected function redirect($path)
    {
        header("Location: /gym/" . $path);
        exit;
    }

    protected function checkRole($allowedRoles)
    {
        if (!$this->auth->isLoggedIn()) {
            $role = $allowedRoles[0] ?? '';
            switch ($role) {
                case 'ADMIN':
                    $this->redirect('admin-login');
                    break;
                case 'TRAINERS':
                    $this->redirect('trainers-login');
                    break;
                default:
                    $this->redirect('login');
            }
            return;
        }

        $userRole = $this->auth->getUserRole();
        if (!in_array($userRole, $allowedRoles)) {
            $this->redirect('403');
            exit;
        }
    }
}
