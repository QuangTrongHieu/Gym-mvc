<?php if (isset($equipment)): ?>
    <div class="modal fade" id="deleteEquipmentModal<?= $equipment['id'] ?>" tabindex="-1" aria-labelledby="deleteEquipmentModalLabel<?= $equipment['id'] ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/gym/admin/equipment/delete/<?= $equipment['id'] ?>" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteEquipmentModalLabel<?= $equipment['id'] ?>">Xác nhận xóa thiết bị</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Bạn có chắc chắn muốn xóa thiết bị "<strong><?= htmlspecialchars($equipment['name']) ?></strong>" không?</p>
                        <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> Lưu ý: Hành động này không thể hoàn tác!</p>

                        <?php if (!empty($equipment['image_path'])): ?>
                            <div class="text-center mb-3">
                                <img src="<?= $equipment['image_path'] ?>"
                                    alt="<?= htmlspecialchars($equipment['name']) ?>"
                                    class="img-thumbnail"
                                    style="max-height: 200px">
                            </div>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tr>
                                    <th>ID:</th>
                                    <td><?= $equipment['id'] ?></td>
                                </tr>
                                <tr>
                                    <th>Tên thiết bị:</th>
                                    <td><?= htmlspecialchars($equipment['name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Giá:</th>
                                    <td><?= number_format($equipment['price'], 0, ',', '.') ?> VNĐ</td>
                                </tr>
                                <tr>
                                    <th>Trạng thái:</th>
                                    <td>
                                        <span class="badge <?= $equipment['status'] === 'active' ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $equipment['status'] === 'active' ? 'Hoạt động' : 'Ngừng hoạt động' ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Xác nhận xóa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php else: ?>
<?php endif; ?>