<div class="modal fade" id="addScheduleModal" tabindex="-1" aria-labelledby="addScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="/gym/admin/schedule/create" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addScheduleModalLabel">Thêm Lịch Tập Mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="user_id" class="form-label">Khách hàng</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Chọn khách hàng</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>"><?= $user['fullName'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="trainer_id" class="form-label">Huấn luyện viên</label>
                            <select class="form-select" id="trainer_id" name="trainer_id" required>
                                <option value="">Chọn huấn luyện viên</option>
                                <?php foreach ($trainers as $trainer): ?>
                                    <option value="<?= $trainer['id'] ?>"><?= $trainer['fullName'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="training_date" class="form-label">Ngày tập</label>
                            <input type="date" class="form-control" id="training_date" name="training_date" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="start_time" class="form-label">Giờ bắt đầu</label>
                            <input type="time" class="form-control" id="start_time" name="start_time" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="end_time" class="form-label">Giờ kết thúc</label>
                            <input type="time" class="form-control" id="end_time" name="end_time" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Ghi chú</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending">Chờ xử lý</option>
                            <option value="confirmed">Đã xác nhận</option>
                            <option value="completed">Hoàn thành</option>
                            <option value="cancelled">Đã hủy</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm lịch tập</button>
                </div>
            </form>
        </div>
    </div>
</div>