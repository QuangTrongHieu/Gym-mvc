<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm Hội Viên Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="/gym/admin/member/create" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <div class="mb-3">
                        <label class="form-label">Ảnh đại diện</label>
                        <input type="file" name="avatar" class="form-control" accept="image/*">
                        <small class="text-muted">Tối đa 5MB (JPG, PNG)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" required
                               pattern="^[a-zA-Z0-9_]{3,20}$"
                               title="3-20 ký tự, chỉ dùng chữ, số và gạch dưới">
                        <div class="invalid-feedback">Tên đăng nhập không hợp lệ</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="fullName" class="form-control" required
                               pattern="^[a-zA-ZÀ-ỹ\s]{3,50}$">
                        <div class="invalid-feedback">Họ tên phải từ 3-50 ký tự</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                        <div class="invalid-feedback">Email không hợp lệ</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                        <div class="invalid-feedback">Mật khẩu ít nhất 6 ký tự</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ngày sinh <span class="text-danger">*</span></label>
                        <input type="date" name="dateOfBirth" class="form-control" required
                               max="<?= date('Y-m-d', strtotime('-16 years')) ?>">
                        <div class="invalid-feedback">Hội viên phải từ 16 tuổi trở lên</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Giới tính <span class="text-danger">*</span></label>
                        <select name="sex" class="form-control" required>
                            <option value="">Chọn giới tính</option>
                            <option value="Male">Nam</option>
                            <option value="Female">Nữ</option>
                            <option value="Other">Khác</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control" required
                               pattern="(0|\+84)[3|5|7|8|9][0-9]{8}">
                        <div class="invalid-feedback">Số điện thoại không hợp lệ</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <textarea name="address" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Thêm mới</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>