<div class="container-fluid px-4 py-4">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?= $_SESSION['success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= $_SESSION['error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php
    $defaultAvatarBase64 = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMTAwIDEwMCI+PHJlY3Qgd2lkdGg9IjEwMCIgaGVpZ2h0PSIxMDAiIGZpbGw9IiNlOWVjZWYiLz48Y2lyY2xlIGN4PSI1MCIgY3k9IjM1IiByPSIyMCIgZmlsbD0iI2FkYjViZCIvPjxwYXRoIGQ9Ik0xNSw4NWMwLTIwLDE1LTM1LDM1LTM1czM1LDE1LDM1LDM1IiBmaWxsPSIjYWRiNWJkIi8+PC9zdmc+';

    function getAvatarUrl($avatar) {
        if (empty($avatar)) return null;
        
        if ($avatar === 'default.jpg') {
            $defaultPath = ROOT_PATH . '/public/uploads/trainers/default.jpg';
            return file_exists($defaultPath) ? '/gym/public/uploads/trainers/default.jpg' : null;
        }
        
        $uploadDir = ROOT_PATH . '/public/uploads/trainers';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);
        
        $avatarPath = $uploadDir . '/' . $avatar;
        if (!file_exists($avatarPath) || !is_readable($avatarPath)) {
            error_log("Avatar issue: " . $avatarPath);
            return null;
        }
        
        return '/gym/public/uploads/trainers/' . htmlspecialchars($avatar);
    }
    ?>

    <style>
        .trainer-avatar {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #e9ecef;
            transition: transform 0.2s;
        }
        .trainer-avatar:hover {
            transform: scale(1.1);
        }
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            transition: all 0.2s;
        }
        .action-buttons .btn:hover {
            transform: translateY(-2px);
        }
        .table td {
            vertical-align: middle;
            padding: 1rem 0.75rem;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .btn-excel {
            background-color: #1D6F42;
            border-color: #1D6F42;
            color: white;
        }
        .btn-excel:hover {
            background-color: #185735;
            border-color: #185735;
            color: white;
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800">Quản lý Huấn luyện viên</h1>
            <p class="mb-0 text-muted">Quản lý thông tin và hoạt động của huấn luyện viên</p>
        </div>
        <div class="d-flex gap-2">
            <a href="/gym/admin/trainer/export" class="btn btn-excel">
                <i class="fas fa-file-excel me-2"></i>Xuất Excel
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="fas fa-plus me-2"></i>Thêm Huấn luyện viên
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">ID</th>
                            <th>Ảnh</th>
                            <th>Họ tên</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>SĐT</th>
                            <th>Chuyên môn</th>
                            <th>Lương</th>
                            <th class="text-end pe-3">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($trainer)): ?>
                            <?php foreach ($trainer as $item): ?>
                                <tr data-id="<?= htmlspecialchars($item['id']) ?>">
                                    <td class="ps-3"><?= htmlspecialchars($item['id']) ?></td>
                                    <td>
                                        <?php
                                        $avatarUrl = getAvatarUrl($item['avatar']);
                                        if ($avatarUrl) {
                                            echo '<img src="' . $avatarUrl . '" 
                                                      class="trainer-avatar" 
                                                      alt="Avatar of ' . htmlspecialchars($item['fullName']) . '"
                                                      onerror="this.src=\'' . $defaultAvatarBase64 . '\'">';
                                        } else {
                                            echo '<img src="' . $defaultAvatarBase64 . '" 
                                                      class="trainer-avatar" 
                                                      alt="Default Avatar">';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($item['fullName']) ?></div>
                                    </td>
                                    <td><?= htmlspecialchars($item['username']) ?></td>
                                    <td>
                                        <a href="mailto:<?= htmlspecialchars($item['email']) ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($item['email']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="tel:<?= htmlspecialchars($item['phone']) ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($item['phone']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($item['specialization']) ?></td>
                                    <td><?= number_format($item['salary'], 0, ',', '.') ?> VNĐ</td>
                                    <td class="text-end pe-3">
                                        <div class="action-buttons">
                                            <button type="button" class="btn btn-outline-primary btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editModal<?= $item['id'] ?>"
                                                title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button"
                                                class="btn btn-outline-danger btn-sm"
                                                onclick="showDeleteModal(<?= $item['id'] ?>)"
                                                title="Xóa">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p class="mb-0">Không có dữ liệu huấn luyện viên</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
require_once ROOT_PATH . '/src/App/Views/admin/Trainer/edit.php';
require_once ROOT_PATH . '/src/App/Views/admin/Trainer/create.php';
require_once ROOT_PATH . '/src/App/Views/admin/Trainer/delete.php';
?>