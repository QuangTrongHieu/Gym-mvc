<?php if (isset($trainer)): ?>
    <div class="container-fluid px-4">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error'];
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php foreach ($trainer as $item): ?>
            <div class="modal fade" id="editModal<?= $item['id'] ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-user-edit me-2"></i>Chỉnh sửa thông tin: <?= htmlspecialchars($item['fullName']) ?>
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editForm<?= $item['id'] ?>" action="/gym/admin/trainer/edit/<?= $item['id'] ?>" method="POST" 
                                enctype="multipart/form-data" class="needs-validation" novalidate>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Ảnh đại diện hiện tại</label>
                                            <div class="mb-2">
                                                <?php
                                                $avatarUrl = !empty($item['avatar']) ? '/gym/public/uploads/trainers/' . $item['avatar'] : null;
                                                if ($avatarUrl && file_exists(ROOT_PATH . '/public/uploads/trainers/' . $item['avatar'])):
                                                ?>
                                                    <img src="<?= $avatarUrl ?>" alt="Avatar" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="alert alert-info">Chưa có ảnh đại diện</div>
                                                <?php endif; ?>
                                            </div>
                                            <input type="file" name="avatar" class="form-control" accept="image/*">
                                            <div class="form-text">Hỗ trợ định dạng: JPG, PNG, GIF (Max: 5MB)</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Username</label>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($item['username']) ?>" disabled>
                                            <div class="form-text">Username không thể thay đổi</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Họ tên</label>
                                            <input type="text" name="fullName" class="form-control" required
                                                minlength="2" maxlength="50" value="<?= htmlspecialchars($item['fullName']) ?>">
                                            <div class="invalid-feedback">
                                                Vui lòng nhập họ tên từ 2-50 ký tự
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" required
                                                value="<?= htmlspecialchars($item['email']) ?>">
                                            <div class="invalid-feedback">
                                                Vui lòng nhập email hợp lệ
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Mật khẩu mới (để trống nếu không đổi)</label>
                                            <div class="input-group">
                                                <input type="password" name="password" class="form-control" minlength="6"
                                                    id="password<?= $item['id'] ?>">
                                                <button class="btn btn-outline-secondary" type="button" 
                                                    onclick="togglePassword(<?= $item['id'] ?>)">
                                                    <i class="fas fa-eye" id="toggleIcon<?= $item['id'] ?>"></i>
                                                </button>
                                            </div>
                                            <div class="invalid-feedback">
                                                Mật khẩu phải có ít nhất 6 ký tự
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Ngày sinh</label>
                                            <input type="date" name="dateOfBirth" class="form-control" required
                                                max="<?= date('Y-m-d', strtotime('-18 years')) ?>"
                                                value="<?= $item['dateOfBirth'] ?>">
                                            <div class="invalid-feedback">
                                                Huấn luyện viên phải đủ 18 tuổi
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Giới tính</label>
                                            <select name="sex" class="form-select" required>
                                                <option value="Male" <?= $item['sex'] == 'Male' ? 'selected' : '' ?>>Nam</option>
                                                <option value="Female" <?= $item['sex'] == 'Female' ? 'selected' : '' ?>>Nữ</option>
                                                <option value="Other" <?= $item['sex'] == 'Other' ? 'selected' : '' ?>>Khác</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Số điện thoại</label>
                                            <input type="tel" name="phone" class="form-control" required
                                                pattern="[0-9]{10,11}" value="<?= htmlspecialchars($item['phone']) ?>">
                                            <div class="invalid-feedback">
                                                Số điện thoại phải có 10-11 chữ số
                                            </div>
                                        </div>
                                        <!-- <div class="mb-3">
                                            <label class="form-label">Chuyên môn</label>
                                            <select name="specialization" class="form-select" required>
                                                <option value="">Chọn chuyên môn</option>
                                                <?php
                                                $specializations = ['Yoga', 'Fitness', 'Boxing', 'Cardio', 'Strength Training', 'CrossFit'];
                                                foreach ($specializations as $spec):
                                                ?>
                                                    <option value="<?= $spec ?>" <?= $item['specialization'] == $spec ? 'selected' : '' ?>>
                                                        <?= $spec ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div> -->
                                        <div class="mb-3">
                                            <label class="form-label">Kinh nghiệm (năm)</label>
                                            <input type="number" name="experience" class="form-control" required
                                                min="0" max="50" value="<?= htmlspecialchars($item['experience']) ?>">
                                            <div class="invalid-feedback">
                                                Số năm kinh nghiệm phải từ 0-50
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Chứng chỉ</label>
                                    <textarea name="certification" class="form-control" rows="3" required
                                        minlength="10" maxlength="500"><?= htmlspecialchars($item['certification']) ?></textarea>
                                    <div class="invalid-feedback">
                                        Vui lòng nhập thông tin chứng chỉ (10-500 ký tự)
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Lương (VNĐ)</label>
                                    <input type="number" name="salary" class="form-control" required
                                        min="1000000" step="100000" value="<?= $item['salary'] ?>">
                                    <div class="invalid-feedback">
                                        Lương phải từ 1,000,000 VNĐ trở lên
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="fas fa-times me-2"></i>Hủy
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Lưu thay đổi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form validation for all edit forms
            document.querySelectorAll('form[id^="editForm"]').forEach(form => {
                form.addEventListener('submit', function(event) {
                    if (!this.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    this.classList.add('was-validated');
                });
            });

            // File size validation for all avatar inputs
            document.querySelectorAll('input[name="avatar"]').forEach(input => {
                input.addEventListener('change', function() {
                    if (this.files[0]) {
                        if (this.files[0].size > 5 * 1024 * 1024) {
                            alert('Kích thước file không được vượt quá 5MB');
                            this.value = '';
                        }
                    }
                });
            });

            // Salary validation for all salary inputs
            document.querySelectorAll('input[name="salary"]').forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value) {
                        this.value = Math.max(1000000, Math.min(this.value, 100000000));
                    }
                });
            });
        });

        // Password visibility toggle function
        function togglePassword(id) {
            const passwordInput = document.getElementById('password' + id);
            const toggleIcon = document.getElementById('toggleIcon' + id);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        </script>
    </div>
<?php else: ?>
    <div class="alert alert-danger">
        Không tìm thấy thông tin huấn luyện viên
    </div>
<?php endif; ?>