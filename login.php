<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/config/db.php';

$username = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = is_string($_POST['username'] ?? null) ? trim($_POST['username']) : '';
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

    if ($username === '' || $password === '') {
        $error = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.';
    } elseif (strpos($password, '@') === false) {
        $error = "Mật khẩu bắt buộc phải chứa ký tự '@'.";
    } else {
        $stmt = $conn->prepare(
            'SELECT id, username, password, role, sinhvien_id
             FROM taikhoan
             WHERE username = ?
             LIMIT 1'
        );
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $account = $result->fetch_assoc();
        $stmt->close();

        if ($account !== null && password_verify($password, $account['password'])) {
            if (
                $account['role'] === 'giang_vien'
                || ($account['role'] === 'sinh_vien' && $account['sinhvien_id'] !== null)
            ) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $account['id'];
                $_SESSION['username'] = $account['username'];
                $_SESSION['role'] = $account['role'];
                $_SESSION['sinhvien_id'] = $account['sinhvien_id'] === null
                    ? null
                    : (int) $account['sinhvien_id'];

                if ($account['role'] === 'giang_vien') {
                    header('Location: dashboard.php');
                    exit;
                }

                header('Location: dashboard_sv.php');
                exit;
            }
        }

        $error = 'Tên đăng nhập hoặc mật khẩu không chính xác.';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập - Quản lý điểm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="card border-0 shadow-sm w-100" style="max-width: 440px;">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <h1 class="h3 fw-bold mb-2">Quản Lý Điểm</h1>
                    <p class="text-muted mb-0">Đăng nhập vào tài khoản của bạn</p>
                </div>

                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="login.php">
                    <div class="mb-3">
                        <label for="username" class="form-label">Tên đăng nhập</label>
                        <input
                            type="text"
                            class="form-control"
                            id="username"
                            name="username"
                            value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>"
                            autocomplete="username"
                            required
                            autofocus
                        >
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            autocomplete="current-password"
                            required
                        >
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
