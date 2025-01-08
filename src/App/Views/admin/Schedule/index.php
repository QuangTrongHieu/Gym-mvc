<?php
$title = 'Quản lý lịch tập';
?>

<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Quản lý lịch tập</h1>
        <div class="d-flex gap-2">
            <a href="/gym/admin/schedule/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm lịch tập
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Tháng</label>
                    <select name="month" class="form-select" onchange="this.form.submit()">
                        <?php for ($i = 1; $i <= 12; $i++) : ?>
                            <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>"
                                <?= $currentMonth == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' ?>>
                                Tháng <?= $i ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Năm</label>
                    <select name="year" class="form-select" onchange="this.form.submit()">
                        <?php
                        $currentYear = date('Y');
                        for ($i = $currentYear - 1; $i <= $currentYear + 1; $i++) :
                        ?>
                            <option value="<?= $i ?>" <?= $year == $i ? 'selected' : '' ?>>
                                <?= $i ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Lọc theo</label>
                    <select name="filter_type" class="form-select" id="filterType">
                        <option value="all" <?= $filter_type == 'all' ? 'selected' : '' ?>>Tất cả</option>
                        <option value="user" <?= $filter_type == 'user' ? 'selected' : '' ?>>Người dùng</option>
                        <option value="trainer" <?= $filter_type == 'trainer' ? 'selected' : '' ?>>Huấn luyện viên</option>
                    </select>
                </div>
                <div class="col-md-3" id="filterIdContainer" style="display: <?= $filter_type != 'all' ? 'block' : 'none' ?>;">
                    <label class="form-label">Chọn người dùng/HLV</label>
                    <select name="filter_id" class="form-select">
                        <option value="">Chọn...</option>
                        <?php if ($filter_type == 'user') : ?>
                            <?php foreach ($users as $user) : ?>
                                <option value="<?= $user['id'] ?>" <?= $filter_id == $user['id'] ? 'selected' : '' ?>>
                                    <?= $user['fullName'] ?>
                                </option>
                            <?php endforeach; ?>
                        <?php elseif ($filter_type == 'trainer') : ?>
                            <?php foreach ($trainers as $trainer) : ?>
                                <option value="<?= $trainer['id'] ?>" <?= $filter_id == $trainer['id'] ? 'selected' : '' ?>>
                                    <?= $trainer['fullName'] ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Lọc</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Calendar -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr class="text-center">
                            <th>Chủ Nhật</th>
                            <th>Thứ 2</th>
                            <th>Thứ 3</th>
                            <th>Thứ 4</th>
                            <th>Thứ 5</th>
                            <th>Thứ 6</th>
                            <th>Thứ 7</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $firstDay = strtotime("$currentYear-$currentMonth-01");
                        $daysInMonth = date('t', $firstDay);
                        $startDay = date('w', $firstDay);
                        $currentDay = 1;
                        $weeks = ceil(($daysInMonth + $startDay) / 7);

                        for ($i = 0; $i < $weeks; $i++) {
                            echo "<tr class='calendar-row' style='height: 120px;'>";
                            for ($j = 0; $j < 7; $j++) {
                                if (($i == 0 && $j < $startDay) || ($currentDay > $daysInMonth)) {
                                    echo "<td class='bg-light'></td>";
                                } else {
                                    $date = "$currentYear-$currentMonth-" . str_pad($currentDay, 2, '0', STR_PAD_LEFT);
                                    $daySchedules = array_filter($schedules, function ($schedule) use ($date) {
                                        return $schedule['training_date'] == $date;
                                    });

                                    echo "<td class='position-relative'>";
                                    echo "<div class='date-number'>$currentDay</div>";
                                    echo "<div class='schedule-list' style='max-height: 100px; overflow-y: auto;'>";

                                    foreach ($daySchedules as $schedule) {
                                        $statusClass = '';
                                        switch ($schedule['status']) {
                                            case 'pending':
                                                $statusClass = 'bg-warning';
                                                break;
                                            case 'confirmed':
                                                $statusClass = 'bg-success';
                                                break;
                                            case 'cancelled':
                                                $statusClass = 'bg-danger';
                                                break;
                                        }

                                        echo "<div class='schedule-item small p-1 mb-1 rounded {$statusClass} text-white'>";
                                        echo "<div>" . date('H:i', strtotime($schedule['start_time'])) . " - " .
                                            date('H:i', strtotime($schedule['end_time'])) . "</div>";
                                        echo "<div>HLV: {$schedule['trainer_name']}</div>";
                                        echo "<div>KH: {$schedule['user_name']}</div>";
                                        echo "</div>";
                                    }

                                    echo "</div></td>";
                                    $currentDay++;
                                }
                            }
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .date-number {
        position: absolute;
        top: 5px;
        right: 5px;
        font-weight: bold;
    }

    .schedule-list {
        padding-top: 25px;
    }

    .schedule-item {
        font-size: 0.8rem;
        cursor: pointer;
    }
</style>

<script>
    document.getElementById('filterType').addEventListener('change', function() {
        const filterIdContainer = document.getElementById('filterIdContainer');
        filterIdContainer.style.display = this.value === 'all' ? 'none' : 'block';
    });
</script>
<?php
require_once ROOT_PATH . '/src/App/Views/admin/Schedule/edit.php';
require_once ROOT_PATH . '/src/App/Views/admin/Schedule/create.php';
require_once ROOT_PATH . '/src/App/Views/admin/Schedule/delete.php';
?>