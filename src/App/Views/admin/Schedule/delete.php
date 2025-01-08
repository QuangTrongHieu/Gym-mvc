<?php foreach ($schedules as $schedule): ?>
    <div class="modal fade" id="deleteScheduleModal<?= $schedule['id'] ?>" tabindex="-1" aria-labelledby="deleteScheduleModalLabel<?= $schedule['id'] ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/gym/admin/schedule/delete/<?= $schedule['id'] ?>" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteScheduleModalLabel<?= $schedule['id'] ?>">Xác nhận xóa lịch tập</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Bạn có chắc chắn muốn xóa lịch tập này?</p>
                        <div class="alert alert-info">
                            <p><strong>Khách hàng:</strong> <?= $schedule['user_name'] ?></p>
                            <p><strong>Huấn luyện viên:</strong> <?= $schedule['trainer_name'] ?></p>
                            <p><strong>Ngày tập:</strong> <?= date('d/m/Y', strtotime($schedule['training_date'])) ?></p>
                            <p><strong>Thời gian:</strong> <?= date('H:i', strtotime($schedule['start_time'])) ?> - <?= date('H:i', strtotime($schedule['end_time'])) ?></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger">Xóa lịch tập</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>