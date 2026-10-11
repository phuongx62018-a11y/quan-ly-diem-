<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

$stmt = $conn->prepare(
    'SELECT d.id, sv.ho_ten, mh.ten_mh, d.diem_giua_ky, d.diem_cuoi_ky, d.diem_tong_ket
     FROM diem AS d
     INNER JOIN sinhvien AS sv ON d.sinhvien_id = sv.id
     INNER JOIN monhoc AS mh ON d.monhoc_id = mh.id
     ORDER BY d.id DESC'
);
$stmt->execute();
$grades = $stmt->get_result();
$statusMessages = [
    'deleted' => ['success', 'Đã xóa điểm thành công.'],
    'not_found' => ['warning', 'Không tìm thấy điểm cần xóa.'],
    'invalid_id' => ['danger', 'Mã điểm cần xóa không hợp lệ.'],
    'invalid_request' => ['danger', 'Yêu cầu xóa không hợp lệ.'],
];
$status = is_string($_GET['status'] ?? null) ? $_GET['status'] : '';
?>

<main class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h2 mb-1">Quản lý điểm</h1>
            <p class="text-muted mb-0">Danh sách điểm theo sinh viên và môn học.</p>
        </div>
        <a class="btn btn-primary" href="add.php">Thêm điểm</a>
    </div>

    <?php if (isset($statusMessages[$status])): ?>
        <div class="alert alert-<?php echo $statusMessages[$status][0]; ?>" role="alert">
            <?php echo htmlspecialchars($statusMessages[$status][1], ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Sinh viên</th>
                            <th scope="col">Môn học</th>
                            <th scope="col">Điểm giữa kỳ</th>
                            <th scope="col">Điểm cuối kỳ</th>
                            <th scope="col">Điểm tổng kết</th>
                            <th scope="col" class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($grades->num_rows === 0): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Chưa có điểm nào.</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($grade = $grades->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($grade['ho_ten'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($grade['ten_mh'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars((string) $grade['diem_giua_ky'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars((string) $grade['diem_cuoi_ky'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars((string) $grade['diem_tong_ket'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="text-end text-nowrap">
                                        <a class="btn btn-sm btn-outline-primary" href="edit.php?id=<?php echo (int) $grade['id']; ?>">Sửa</a>
                                        <form method="post" action="delete.php" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa điểm này?');">
                                            <input type="hidden" name="id" value="<?php echo (int) $grade['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php
$stmt->close();
require_once __DIR__ . '/../includes/footer.php';
?>
