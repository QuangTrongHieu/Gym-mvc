<?php foreach ($Packages as $Package): ?> // Thay đổi từ $Trainers sang $Packages
    <div class="modal fade" id="deleteModal<?= $Package['id'] ?>" tabindex="-1"> // Thay đổi từ $Trainer sang $Package
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận Xóa Gói Tập</h5> // Cập nhật tiêu đề
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="/gym/admin/package/delete/<?= $Package['id'] ?>" method="POST"> //Cập nhật đường dẫn
                        <input type="hidden" name="id" value="<?= $Package['id'] ?>">
                        <p>Bạn có chắc muốn xóa gói tập này?</p> // Cập nhật thông điệp
                        <p><strong>Tên Gói:</strong> <?= $Package['name'] ?></p> // Cập nhật thông tin

                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-danger">Xóa</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>