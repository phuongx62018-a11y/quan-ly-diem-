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
$course = null;

if ($id === false || $id === null) {
    $error = 'Mã môn học cần cập nhật không hợp lệ.';
} else {
    $selectStmt = $conn->prepare('SELECT id, ma_mh, ten_mh, so_tin_chi FROM monhoc WHERE id = ?');
    $selectStmt->bind_param('i', $id);
    $selectStmt->execute();
    $result = $selectStmt->get_result();
    $course = $result->fetch_assoc();
    $selectStmt->close();

    if ($course === null) {
        $error = 'Không tìm thấy môn học cần cập nhật.';
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $maMh = is_string($_POST['ma_mh'] ?? null) ? trim($_POST['ma_mh']) : '';
        $tenMh = is_string($_POST['ten_mh'] ?? null) ? trim($_POST['ten_mh']) : '';
        $soTinChi = is_string($_POST['so_tin_chi'] ?? null) ? trim($_POST['so_tin_chi']) : '';
        $validatedCredits = filter_var($soTinChi, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if ($maMh === '' || $tenMh === '' || $soTinChi === '') {
            $error = 'Vui lòng nhập đầy đủ thông tin môn học.';
        } elseif ($validatedCredits === false) {
            $error = 'Số tín chỉ phải là số nguyên lớn hơn 0.';
        } else {
            $checkStmt = $conn->prepare('SELECT id FROM monhoc WHERE ma_mh = ? AND id <> ? LIMIT 1');
            $checkStmt->bind_param('si', $maMh, $id);
            $checkStmt->execute();
            $existingCourse = $checkStmt->get_result();

            if ($existingCourse->num_rows > 0) {
                $error = 'Mã môn học đã được sử dụng bởi môn học khác.';
            } else {
                $updateStmt = $conn->prepare('UPDATE monhoc SET ma_mh = ?, ten_mh = ?, so_tin_chi = ? WHERE id = ?');
                $updateStmt->bind_param('ssii', $maMh, $tenMh, $validatedCredits, $id);
                $updateStmt->execute();

                $course = [
                    'id' => $id,
                    'ma_mh' => $maMh,
                    'ten_mh' => $tenMh,
                    'so_tin_chi' => $validatedCredits,
                ];
                $success = 'Đã cập nhật môn học thành công.';
                $updateStmt->close();
            }

            $checkStmt->close();
        }

        if ($error !== '') {
            $course['ma_mh'] = $maMh;
            $course['ten_mh'] = $tenMh;
            $course['so_tin_chi'] = $soTinChi;
        }
    }
}
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="mb-4">
                <a class="text-decoration-none" href="index.php">&larr; Danh sách môn học</a>
                <h1 class="h2 mt-3 mb-1">Cập nhật môn học</h1>
                <p class="text-muted mb-0">Chỉnh sửa thông tin môn học.</p>
            </div>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php elseif ($success !== ''): ?>
                <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if ($course !== null): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form method="post" action="edit.php">
                            <input type="hidden" name="id" value="<?php echo (int) $course['id']; ?>">
                            <div class="mb-3">
                                <label for="ma_mh" class="form-label">Mã môn học</label>
                                <input type="text" class="form-control" id="ma_mh" name="ma_mh" maxlength="50" value="<?php echo htmlspecialchars($course['ma_mh'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="ten_mh" class="form-label">Tên môn học</label>
                                <input type="text" class="form-control" id="ten_mh" name="ten_mh" maxlength="255" value="<?php echo htmlspecialchars($course['ten_mh'], ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                            <div class="mb-4">
                                <label for="so_tin_chi" class="form-label">Số tín chỉ</label>
                                <input type="number" class="form-control" id="so_tin_chi" name="so_tin_chi" min="1" step="1" value="<?php echo htmlspecialchars((string) $course['so_tin_chi'], ENT_QUOTES, 'UTF-8'); ?>" required>
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
