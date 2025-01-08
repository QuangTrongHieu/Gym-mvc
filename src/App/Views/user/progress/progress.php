<div class="container mt-4">
    <h2 class="text-center mb-4">Theo Dõi Tiến Độ Tập Luyện</h2>

    <!-- Phần tổng quan -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Số buổi tập</h5>
                    <h3 class="card-text"><?php echo $data['total_workouts'] ?? 0; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tổng thời gian tập</h5>
                    <h3 class="card-text"><?php echo $data['total_hours'] ?? 0; ?> giờ</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Calo đã đốt</h5>
                    <h3 class="card-text"><?php echo $data['total_calories'] ?? 0; ?> kcal</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ tiến độ -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Biểu Đồ Tiến Độ</h5>
                    <canvas id="progressChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Lịch sử tập luyện -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Lịch Sử Tập Luyện</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Ngày</th>
                                    <th>Bài tập</th>
                                    <th>Thời gian</th>
                                    <th>Calo</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($data['workout_history'])): ?>
                                    <?php foreach ($data['workout_history'] as $workout): ?>
                                        <tr>
                                            <td><?php echo $workout->date; ?></td>
                                            <td><?php echo $workout->exercise_name; ?></td>
                                            <td><?php echo $workout->duration; ?> phút</td>
                                            <td><?php echo $workout->calories; ?> kcal</td>
                                            <td><?php echo $workout->notes; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script cho biểu đồ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('progressChart').getContext('2d');
    const progressChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($data['chart_labels'] ?? []); ?>,
            datasets: [{
                label: 'Thời gian tập (phút)',
                data: <?php echo json_encode($data['chart_data'] ?? []); ?>,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>