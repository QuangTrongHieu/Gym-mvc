<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý hội viên</h1>
    
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Danh sách hội viên
            <div class="float-end">
                <a href="/gym/admin/member/export" class="btn btn-success me-2">
                    <i class="fas fa-file-excel"></i> Xuất Excel
                </a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createMemberModal">
                    <i class="fas fa-plus"></i> Thêm mới
                </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tên đăng nhập</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Ngày sinh</th>
                        <th>Giới tính</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($members as $member): ?>
                    <tr>
                        <td><?= $member['id'] ?></td>
                        <td><?= htmlspecialchars($member['username']) ?></td>
                        <td><?= htmlspecialchars($member['fullName']) ?></td>
                        <td><?= htmlspecialchars($member['email']) ?></td>
                        <td><?= htmlspecialchars($member['phone']) ?></td>
                        <td><?= htmlspecialchars($member['dateOfBirth']) ?></td>
                        <td><?= $member['sex'] == 'Male' ? 'Nam' : ($member['sex'] == 'Female' ? 'Nữ' : 'Khác') ?></td>
                        <td>
                            <span class="badge <?= $member['status'] == 'active' ? 'bg-success' : 'bg-danger' ?>">
                                <?= $member['status'] == 'active' ? 'Hoạt động' : 'Không hoạt động' ?>
                            </span>
                        </td>
                        <td>
                            <a href="/gym/admin/member/edit/<?= $member['id'] ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            <form action="/gym/admin/member/delete/<?= $member['id'] ?>" method="POST" class="d-inline">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Member Modal -->
<div class="modal fade" id="createMemberModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm hội viên mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="/gym/admin/member/create" method="POST" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên đăng nhập</label>
                            <input type="text" name="username" class="form-control" required>
                            <div class="invalid-feedback">Vui lòng nhập tên đăng nhập</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mật khẩu</label>
                            <input type="password" name="password" class="form-control" required>
                            <div class="invalid-feedback">Vui lòng nhập mật khẩu</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Họ tên</label>
                            <input type="text" name="fullName" class="form-control" required>
                            <div class="invalid-feedback">Vui lòng nhập họ tên</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                            <div class="invalid-feedback">Vui lòng nhập email hợp lệ</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="tel" name="phone" class="form-control" pattern="[0-9]{10}" required>
                            <div class="invalid-feedback">Vui lòng nhập số điện thoại hợp lệ (10 số)</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ngày sinh</label>
                            <input type="date" name="dateOfBirth" class="form-control" required>
                            <div class="invalid-feedback">Vui lòng chọn ngày sinh</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giới tính</label>
                            <select name="sex" class="form-select" required>
                                <option value="">Chọn giới tính</option>
                                <option value="Male">Nam</option>
                                <option value="Female">Nữ</option>
                                <option value="Other">Khác</option>
                            </select>
                            <div class="invalid-feedback">Vui lòng chọn giới tính</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gói hội viên</label>
                            <select name="package" class="form-select" required>
                                <option value="">Chọn gói hội viên</option>
                                <option value="basic">Cơ bản</option>
                                <option value="premium">Premium</option>
                                <option value="vip">VIP</option>
                            </select>
                            <div class="invalid-feedback">Vui lòng chọn gói hội viên</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ngày bắt đầu</label>
                            <input type="date" name="startDate" class="form-control" required>
                            <div class="invalid-feedback">Vui lòng chọn ngày bắt đầu</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ngày kết thúc</label>
                            <input type="date" name="endDate" class="form-control" required>
                            <div class="invalid-feedback">Vui lòng chọn ngày kết thúc</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select" required>
                            <option value="active">Hoạt động</option>
                            <option value="inactive">Không hoạt động</option>
                        </select>
                        <div class="invalid-feedback">Vui lòng chọn trạng thái</div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Hủy
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
})()

// Date validation for the create form
const startDate = document.querySelector('[name="startDate"]');
const endDate = document.querySelector('[name="endDate"]');

if (startDate && endDate) {
    startDate.addEventListener('change', function() {
        endDate.min = this.value;
        if (endDate.value && endDate.value < this.value) {
            endDate.value = this.value;
        }
    });
    
    endDate.addEventListener('change', function() {
        startDate.max = this.value;
        if (startDate.value && startDate.value > this.value) {
            startDate.value = this.value;
        }
    });
}
</script>