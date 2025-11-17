<?php
header('Content-Type: application/json; charset=UTF-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/function.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🟩 Nhận tham số lọc từ FE
$search        = $_GET['search'] ?? '';
$loai_tai_lieu = $_GET['loai_tai_lieu'] ?? '';
$vai_tro_loc   = $_GET['vai_tro'] ?? '';
$trang_thai    = $_GET['trang_thai'] ?? '';

$sql = "
    SELECT 
        tl.id,
        tl.tieu_de,
        tl.mo_ta,
        tl.loai_tai_lieu,
        tl.file_url,
        tl.trang_thai,
        tl.ngay_upload,
        tl.vai_tro_id,
        v.ten_vai_tro AS vai_tro,
        tk.ho_ten AS nguoi_dang,
        tk.ten_dang_nhap
    FROM tai_lieu tl
    LEFT JOIN vai_tro v ON tl.vai_tro_id = v.id
    LEFT JOIN tai_khoan tk ON tl.tai_khoan_id = tk.id
    WHERE 1
      AND tl.vai_tro_id != 1   -- 🚫 loại bỏ người dùng thường
";

$params = [];

// 🔍 Lọc theo trạng thái nếu có
if ($trang_thai !== '') {
    $sql .= " AND tl.trang_thai = :trang_thai";
    $params[':trang_thai'] = $trang_thai;
}

// 🔍 Lọc theo loại tài liệu
if ($loai_tai_lieu !== '') {
    $sql .= " AND tl.loai_tai_lieu = :loai_tai_lieu";
    $params[':loai_tai_lieu'] = $loai_tai_lieu;
}

// 🔍 Lọc theo vai trò (chuyên gia hoặc quản trị viên)
if ($vai_tro_loc !== '') {
    $sql .= " AND tl.vai_tro_id = :vai_tro_loc";
    $params[':vai_tro_loc'] = $vai_tro_loc;
}

// 🔍 Tìm kiếm theo tiêu đề hoặc người đăng
if ($search !== '') {
    $sql .= " AND (tl.tieu_de LIKE :kw OR tk.ho_ten LIKE :kw)";
    $params[':kw'] = "%{$search}%";
}

$sql .= " ORDER BY tl.ngay_upload DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 🧭 Base URL để xử lý đường dẫn file
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    $baseUrl = $protocol . $host . $path . '/';

    $data = [];
    foreach ($rows as $row) {
        $file_url = $row['file_url'] ?? '';
        if ($file_url && !preg_match('/^https?:\/\//', $file_url)) {
            $file_url = $baseUrl . ltrim($file_url, '/');
        }

        $nguoi_dang = $row['nguoi_dang'] ?: ($row['ten_dang_nhap'] ?: 'Không rõ');

        $data[] = [
            "id"            => (int)$row['id'],
            "tieu_de"       => $row['tieu_de'],
            "mo_ta"         => $row['mo_ta'],
            "loai_tai_lieu" => $row['loai_tai_lieu'],
            "file_url"      => $file_url ?: null,
            "file_name"     => $file_url ? basename($file_url) : null,
            "trang_thai"    => $row['trang_thai'],
            "ngay_upload"   => $row['ngay_upload'],
            "vai_tro_id"    => (int)($row['vai_tro_id'] ?? 0),
            "vai_tro"       => $row['vai_tro'] ?: 'Không rõ',
            "nguoi_dang"    => $nguoi_dang
        ];
    }

    echo json_encode(["success" => true, "items" => $data], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
