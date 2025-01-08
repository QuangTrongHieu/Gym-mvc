<?php
namespace Core\Middleware;

class AuthMiddleware
{
    public function handle($request, $allowedRoles = [])
    {
        if (!isset($_SESSION['user_id'])) {
            $role = $allowedRoles[0] ?? '';
            switch($role) {
                case 'ADMIN':
                    header('Location: /gym/admin-login');
                    break;
                case 'Trainer':
                    header('Location: /gym/Trainer-login'); 
                    break;
                default:
                    header('Location: /gym/login');
            }
            exit;   
        }

        if (!empty($allowedRoles)) {
            $userRole = $_SESSION['user_role'] ?? null;
            if (!in_array($userRole, $allowedRoles)) {
                header('Location: /gym/403');
                exit;
            }
        }

        return true;
    }
} 