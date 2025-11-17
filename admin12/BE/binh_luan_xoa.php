<?php
// 📄 BE/binh_luan_xoa.php — Xóa bình luận (PDO chuẩn hóa + CORS động + đa môi trường)
header('Content-Type: application/json; charset=UTF-8');

// ================================
// 🌐 CORS động (đa môi trường, không hardcode)
// ================================
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
}

// ✅ Preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/db.php'; // ✅ Kết nối PDO đa môi trường
require_once __DIR__ . '/function.php';

// ================================
// 🔒 Kiểm tra phiên và quyền
// ================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['admin_id']) && (empty($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'mod']))) {
    json_err("Bạn không có quyền truy cập!");
}

// ================================
// 🧾 Nhận ID bình luận cần xóa
// ================================
$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    json_err("Thiếu hoặc sai ID bình luận!");
}

// ================================
// 🗑 Thực hiện xóa bình luận bằng PDO
// ================================
try {
    $stmt = $pdo->prepare("DELETE FROM binh_luan WHERE id = :id");
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() > 0) {
        json_ok([
            "message" => "Đã xóa bình luận thành công",
            "id" => $id
        ]);
    } else {
        json_err("Không tìm thấy bình luận cần xóa!");
    }
} catch (PDOException $e) {
    json_err("Lỗi khi xóa bình luận: " . $e->getMessage());
}
