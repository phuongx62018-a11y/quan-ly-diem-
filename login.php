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
<body>
    <div class="login-container" style="width: 300px; margin: 100px auto; font-family: Arial;">
        <h2>Đăng Nhập Hệ Thống</h2>
        <?php if (!empty($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
        <form method="POST" action="">
            <label>Tài khoản:</label><br>
            <input type="text" name="username" required style="width: 100%; padding: 8px; margin: 5px 0;"><br>
            
            <label>Mật khẩu (phải có ký tự '@'):</label><br>
            <input type="password" name="password" required style="width: 100%; padding: 8px; margin: 5px 0;"><br><br>
            
            <button type="submit" style="padding: 10px 20px;">Đăng Nhập</button>
        </form>
    </div>
</body>
</html>