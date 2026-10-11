<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?status=invalid_request');
    exit;
}

$id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false || $id === null) {
    header('Location: index.php?status=invalid_id');
    exit;
}

$stmt = $conn->prepare('DELETE FROM diem WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$deleted = $stmt->affected_rows === 1;
$stmt->close();

header('Location: index.php?status=' . ($deleted ? 'deleted' : 'not_found'));
exit;
