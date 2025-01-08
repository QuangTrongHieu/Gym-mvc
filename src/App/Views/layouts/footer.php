<footer class="footer-dark">
    <div class="container">
        <div class="row py-5">
            <!-- Cột 1: Menu chính -->
            <div class="col-md-3 col-sm-6 mb-4">
                <h5 class="text-primary fw-bold mb-4 border-bottom pb-2">MENU</h5>
                <ul class="list-unstyled footer-links">
                    <li class="mb-3">
                        <a href="gym/programs" class="text-light hover-effect">
                            <i class="fas fa-dumbbell me-2"></i>Chương Trình Tập
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="gym/list-trainers" class="text-light hover-effect">
                            <i class="fas fa-user-friends me-2"></i>Huấn Luyện Viên
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="gym/user/training" class="text-light hover-effect">
                            <i class="far fa-calendar-alt me-2"></i>Lịch Tập
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="gym/user/profile" class="text-light hover-effect">
                            <i class="fas fa-user-circle me-2"></i>Tài Khoản
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Cột 2: Dịch vụ
            <div class="col-md-3 col-sm-6 mb-4">
                <h5 class="text-primary fw-bold mb-4 border-bottom pb-2">DỊCH VỤ</h5>
                <ul class="list-unstyled footer-services">
                    <li class="mb-3"><i class="fas fa-user-shield me-2"></i>Tập Luyện Cá Nhân</li>
                    <li class="mb-3"><i class="fas fa-spa me-2"></i>Yoga & Thiền</li>
                    <li class="mb-3"><i class="fas fa-heartbeat me-2"></i>Cardio & Strength</li>
                    <li class="mb-3"><i class="fas fa-fire-alt me-2"></i>CrossFit</li>
                    <li class="mb-3"><i class="fas fa-apple-alt me-2"></i>Dinh Dưỡng Thể Thao</li>
                </ul>
            </div> -->

            <!-- Cột 3: Thông tin liên hệ -->
            <div class="col-md-3 col-sm-6 mb-4">
                <h5 class="text-primary fw-bold mb-4 border-bottom pb-2">LIÊN HỆ</h5>
                <ul class="list-unstyled footer-contact">
                    <li class="mb-3">
                        <i class="fas fa-building me-2"></i>
                        <strong>POWER GYM</strong>
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Số 123 Đường ABC, TP. Sơn La
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-envelope me-2"></i>
                        info@powergym.vn
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-phone-alt me-2"></i>
                        0123.456.789
                    </li>
                </ul>
            </div>

            <!-- Cột 4: Giờ hoạt động và Social -->
            <div class="col-md-3 col-sm-6 mb-4">
                <h5 class="text-primary fw-bold mb-4 border-bottom pb-2">GIỜ HOẠT ĐỘNG</h5>
                <ul class="list-unstyled footer-hours mb-4">
                    <li class="mb-3">
                        <i class="far fa-clock me-2"></i>
                        Thứ 2 - Thứ 6: 5:30 - 22:00
                    </li>
                    <li class="mb-3">
                        <i class="far fa-clock me-2"></i>
                        Thứ 7 - CN: 7:00 - 20:00
                    </li>
                </ul>
                <h5 class="text-primary fw-bold mb-4 border-bottom pb-2">KẾT NỐI</h5>
                <div class="social-links">
                    <a href="#" class="btn btn-outline-light me-2"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-outline-light me-2"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="btn btn-outline-light me-2"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="btn btn-outline-light"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Copyright -->
<div class="copyright py-3">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0 text-light">
                    <i class="far fa-copyright me-2"></i>2024 POWER GYM. Tất cả quyền được bảo lưu.
                </p>
            </div>
            <div class="col-md-6 text-end">
                <p class="mb-0 text-light">
                    <i class="fas fa-globe me-2"></i>www.powergym.vn
                </p>
            </div>
        </div>
    </div>
</div>

<!-- CSS để thêm vào file styles.css của bạn -->
<style>
    .footer-dark {
        background: linear-gradient(45deg, #1a1a1a, #2d2d2d);
        color: #ffffff;
    }

    .copyright {
        background-color: #000000;
    }

    .footer-links a,
    .footer-services li,
    .footer-contact li,
    .footer-hours li {
        color: #ffffff;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .footer-links a:hover {
        color: #0d6efd;
        padding-left: 10px;
    }

    .social-links .btn {
        width: 40px;
        height: 40px;
        padding: 0;
        line-height: 40px;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .social-links .btn:hover {
        background-color: #0d6efd;
        border-color: #0d6efd;
        transform: translateY(-3px);
    }

    .border-bottom {
        border-color: rgba(255, 255, 255, 0.1) !important;
    }

    .hover-effect {
        transition: all 0.3s ease;
    }

    .hover-effect:hover {
        transform: translateX(5px);
    }

    /* Hiệu ứng hover cho các mục dịch vụ */
    .footer-services li:hover,
    .footer-contact li:hover,
    .footer-hours li:hover {
        color: #0d6efd;
        cursor: pointer;
    }

    /* Animation cho icons */
    .fas,
    .far,
    .fab {
        transition: all 0.3s ease;
    }

    li:hover .fas,
    li:hover .far,
    li:hover .fab {
        transform: scale(1.2);
        color: #0d6efd;
    }
</style>