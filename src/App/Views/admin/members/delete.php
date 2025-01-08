
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h1 class="h3 mb-0">Xóa Hội viên</h1>
            </div>
            <div class="card-body">
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Bạn có chắc chắn muốn xóa hội viên <strong><?= htmlspecialchars($member['fullName']) ?></strong> không?
                    <br>
                    <small class="text-muted">Hành động này không thể hoàn tác.</small>
                </div>

                <form action="/gym/admin/member/destroy/<?= $member['id'] ?>" method="POST">
                    <input type="hidden" name="id" value="<?= $member['id'] ?>">
                    <div class="d-flex gap-2">
                        <a href="/gym/admin/member" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt"></i> Xác nhận xóa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

