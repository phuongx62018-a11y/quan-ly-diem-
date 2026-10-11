<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

$maSv = '';
$hoTen = '';
$ngaySinh = '';
$lop = '';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maSv = trim($_POST['ma_sv'] ?? '');
    $hoTen = trim($_POST['ho_ten'] ?? '');
    $ngaySinh = trim($_POST['ngay_sinh'] ?? '');
    $lop = trim($_POST['lop'] ?? '');

    if ($maSv === '' || $hoTen === '' || $ngaySinh === '' || $lop === '') {
        $error = 'Vui lòng nhập đầy đủ thông tin sinh viên.';
    } elseif (
        !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $ngaySinh, $dateParts)
        || !checkdate((int) $dateParts[2], (int) $dateParts[3], (int) $dateParts[1])
    ) {
        $error = 'Ngày sinh không hợp lệ.';
    } else {
        $checkStmt = $conn->prepare('SELECT id FROM sinhvien WHERE ma_sv = ? LIMIT 1');
        $checkStmt->bind_param('s', $maSv);
        $checkStmt->execute();
        $existingStudent = $checkStmt->get_result();

        if ($existingStudent->num_rows > 0) {
            $error = 'Mã sinh viên đã tồn tại.';
        } else {
            $stmt = $conn->prepare('INSERT INTO sinhvien (ma_sv, ho_ten, ngay_sinh, lop) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('ssss', $maSv, $hoTen, $ngaySinh, $lop);
            $stmt->execute();

            $success = 'Đã thêm sinh viên thành công.';
            $maSv = '';
            $hoTen = '';
            $ngaySinh = '';
            $lop = '';
            $stmt->close();
        }

        $checkStmt->close();
    }
}

?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="mb-4">
                <a class="text-decoration-none" href="index.php">&larr; Danh sách sinh viên</a>
                <h1 class="h2 mt-3 mb-1">Thêm sinh viên</h1>
                <p class="text-muted mb-0">Nhập thông tin sinh viên mới.</p>
            </div>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php elseif ($success !== ''): ?>
                <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="post" action="add.php">
                        <div class="mb-3">
                            <label for="ma_sv" class="form-label">Mã sinh viên</label>
                            <input type="text" class="form-control" id="ma_sv" name="ma_sv" maxlength="50" value="<?php echo htmlspecialchars($maSv, ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="ho_ten" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="ho_ten" name="ho_ten" maxlength="255" value="<?php echo htmlspecialchars($hoTen, ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="ngay_sinh" class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control" id="ngay_sinh" name="ngay_sinh" value="<?php echo htmlspecialchars($ngaySinh, ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="mb-4">
                            <label for="lop" class="form-label">Lớp</label>
                            <input type="text" class="form-control" id="lop" name="lop" maxlength="100" value="<?php echo htmlspecialchars($lop, ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Lưu sinh viên</button>
                            <a href="index.php" class="btn btn-outline-secondary">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
