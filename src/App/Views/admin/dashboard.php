<div class="container mt-4">
    <h1>Chào mừng đến với Trang quản trị</h1>
    <div class="row mt-4">

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-user-shield me-2"></i>
                        Quản lý Admin
                    </h5>
                    <p class="card-text">Quản lý tài khoản admin của hệ thống</p>
                    <a href="/gym/admin/admin-management" class="btn btn-primary">Truy cập</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-dumbbell me-2"></i>
                        Quản lý huấn luyện viên
                    </h5>
                    <p class="card-text">Quản lý thông tin huấn luyện viên</p>
                    <a href="/gym/admin/trainer" class="btn btn-primary">Truy cập</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-cogs me-2"></i>
                        Quản lý Thiết bị
                    </h5>
                    <p class="card-text">Quản lý thông tin thiết bị</p>
                    <a href="/gym/admin/equipment" class="btn btn-primary">Truy cập</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-chart-pie me-2"></i>
                        Thống kê gói tập
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Gói tập</th>
                                    <th scope="col">Số người đăng ký</th>
                                    <th scope="col">Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalUsers = 0;
                                $totalRevenue = 0;
                                foreach ($revenueData as $revenue): 
                                    $totalUsers += $revenue['total_users'];
                                    $totalRevenue += $revenue['total_revenue'];
                                ?>
                                    <tr>
                                        <td>
                                            <i class="fas fa-cube me-2 text-primary"></i>
                                            <?php echo htmlspecialchars($revenue['package_name']); ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-users me-2 text-success"></i>
                                            <?php echo htmlspecialchars($revenue['total_users']); ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-money-bill-wave me-2 text-warning"></i>
                                            <?php echo number_format($revenue['total_revenue'], 0, ',', '.'); ?> VNĐ
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="table-info">
                                    <td><strong>Tổng cộng</strong></td>
                                    <td><strong><?php echo $totalUsers; ?> người</strong></td>
                                    <td><strong><?php echo number_format($totalRevenue, 0, ',', '.'); ?> VNĐ</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-chart-pie me-2"></i>
                        Thống kê người dùng
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="userPieChart"></canvas>
                        </div>
                        <div class="col-md-6">
                            <div class="stats-legend mt-3">
                                <div class="legend-item mb-2">
                                    <span class="legend-color" style="background-color: rgb(75, 192, 192)"></span>
                                    <span>Hội viên</span>
                                    <span class="member-count"></span>
                                </div>
                                <div class="legend-item mb-2">
                                    <span class="legend-color" style="background-color: rgb(255, 99, 132)"></span>
                                    <span>Huấn luyện viên</span>
                                    <span class="trainer-count"></span>
                                </div>
                                <div class="legend-item mb-2">
                                    <span class="legend-color" style="background-color: rgb(54, 162, 235)"></span>
                                    <span>Người dùng</span>
                                    <span class="user-count"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.legend-item {
    display: flex;
    align-items: center;
    gap: 10px;
}
.legend-color {
    width: 20px;
    height: 20px;
    border-radius: 3px;
}
</style>

<!-- Add Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let userPieChart;
    let revenueChart;

    function initCharts(data) {
        initUserChart(data);
        initRevenueChart();
    }

    function initUserChart(data) {
        const ctx = document.getElementById('userPieChart').getContext('2d');
        
        if (userPieChart) {
            userPieChart.destroy();
        }

        // Calculate totals
        const memberTotal = data.members[data.members.length - 1];
        const trainerTotal = data.trainers[data.trainers.length - 1];
        const userTotal = data.users[data.users.length - 1];

        // Update legend counts
        document.querySelector('.member-count').textContent = `(${memberTotal})`;
        document.querySelector('.trainer-count').textContent = `(${trainerTotal})`;
        document.querySelector('.user-count').textContent = `(${userTotal})`;

        userPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Hội viên', 'Huấn luyện viên', 'Người dùng'],
                datasets: [{
                    data: [memberTotal, trainerTotal, userTotal],
                    backgroundColor: [
                        'rgb(75, 192, 192)',
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const value = context.raw;
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${context.label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    function initRevenueChart() {
        const revenueCtx = document.getElementById('revenueChart');
        if (!revenueCtx) return;

        const revenueData = <?php echo json_encode($revenueData ?? []); ?>;
        if (revenueData.length === 0) return;

        if (revenueChart) {
            revenueChart.destroy();
        }

        revenueChart = new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: revenueData.map(item => item.package_name),
                datasets: [
                    {
                        label: 'Số người đăng ký',
                        data: revenueData.map(item => item.total_users),
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor: 'rgb(75, 192, 192)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Doanh thu (VNĐ)',
                        data: revenueData.map(item => item.total_revenue),
                        backgroundColor: 'rgba(255, 159, 64, 0.5)',
                        borderColor: 'rgb(255, 159, 64)',
                        borderWidth: 1,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Số người đăng ký'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Doanh thu (VNĐ)'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
    }

    function fetchData() {
        fetch('/gym/admin/getUserStats?days=1')
            .then(response => response.json())
            .then(data => {
                initCharts(data);
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    }

    // Initial load
    fetchData();

    // Update every minute
    setInterval(fetchData, 60000);
});
</script>

<style>
.card {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
    margin-bottom: 1.5rem;
}

.card:hover {
    transform: translateY(-5px);
}

.card-title {
    color: #2c3e50;
    font-weight: bold;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #eee;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 5px 0;
}

.legend-color {
    width: 20px;
    height: 20px;
    border-radius: 3px;
}

.table {
    margin-bottom: 0;
}

.table th {
    background-color: #f8f9fa;
    border-top: none;
}

.table td, .table th {
    vertical-align: middle;
}

.alert {
    margin-bottom: 0;
}

.fa-money-bill-wave {
    color: #f1c40f;
}

.fa-users {
    color: #2ecc71;
}

.fa-cube {
    color: #3498db;
}
</style>