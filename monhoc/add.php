<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

$maMh = '';
$tenMh = '';
$soTinChi = '';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maMh = is_string($_POST['ma_mh'] ?? null) ? trim($_POST['ma_mh']) : '';
    $tenMh = is_string($_POST['ten_mh'] ?? null) ? trim($_POST['ten_mh']) : '';
    $soTinChi = is_string($_POST['so_tin_chi'] ?? null) ? trim($_POST['so_tin_chi']) : '';
    $validatedCredits = filter_var($soTinChi, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    if ($maMh === '' || $tenMh === '' || $soTinChi === '') {
        $error = 'Vui lòng nhập đầy đủ thông tin môn học.';
    } elseif ($validatedCredits === false) {
        $error = 'Số tín chỉ phải là số nguyên lớn hơn 0.';
    } else {
        $checkStmt = $conn->prepare('SELECT id FROM monhoc WHERE ma_mh = ? LIMIT 1');
        $checkStmt->bind_param('s', $maMh);
        $checkStmt->execute();
        $existingCourse = $checkStmt->get_result();

        if ($existingCourse->num_rows > 0) {
            $error = 'Mã môn học đã tồn tại.';
        } else {
            $stmt = $conn->prepare('INSERT INTO monhoc (ma_mh, ten_mh, so_tin_chi) VALUES (?, ?, ?)');
            $stmt->bind_param('ssi', $maMh, $tenMh, $validatedCredits);
            $stmt->execute();

            $success = 'Đã thêm môn học thành công.';
            $maMh = '';
            $tenMh = '';
            $soTinChi = '';
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
                <a class="text-decoration-none" href="index.php">&larr; Danh sách môn học</a>
                <h1 class="h2 mt-3 mb-1">Thêm môn học</h1>
                <p class="text-muted mb-0">Nhập thông tin môn học mới.</p>
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
                            <label for="ma_mh" class="form-label">Mã môn học</label>
                            <input type="text" class="form-control" id="ma_mh" name="ma_mh" maxlength="50" value="<?php echo htmlspecialchars($maMh, ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="ten_mh" class="form-label">Tên môn học</label>
                            <input type="text" class="form-control" id="ten_mh" name="ten_mh" maxlength="255" value="<?php echo htmlspecialchars($tenMh, ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="mb-4">
                            <label for="so_tin_chi" class="form-label">Số tín chỉ</label>
                            <input type="number" class="form-control" id="so_tin_chi" name="so_tin_chi" min="1" step="1" value="<?php echo htmlspecialchars($soTinChi, ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Lưu môn học</button>
                            <a href="index.php" class="btn btn-outline-secondary">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
