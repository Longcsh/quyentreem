<?php
// =======================================
// 📂 File: BE/tailieu_xoa.php
// 📌 Dùng PDO, đa môi trường, giữ nguyên logic gốc
// =======================================

header('Content-Type: application/json; charset=UTF-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/function.php';

// 🧩 Bắt đầu session để xác thực
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? 0;
$vai_tro_id = $_SESSION['vai_tro_id'] ?? 0;

if ($user_id <= 0) {
    json_err('⚠️ Bạn chưa đăng nhập');
}

// 🟢 Lấy ID tài liệu
$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    json_err('Thiếu ID tài liệu');
}

try {
    // 🔍 Lấy thông tin tài liệu
    $stmt = $pdo->prepare("SELECT tai_khoan_id, file_url, trang_thai FROM tai_lieu WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$file) {
        json_err("❌ Không tìm thấy tài liệu");
    }

    // =====================================
    // 🧩 Kiểm tra quyền xóa
    // =====================================
    $allowDelete = false;

    if ($vai_tro_id == 3) {
        // 👑 Admin → có thể xóa tất cả
        $allowDelete = true;
    } elseif ($vai_tro_id == 2) {
        // 👨‍🏫 Chuyên gia → chỉ xóa bài của mình và chưa duyệt
        if ($file['tai_khoan_id'] == $user_id && $file['trang_thai'] != 'Duyệt') {
            $allowDelete = true;
        } else {
            json_err('❌ Bạn không thể xóa tài liệu này vì đã được duyệt hoặc không phải của bạn');
        }
    } else {
        // 👤 Người dùng thường → không có quyền
        json_err('❌ Bạn không có quyền xóa tài liệu');
    }

    // =====================================
    // 🗑️ Xóa bản ghi trong DB
    // =====================================
    if ($allowDelete) {
        $stmt2 = $pdo->prepare("DELETE FROM tai_lieu WHERE id = :id");
        $stmt2->execute([':id' => $id]);

        if ($stmt2->rowCount() > 0) {
            // 🧹 Xóa file vật lý (nếu có)
            if (!empty($file['file_url'])) {
                $filePath = realpath(__DIR__ . '/../' . str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $file['file_url']));
                if ($filePath && file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            json_ok([
                "success" => true,
                "message" => "✅ Đã xóa tài liệu thành công"
            ]);
        } else {
            json_err("⚠️ Không thể xóa tài liệu (ID không tồn tại hoặc lỗi SQL)");
        }
    }

} catch (PDOException $e) {
    json_err("💥 Lỗi PDO khi xóa tài liệu: " . $e->getMessage());
}
?>
