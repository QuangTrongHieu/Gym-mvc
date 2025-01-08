
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h1 class="h3 mb-0">Sửa Hội viên</h1>
            </div>
            <div class="card-body">
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                
                <form action="/gym/admin/member/update/<?= $member['id'] ?>" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="id" value="<?= $member['id'] ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fullName" class="form-label">Họ tên</label>
                            <input type="text" class="form-control" id="fullName" name="fullName" 
                                value="<?= htmlspecialchars($member['fullName'] ?? '') ?>" required>
                            <div class="invalid-feedback">
                                Vui lòng nhập họ tên
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                value="<?= htmlspecialchars($member['email'] ?? '') ?>" required>
                            <div class="invalid-feedback">
                                Vui lòng nhập email hợp lệ
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                value="<?= htmlspecialchars($member['phone'] ?? '') ?>" 
                                pattern="[0-9]{10}" required>
                            <div class="invalid-feedback">
                                Vui lòng nhập số điện thoại hợp lệ (10 số)
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="package" class="form-label">Gói hội viên</label>
                            <select class="form-select" id="package" name="package" required>
                                <option value="">Chọn gói hội viên</option>
                                <option value="basic" <?= ($member['package'] ?? '') == 'basic' ? 'selected' : '' ?>>Cơ bản</option>
                                <option value="premium" <?= ($member['package'] ?? '') == 'premium' ? 'selected' : '' ?>>Premium</option>
                                <option value="vip" <?= ($member['package'] ?? '') == 'vip' ? 'selected' : '' ?>>VIP</option>
                            </select>
                            <div class="invalid-feedback">
                                Vui lòng chọn gói hội viên
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="startDate" class="form-label">Ngày bắt đầu</label>
                            <input type="date" class="form-control" id="startDate" name="startDate" 
                                value="<?= htmlspecialchars($member['startDate'] ?? '') ?>" required>
                            <div class="invalid-feedback">
                                Vui lòng chọn ngày bắt đầu
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="endDate" class="form-label">Ngày kết thúc</label>
                            <input type="date" class="form-control" id="endDate" name="endDate" 
                                value="<?= htmlspecialchars($member['endDate'] ?? '') ?>" required>
                            <div class="invalid-feedback">
                                Vui lòng chọn ngày kết thúc
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" <?= ($member['status'] ?? '') == 'active' ? 'selected' : '' ?>>
                                <i class="fas fa-check-circle text-success"></i> Hoạt động
                            </option>
                            <option value="inactive" <?= ($member['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>
                                <i class="fas fa-times-circle text-danger"></i> Không hoạt động
                            </option>
                        </select>
                        <div class="invalid-feedback">
                            Vui lòng chọn trạng thái
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="/gym/admin/member" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" href="/gym/admin/member" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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
    // Date validation
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');

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

    // Set initial min/max dates
    if (startDate.value) {
        endDate.min = startDate.value;
    }
    if (endDate.value) {
        startDate.max = endDate.value;
    }
    </script>