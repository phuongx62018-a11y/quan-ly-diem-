<?php
require_once __DIR__ . '/includes/auth.php';

if (($_SESSION['role'] ?? null) !== 'sinh_vien') {
    header('Location: login.php');
    exit;
}

$studentId = filter_var(
    $_SESSION['sinhvien_id'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($studentId === false || $studentId === null) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/db.php';

$stmt = $conn->prepare(
    'SELECT mh.ten_mh, d.diem_giua_ky, d.diem_cuoi_ky, d.diem_tong_ket
     FROM diem AS d
     INNER JOIN monhoc AS mh ON d.monhoc_id = mh.id
     WHERE d.sinhvien_id = ?
     ORDER BY mh.ten_mh'
);
$stmt->bind_param('i', $studentId);
$stmt->execute();
$grades = $stmt->get_result();

require_once __DIR__ . '/includes/header.php';
?>

<main class="container py-5">
    <div class="mb-4 p-4 bg-white rounded-4 shadow-sm border-start border-4 border-primary">
        <h1 class="h2 fw-bold text-dark mb-1">Xin chào Sinh viên, đây là bảng điểm của bạn</h1>
        <p class="text-muted mb-0">Các điểm bên dưới chỉ dành cho tài khoản sinh viên đang đăng nhập.</p>
    </div>

    <section class="card border-0 shadow-sm" aria-labelledby="grades-heading">
        <div class="card-header bg-white py-3">
            <h2 id="grades-heading" class="h5 mb-0">Bảng điểm</h2>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Tên môn học</th>
                            <th scope="col">Điểm giữa kỳ</th>
                            <th scope="col">Điểm cuối kỳ</th>
                            <th scope="col">Điểm tổng kết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($grades->num_rows === 0): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Bạn chưa có điểm nào.</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($grade = $grades->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($grade['ten_mh'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars((string) $grade['diem_giua_ky'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars((string) $grade['diem_cuoi_ky'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars((string) $grade['diem_tong_ket'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>

<?php
$stmt->close();
require_once __DIR__ . '/includes/footer.php';
?>
