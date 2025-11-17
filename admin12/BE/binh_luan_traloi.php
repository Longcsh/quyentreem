<?php
// 📄 BE/admin_tra_loi_binh_luan.php — Trả lời bình luận (PDO + CORS động + đa môi trường)
header('Content-Type: application/json; charset=UTF-8');

// ✅ CORS động
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
}

// ✅ OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/db.php';     // ✅ PDO đa môi trường
require_once __DIR__ . '/function.php';   // ✅ json_ok, json_err, requireRole,...

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔒 Chỉ admin được phép
requireRole('admin');

// ================================
// 🟢 Nhận dữ liệu từ request
// ================================
$id = intval($_POST['id'] ?? 0);
$admin_tra_loi = trim($_POST['admin_tra_loi'] ?? '');

if ($id <= 0 || !$admin_tra_loi) {
    json_err('Thiếu dữ liệu bắt buộc.');
}

try {
    // 🔍 Kiểm tra bình luận có tồn tại
    $stmt = $pdo->prepare("SELECT id FROM binh_luan WHERE id = :id");
    $stmt->execute(['id' => $id]);
    if (!$stmt->fetch()) {
        json_err('Không tìm thấy bình luận cần trả lời.');
    }

    // 💬 Cập nhật phản hồi admin (KHÔNG có cột ngay_tra_loi)
    $stmtUpdate = $pdo->prepare("
        UPDATE binh_luan
        SET admin_tra_loi = :admin_tra_loi
        WHERE id = :id
    ");
    $stmtUpdate->execute([
        'admin_tra_loi' => $admin_tra_loi,
        'id' => $id
    ]);

    json_ok([
        'status' => 'success',
        'message' => '✅ Admin đã trả lời bình luận thành công.'
    ]);

} catch (PDOException $e) {
    json_err('Lỗi khi cập nhật bình luận: ' . $e->getMessage());
}
