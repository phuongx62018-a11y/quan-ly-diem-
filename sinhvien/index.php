<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

$stmt = $conn->prepare('SELECT id, ma_sv, ho_ten, ngay_sinh, lop FROM sinhvien ORDER BY id DESC');
$stmt->execute();
$students = $stmt->get_result();
?>

<main class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h2 mb-1">Quản lý sinh viên</h1>
            <p class="text-muted mb-0">Danh sách sinh viên trong hệ thống.</p>
        </div>
        <a class="btn btn-primary" href="add.php">Thêm sinh viên</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Mã sinh viên</th>
                            <th scope="col">Họ và tên</th>
                            <th scope="col">Ngày sinh</th>
                            <th scope="col">Lớp</th>
                            <th scope="col" class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($students->num_rows === 0): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Chưa có sinh viên nào.</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($student = $students->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['ma_sv'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($student['ho_ten'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($student['ngay_sinh'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($student['lop'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="text-end text-nowrap">
                                        <a class="btn btn-sm btn-outline-primary" href="edit.php?id=<?php echo (int) $student['id']; ?>">Sửa</a>
                                        <a class="btn btn-sm btn-outline-danger" href="delete.php?id=<?php echo (int) $student['id']; ?>">Xóa</a>
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
