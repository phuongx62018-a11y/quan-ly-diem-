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
    <title>Trang Chủ - Quản Lý Điểm</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body style="font-family: Arial; text-align: center; margin-top: 50px;">
    <h1>Chào mừng bạn đến với hệ thống Quản lý Điểm!</h1>
    <p>Tài khoản: <b><?php echo $_SESSION['username']; ?></b> | Quyền: <?php echo $_SESSION['role']; ?></p>
    
    <div style="margin-top: 20px;">
        <a href="quanly_Diem.php" style="padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;">Đi đến trang Quản lý Điểm</a>
        <br><br>
        <a href="logout.php" style="color: red;">Đăng xuất hệ thống</a>
    </div>
</body>
</html>