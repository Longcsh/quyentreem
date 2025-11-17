<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');
require_once '../admin12/BE/db.php'; // File chứa kết nối PDO

try {
    if (!isset($_SESSION['expert_id'])) {
        echo json_encode(['error' => 'Chưa đăng nhập']);
        exit;
    }

    $id = $_SESSION['expert_id'];

    // Truy vấn chỉ các cột có thật trong CSDL
    $stmt = $conn->prepare("
        SELECT ho_ten, ngay_sinh, so_dien_thoai, email, dia_chi
        FROM tai_khoan
        WHERE id = ?
    ");
    $stmt->execute([$id]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($res ?: ['error' => 'Không tìm thấy dữ liệu'], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    error_log("Lỗi SQL (get_profile.php): " . $e->getMessage(), 3, "../admin12/BE/error.log");
    echo json_encode(['error' => 'Lỗi truy vấn dữ liệu']);
}
?>
