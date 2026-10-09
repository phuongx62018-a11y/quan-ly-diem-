<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ - Quản Lý Điểm Đồ Án</title>
    <!-- Nhúng Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Nhúng FontAwesome để dùng icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .dashboard-card {
            border: none;
            border-radius: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>
</head>
<body>

    <!-- Thanh Điều Hướng (Navbar) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-graduation-cap text-primary me-2"></i>Quản Lý Điểm Đồ Án
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item me-3 text-light">
                        <i class="fas fa-user-shield text-warning me-1"></i> Tài khoản: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> 
                        <span class="badge bg-primary ms-1"><?php echo htmlspecialchars($_SESSION['role'] ?? ''); ?></span>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="btn btn-outline-danger btn-sm px-3">
                            <i class="fas fa-sign-out-alt me-1"></i> Đăng xuất
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Nội dung chính -->
    <div class="container my-5">
        <!-- Lời chào -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="p-4 bg-white rounded-4 shadow-sm border-start border-4 border-primary">
                    <h2 class="fw-bold text-dark mb-1">Chào mừng bạn trở lại, <?php echo htmlspecialchars($_SESSION['username']); ?>! 👋</h2>
                    <p class="text-muted mb-0">Hôm nay bạn muốn thực hiện thao tác quản lý nào?</p>
                </div>
            </div>
        </div>

        <!-- Các thẻ chức năng nhanh -->
        <div class="row g-4">
            <!-- Quản lý Điểm (Khớp với link cũ quanly_Diem.php của bạn) -->
            <div class="col-md-4">
                <div class="card dashboard-card shadow-sm h-100 p-4 bg-white">
                    <div class="card-body text-center">
                        <div class="text-primary mb-3">
                            <i class="fas fa-table-list fa-3x"></i>
                        </div>
                        <h4 class="card-title fw-bold">Quản Lý Điểm</h4>
                        <p class="card-text text-muted small">Nhập, sửa, xóa và theo dõi bảng điểm chi tiết của sinh viên.</p>
                        <a href="quanly_Diem.php" class="btn btn-primary w-100 mt-3">
                            <i class="fas fa-arrow-right me-1"></i> Truy cập
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quản lý Môn học -->
            <div class="col-md-4">
                <div class="card dashboard-card shadow-sm h-100 p-4 bg-white">
                    <div class="card-body text-center">
                        <div class="text-success mb-3">
                            <i class="fas fa-book-open fa-3x"></i>
                        </div>
                        <h4 class="card-title fw-bold">Quản Lý Môn Học</h4>
                        <p class="card-text text-muted small">Thêm mới, cập nhật danh sách các học phần và môn học.</p>
                        <a href="monhoc/index.php" class="btn btn-success w-100 mt-3 text-white">
                            <i class="fas fa-arrow-right me-1"></i> Truy cập
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quản lý Sinh viên -->
            <div class="col-md-4">
                <div class="card dashboard-card shadow-sm h-100 p-4 bg-white">
                    <div class="card-body text-center">
                        <div class="text-warning mb-3">
                            <i class="fas fa-users-rectangle fa-3x"></i>
                        </div>
                        <h4 class="card-title fw-bold">Quản Lý Sinh Viên</h4>
                        <p class="card-text text-muted small">Quản lý hồ sơ, thông tin cá nhân của các sinh viên.</p>
                        <a href="sinhvien/index.php" class="btn btn-warning w-100 mt-3 text-dark fw-semibold">
                            <i class="fas fa-arrow-right me-1"></i> Truy cập
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chân trang -->
    <footer class="text-center py-4 text-muted mt-5 border-top bg-white">
        <small>&copy; 2026 Hệ thống Quản Lý Điểm. All rights reserved.</small>
    </footer>

    <!-- Nhúng Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>