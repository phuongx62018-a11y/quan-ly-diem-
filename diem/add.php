<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

$studentsStmt = $conn->prepare('SELECT id, ho_ten FROM sinhvien ORDER BY ho_ten');
$studentsStmt->execute();
$studentsResult = $studentsStmt->get_result();
$students = [];
while ($student = $studentsResult->fetch_assoc()) {
    $students[(int) $student['id']] = $student['ho_ten'];
}
$studentsStmt->close();

$coursesStmt = $conn->prepare('SELECT id, ten_mh FROM monhoc ORDER BY ten_mh');
$coursesStmt->execute();
$coursesResult = $coursesStmt->get_result();
$courses = [];
while ($course = $coursesResult->fetch_assoc()) {
    $courses[(int) $course['id']] = $course['ten_mh'];
}
$coursesStmt->close();

$studentId = '';
$courseId = '';
$midtermInput = '';
$finalInput = '';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = is_string($_POST['sinhvien_id'] ?? null) ? $_POST['sinhvien_id'] : '';
    $courseId = is_string($_POST['monhoc_id'] ?? null) ? $_POST['monhoc_id'] : '';
    $midtermInput = is_string($_POST['diem_giua_ky'] ?? null) ? trim($_POST['diem_giua_ky']) : '';
    $finalInput = is_string($_POST['diem_cuoi_ky'] ?? null) ? trim($_POST['diem_cuoi_ky']) : '';

    $validatedStudentId = filter_var($studentId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $validatedCourseId = filter_var($courseId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $diem_giua_ky = filter_var($midtermInput, FILTER_VALIDATE_FLOAT);
    $diem_cuoi_ky = filter_var($finalInput, FILTER_VALIDATE_FLOAT);

    if ($validatedStudentId === false || $validatedCourseId === false) {
        $error = 'Vui lòng chọn sinh viên và môn học hợp lệ.';
    } elseif (!array_key_exists($validatedStudentId, $students) || !array_key_exists($validatedCourseId, $courses)) {
        $error = 'Sinh viên hoặc môn học đã chọn không còn tồn tại.';
    } elseif ($diem_giua_ky === false || $diem_cuoi_ky === false) {
        $error = 'Điểm phải là số hợp lệ.';
    } elseif ($diem_giua_ky < 0 || $diem_giua_ky > 10 || $diem_cuoi_ky < 0 || $diem_cuoi_ky > 10) {
        $error = 'Điểm phải nằm trong khoảng từ 0 đến 10.';
    } else {
        $diem_tong_ket = ($diem_giua_ky * 0.4) + ($diem_cuoi_ky * 0.6);
        $stmt = $conn->prepare(
            'INSERT INTO diem (sinhvien_id, monhoc_id, diem_giua_ky, diem_cuoi_ky, diem_tong_ket)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'iiddd',
            $validatedStudentId,
            $validatedCourseId,
            $diem_giua_ky,
            $diem_cuoi_ky,
            $diem_tong_ket
        );
        $stmt->execute();
        $stmt->close();

        $success = 'Đã thêm điểm thành công.';
        $studentId = '';
        $courseId = '';
        $midtermInput = '';
        $finalInput = '';
    }
}
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="mb-4">
                <a class="text-decoration-none" href="index.php">&larr; Danh sách điểm</a>
                <h1 class="h2 mt-3 mb-1">Thêm điểm</h1>
                <p class="text-muted mb-0">Chọn sinh viên, môn học và nhập điểm thành phần.</p>
            </div>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php elseif ($success !== ''): ?>
                <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if (count($students) === 0 || count($courses) === 0): ?>
                <div class="alert alert-warning" role="alert">
                    Cần có ít nhất một sinh viên và một môn học trước khi nhập điểm.
                    <a href="../sinhvien/index.php" class="alert-link">Quản lý sinh viên</a> |
                    <a href="../monhoc/index.php" class="alert-link">Quản lý môn học</a>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form method="post" action="add.php">
                            <div class="mb-3">
                                <label for="sinhvien_id" class="form-label">Sinh viên</label>
                                <select class="form-select" id="sinhvien_id" name="sinhvien_id" required>
                                    <option value="">-- Chọn sinh viên --</option>
                                    <?php foreach ($students as $id => $name): ?>
                                        <option value="<?php echo (int) $id; ?>" <?php echo (string) $id === $studentId ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="monhoc_id" class="form-label">Môn học</label>
                                <select class="form-select" id="monhoc_id" name="monhoc_id" required>
                                    <option value="">-- Chọn môn học --</option>
                                    <?php foreach ($courses as $id => $name): ?>
                                        <option value="<?php echo (int) $id; ?>" <?php echo (string) $id === $courseId ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="diem_giua_ky" class="form-label">Điểm giữa kỳ</label>
                                <input type="number" class="form-control" id="diem_giua_ky" name="diem_giua_ky" min="0" max="10" step="any" value="<?php echo htmlspecialchars($midtermInput, ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                            <div class="mb-4">
                                <label for="diem_cuoi_ky" class="form-label">Điểm cuối kỳ</label>
                                <input type="number" class="form-control" id="diem_cuoi_ky" name="diem_cuoi_ky" min="0" max="10" step="any" value="<?php echo htmlspecialchars($finalInput, ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Lưu điểm</button>
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
