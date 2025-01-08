<?php


// Thêm phần theo dõi lịch tập
function displayTrainingSchedule($userId)
{
    // Lấy dữ liệu lịch tập từ cơ sở dữ liệu
    $trainingSchedule = getTrainingSchedule($userId);

    echo "<h1>Lịch Tập Của Bạn</h1>";
    echo "<table>";
    echo "<tr><th>Ngày</th><th>Hoạt Động</th><th>Thời Gian</th></tr>";

    foreach ($trainingSchedule as $training) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($training['date']) . "</td>";
        echo "<td>" . htmlspecialchars($training['activity']) . "</td>";
        echo "<td>" . htmlspecialchars($training['duration']) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
}

// ... mã hiện tại ...
?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-5 fw-bold mb-3">Lịch Tập Của Bạn</h1>
            <div class="d-flex justify-content-between align-items-center">
                <p class="text-muted">Theo dõi và quản lý lịch tập của bạn</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTrainingModal">
                    <i class="fas fa-plus me-2"></i>Thêm Lịch Tập
                </button>
            </div>
        </div>
    </div>

    <!-- Calendar View -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Schedule -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Lịch Tập Tuần Này</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Thời Gian</th>
                                    <th>Hoạt Động</th>
                                    <th>Huấn Luyện Viên</th>
                                    <th>Trạng Thái</th>
                                    <th>Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($schedules)): ?>
                                    <?php foreach ($schedules as $schedule): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold"><?= htmlspecialchars(date('d/m/Y', strtotime($schedule['date']))) ?></span>
                                                    <span class="text-muted"><?= htmlspecialchars($schedule['time']) ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-dumbbell me-2 text-primary"></i>
                                                    <span><?= htmlspecialchars($schedule['activity']) ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= $schedule['trainer_avatar'] ?? '/gym/public/assets/images/default-avatar.png' ?>"
                                                        class="rounded-circle me-2"
                                                        width="32"
                                                        height="32"
                                                        alt="Trainer">
                                                    <span><?= htmlspecialchars($schedule['trainer_name']) ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = match ($schedule['status']) {
                                                    'completed' => 'success',
                                                    'upcoming' => 'primary',
                                                    'cancelled' => 'danger',
                                                    default => 'secondary'
                                                };
                                                ?>
                                                <span class="badge bg-<?= $statusClass ?>-subtle text-<?= $statusClass ?>">
                                                    <?= ucfirst(htmlspecialchars($schedule['status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editTrainingModal"
                                                        data-schedule-id="<?= $schedule['id'] ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger"
                                                        onclick="deleteSchedule(<?= $schedule['id'] ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                                <p class="mb-0">Chưa có lịch tập nào được đặt</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Training Modal -->
<div class="modal fade" id="addTrainingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm Lịch Tập Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addTrainingForm">
                    <div class="mb-3">
                        <label class="form-label">Ngày</label>
                        <input type="date" class="form-control" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Thời gian</label>
                        <input type="time" class="form-control" name="time" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hoạt động</label>
                        <select class="form-select" name="activity" required>
                            <option value="">Chọn hoạt động</option>
                            <option value="cardio">Cardio</option>
                            <option value="strength">Tập sức mạnh</option>
                            <option value="yoga">Yoga</option>
                            <option value="swimming">Bơi lội</option>
                            <option value="boxing">Boxing</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Huấn luyện viên</label>
                        <select class="form-select" name="trainer_id" required>
                            <option value="">Chọn huấn luyện viên</option>
                            <?php if (!empty($trainers)): ?>
                                <?php foreach ($trainers as $trainer): ?>
                                    <option value="<?= $trainer['id'] ?>">
                                        <?= htmlspecialchars($trainer['fullName']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="saveTraining()">Lưu</button>
            </div>
        </div>
    </div>
</div>

<style>
    .badge {
        font-weight: 500;
        padding: 0.5em 0.75em;
    }

    .bg-success-subtle {
        background-color: rgba(25, 135, 84, 0.1);
    }

    .bg-primary-subtle {
        background-color: rgba(13, 110, 253, 0.1);
    }

    .bg-danger-subtle {
        background-color: rgba(220, 53, 69, 0.1);
    }

    .table> :not(caption)>*>* {
        padding: 1rem 0.75rem;
    }

    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }

    .card {
        border: none;
        transition: transform 0.2s ease-in-out;
    }

    .card:hover {
        transform: translateY(-2px);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize calendar
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: '/gym/api/training/schedule',
            eventClick: function(info) {
                // Handle event click
                showEventDetails(info.event);
            }
        });
        calendar.render();
    });

    function saveTraining() {
        const form = document.getElementById('addTrainingForm');
        const formData = new FormData(form);

        fetch('/gym/api/training/add', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Có lỗi xảy ra: ' + data.message);
                }
            });
    }

    function deleteSchedule(id) {
        if (confirm('Bạn có chắc muốn xóa lịch tập này?')) {
            fetch(`/gym/api/training/delete/${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Có lỗi xảy ra: ' + data.message);
                    }
                });
        }
    }
</script>