<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý Thiết bị</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-dumbbell me-1"></i>
                Danh sách thiết bị
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
                <i class="fas fa-plus"></i> Thêm thiết bị mới
            </button>
        </div>
        <div class="card-body">
            <table id="equipmentTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hình ảnh</th>
                        <th>Tên thiết bị</th>
                        <th>Mô tả</th>
                        <th>Ngày mua</th>
                        <th>Giá (VNĐ)</th>
                        <th>Trạng thái</th>
                        <th>Bảo trì gần nhất</th>
                        <th>Bảo trì tiếp theo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($equipments) && is_array($equipments)): ?>
                        <?php foreach ($equipments as $equipment): ?>
                            <tr>
                                <td><?= $equipment['id'] ?></td>
                                <td class="text-center">
                                    <?php if (!empty($equipment['image_path'])): ?>
                                        <img src="/gym<?= $equipment['image_path'] ?>"
                                            alt="<?= htmlspecialchars($equipment['name']) ?>"
                                            class="img-thumbnail"
                                            style="max-width: 100px; max-height: 100px; object-fit: cover;">
                                    <?php else: ?>
                                        <span class="text-muted">Không có ảnh</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($equipment['name']) ?></td>
                                <td><?= htmlspecialchars($equipment['description']) ?></td>
                                <td><?= date('d/m/Y', strtotime($equipment['purchaseDate'])) ?></td>
                                <td><?= number_format($equipment['price'], 0, ',', '.') ?></td>
                                <td>
                                    <span class="badge <?= $equipment['status'] === 'active' ? 'bg-success' : 'bg-danger' ?>">
                                        <?= $equipment['status'] === 'active' ? 'Hoạt động' : 'Ngừng hoạt động' ?>
                                    </span>
                                </td>
                                <td><?= $equipment['lastMaintenanceDate'] ? date('d/m/Y', strtotime($equipment['lastMaintenanceDate'])) : 'Chưa có' ?></td>
                                <td><?= $equipment['nextMaintenanceDate'] ? date('d/m/Y', strtotime($equipment['nextMaintenanceDate'])) : 'Chưa có' ?></td>
                                <td>
                                    <button type="button"
                                        class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editEquipmentModal<?= $equipment['id'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button"
                                        class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteEquipmentModal<?= $equipment['id'] ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Equipment Modal -->
<?php require_once('create.php'); ?>

<!-- Edit Equipment Modals -->
<?php if (isset($equipments) && is_array($equipments)): ?>
    <?php foreach ($equipments as $equipment): ?>
        <?php require('edit.php'); ?>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Delete Equipment Modals -->
<?php if (isset($equipments) && is_array($equipments)): ?>
    <?php foreach ($equipments as $equipment): ?>
        <?php require('delete.php'); ?>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Initialize DataTable -->
<script>
    $(document).ready(function() {
        $('#equipmentTable').DataTable({
            "language": {
                "lengthMenu": "Hiển thị _MENU_ mục",
                "zeroRecords": "Không tìm thấy dữ liệu",
                "info": "Hiển thị _START_ đến _END_ của _TOTAL_ mục",
                "infoEmpty": "Không có mục nào",
                "infoFiltered": "(lọc từ _MAX_ mục)",
                "search": "Tìm kiếm:",
                "paginate": {
                    "first": "Đầu",
                    "last": "Cuối",
                    "next": "Sau",
                    "previous": "Trước"
                }
            },
            "pageLength": 10,
            "order": [
                [0, "desc"]
            ]
        });
    });
</script>