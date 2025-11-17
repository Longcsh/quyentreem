<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/db.php';

try {
    global $conn;
    if (!isset($conn) || !$conn instanceof PDO) {
        if (isset($pdo) && $pdo instanceof PDO) {
            $conn = $pdo;
        } else {
            throw new Exception("Không thể khởi tạo kết nối CSDL");
        }
    }

    // ✅ Lấy tất cả chuyên môn (không phụ thuộc vào thống kê)
    $stmt = $conn->prepare("SELECT id, ten_chuyen_mon FROM chuyen_mon ORDER BY id ASC");
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
}
catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi PDO: ' . $e->getMessage()]);
}
catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>
