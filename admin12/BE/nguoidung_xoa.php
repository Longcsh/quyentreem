<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/db.php'; // file db.php khởi tạo PDO trong biến $conn

try {
    if (!isset($_GET['id'])) {
        throw new Exception("Thiếu ID người dùng");
    }

    $id = (int)$_GET['id'];

    $stmt = $conn->prepare("DELETE FROM tai_khoan WHERE id = :id");
    $stmt->execute([':id' => $id]);

    echo json_encode([
        'success' => true,
        'message' => 'Đã xóa người dùng'
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
