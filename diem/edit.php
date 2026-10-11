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

$requestId = $_SERVER['REQUEST_METHOD'] === 'POST'
    ? ($_POST['id'] ?? '')
    : ($_GET['id'] ?? '');
$id = filter_var($requestId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$error = '';
$success = '';
$grade = null;

if ($id === false || $id === null) {
    $error = 'Mã điểm cần cập nhật không hợp lệ.';
} else {
    $selectStmt = $conn->prepare(
        'SELECT id, sinhvien_id, monhoc_id, diem_giua_ky, diem_cuoi_ky
         FROM diem
         WHERE id = ?'
    );
    $selectStmt->bind_param('i', $id);
    $selectStmt->execute();
    $result = $selectStmt->get_result();
    $grade = $result->fetch_assoc();
    $selectStmt->close();

    if ($grade === null) {
        $error = 'Không tìm thấy điểm cần cập nhật.';
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $studentInput = is_string($_POST['sinhvien_id'] ?? null) ? $_POST['sinhvien_id'] : '';
        $courseInput = is_string($_POST['monhoc_id'] ?? null) ? $_POST['monhoc_id'] : '';
        $midtermInput = is_string($_POST['diem_giua_ky'] ?? null) ? trim($_POST['diem_giua_ky']) : '';
        $finalInput = is_string($_POST['diem_cuoi_ky'] ?? null) ? trim($_POST['diem_cuoi_ky']) : '';

        $validatedStudentId = filter_var($studentInput, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $validatedCourseId = filter_var($courseInput, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
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
            $updateStmt = $conn->prepare(
                'UPDATE diem
                 SET sinhvien_id = ?, monhoc_id = ?, diem_giua_ky = ?, diem_cuoi_ky = ?, diem_tong_ket = ?
                 WHERE id = ?'
            );
            $updateStmt->bind_param(
                'iidddi',
                $validatedStudentId,
                $validatedCourseId,
                $diem_giua_ky,
                $diem_cuoi_ky,
                $diem_tong_ket,
                $id
            );
            $updateStmt->execute();
            $updateStmt->close();

            $grade = [
                'id' => $id,
                'sinhvien_id' => $validatedStudentId,
                'monhoc_id' => $validatedCourseId,
                'diem_giua_ky' => $diem_giua_ky,
                'diem_cuoi_ky' => $diem_cuoi_ky,
            ];
            $success = 'Đã cập nhật điểm thành công.';
        }

        if ($error !== '') {
            $grade['sinhvien_id'] = $studentInput;
            $grade['monhoc_id'] = $courseInput;
            $grade['diem_giua_ky'] = $midtermInput;
            $grade['diem_cuoi_ky'] = $finalInput;
        }
    }
}
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="mb-4">
                <a class="text-decoration-none" href="index.php">&larr; Danh sách điểm</a>
                <h1 class="h2 mt-3 mb-1">Cập nhật điểm</h1>
                <p class="text-muted mb-0">Chỉnh sửa thông tin và điểm thành phần.</p>
            </div>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php elseif ($success !== ''): ?>
                <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if ($grade !== null): ?>
                <?php if (count($students) === 0 || count($courses) === 0): ?>
                    <div class="alert alert-warning" role="alert">Cần có sinh viên và môn học để cập nhật điểm.</div>
                <?php else: ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <form method="post" action="edit.php">
                                <input type="hidden" name="id" value="<?php echo (int) $grade['id']; ?>">
                                <div class="mb-3">
                                    <label for="sinhvien_id" class="form-label">Sinh viên</label>
                                    <select class="form-select" id="sinhvien_id" name="sinhvien_id" required>
                                        <option value="">-- Chọn sinh viên --</option>
                                        <?php foreach ($students as $studentOptionId => $name): ?>
                                            <option value="<?php echo (int) $studentOptionId; ?>" <?php echo (string) $studentOptionId === (string) $grade['sinhvien_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="monhoc_id" class="form-label">Môn học</label>
                                    <select class="form-select" id="monhoc_id" name="monhoc_id" required>
                                        <option value="">-- Chọn môn học --</option>
                                        <?php foreach ($courses as $courseOptionId => $name): ?>
                                            <option value="<?php echo (int) $courseOptionId; ?>" <?php echo (string) $courseOptionId === (string) $grade['monhoc_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="diem_giua_ky" class="form-label">Điểm giữa kỳ</label>
                                    <input type="number" class="form-control" id="diem_giua_ky" name="diem_giua_ky" min="0" max="10" step="any" value="<?php echo htmlspecialchars((string) $grade['diem_giua_ky'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>
                                <div class="mb-4">
                                    <label for="diem_cuoi_ky" class="form-label">Điểm cuối kỳ</label>
                                    <input type="number" class="form-control" id="diem_cuoi_ky" name="diem_cuoi_ky" min="0" max="10" step="any" value="<?php echo htmlspecialchars((string) $grade['diem_cuoi_ky'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                    <a href="index.php" class="btn btn-outline-secondary">Hủy</a>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
