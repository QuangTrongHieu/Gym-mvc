<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center font-weight-light my-2">Thêm Huấn Luyện Viên Mới</h3>
                </div>
                <div class="card-body">
                    <form id="createTrainerForm" action="/gym/admin/trainer/create" method="POST" enctype="multipart/form-data" novalidate>
                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                        <div class="row">
                            <div class="col-md-4 text-center mb-3">
                                <div class="mb-3">
                                    <label class="form-label">Ảnh đại diện</label>
                                    <div class="avatar-preview mb-2">
                                        <img id="avatarPreview" src="/gym/public/uploads/trainers/default.jpg" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                                    </div>
                                    <input type="file" name="avatar" id="avatar" class="form-control" accept="image/*" onchange="previewImage(this)">
                                    <small class="text-muted">Chọn ảnh đại diện (JPG, PNG, tối đa 5MB)</small>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <!-- Thông tin cơ bản -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                                        <input type="text" name="fullName" class="form-control" required 
                                               pattern="^[a-zA-ZÀ-ỹ\s]{3,50}$"
                                               title="Họ tên phải từ 3-50 ký tự, không chứa số và ký tự đặc biệt"
                                               placeholder="Nhập họ tên đầy đủ">
                                        <div class="invalid-feedback">Vui lòng nhập họ tên hợp lệ</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                                        <input type="text" name="username" class="form-control" required
                                               pattern="^[a-zA-Z0-9_]{3,20}$"
                                               title="Username 3-20 ký tự, chỉ bao gồm chữ, số và dấu gạch dưới"
                                               placeholder="Tên đăng nhập">
                                        <div class="invalid-feedback">Username không hợp lệ</div>
                                    </div>
                                </div>

                                <!-- Email và Password -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control" required
                                               pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                               placeholder="Nhập địa chỉ email">
                                        <div class="invalid-feedback">Email không hợp lệ</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                        <input type="password" name="password" class="form-control" required
                                               minlength="6"
                                               placeholder="Nhập mật khẩu">
                                        <div class="invalid-feedback">Mật khẩu tối thiểu 6 ký tự</div>
                                    </div>
                                </div>

                                <!-- Ngày sinh và Giới tính -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Ngày sinh <span class="text-danger">*</span></label>
                                        <input type="date" name="dateOfBirth" class="form-control" required
                                               max="<?= date('Y-m-d', strtotime('-18 years')) ?>">
                                        <div class="invalid-feedback">Phải đủ 18 tuổi trở lên</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Giới tính <span class="text-danger">*</span></label>
                                        <select name="sex" class="form-control" required>
                                            <option value="">Chọn giới tính</option>
                                            <option value="Male">Nam</option>
                                            <option value="Female">Nữ</option>
                                            <option value="Other">Khác</option>
                                        </select>
                                        <div class="invalid-feedback">Vui lòng chọn giới tính</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin liên hệ và kinh nghiệm -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="form-control" required
                                       pattern="(0|\+84)[3|5|7|8|9][0-9]{8}"
                                       placeholder="Nhập số điện thoại">
                                <div class="invalid-feedback">Số điện thoại không hợp lệ</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kinh nghiệm (năm)</label>
                                <input type="number" name="experience" class="form-control" required
                                       min="0" max="50"
                                       placeholder="Số năm kinh nghiệm">
                                <div class="invalid-feedback">Vui lòng nhập số năm kinh nghiệm hợp lệ</div>
                            </div>
                        </div>

                        <!-- Chuyên môn và chứng chỉ -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Chuyên môn <span class="text-danger">*</span></label>
                                <textarea name="specialization" class="form-control" required
                                          minlength="10" maxlength="500"
                                          placeholder="Mô tả chuyên môn"></textarea>
                                <div class="invalid-feedback">Vui lòng nhập chuyên môn (10-500 ký tự)</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Chứng chỉ <span class="text-danger">*</span></label>
                                <textarea name="certification" class="form-control" required
                                          minlength="10" maxlength="500"
                                          placeholder="Các chứng chỉ đã đạt"></textarea>
                                <div class="invalid-feedback">Vui lòng nhập thông tin chứng chỉ (10-500 ký tự)</div>
                            </div>
                        </div>

                        <!-- Lương -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Lương (VNĐ) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="salary" class="form-control" required
                                           min="1000000" max="100000000"
                                           placeholder="Nhập mức lương">
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                                <div class="invalid-feedback">Mức lương phải từ 1,000,000 đến 100,000,000 VNĐ</div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between mt-4 mb-0">
                            <a href="/gym/admin/trainer" class="btn btn-secondary">Quay lại</a>
                            <button type="submit" class="btn btn-primary">Thêm mới</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview image script -->
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Form validation
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>