<?php
// 📄 BE/binh_luan_danh_sach.php — Lấy danh sách bình luận (PDO chuẩn hóa, CORS đa môi trường)
header('Content-Type: application/json; charset=UTF-8');

// ✅ CORS động (đa môi trường)
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
}

// ✅ OPTIONS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/db.php';   // ✅ dùng PDO
require_once __DIR__ . '/function.php'; // ✅ các hàm json_ok, json_err, v.v.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ================================
// 🔒 Kiểm tra quyền truy cập
// ================================
// 3 = admin, 2 = mod
if (empty($_SESSION['vai_tro_id']) || !in_array($_SESSION['vai_tro_id'], [2, 3])) {
    echo json_encode([
        "success" => false,
        "error" => "Không có quyền thực hiện hành động này"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ================================
// 🟩 Nhận tham số lọc
// ================================
$loai_noi_dung = $_GET['loai_noi_dung'] ?? '';
$trang_thai    = $_GET['trang_thai'] ?? '';
$tu_khoa       = $_GET['tu_khoa'] ?? '';

// ================================
// 🟦 Tạo câu truy vấn động
// ================================
$sql = "SELECT * FROM binh_luan WHERE 1";
$params = [];

if ($loai_noi_dung !== '') {
    $sql .= " AND loai_noi_dung = :loai_noi_dung";
    $params[':loai_noi_dung'] = $loai_noi_dung;
}

if ($trang_thai !== '') {
    $sql .= " AND trang_thai = :trang_thai";
    $params[':trang_thai'] = $trang_thai;
}

if ($tu_khoa !== '') {
    $sql .= " AND (ten LIKE :kw OR email LIKE :kw OR noi_dung LIKE :kw)";
    $params[':kw'] = "%$tu_khoa%";


}

$sql .= " ORDER BY ngay_gio DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "count"   => count($data),
        "data"    => $data
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "error"   => "Lỗi truy vấn CSDL: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
