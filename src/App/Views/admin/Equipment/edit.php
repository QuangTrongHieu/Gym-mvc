<?php if (isset($equipment)): ?>
    <div class="modal fade" id="editEquipmentModal<?= $equipment['id'] ?>" tabindex="-1" aria-labelledby="editEquipmentModalLabel<?= $equipment['id'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="/gym/admin/equipment/update/<?= $equipment['id'] ?>" method="POST" enctype="multipart/form-data" id="editEquipmentForm<?= $equipment['id'] ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editEquipmentModalLabel<?= $equipment['id'] ?>">Chỉnh sửa thiết bị</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name<?= $equipment['id'] ?>" class="form-label required">Tên thiết bị</label>
                                    <input type="text" class="form-control" id="name<?= $equipment['id'] ?>" name="name" value="<?= htmlspecialchars($equipment['name']) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="description<?= $equipment['id'] ?>" class="form-label">Mô tả</label>
                                    <textarea class="form-control" id="description<?= $equipment['id'] ?>" name="description" rows="3"><?= htmlspecialchars($equipment['description']) ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="price<?= $equipment['id'] ?>" class="form-label required">Giá (VNĐ)</label>
                                    <input type="number" class="form-control" id="price<?= $equipment['id'] ?>" name="price" value="<?= $equipment['price'] ?>" min="0" required>
                                </div>

                                <div class="mb-3">
                                    <label for="status<?= $equipment['id'] ?>" class="form-label required">Trạng thái</label>
                                    <select class="form-select" id="status<?= $equipment['id'] ?>" name="status" required>
                                        <option value="active" <?= $equipment['status'] === 'active' ? 'selected' : '' ?>>Hoạt động</option>
                                        <option value="inactive" <?= $equipment['status'] === 'inactive' ? 'selected' : '' ?>>Ngừng hoạt động</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="image<?= $equipment['id'] ?>" class="form-label">Hình ảnh</label>
                                    <?php if (!empty($equipment['image_path'])): ?>
                                        <div class="mb-2">
                                            <img src="<?= $equipment['image_path'] ?>" alt="Current image" class="img-thumbnail" style="max-height: 200px">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="image<?= $equipment['id'] ?>" name="image" accept="image/*">
                                    <div id="imagePreview<?= $equipment['id'] ?>" class="mt-2"></div>
                                    <small class="text-muted">Để trống nếu không muốn thay đổi ảnh. Hỗ trợ: JPG, PNG, GIF (tối đa 2MB)</small>
                                </div>

                                <div class="mb-3">
                                    <label for="purchaseDate<?= $equipment['id'] ?>" class="form-label required">Ngày mua</label>
                                    <input type="date" class="form-control" id="purchaseDate<?= $equipment['id'] ?>" name="purchaseDate" value="<?= $equipment['purchaseDate'] ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="lastMaintenanceDate<?= $equipment['id'] ?>" class="form-label">Ngày bảo trì gần nhất</label>
                                    <input type="date" class="form-control" id="lastMaintenanceDate<?= $equipment['id'] ?>" name="lastMaintenanceDate" value="<?= $equipment['lastMaintenanceDate'] ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="nextMaintenanceDate<?= $equipment['id'] ?>" class="form-label">Ngày bảo trì tiếp theo</label>
                                    <input type="date" class="form-control" id="nextMaintenanceDate<?= $equipment['id'] ?>" name="nextMaintenanceDate" value="<?= $equipment['nextMaintenanceDate'] ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('image<?= $equipment['id'] ?>').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagePreview<?= $equipment['id'] ?>');

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

        document.getElementById('editEquipmentForm<?= $equipment['id'] ?>').addEventListener('submit', function(e) {
            const lastMaintenanceDate = document.getElementById('lastMaintenanceDate<?= $equipment['id'] ?>').value;
            const nextMaintenanceDate = document.getElementById('nextMaintenanceDate<?= $equipment['id'] ?>').value;

            if (lastMaintenanceDate && nextMaintenanceDate) {
                if (new Date(lastMaintenanceDate) >= new Date(nextMaintenanceDate)) {
                    e.preventDefault();
                    alert('Ngày bảo trì tiếp theo phải sau ngày bảo trì gần nhất');
                }
            }
        });
    </script>
<?php else: ?>
<?php endif; ?>