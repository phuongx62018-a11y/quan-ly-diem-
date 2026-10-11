<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php';

$stmt = $conn->prepare('SELECT id, ma_mh, ten_mh, so_tin_chi FROM monhoc ORDER BY id DESC');
$stmt->execute();
$courses = $stmt->get_result();
?>

<main class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h2 mb-1">Quản lý môn học</h1>
            <p class="text-muted mb-0">Danh sách môn học trong hệ thống.</p>
        </div>
        <a class="btn btn-primary" href="add.php">Thêm môn học</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Mã môn học</th>
                            <th scope="col">Tên môn học</th>
                            <th scope="col">Số tín chỉ</th>
                            <th scope="col" class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($courses->num_rows === 0): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Chưa có môn học nào.</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($course = $courses->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($course['ma_mh'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($course['ten_mh'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo (int) $course['so_tin_chi']; ?></td>
                                    <td class="text-end text-nowrap">
                                        <a class="btn btn-sm btn-outline-primary" href="edit.php?id=<?php echo (int) $course['id']; ?>">Sửa</a>
                                        <a class="btn btn-sm btn-outline-danger" href="delete.php?id=<?php echo (int) $course['id']; ?>">Xóa</a>
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
