<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

$requestId = $_SERVER['REQUEST_METHOD'] === 'POST'
    ? ($_POST['id'] ?? '')
    : ($_GET['id'] ?? '');
$id = filter_var($requestId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$error = '';
$success = '';
$student = null;

if ($id === false || $id === null) {
    $error = 'Mã sinh viên cần cập nhật không hợp lệ.';
} else {
    $selectStmt = $conn->prepare('SELECT id, ma_sv, ho_ten, ngay_sinh, lop FROM sinhvien WHERE id = ?');
    $selectStmt->bind_param('i', $id);
    $selectStmt->execute();
    $result = $selectStmt->get_result();
    $student = $result->fetch_assoc();
    $selectStmt->close();

    if ($student === null) {
        $error = 'Không tìm thấy sinh viên cần cập nhật.';
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            $checkStmt = $conn->prepare('SELECT id FROM sinhvien WHERE ma_sv = ? AND id <> ? LIMIT 1');
            $checkStmt->bind_param('si', $maSv, $id);
            $checkStmt->execute();
            $existingStudent = $checkStmt->get_result();

            if ($existingStudent->num_rows > 0) {
                $error = 'Mã sinh viên đã được sử dụng bởi sinh viên khác.';
            } else {
                $updateStmt = $conn->prepare('UPDATE sinhvien SET ma_sv = ?, ho_ten = ?, ngay_sinh = ?, lop = ? WHERE id = ?');
                $updateStmt->bind_param('ssssi', $maSv, $hoTen, $ngaySinh, $lop, $id);
                $updateStmt->execute();

                $student = [
                    'id' => $id,
                    'ma_sv' => $maSv,
                    'ho_ten' => $hoTen,
                    'ngay_sinh' => $ngaySinh,
                    'lop' => $lop,
                ];
                $success = 'Đã cập nhật sinh viên thành công.';
                $updateStmt->close();
            }

            $checkStmt->close();
        }

        if ($error !== '') {
            $student['ma_sv'] = $maSv;
            $student['ho_ten'] = $hoTen;
            $student['ngay_sinh'] = $ngaySinh;
            $student['lop'] = $lop;
        }
    }
}

?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="mb-4">
                <a class="text-decoration-none" href="index.php">&larr; Danh sách sinh viên</a>
                <h1 class="h2 mt-3 mb-1">Cập nhật sinh viên</h1>
                <p class="text-muted mb-0">Chỉnh sửa thông tin sinh viên.</p>
            </div>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php elseif ($success !== ''): ?>
                <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if ($student !== null): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form method="post" action="edit.php">
                            <input type="hidden" name="id" value="<?php echo (int) $student['id']; ?>">
                            <div class="mb-3">
                                <label for="ma_sv" class="form-label">Mã sinh viên</label>
                                <input type="text" class="form-control" id="ma_sv" name="ma_sv" maxlength="50" value="<?php echo htmlspecialchars($student['ma_sv'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="ho_ten" class="form-label">Họ và tên</label>
                                <input type="text" class="form-control" id="ho_ten" name="ho_ten" maxlength="255" value="<?php echo htmlspecialchars($student['ho_ten'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="ngay_sinh" class="form-label">Ngày sinh</label>
                                <input type="date" class="form-control" id="ngay_sinh" name="ngay_sinh" value="<?php echo htmlspecialchars($student['ngay_sinh'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                            <div class="mb-4">
                                <label for="lop" class="form-label">Lớp</label>
                                <input type="text" class="form-control" id="lop" name="lop" maxlength="100" value="<?php echo htmlspecialchars($student['lop'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                <a href="index.php" class="btn btn-outline-secondary">Hủy</a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
