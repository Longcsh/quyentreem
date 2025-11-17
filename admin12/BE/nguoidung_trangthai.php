<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/db.php'; // file này phải có $conn là PDO

try {
    // ✅ Kiểm tra ID
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception("Thiếu hoặc sai ID người dùng");
    }

    $id = (int)$_GET['id'];

    // 🔍 Lấy trạng thái hiện tại
    $stmt = $conn->prepare("SELECT trang_thai FROM tai_khoan WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("Không tìm thấy người dùng");
    }

    // 🔁 Đảo trạng thái (theo kiểu chuỗi)
    $current = trim($user['trang_thai']); // tránh lỗi khoảng trắng
    $newStatus = ($current === 'Hoạt động') ? 'Bị khóa' : 'Hoạt động';

    // 📝 Cập nhật vào DB
    $update = $conn->prepare("UPDATE tai_khoan SET trang_thai = :newStatus WHERE id = :id");
    $update->execute([
        ':newStatus' => $newStatus,
        ':id' => $id
    ]);

    echo json_encode([
        'success' => true,
        'newStatus' => $newStatus
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
