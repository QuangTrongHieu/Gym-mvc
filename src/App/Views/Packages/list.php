<div class="container py-5">
    <h1 class="text-center mb-5"><?= $title ?></h1>

  <!-- Debug session 
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    ?>

    <?php if (isset($_SESSION)): ?>
    <div class="alert alert-info">
        Debug: Session contents:<br>
        <pre><?php print_r($_SESSION); ?></pre>
    </div>
    <?php endif; ?>-->

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($packages as $package): ?>
            <div class="col">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-center mb-4"><?= htmlspecialchars($package['name']) ?></h5>
                        <div class="package-info mb-4">
                            <p class="card-text">
                                <strong>Mô tả:</strong> <?= htmlspecialchars($package['description']) ?><br>
                                <strong>Thời hạn:</strong> <?= htmlspecialchars($package['duration']) ?> tháng<br>
                                <strong>Giá:</strong> <?= number_format($package['price'], 0, ',', '.') ?> VNĐ
                            </p>
                        </div>
                        <div class="text-center mt-auto">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="/gym/membership/register/<?= $package['id'] ?>" 
                                   class="btn btn-primary btn-lg w-75">
                                    Đăng ký ngay
                                </a>
                            <?php else: ?>
                                <a href="/gym/login" 
                                   class="btn btn-secondary btn-lg w-75">
                                    Đăng nhập để đăng ký
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}

.card-body {
    padding: 2rem;
    min-height: 400px;
}

.card-title {
    color: #2c3e50;
    font-size: 1.5rem;
    font-weight: bold;
}

.package-info {
    color: #34495e;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
}

.btn-primary {
    background-color: #3498db;
    border-color: #3498db;
}

.btn-primary:hover {
    background-color: #2980b9;
    border-color: #2980b9;
}

.btn-secondary {
    background-color: #95a5a6;
    border-color: #95a5a6;
}

.btn-secondary:hover {
    background-color: #7f8c8d;
    border-color: #7f8c8d;
}
</style><!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="container py-5">
    <h1 class="text-center mb-5"><?= $title ?></h1>

    {{ ... }}

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($packages as $package): ?>
            <div class="col">
                <div class="card h-100 package-card" data-aos="fade-up">
                    <div class="card-body d-flex flex-column">
                        <div class="package-icon text-center mb-4">
                            <i class="fas fa-dumbbell fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title text-center mb-4"><?= htmlspecialchars($package['name']) ?></h5>
                        <div class="package-info mb-4">
                            <p class="card-text">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Mô tả:</strong> <?= htmlspecialchars($package['description']) ?><br>
                                <i class="fas fa-clock me-2"></i>
                                <strong>Thời hạn:</strong> <?= htmlspecialchars($package['duration']) ?> tháng<br>
                                <i class="fas fa-tag me-2"></i>
                                <strong>Giá:</strong> <?= number_format($package['price'], 0, ',', '.') ?> VNĐ
                            </p>
                        </div>
                        <div class="text-center mt-auto">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="/gym/membership/register/<?= $package['id'] ?>" 
                                   class="btn btn-primary btn-lg w-75">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Đăng ký ngay
                                </a>
                            <?php else: ?>
                                <a href="/gym/login" 
                                   class="btn btn-secondary btn-lg w-75">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Đăng nhập để đăng ký
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    overflow: hidden;
    background: linear-gradient(145deg, #ffffff, #f5f5f5);
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
}

.card-body {
    padding: 2rem;
    min-height: 400px;
}

.card-title {
    color: #2c3e50;
    font-size: 1.5rem;
    font-weight: bold;
    position: relative;
}

.card-title:after {
    content: '';
    display: block;
    width: 50px;
    height: 3px;
    background: #3498db;
    margin: 15px auto;
}

.package-info {
    color: #34495e;
}

.package-icon {
    margin-bottom: 1.5rem;
}

.package-icon i {
    background: linear-gradient(45deg, #3498db, #2980b9);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(45deg, #3498db, #2980b9);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(45deg, #2980b9, #2573a7);
    transform: translateY(-2px);
}

.btn-secondary {
    background: linear-gradient(45deg, #95a5a6, #7f8c8d);
    border: none;
}

.btn-secondary:hover {
    background: linear-gradient(45deg, #7f8c8d, #6c7a7d);
    transform: translateY(-2px);
}

.me-2 {
    margin-right: 0.5rem;
}

/* Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.package-card {
    animation: fadeInUp 0.6s ease-out;
    animation-fill-mode: both;
}

.package-card:nth-child(1) { animation-delay: 0.1s; }
.package-card:nth-child(2) { animation-delay: 0.2s; }
.package-card:nth-child(3) { animation-delay: 0.3s; }
</style>

<!-- AOS Library -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS
    AOS.init({
        duration: 800,
        easing: 'ease-out',
        once: true
    });

    // Add hover effect for cards
    const cards = document.querySelectorAll('.package-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>