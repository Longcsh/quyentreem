<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/db.php';

try {
    $stmt = $conn->query("SELECT id, ten_chuyen_mon FROM chuyen_mon ORDER BY ten_chuyen_mon ASC");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $data]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi lấy chuyên môn: ' . $e->getMessage()]);
}
