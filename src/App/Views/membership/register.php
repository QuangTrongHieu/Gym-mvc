<?php
// Kiểm tra session
$isLoggedIn = isset($_SESSION['user']);
$userRole = $_SESSION['user']['erole'] ?? '';
$userId = $_SESSION['user']['id'] ?? null;

if (!$isLoggedIn) {
    $_SESSION['error'] = 'Vui lòng đăng nhập để đăng ký gói tập';
    header('Location: /gym/login');
    exit;
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Đăng ký gói tập: <?= htmlspecialchars($package['name']) ?></h3>
                </div>
                <div class="card-body">
                    <form action="/gym/membership/process-registration" method="POST">
                        <input type="hidden" name="package_id" value="<?= $package['id'] ?>">

                        <!-- Thông tin hội viên -->
                        <div class="mb-3">
                            <label class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['fullName']) ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>" readonly>
                        </div>

                        <!-- Thông tin gói tập -->
                        <div class="mb-3">
                            <label class="form-label">Gói tập</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($package['name']) ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Thời hạn</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($package['duration']) ?> tháng" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Giá</label>
                            <input type="text" class="form-control" value="<?= number_format($package['price'], 0, ',', '.') ?> VNĐ" readonly>
                        </div>

                        <!-- Chọn huấn luyện viên -->
                        <div class="mb-3">
                            <label class="form-label">Chọn huấn luyện viên</label>
                            <select name="trainer_id" class="form-select" required>
                                <option value="">-- Chọn huấn luyện viên --</option>
                                <?php foreach ($trainers as $trainer): ?>
                                    <option value="<?= $trainer['id'] ?>">
                                        <?= htmlspecialchars($trainer['fullName']) ?>
                                        (<?= htmlspecialchars($trainer['specialization']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="text-end">
                            <a href="/gym/packages" class="btn btn-secondary">Hủy</a>
                            <button type="submit" class="btn btn-primary">Xác nhận đăng ký</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const packageDuration = <?= $package['duration'] ?>;

        startDateInput.addEventListener('change', function() {
            if (this.value) {
                const startDate = new Date(this.value);
                const endDate = new Date(startDate);
                endDate.setMonth(endDate.getMonth() + packageDuration);

                // Format the date as YYYY-MM-DD
                const formattedDate = endDate.toISOString().split('T')[0];
                endDateInput.value = formattedDate;
            } else {
                endDateInput.value = '';
            }
        });

        // Set minimum date for start date
        const today = new Date();
        const formattedToday = today.toISOString().split('T')[0];
        startDateInput.min = formattedToday;
    });
</script>

<style>
    .card {
        border: none;
        border-radius: 15px;
    }

    .card-header {
        border-radius: 15px 15px 0 0 !important;
    }

    .form-control,
    .form-select {
        padding: 0.75rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .btn {
        padding: 0.75rem 1.25rem;
        font-weight: 500;
    }
</style>