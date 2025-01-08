<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: /gym/login');
    exit;
}
?>

<div class="container py-5">
    <h1 class="text-center mb-5">Gói tập của tôi</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (empty($memberships)): ?>
        <div class="alert alert-info">
            Bạn chưa đăng ký gói tập nào. <a href="/gym/list-packages" class="alert-link">Đăng ký ngay</a>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 g-4">
            <?php foreach ($memberships as $membership): ?>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($membership['package_name']) ?></h5>
                            
                            <div class="mb-3">
                                <strong>Trạng thái:</strong>
                                <?php
                                $statusClass = '';
                                $statusText = '';
                                switch ($membership['status']) {
                                    case 'PENDING':
                                        $statusClass = 'text-warning';
                                        $statusText = 'Đang chờ xác nhận';
                                        break;
                                    case 'ACTIVE':
                                        $statusClass = 'text-success';
                                        $statusText = 'Đang hoạt động';
                                        break;
                                    case 'EXPIRED':
                                        $statusClass = 'text-danger';
                                        $statusText = 'Đã hết hạn';
                                        break;
                                    case 'CANCELLED':
                                        $statusClass = 'text-secondary';
                                        $statusText = 'Đã hủy';
                                        break;
                                    case 'REJECTED':
                                        $statusClass = 'text-danger';
                                        $statusText = 'Bị từ chối';
                                        break;
                                }
                                ?>
                                <span class="<?= $statusClass ?>"><?= $statusText ?></span>
                            </div>

                            <p class="card-text">
                                <strong>Thời hạn:</strong> <?= htmlspecialchars($membership['duration']) ?> tháng<br>
                                <strong>Ngày bắt đầu:</strong> <?= date('d/m/Y', strtotime($membership['startDate'])) ?><br>
                                <strong>Ngày kết thúc:</strong> <?= date('d/m/Y', strtotime($membership['endDate'])) ?><br>
                                <strong>Giá:</strong> <?= number_format($membership['price'], 0, ',', '.') ?> VNĐ
                            </p>

                            <?php if ($membership['status'] === 'PENDING'): ?>
                                <div class="text-end">
                                    <a href="/gym/membership/cancel/<?= $membership['id'] ?>" 
                                       class="btn btn-danger"
                                       onclick="return confirm('Bạn có chắc muốn hủy đăng ký này?')">
                                        Hủy đăng ký
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}
</style>
