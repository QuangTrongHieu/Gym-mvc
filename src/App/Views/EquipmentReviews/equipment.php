<?php
$title = 'Gym Equipment';
?>

<!-- Custom CSS -->
<style>
    .equipment-section {
        background-color: #f8f9fa;
        padding: 3rem 0;
    }

    .section-title {
        color: #2c3e50;
        font-weight: 700;
        margin-bottom: 2rem;
        position: relative;
        padding-bottom: 1rem;
    }

    .section-title:after {
        content: '';
        display: block;
        width: 50px;
        height: 3px;
        background: #3498db;
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
    }

    .equipment-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }

    .equipment-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .equipment-image {
        height: 250px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .equipment-card:hover .equipment-image {
        transform: scale(1.05);
    }

    .card-body {
        padding: 1.5rem;
    }

    .equipment-title {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .equipment-description {
        color: #7f8c8d;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 1rem;
        height: 80px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 500;
        font-size: 0.85rem;
    }

    .status-available {
        background-color: #2ecc71;
        color: white;
    }

    .status-unavailable {
        background-color: #e74c3c;
        color: white;
    }

    .card-footer {
        background-color: white;
        border-top: 1px solid rgba(0,0,0,0.05);
        padding: 1rem 1.5rem;
    }

    .maintenance-date {
        color: #95a5a6;
        font-size: 0.9rem;
    }

    .no-equipment {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .no-equipment i {
        font-size: 3rem;
        color: #95a5a6;
        margin-bottom: 1rem;
    }
</style>

<div class="equipment-section">
    <div class="container">
        <h2 class="text-center section-title">Thiết bị của chúng tôi</h2>
        
        <div class="row g-4">
            <?php if (!empty($equipments)) : ?>
                <?php foreach ($equipments as $equipment) : ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card equipment-card">
                            <?php if (!empty($equipment['image_path'])) : ?>
                                <img src="/gym<?php echo $equipment['image_path']; ?>"
                                    class="card-img-top equipment-image"
                                    alt="<?php echo htmlspecialchars($equipment['name']); ?>">
                            <?php else : ?>
                                <img src="/gym/public/assets/images/default-equipment.jpg"
                                    class="card-img-top equipment-image"
                                    alt="Default Equipment Image">
                            <?php endif; ?>

                            <div class="card-body">
                                <h5 class="equipment-title"><?php echo htmlspecialchars($equipment['name']); ?></h5>
                                <p class="equipment-description"><?php echo htmlspecialchars($equipment['description']); ?></p>
                                <div class="d-flex align-items-center">
                                    <span class="status-badge <?php echo $equipment['status'] === 'Available' ? 'status-available' : 'status-unavailable'; ?>">
                                        <i class="fas <?php echo $equipment['status'] === 'Available' ? 'fa-check-circle' : 'fa-times-circle'; ?> me-1"></i>
                                        <?php echo htmlspecialchars($equipment['status']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="maintenance-date">
                                    <i class="fas fa-tools me-2"></i>
                                    Last maintained: <?php echo date('M d, Y', strtotime($equipment['lastMaintenanceDate'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="col-12">
                    <div class="no-equipment">
                        <i class="fas fa-dumbbell mb-3"></i>
                        <h4>No Equipment Available</h4>
                        <p class="text-muted">Check back later for updates on our gym equipment.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Make sure Font Awesome is included in your layout -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">