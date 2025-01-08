<?php if (!empty($trainers)): ?>
    <?php foreach ($trainers as $trainer): ?>
        <div class="container mt-4">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h1 class="h3 mb-0">Xóa Hội viên</h1>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error'];
                            unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <button type="button" class="btn btn-danger" onclick="showDeleteModal(<?= $trainer['id'] ?>, '<?= htmlspecialchars($trainer['fullName']) ?>')">
                        <i class="fas fa-trash-alt"></i> Xóa
                    </button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
<?php endif; ?>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Xóa Hội viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Bạn có chắc chắn muốn xóa hội viên <strong id="deleteMemberName"></strong>?</p>
                <small class="text-muted">Hành động này không thể hoàn tác.</small>
            </div>
            <div class="modal-footer">
                <form id="deleteMemberForm" action="" method="POST">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showDeleteModal(id, name) {
        document.getElementById('deleteMemberName').textContent = name;
        document.getElementById('deleteMemberForm').action = `/gym/admin/member/delete/${id}`;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>