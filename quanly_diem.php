<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';
$message = "";

// Xử lý thêm điểm mới
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_score'])) {
    $ma_sv = trim($_POST['ma_sv']);
    $ho_ten = trim($_POST['ho_ten']);
    $diem_mon_hoc = floatval($_POST['diem_mon_hoc']);

    if ($diem_mon_hoc < 0 || $diem_mon_hoc > 10) {
        $message = "Điểm môn học phải từ 0 đến 10!";
    } else {
        $sql = "INSERT INTO diem_sinh_vien (ma_sv, ho_ten, diem_mon_hoc) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssd", $ma_sv, $ho_ten, $diem_mon_hoc);
        if ($stmt->execute()) {
            $message = "Thêm điểm thành công!";
        } else {
            $message = "Lỗi: " . $conn->error;
        }
    }
}

$result = $conn->query("SELECT * FROM diem_sinh_vien");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Điểm Sinh Viên</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body style="font-family: Arial; margin: 20px;">
    <h2>Hệ Thống Quản Lý Điểm Sinh Viên</h2>
    <p>Xin chào, <b><?php echo $_SESSION['username']; ?></b> | <a href="dashboard.php">Trang chủ</a> | <a href="logout.php">Đăng xuất</a></p>
    <hr>

    <div style="background: #f9f9f9; padding: 15px; width: 300px; margin-bottom: 20px;">
        <h3>Thêm Điểm Mới</h3>
        <?php if (!empty($message)) { echo "<p style='color: green;'>$message</p>"; } ?>
        <form method="POST" action="">
            <label>Mã Sinh Viên:</label><br>
            <input type="text" name="ma_sv" required style="width: 100%; margin: 5px 0;"><br>
            <label>Họ và Tên:</label><br>
            <input type="text" name="ho_ten" required style="width: 100%; margin: 5px 0;"><br>
            <label>Điểm Môn Học:</label><br>
            <input type="number" step="0.1" name="diem_mon_hoc" required style="width: 100%; margin: 5px 0;"><br><br>
            <button type="submit" name="add_score">Lưu Điểm</button>
        </form>
    </div>

    <h3>Danh Sách Điểm</h3>
    <table border="1" style="width: 100%; border-collapse: collapse; text-align: center;">
        <thead>
            <tr style="background: #f4f4f4;">
                <th>ID</th>
                <th>Mã Sinh Viên</th>
                <th>Họ và Tên</th>
                <th>Điểm Môn Học</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['ma_sv']); ?></td>
                        <td><?php echo htmlspecialchars($row['ho_ten']); ?></td>
                        <td><?php echo $row['diem_mon_hoc']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">Chưa có dữ liệu điểm nào.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>