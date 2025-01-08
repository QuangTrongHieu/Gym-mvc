<?php
$pageTitle = "Chỉnh sửa thông tin cá nhân";
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Chỉnh sửa thông tin cá nhân</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error'] ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success'] ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="/gym/trainer/profile/update" method="POST" class="user">
                <div class="form-group">
                    <label for="fullName">Họ và tên</label>
                    <input type="text" class="form-control" id="fullName" name="fullName"
                        value="<?= htmlspecialchars($trainer->fullName ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="<?= htmlspecialchars($trainer->email ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">Số điện thoại</label>
                    <input type="tel" class="form-control" id="phone" name="phone"
                        value="<?= htmlspecialchars($trainer->phone ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="specialization">Chuyên môn</label>
                    <textarea class="form-control" id="specialization" name="specialization"
                        rows="3"><?= htmlspecialchars($trainer->specialization ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="experience">Kinh nghiệm (năm)</label>
                    <input type="number" class="form-control" id="experience" name="experience"
                        value="<?= htmlspecialchars($trainer->experience ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu mới (để trống nếu không muốn thay đổi)</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>

                <div class="form-group">
                    <label for="password_confirm">Xác nhận mật khẩu mới</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu thay đổi
                </button>
                <a href="/gym/trainer/dashboard" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </form>
        </div>
    </div>
</div>