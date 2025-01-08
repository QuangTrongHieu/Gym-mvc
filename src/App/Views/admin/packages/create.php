<div class="container-fluid px-4">
    <h1 class="mt-4">Thêm Gói tập mới</h1>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus me-1"></i>
            Thông tin gói tập
        </div>
        <div class="card-body">
            <form action="/gym/admin/packages/create" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Tên gói tập</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="duration" class="form-label">Thời hạn (tháng)</label>
                    <input type="number" class="form-control" id="duration" name="duration" required min="1">
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Giá (VNĐ)</label>
                    <input type="number" class="form-control" id="price" name="price" required min="0">
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-control" id="status" name="status">
                        <option value="active">Hoạt động</option>
                        <option value="inactive">Không hoạt động</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Thêm mới</button>
                <a href="/gym/admin/packages" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>