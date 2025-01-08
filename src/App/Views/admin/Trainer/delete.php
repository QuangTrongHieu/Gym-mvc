<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Xác nhận xóa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-user-times fa-4x text-danger mb-3"></i>
                    <h5>Bạn có chắc chắn muốn xóa huấn luyện viên này?</h5>
                    <p class="text-muted">Hành động này không thể hoàn tác.</p>
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Lưu ý:</strong> Việc xóa huấn luyện viên sẽ:
                    <ul class="mb-0 mt-2">
                        <li>Hủy tất cả các lịch huấn luyện trong tương lai</li>
                        <li>Vô hiệu hóa tài khoản huấn luyện viên</li>
                        <li>Ẩn thông tin huấn luyện viên khỏi hệ thống</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Hủy
                </button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="fas fa-trash-alt me-2"></i>Xác nhận xóa
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let trainerId = null;
    const deleteModal = document.getElementById('deleteModal');
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    
    if (deleteModal) {
        const modal = new bootstrap.Modal(deleteModal);
        
        // Show delete modal function
        window.showDeleteModal = function(id) {
            trainerId = id;
            
            // Clear any existing error messages
            const existingError = deleteModal.querySelector('.alert-danger');
            if (existingError) {
                existingError.remove();
            }
            
            modal.show();
        };
        
        // Handle delete confirmation
        confirmDeleteBtn?.addEventListener('click', function() {
            if (!trainerId) return;
            
            // Disable button and show loading state
            const originalContent = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
            
            // Send delete request
            fetch(`/gym/admin/trainer/delete/${trainerId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success message
                    const successAlert = document.createElement('div');
                    successAlert.className = 'alert alert-success alert-dismissible fade show fixed-top m-3';
                    successAlert.innerHTML = `
                        <i class="fas fa-check-circle me-2"></i>
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(successAlert);
                    
                    // Remove the trainer row
                    const trainerRow = document.querySelector(`tr[data-id="${trainerId}"]`);
                    if (trainerRow) {
                        trainerRow.remove();
                    }
                    
                    // Close modal
                    modal.hide();
                    
                    // Check if table is empty
                    const tbody = document.querySelector('table tbody');
                    if (!tbody.children.length) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p class="mb-0">Không có dữ liệu huấn luyện viên</p>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }
                    
                    // Auto-remove success message after 3 seconds
                    setTimeout(() => {
                        successAlert.remove();
                    }, 3000);
                } else {
                    throw new Error(data.message || 'Có lỗi xảy ra khi xóa huấn luyện viên');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Show error message in modal
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger mt-3 mb-0';
                errorDiv.innerHTML = `<i class="fas fa-exclamation-circle me-2"></i>${error.message}`;
                
                const modalBody = deleteModal.querySelector('.modal-body');
                const existingError = modalBody.querySelector('.alert-danger');
                if (existingError) {
                    existingError.remove();
                }
                modalBody.appendChild(errorDiv);
            })
            .finally(() => {
                // Reset button state
                confirmDeleteBtn.disabled = false;
                confirmDeleteBtn.innerHTML = originalContent;
            });
        });
    }
});
</script>