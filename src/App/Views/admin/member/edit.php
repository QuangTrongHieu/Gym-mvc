<?php if (isset($member)): ?>
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Chỉnh sửa thông tin hội viên</h5>
            </div>
            <div class="card-body">
                <form action="/gym/admin/member/edit/<?= $member['id'] ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $this->csrf_token ?>" />
                    <input type="hidden" name="id" value="<?= $member['id'] ?>" />

                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="mb-4">
                                <img src="<?= !empty($member['avatar']) ? '/gym/public/uploads/members/avatars/' . $member['avatar'] : '/gym/public/assets/images/default-avatar.png' ?>"
                                     alt="Avatar"
                                     class="rounded-circle mb-3"
                                     style="width: 150px; height: 150px; object-fit: cover;"
                                     id="avatarPreview">
                                <div>
                                    <label for="avatar" class="form-label">Ảnh đại diện</label>
                                    <input type="file"
                                           id="avatar"
                                           name="avatar"
                                           class="form-control"
                                           accept="image/*"
                                           onchange="previewImage(this, 'avatarPreview')">
                                    <div class="form-text">Để trống nếu không muốn thay đổi ảnh</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tên đăng nhập</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="username" 
                                           value="<?= htmlspecialchars($member['username']) ?>" 
                                           readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Họ và tên</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="fullName" 
                                           value="<?= htmlspecialchars($member['fullName']) ?>" 
                                           required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" 
                                           class="form-control" 
                                           name="email" 
                                           value="<?= htmlspecialchars($member['email']) ?>" 
                                           required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="tel" 
                                           class="form-control" 
                                           name="phone" 
                                           value="<?= htmlspecialchars($member['phone']) ?>" 
                                           required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Giới tính</label>
                                    <select class="form-select" name="sex">
                                        <option value="Male" <?= $member['sex'] == 'Male' ? 'selected' : '' ?>>Nam</option>
                                        <option value="Female" <?= $member['sex'] == 'Female' ? 'selected' : '' ?>>Nữ</option>
                                        <option value="Other" <?= $member['sex'] == 'Other' ? 'selected' : '' ?>>Khác</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Trạng thái</label>
                                    <select class="form-select" name="status">
                                        <option value="ACTIVE" <?= $member['status'] == 'ACTIVE' ? 'selected' : '' ?>>Hoạt động</option>
                                        <option value="INACTIVE" <?= $member['status'] == 'INACTIVE' ? 'selected' : '' ?>>Không hoạt động</option>
                                    </select>
                                </div>

                                <?php if (isset($packages) && !empty($packages)): ?>
                                <div class="col-md-12">
                                    <label class="form-label">Gói tập</label>
                                    <select class="form-select" name="package_id">
                                        <option value="">-- Chọn gói tập --</option>
                                        <?php foreach ($packages as $package): ?>
                                            <option value="<?= $package['id'] ?>" 
                                                    <?= isset($member['package_id']) && $member['package_id'] == $package['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($package['name']) ?> 
                                                (<?= number_format($package['price']) ?> VNĐ)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php endif; ?>

                                <?php if (isset($member['package_id'])): ?>
                                <div class="col-md-6">
                                    <label class="form-label">Ngày bắt đầu</label>
                                    <input type="date" 
                                           class="form-control" 
                                           name="startDate" 
                                           value="<?= $member['startDate'] ?? date('Y-m-d') ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Ngày kết thúc</label>
                                    <input type="date" 
                                           class="form-control" 
                                           name="endDate" 
                                           value="<?= $member['endDate'] ?? '' ?>">
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <a href="/gym/admin/member" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-danger">
        Không tìm thấy thông tin hội viên
    </div>
<?php endif; ?>

<script>
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(previewId).src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>