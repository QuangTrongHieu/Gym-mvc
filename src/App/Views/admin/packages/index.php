<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý Gói tập</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success'];
            unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error'];
            unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Danh sách gói tập
            <a href="/gym/admin/packages/create" class="btn btn-primary float-end">Thêm mới</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên gói</th>
                        <th>Mô tả</th>
                        <th>Thời hạn (tháng)</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($packages as $package): ?>
                        <tr>
                            <td><?= $package['id'] ?></td>
                            <td><?= $package['name'] ?></td>
                            <td><?= $package['description'] ?></td>
                            <td><?= $package['duration'] ?></td>
                            <td><?= number_format($package['price']) ?> VNĐ</td>
                            <td><?= $package['status'] ?></td>
                            <td>
                                <a href="/gym/admin/packages/edit/<?= $package['id'] ?>" class="btn btn-sm btn-primary">Sửa</a>
                                <form action="/gym/admin/packages/delete/<?= $package['id'] ?>" method="POST" class="d-inline">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>