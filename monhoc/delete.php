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
$deleted = false;
$course = null;

if ($id === false || $id === null) {
    $error = 'Mã môn học cần xóa không hợp lệ.';
} else {
    $selectStmt = $conn->prepare('SELECT id, ma_mh, ten_mh, so_tin_chi FROM monhoc WHERE id = ?');
    $selectStmt->bind_param('i', $id);
    $selectStmt->execute();
    $result = $selectStmt->get_result();
    $course = $result->fetch_assoc();
    $selectStmt->close();

    if ($course === null) {
        $error = 'Không tìm thấy môn học cần xóa.';
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $deleteStmt = $conn->prepare('DELETE FROM monhoc WHERE id = ?');
        $deleteStmt->bind_param('i', $id);
        $deleteStmt->execute();

        if ($deleteStmt->affected_rows === 1) {
            $success = 'Đã xóa môn học thành công.';
            $deleted = true;
        } else {
            $error = 'Không thể xóa môn học. Vui lòng thử lại.';
        }

        $deleteStmt->close();
    }
}
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="mb-4">
                <a class="text-decoration-none" href="index.php">&larr; Danh sách môn học</a>
                <h1 class="h2 mt-3 mb-1">Xóa môn học</h1>
            </div>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php elseif ($success !== ''): ?>
                <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if ($course !== null && !$deleted): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <p class="mb-3">Bạn có chắc chắn muốn xóa môn học này không? Thao tác này không thể hoàn tác.</p>
                        <dl class="row mb-4">
                            <dt class="col-sm-4">Mã môn học</dt>
                            <dd class="col-sm-8"><?php echo htmlspecialchars($course['ma_mh'], ENT_QUOTES, 'UTF-8'); ?></dd>
                            <dt class="col-sm-4">Tên môn học</dt>
                            <dd class="col-sm-8"><?php echo htmlspecialchars($course['ten_mh'], ENT_QUOTES, 'UTF-8'); ?></dd>
                            <dt class="col-sm-4">Số tín chỉ</dt>
                            <dd class="col-sm-8"><?php echo (int) $course['so_tin_chi']; ?></dd>
                        </dl>
                        <form method="post" action="delete.php" class="d-flex gap-2">
                            <input type="hidden" name="id" value="<?php echo (int) $course['id']; ?>">
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
