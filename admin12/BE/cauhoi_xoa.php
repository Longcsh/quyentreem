<?php
// 📂 File: admin12/BE/cauhoi_xoa.php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/db.php';

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Thiếu ID để xóa']);
    exit;
}

try {
    // 🔍 Lấy thông tin ảnh để xóa file vật lý nếu có
    $stmt = $conn->prepare("SELECT anh_minh_hoa FROM tu_van WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && !empty($row['anh_minh_hoa'])) {
        $filePath = __DIR__ . '/../../' . $row['anh_minh_hoa'];
        if (file_exists($filePath)) unlink($filePath);
    }

    // 🗑️ Xóa bản ghi khỏi DB
    $stmt = $conn->prepare("DELETE FROM tu_van WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['success' => true, 'message' => '🗑️ Câu hỏi đã được xóa vĩnh viễn']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi SQL: ' . $e->getMessage()]);
}
?>
