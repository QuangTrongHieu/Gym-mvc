<div class="container contact-section py-5">
    <div class="contact-header text-center mb-5">
        <h1 class="display-4 fw-bold">Liên Hệ Với Chúng Tôi</h1>
        <p class="lead text-muted">Hãy để lại thông tin, chúng tôi sẽ liên hệ với bạn sớm nhất</p>
    </div>

    <div class="contact-form">
        <div class="contact-info mb-5">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="contact-info-item h-100 p-4 bg-white rounded-3 shadow-sm">
                        <div class="contact-icon mb-3">
                            <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold">Địa Chỉ</h5>
                            <p class="mb-0 text-muted">19 Lê Duẩn, Quyết tâm, TP-Sơn La</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-info-item h-100 p-4 bg-white rounded-3 shadow-sm">
                        <div class="contact-icon mb-3">
                            <i class="fas fa-phone fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold">Điện Thoại</h5>
                            <p class="mb-0 text-muted">+84 899 813 764</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-info-item h-100 p-4 bg-white rounded-3 shadow-sm">
                        <div class="contact-icon mb-3">
                            <i class="fas fa-envelope fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold">Email</h5>
                            <p class="mb-0 text-muted">info@powergym.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <form id="contactForm" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="name" placeholder="Họ tên" required>
                                        <label for="name">Họ Tên</label>
                                        <div class="invalid-feedback">
                                            Vui lòng nhập họ tên của bạn
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control" id="email" placeholder="Email" required>
                                        <label for="email">Email</label>
                                        <div class="invalid-feedback">
                                            Vui lòng nhập email hợp lệ
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="subject" placeholder="Tiêu đề" required>
                                <label for="subject">Tiêu Đề</label>
                                <div class="invalid-feedback">
                                    Vui lòng nhập tiêu đề
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="message" style="height: 150px" placeholder="Tin nhắn" required></textarea>
                                <label for="message">Tin Nhắn</label>
                                <div class="invalid-feedback">
                                    Vui lòng nhập nội dung tin nhắn
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-paper-plane me-2"></i>Gửi Tin Nhắn
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="success-message" id="successMessage">
            <i class="fas fa-check-circle fa-3x mb-3"></i>
            <h4>Cảm ơn bạn đã liên hệ!</h4>
            <p>Chúng tôi sẽ phản hồi trong thời gian sớm nhất.</p>
        </div>
    </div>
</div>

<style>
    .contact-section {
        background-color: #f8f9fa;
    }

    .contact-info-item {
        text-align: center;
        transition: all 0.3s ease;
    }

    .contact-info-item:hover {
        transform: translateY(-5px);
    }

    .contact-icon {
        color: var(--bs-primary);
    }

    .form-control:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .btn-primary {
        padding: 0.8rem 2rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .success-message {
        display: none;
        text-align: center;
        padding: 2rem;
        margin: 2rem auto;
        max-width: 500px;
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        animation: slideIn 0.5s ease-out;
    }

    .success-message i {
        color: #28a745;
        margin-bottom: 1.5rem;
        animation: scaleIn 0.5s ease-out;
    }

    .success-message h4 {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 1rem;
        font-size: 1.5rem;
    }

    .success-message p {
        color: #6c757d;
        font-size: 1.1rem;
        line-height: 1.6;
        margin-bottom: 0;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes scaleIn {
        from {
            transform: scale(0);
        }

        to {
            transform: scale(1);
        }
    }
</style>

<script>
    // Form validation
    (function() {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                } else {
                    event.preventDefault()
                    // Show success message
                    form.style.display = 'none'
                    document.getElementById('successMessage').style.display = 'block'
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>