<?php
session_start();
include 'config/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // --- Ý 2a: KIỂM SOÁT TÍNH NĂNG (Ràng buộc mật khẩu phải chứa ký tự '@') ---
    if (strpos($password, '@') === false) {
        $error = "Mật khẩu bảo mật yếu! Bắt buộc phải chứa ký tự '@'.";
    } else {
        // Truy vấn kiểm tra tài khoản trong database
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if ($password === $row['password']) {
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Mật khẩu không chính xác!";
            }
        } else {
            $error = "Tên đăng nhập không tồn tại!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập - Quản lý điểm</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body style="background-color: #f4f6f9; font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;">

    <div class="login-container" style="width: 350px; padding: 30px; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; margin-bottom: 20px; color: #333;">Hệ thống Quản Lý Điểm</h2>
        
        <?php if (!empty($error)) { echo "<p style='color: red; text-align: center; margin-bottom: 15px;'>$error</p>"; } ?>
        
        <form method="POST" action="">
            <label style="font-weight: bold; color: #555;">Tài khoản:</label><br>
            <input type="text" name="username" required style="width: 100%; padding: 10px; margin: 5px 0 15px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><br>
            
            <label style="font-weight: bold; color: #555;">Mật khẩu:</label><br>
            <input type="password" name="password" required style="width: 100%; padding: 10px; margin: 5px 0 20px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><br>
            
            <button type="submit" style="width: 100%; padding: 10px; background: #007bff; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">Đăng Nhập</button>
        </form>
    </div>

</body>
</html>