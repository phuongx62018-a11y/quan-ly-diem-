<?php
$appRoot = realpath(dirname(__DIR__));
$documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
$appBasePath = '';

if ($appRoot !== false && $documentRoot !== false) {
    $normalizedAppRoot = str_replace('\\', '/', $appRoot);
    $normalizedDocumentRoot = rtrim(str_replace('\\', '/', $documentRoot), '/');

    if (strcasecmp($normalizedAppRoot, $normalizedDocumentRoot) === 0) {
        $appBasePath = '';
    } elseif (stripos($normalizedAppRoot, $normalizedDocumentRoot . '/') === 0) {
        $appBasePath = substr($normalizedAppRoot, strlen($normalizedDocumentRoot));
    }
}

$escapedAppBasePath = htmlspecialchars($appBasePath, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản Lý Điểm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo $escapedAppBasePath; ?>/dashboard.php">
                Quản Lý Điểm
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Mở menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $escapedAppBasePath; ?>/dashboard.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $escapedAppBasePath; ?>/sinhvien/index.php">Sinh viên</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $escapedAppBasePath; ?>/monhoc/index.php">Môn học</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $escapedAppBasePath; ?>/diem/index.php">Điểm</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
