<?php
$pageTitle = "Dashboard";
require_once __DIR__ . '/../../layouts/Trainer_layout.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">Bảng điều khiển</h2>

            <!-- Lịch tập Card -->
            <div class="col-xl-6 col-md-12 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Lịch tập</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $trainingSchedule ?? 'Chưa có lịch tập' ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin cá nhân Card -->
            <div class="col-xl-6 col-md-12 mb-4">
                <div class="card border-left-secondary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                    Thông tin cá nhân</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $personalInfo ?? 'Chưa có thông tin' ?></div>
                            </div>
                            <div class="col-auto">
                                <a href="/gym/trainer/profile/edit" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Chỉnh sửa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin cá nhân -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Thông tin cá nhân</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Tên:</strong> <?= htmlspecialchars($trainer->fullName) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($trainer->email) ?></p>
                            <?php if (!empty($trainer->phone)): ?>
                                <p><strong>SĐT:</strong> <?= htmlspecialchars($trainer->phone) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <?php if (!empty($trainer->specialization)): ?>
                                <p><strong>Chuyên môn:</strong> <?= htmlspecialchars($trainer->specialization) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($trainer->experience)): ?>
                                <p><strong>Kinh nghiệm:</strong> <?= htmlspecialchars($trainer->experience) ?> năm</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lịch huấn luyện -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Lịch huấn luyện</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($trainingSchedule)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Học viên</th>
                                        <th>Ngày</th>
                                        <th>Thời gian</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($trainingSchedule as $schedule): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($schedule->member_name) ?></td>
                                            <td><?= date('d/m/Y', strtotime($schedule->date)) ?></td>
                                            <td><?= date('H:i', strtotime($schedule->time)) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $schedule->status == 'completed' ? 'success' : 'primary' ?>">
                                                    <?= $schedule->status == 'completed' ? 'Hoàn thành' : 'Chờ thực hiện' ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Không có lịch huấn luyện nào sắp tới.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>