<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "db_quanlydiemdoan";

$conn = new mysqli($host, $user, $pass, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>