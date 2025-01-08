<?php $title = 'Đội ngũ Huấn luyện viên'; ?>

<div class="trainer-section py-5">
    <div class="container">
        <h1 class="section-title text-center mb-5">Đội ngũ Huấn luyện viên</h1>

        <?php if (!empty($trainers)): ?>
            <?php 
            $itemsPerPage = 30;
            $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $totalTrainers = count($trainers);
            $totalPages = ceil($totalTrainers / $itemsPerPage);
            $currentPage = max(1, min($currentPage, $totalPages));
            $offset = ($currentPage - 1) * $itemsPerPage;
            $displayedTrainers = array_slice($trainers, $offset, $itemsPerPage);

            // Build base URL with existing parameters
            $params = $_GET;
            unset($params['page']); // Remove page from existing parameters
            $baseUrl = '/gym/list-trainers';
            $queryString = http_build_query($params);
            $baseUrl = $baseUrl . ($queryString ? '?' . $queryString . '&' : '?');
            ?>

            <div class="row g-4 justify-content-center">
                <?php foreach ($displayedTrainers as $trainer): ?>
                    <div class="col-lg trainer-col">
                        <div class="trainer-card">
                            <div class="trainer-header">
                                <?php
                                $avatar = $trainer['avatar'] ?? 'default.jpg';
                                $avatarFullPath = ROOT_PATH . '/public/uploads/trainers/' . $avatar;
                                $avatarUrl = file_exists($avatarFullPath)
                                    ? '/gym/public/uploads/trainers/' . $avatar
                                    : '/gym/public/assets/images/default-avatar.png';
                                ?>
                                <div class="trainer-image-wrapper">
                                    <img src="<?= htmlspecialchars($avatarUrl) ?>"
                                        class="trainer-image"
                                        alt="<?= htmlspecialchars($trainer['fullName'] ?? 'Huấn luyện viên') ?>"
                                        loading="lazy">
                                </div>
                                <div class="trainer-overlay">
                                    <div class="social-links">
                                        <?php if (!empty($trainer['facebook'])): ?>
                                            <a href="<?= htmlspecialchars($trainer['facebook']) ?>"
                                                target="_blank"
                                                title="Facebook"
                                                rel="noopener noreferrer"
                                                class="social-link">
                                                <i class="fab fa-facebook-f"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if (!empty($trainer['instagram'])): ?>
                                            <a href="<?= htmlspecialchars($trainer['instagram']) ?>"
                                                target="_blank"
                                                title="Instagram"
                                                rel="noopener noreferrer"
                                                class="social-link">
                                                <i class="fab fa-instagram"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="trainer-content">
                                <h3 class="trainer-name"><?= htmlspecialchars($trainer['fullName'] ?? '') ?></h3>
                                
                                <div class="trainer-specialty">
                                    <i class="fas fa-dumbbell"></i>
                                    <span><?= htmlspecialchars($trainer['specialization'] ?? 'Chuyên môn chưa cập nhật') ?></span>
                                </div>

                                <div class="trainer-details">
                                    <div class="detail-item">
                                        <i class="fas fa-clock"></i>
                                        <span><?= intval($trainer['experience'] ?? 0) > 0 ? intval($trainer['experience']) . ' năm kinh nghiệm' : 'Mới vào nghề' ?></span>
                                    </div>
                                    
                                    <?php if (!empty($trainer['certification'])): ?>
                                        <div class="detail-item">
                                            <i class="fas fa-certificate"></i>
                                            <span><?= htmlspecialchars($trainer['certification']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav aria-label="Trainer pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($currentPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= $baseUrl ?>page=<?= $currentPage - 1 ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-chevron-left"></i>
                                </span>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= $baseUrl ?>page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= $baseUrl ?>page=<?= $currentPage + 1 ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-2"></i>
                Hiện chưa có huấn luyện viên nào.
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .trainer-section {
        background-color: #f8f9fa;
    }

    .section-title {
        color: #2c3e50;
        font-weight: 700;
        position: relative;
        padding-bottom: 15px;
    }

    .section-title:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: linear-gradient(to right, #ff4d4d, #ff6b6b);
    }

    .trainer-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .trainer-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .trainer-header {
        position: relative;
        padding-top: 100%; /* 1:1 Aspect Ratio */
        overflow: hidden;
    }

    .trainer-image-wrapper {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0,0,0,0.1), rgba(0,0,0,0.3));
    }

    .trainer-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .trainer-card:hover .trainer-image {
        transform: scale(1.1);
    }

    .trainer-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 20px;
        background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .trainer-card:hover .trainer-overlay {
        opacity: 1;
    }

    .trainer-content {
        padding: 25px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .trainer-name {
        font-size: 1.5rem;
        color: #2c3e50;
        font-weight: 700;
        margin: 0;
        text-align: center;
    }

    .trainer-specialty {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        color: #ff4d4d;
        font-size: 1.1rem;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .trainer-details {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: auto;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #666;
        font-size: 0.95rem;
    }

    .detail-item i {
        color: #ff4d4d;
        font-size: 1.1rem;
        width: 20px;
        text-align: center;
    }

    .social-links {
        display: flex;
        justify-content: center;
        gap: 15px;
    }

    .social-link {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        color: #ff4d4d;
        border-radius: 50%;
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }

    .social-link:hover {
        background: #ff4d4d;
        color: white;
        transform: translateY(-3px);
    }

    @media (max-width: 768px) {
        .trainer-header {
            padding-top: 75%; /* 4:3 Aspect Ratio for mobile */
        }
    }

    .trainer-col {
        flex: 0 0 auto;
        width: calc(100% / 5);
        max-width: calc(100% / 5);
        padding: 0 10px;
    }

    @media (max-width: 1400px) {
        .trainer-col {
            width: calc(100% / 4);
            max-width: calc(100% / 4);
        }
    }

    @media (max-width: 1200px) {
        .trainer-col {
            width: calc(100% / 3);
            max-width: calc(100% / 3);
        }
    }

    @media (max-width: 992px) {
        .trainer-col {
            width: calc(100% / 2);
            max-width: calc(100% / 2);
        }
    }

    @media (max-width: 576px) {
        .trainer-col {
            width: 100%;
            max-width: 100%;
        }
    }

    .pagination {
        gap: 5px;
    }

    .page-link {
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2c3e50;
        border: none;
        background-color: white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .page-link:hover {
        background-color: #ff4d4d;
        color: white;
        transform: translateY(-2px);
    }

    .page-item.active .page-link {
        background-color: #ff4d4d;
        color: white;
    }

    .page-item.disabled .page-link {
        background-color: #f8f9fa;
        color: #6c757d;
        cursor: not-allowed;
    }
</style>