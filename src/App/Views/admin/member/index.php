<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= $_SESSION['success']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= $_SESSION['error']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Quản lý Hội viên</h1>
    <div>
        <a href="/gym/admin/member/export" class="btn btn-success me-2">
            <i class="fas fa-file-excel"></i> Xuất Excel
        </a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="fas fa-plus"></i> Thêm Hội viên
        </button>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Họ tên</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>SĐT</th>
                        <th>Gói tập</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($members)): ?>
                        <tr>
                            <td colspan="9" class="text-center">Không có hội viên nào</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($members as $member): ?>
                            <tr>
                                <td><?= $member['id'] ?></td>
                                <td>
                                    <img src="<?= !empty($member['avatarUrl']) ? htmlspecialchars($member['avatarUrl']) : '/gym/public/assets/images/default-avatar.png' ?>"
                                         class="rounded-circle"
                                         alt="Avatar"
                                         style="width: 40px; height: 40px; object-fit: cover;" />
                                </td>
                                <td><?= htmlspecialchars($member['fullName']) ?></td>
                                <td><?= htmlspecialchars($member['username']) ?></td>
                                <td><?= htmlspecialchars($member['email']) ?></td>
                                <td><?= htmlspecialchars($member['phone']) ?></td>
                                <td>
                                    <?php if (isset($member['package'])): ?>
                                        <span class="badge bg-success">
                                            <?= htmlspecialchars($member['package']['name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Chưa đăng ký</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = match($member['status']) {
                                        'ACTIVE' => 'success',
                                        'INACTIVE' => 'danger',
                                        default => 'secondary'
                                    };
                                    $statusText = match($member['status']) {
                                        'ACTIVE' => 'Hoạt động',
                                        'INACTIVE' => 'Không hoạt động',
                                        default => 'Không xác định'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?>">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="/gym/admin/member/edit/<?= $member['id'] ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/gym/admin/member/delete/<?= $member['id'] ?>" 
                                           class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
    }

    .action-buttons .btn {
        padding: 0.25rem 0.5rem;
    }

    .table td {
        vertical-align: middle;
    }

    .badge {
        padding: 0.5em 0.75em;
    }
</style>

<?php
require_once ROOT_PATH . '/src/App/Views/admin/member/edit.php';
require_once ROOT_PATH . '/src/App/Views/admin/member/create.php';
require_once ROOT_PATH . '/src/App/Views/admin/member/delete.php';
?>