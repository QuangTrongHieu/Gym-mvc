<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Dashboard' ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            background-color: #343a40;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 100;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .nav-item .nav-link {
            color: rgba(255, 255, 255, .8);
            padding: 0.8rem 1rem;
            transition: all 0.3s;
        }

        .nav-item .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, .1);
        }

        .nav-item .nav-link i {
            width: 25px;
            margin-right: 10px;
        }

        .table th {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="p-3">
            <h4 class="text-white">Admin Panel</h4>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="/gym/admin/dashboard">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/gym/admin/admin-management">
                    <i class="fas fa-users-cog"></i> Quản lý admin
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/gym/admin/trainer">
                    <i class="fas fa-user-tie"></i> Quản lý huấn luyện viên
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/gym/admin/member">
                    <i class="fas fa-user-friends"></i> Quản lý hội viên
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/gym/admin/equipment">
                    <i class="fas fa-dumbbell"></i> Quản lý thiết bị
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/gym/admin/packages">
                    <i class="fas fa-box-open"></i> Quản lý gói tập
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/gym/admin/schedule">
                    <i class="fas fa-calendar-alt"></i> Lịch tập
                </a>
            </li>
            <li class="nav-item mt-4">
                <a class="nav-link" href="/gym/admin/logout">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            </li>
        </ul>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <?= $content ?>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (needed for some Bootstrap features) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Enable Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // Enable Bootstrap popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl)
        })
    </script>
</body>

</html>