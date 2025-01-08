<?php foreach ($schedules as $schedule): ?>
    <div class="modal fade" id="editScheduleModal<?= $schedule['id'] ?>" tabindex="-1" aria-labelledby="editScheduleModalLabel<?= $schedule['id'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="/gym/admin/schedule/update/<?= $schedule['id'] ?>" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editScheduleModalLabel<?= $schedule['id'] ?>">Sửa Lịch Tập</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="user_id<?= $schedule['id'] ?>" class="form-label">Khách hàng</label>
                                <select class="form-select" id="user_id<?= $schedule['id'] ?>" name="user_id" required>
                                    <option value="">Chọn khách hàng</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= $user['id'] ?>" <?= $user['id'] == $schedule['user_id'] ? 'selected' : '' ?>>
                                            <?= $user['fullName'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="trainer_id<?= $schedule['id'] ?>" class="form-label">Huấn luyện viên</label>
                                <select class="form-select" id="trainer_id<?= $schedule['id'] ?>" name="trainer_id" required>
                                    <option value="">Chọn huấn luyện viên</option>
                                    <?php foreach ($trainers as $trainer): ?>
                                        <option value="<?= $trainer['id'] ?>" <?= $trainer['id'] == $schedule['trainer_id'] ? 'selected' : '' ?>>
                                            <?= $trainer['fullName'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="training_date<?= $schedule['id'] ?>" class="form-label">Ngày tập</label>
                                <input type="date" class="form-control" id="training_date<?= $schedule['id'] ?>"
                                    name="training_date" value="<?= $schedule['training_date'] ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="start_time<?= $schedule['id'] ?>" class="form-label">Giờ bắt đầu</label>
                                <input type="time" class="form-control" id="start_time<?= $schedule['id'] ?>"
                                    name="start_time" value="<?= $schedule['start_time'] ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="end_time<?= $schedule['id'] ?>" class="form-label">Giờ kết thúc</label>
                                <input type="time" class="form-control" id="end_time<?= $schedule['id'] ?>"
                                    name="end_time" value="<?= $schedule['end_time'] ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="notes<?= $schedule['id'] ?>" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="notes<?= $schedule['id'] ?>"
                                name="notes" rows="3"><?= $schedule['notes'] ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="status<?= $schedule['id'] ?>" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status<?= $schedule['id'] ?>" name="status" required>
                                <option value="pending" <?= $schedule['status'] == 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                                <option value="confirmed" <?= $schedule['status'] == 'confirmed' ? 'selected' : '' ?>>Đã xác nhận</option>
                                <option value="completed" <?= $schedule['status'] == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                                <option value="cancelled" <?= $schedule['status'] == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                            </select>
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
<?php endforeach; ?>