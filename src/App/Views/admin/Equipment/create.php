<div class="modal fade" id="addEquipmentModal" tabindex="-1" aria-labelledby="addEquipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="/gym/admin/equipment/create" method="POST" enctype="multipart/form-data" id="addEquipmentForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEquipmentModalLabel">Thêm thiết bị mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label required">Tên thiết bị</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label required">Giá (VNĐ)</label>
                                <input type="number" class="form-control" id="price" name="price" min="0" required>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label required">Trạng thái</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active">Hoạt động</option>
                                    <option value="inactive">Ngừng hoạt động</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="image" class="form-label">Hình ảnh</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <div id="imagePreview" class="mt-2"></div>
                                <small class="text-muted">Hỗ trợ: JPG, PNG, GIF (tối đa 2MB)</small>
                            </div>

                            <div class="mb-3">
                                <label for="purchaseDate" class="form-label required">Ngày mua</label>
                                <input type="date" class="form-control" id="purchaseDate" name="purchaseDate" required>
                            </div>

                            <div class="mb-3">
                                <label for="lastMaintenanceDate" class="form-label">Ngày bảo trì gần nhất</label>
                                <input type="date" class="form-control" id="lastMaintenanceDate" name="lastMaintenanceDate">
                            </div>

                            <div class="mb-3">
                                <label for="nextMaintenanceDate" class="form-label">Ngày bảo trì tiếp theo</label>
                                <input type="date" class="form-control" id="nextMaintenanceDate" name="nextMaintenanceDate">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm thiết bị</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('imagePreview');

        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                alert('Kích thước file không được vượt quá 2MB');
                this.value = '';
                preview.innerHTML = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `
                <img src="${e.target.result}" class="img-thumbnail" style="max-height: 200px">
            `;
            }
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '';
        }
    });

    document.getElementById('addEquipmentForm').addEventListener('submit', function(e) {
        const lastMaintenanceDate = document.getElementById('lastMaintenanceDate').value;
        const nextMaintenanceDate = document.getElementById('nextMaintenanceDate').value;

        if (lastMaintenanceDate && nextMaintenanceDate) {
            if (new Date(lastMaintenanceDate) >= new Date(nextMaintenanceDate)) {
                e.preventDefault();
                alert('Ngày bảo trì tiếp theo phải sau ngày bảo trì gần nhất');
            }
        }
    });
</script>