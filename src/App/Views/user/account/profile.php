<div class="profile-content">
    <h2 class="fs-4 fw-bold mb-3">Hồ Sơ Của Tôi</h2>
    <p class="text-muted mb-4">Quản lý thông tin hồ sơ để bảo mật tài khoản</p>

    <div class="row">
        <div class="col-md-8">
            <form id="profileForm" action="/gym/user/updateProfile" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $this->csrf_token ?>" />
                <input type="hidden" name="id" value="<?= $user['id'] ?>" />
                
                <div class="mb-3">
                    <label class="form-label">Tên đăng nhập</label>
                    <input type="text" class="form-control" name="username" value="<?= isset($username) ? htmlspecialchars($username) : '' ?>" readonly />
                </div>
                <div class="mb-3">
                    <label class="form-label">Họ và tên</label>
                    <input type="text" class="form-control" name="fullName" value="<?= isset($fullName) ? htmlspecialchars($fullName) : '' ?>" required />
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <input type="email" class="form-control" name="email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" readonly />
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changeEmailModal">Thay đổi</button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <div class="input-group">
                        <input type="tel" class="form-control" name="phone" value="<?= isset($phone) ? htmlspecialchars($phone) : '' ?>" readonly />
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changePhoneModal">Thay đổi</button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Giới tính</label>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="sex" value="Male" <?= (isset($sex) && $sex == 'Male') ? 'checked' : '' ?> />
                            <label class="form-check-label">Nam</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="sex" value="Female" <?= (isset($sex) && $sex == 'Female') ? 'checked' : '' ?> />
                            <label class="form-check-label">Nữ</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="sex" value="Other" <?= (isset($sex) && $sex == 'Other') ? 'checked' : '' ?> />
                            <label class="form-check-label">Khác</label>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Ngày sinh</label>
                    <input type="date" class="form-control" name="dateOfBirth" value="<?= isset($dateOfBirth) ? htmlspecialchars($dateOfBirth) : '' ?>" required />
                </div>
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            </form>
        </div>
        
        <div class="col-md-4">
            <div class="text-center">
                <div class="position-relative d-inline-block mb-3">
                    <img id="avatarPreview" 
                         src="<?= isset($avatarUrl) && $avatarUrl ? '/gym/public/uploads/avatars/' . htmlspecialchars($avatarUrl) : '/gym/public/assets/images/default-avatar.png' ?>" 
                         class="rounded-circle" 
                         alt="Avatar" 
                         style="width: 200px; height: 200px; object-fit: cover;" />
                    
                    <div class="position-absolute bottom-0 end-0">
                        <label for="avatarInput" class="btn btn-light rounded-circle p-2" style="cursor: pointer;">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" 
                               id="avatarInput" 
                               name="avatar" 
                               accept="image/*" 
                               style="display: none;" />
                    </div>
                </div>
                <p class="text-muted small">
                    Cho phép JPG, GIF hoặc PNG. Kích thước tối đa 5MB
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('avatarInput').addEventListener('change', async function(e) {
    const file = this.files[0];
    if (!file) return;

    // Hiển thị preview
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('avatarPreview').src = e.target.result;
    }
    reader.readAsDataURL(file);

    // Upload file
    const formData = new FormData();
    formData.append('avatar', file);
    formData.append('csrf_token', '<?= $this->csrf_token ?>');

    try {
        const response = await fetch('/gym/user/updateAvatar', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        if (result.success) {
            // Cập nhật ảnh preview với URL mới
            document.getElementById('avatarPreview').src = result.avatarUrl;
            // Hiển thị thông báo thành công
            alert('Cập nhật ảnh đại diện thành công!');
        } else {
            alert(result.error || 'Có lỗi xảy ra khi cập nhật ảnh đại diện');
        }
    } catch (error) {
        alert('Có lỗi xảy ra khi cập nhật ảnh đại diện');
        console.error('Error:', error);
    }
});

document.getElementById('profileForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    try {
        const response = await fetch(this.action, {
            method: 'POST',
            body: new FormData(this),
            credentials: 'same-origin'
        });

        const data = await response.json();

        if (data.success) {
            alert('Cập nhật thông tin thành công!');
            window.location.reload();
        } else {
            alert(data.error || 'Có lỗi xảy ra khi cập nhật thông tin');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật thông tin');
    }
});
</script>