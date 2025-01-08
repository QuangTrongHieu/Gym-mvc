
    <title>Thêm Hội viên</title>
<body>
    <div class="container mt-4">
        <h1>Thêm Hội viên</h1>
        <form action="/gym/admin/member/store" method="POST">
            <div class="mb-3">
                <label for="fullName" class="form-label">Họ tên</label>
                <input type="text" class="form-control" id="fullName" name="fullName" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">SĐT</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="mb-3">
                <label for="package" class="form-label">Gói hội viên</label>
                <input type="text" class="form-control" id="package" name="package" required>
            </div>
            <div class="mb-3">
                <label for="startDate" class="form-label">Ngày bắt đầu</label>
                <input type="date" class="form-control" id="startDate" name="startDate" required>
            </div>
            <div class="mb-3">
                <label for="endDate" class="form-label">Ngày kết thúc</label>
                <input type="date" class="form-control" id="endDate" name="endDate" required>
            </div>
            <button type="submit" class="btn btn-primary">Thêm Hội viên</button>
        </form>
    </div>
</body>