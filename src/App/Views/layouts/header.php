<?php
// Đảm bảo session được khởi tạo ở đầu file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug session (có thể comment lại sau khi fix xong)
error_log('Session data in header: ' . print_r($_SESSION, true));
error_log('User data in header: ' . print_r($user ?? [], true));
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PowerGym - Phòng Gym Chuyên Nghiệp</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Thêm custom CSS -->
    <style>
        .navbar {
            background-color: #1a1a1a !important;
            padding: 15px 0;
        }

        .navbar-brand {
            font-weight: bold;
            color: #ff4d4d !important;
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.05);
        }

        .nav-link {
            color: #ffffff !important;
            font-weight: 500;
            margin: 0 15px;
            position: relative;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: #ff4d4d !important;
            transform: translateY(-2px);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: #ff4d4d;
            left: 0;
            bottom: -5px;
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .nav-link i {
            margin-right: 8px;
            transition: transform 0.3s ease;
        }

        .nav-link:hover i {
            transform: scale(1.2);
        }

        .btn-login {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background-color: #ff3333;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 77, 77, 0.3);
        }

        .navbar-brand img {
            border-radius: 25px;
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover img {
            transform: rotate(5deg);
        }

        .dropdown-item {
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: #ff4d4d;
            color: white;
            padding-left: 25px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="/gym">
                <img src="/gym/public/images/logo/logo1.png" alt="PowerGym Logo" height="55">
                <span class="ms-2 d-none d-lg-inline">POWERGYM</span>
            </a>

            <!-- Navigation Links -->
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/gym"><i class="fas fa-home"></i>Trang Chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gym/list-trainers"><i class="fas fa-user-friends"></i>Huấn Luyện Viên</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gym/list-packages"><i class="fas fa-cube"></i>Gói tập</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gym/list-equipment"><i class="fas fa-dumbbell"></i>Thiết bị</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gym/contact"><i class="fas fa-envelope"></i>Liên Hệ</a>
                    </li>
                </ul>
            </div>

            <!-- User Account -->
            <?php
            // Kiểm tra session trực tiếp
            $isLoggedIn = isset($_SESSION['user_id']);
            $userName = $_SESSION['user_name'] ?? null;
            $userAvatar = $_SESSION['avatar'] ?? null;

            if ($isLoggedIn):
            ?>
                <!-- Hiển thị dropdown menu khi đã đăng nhập -->
                <div class="dropdown">
                    <button class="btn btn-dark dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                        <img src="<?= $userAvatar ?? 'https://openui.fly.dev/openui/24x24.svg?text=👤' ?>"
                            alt="user-avatar" class="rounded-circle" style="width: 30px; height: 30px;">
                        <span class="ms-2 text-white"><?= htmlspecialchars($userName) ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/gym/user/profile"><i class="fas fa-user me-2"></i>Hồ Sơ Cá Nhân</a></li>
                        <li><a class="dropdown-item" href="/gym/user/training"><i class="fas fa-calendar-alt me-2"></i>Lịch Tập</a></li>
                        <li><a class="dropdown-item" href="/gym/user/progress"><i class="fas fa-chart-line me-2"></i>Theo Dõi Tiến Độ</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="/gym/logout"><i class="fas fa-sign-out-alt me-2"></i>Đăng Xuất</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="/gym/login" class="btn btn-login">
                    <i class="fas fa-sign-in-alt"></i>
                    <span class="ms-2">Đăng Nhập</span>
                </a>
            <?php endif; ?>
            <!-- Toggle Button -->
            <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <!-- Thêm Bootstrap JS và custom JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add smooth scrolling to nav links
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                const icon = this.querySelector('i');
                if (icon) {
                    icon.style.transform = 'scale(1.2) rotate(360deg)';
                    setTimeout(() => {
                        icon.style.transform = 'scale(1) rotate(0)';
                    }, 300);
                }
            });
        });

        // Add hover effect to dropdown items
        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                const icon = this.querySelector('i');
                if (icon) {
                    icon.style.transform = 'scale(1.2)';
                }
            });

            item.addEventListener('mouseleave', function() {
                const icon = this.querySelector('i');
                if (icon) {
                    icon.style.transform = 'scale(1)';
                }
            });
        });
    </script>
</body>

</html>