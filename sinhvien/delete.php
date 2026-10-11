<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

$id = filter_var($_POST['id'] ?? $_GET['id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$error = '';
$success = '';
$deleted = false;
$student = null;

if ($id === false || $id === null) {
    $error = 'Mã sinh viên cần xóa không hợp lệ.';
} else {
    $selectStmt = $conn->prepare('SELECT id, ma_sv, ho_ten, ngay_sinh, lop FROM sinhvien WHERE id = ?');
    $selectStmt->bind_param('i', $id);
    $selectStmt->execute();
    $result = $selectStmt->get_result();
    $student = $result->fetch_assoc();
    $selectStmt->close();

    if ($student === null) {
        $error = 'Không tìm thấy sinh viên cần xóa.';
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $deleteStmt = $conn->prepare('DELETE FROM sinhvien WHERE id = ?');
        $deleteStmt->bind_param('i', $id);
        $deleteStmt->execute();

        if ($deleteStmt->affected_rows === 1) {
            $success = 'Đã xóa sinh viên thành công.';
            $deleted = true;
        } else {
            $error = 'Không thể xóa sinh viên. Vui lòng thử lại.';
        }

        $deleteStmt->close();
    }
}

?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="mb-4">
                <a class="text-decoration-none" href="index.php">&larr; Danh sách sinh viên</a>
                <h1 class="h2 mt-3 mb-1">Xóa sinh viên</h1>
            </div>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php elseif ($success !== ''): ?>
                <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if ($student !== null && !$deleted): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <p class="mb-3">Bạn có chắc chắn muốn xóa sinh viên này không? Thao tác này không thể hoàn tác.</p>
                        <dl class="row mb-4">
                            <dt class="col-sm-4">Mã sinh viên</dt>
                            <dd class="col-sm-8"><?php echo htmlspecialchars($student['ma_sv'], ENT_QUOTES, 'UTF-8'); ?></dd>
                            <dt class="col-sm-4">Họ và tên</dt>
                            <dd class="col-sm-8"><?php echo htmlspecialchars($student['ho_ten'], ENT_QUOTES, 'UTF-8'); ?></dd>
                            <dt class="col-sm-4">Ngày sinh</dt>
                            <dd class="col-sm-8"><?php echo htmlspecialchars($student['ngay_sinh'], ENT_QUOTES, 'UTF-8'); ?></dd>
                            <dt class="col-sm-4">Lớp</dt>
                            <dd class="col-sm-8"><?php echo htmlspecialchars($student['lop'], ENT_QUOTES, 'UTF-8'); ?></dd>
                        </dl>
                        <form method="post" action="delete.php" class="d-flex gap-2">
                            <input type="hidden" name="id" value="<?php echo (int) $student['id']; ?>">
                            <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
                            <a href="index.php" class="btn btn-outline-secondary">Hủy</a>
                        </form>
                    </div>
                </div>
            <?php elseif ($deleted): ?>
                <a href="index.php" class="btn btn-primary">Quay lại danh sách</a>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
