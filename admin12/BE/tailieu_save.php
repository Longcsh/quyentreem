<?php
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

if (session_status() === PHP_SESSION_NONE) session_start();

// ======================================================
// 🧩 1️⃣ Xác định người dùng & quyền
// ======================================================
$tai_khoan_id = $_SESSION['user_id'] ?? ($_POST['tai_khoan_id'] ?? null);
$vai_tro_id   = $_SESSION['vai_tro_id'] ?? ($_POST['vai_tro_id'] ?? null);

if (empty($tai_khoan_id) || empty($vai_tro_id)) {
    json_err("Bạn chưa đăng nhập hoặc thiếu thông tin người dùng.");
}

// 🚫 Chặn user thường (vai_tro_id = 1)
if ((int)$vai_tro_id === 1) {
    json_err("Người dùng không có quyền đăng hoặc sửa tài liệu.", 403);
}

// ======================================================
// 🧩 2️⃣ Nhận dữ liệu từ POST
// ======================================================
$id            = $_POST['id'] ?? null; // có id → sửa, không có → thêm mới
$tieu_de       = trim($_POST['tieu_de'] ?? '');
$mo_ta         = trim($_POST['mo_ta'] ?? '');
$loai_tai_lieu = trim($_POST['loai_tai_lieu'] ?? '');

if ($tieu_de === '' || $loai_tai_lieu === '') {
    json_err("Vui lòng nhập đầy đủ tiêu đề và loại tài liệu.");
}

// ======================================================
// 🧩 3️⃣ Cấu hình upload
// ======================================================
$uploadDir = realpath(__DIR__ . '/../../uploads/tailieu');
if (!$uploadDir) $uploadDir = __DIR__ . '/../../uploads/tailieu';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$baseUrl = rtrim($protocol . $host . "/php/bvte/uploads/tailieu", '/');

$file_url = null;

// ======================================================
// 🧩 4️⃣ Nếu có ID → Sửa → Lấy file cũ để so sánh
// ======================================================
if ($id) {
    $stmt = $pdo->prepare("SELECT file_url FROM tai_lieu WHERE id = ?");
    $stmt->execute([$id]);
    $oldFile = $stmt->fetchColumn();
    $file_url = $oldFile;
}

// ======================================================
// 🧩 5️⃣ Nếu có file upload mới
// ======================================================
if (!empty($_FILES['file']['name'])) {
    $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
    $allowed = ['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg'];
    if (!in_array($ext, $allowed)) json_err("Định dạng file không hợp lệ. Chỉ chấp nhận PDF, DOCX, JPG, PNG.");

    $newName = time() . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
    $targetPath = $uploadDir . '/' . $newName;

    if (!move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
        json_err("Không thể lưu file tải lên (kiểm tra quyền thư mục uploads/tailieu).");
    }

    $file_url = $baseUrl . '/' . $newName;

    // 🧹 Nếu là sửa thì xóa file cũ
    if ($id && $oldFile && preg_match('/uploads\/tailieu\//', $oldFile)) {
        $oldPath = str_replace($baseUrl, $uploadDir, $oldFile);
        if (file_exists($oldPath)) unlink($oldPath);
    }
}

// ======================================================
// 🧩 6️⃣ Ghi cơ sở dữ liệu
// ======================================================
try {
    if ($id) {
        // 🟦 Cập nhật
        $stmt = $pdo->prepare("
            UPDATE tai_lieu 
            SET tieu_de = :tieu_de,
                mo_ta = :mo_ta,
                loai_tai_lieu = :loai_tai_lieu,
                file_url = :file_url
            WHERE id = :id
        ");
        $stmt->execute([
            ':tieu_de' => $tieu_de,
            ':mo_ta' => $mo_ta,
            ':loai_tai_lieu' => $loai_tai_lieu,
            ':file_url' => $file_url,
            ':id' => $id
        ]);
        json_ok(["message" => "Cập nhật tài liệu thành công ✅"]);
    } else {
        // 🟢 Thêm mới
        $stmt = $pdo->prepare("
            INSERT INTO tai_lieu (
                tieu_de, mo_ta, loai_tai_lieu, file_url, trang_thai, tai_khoan_id, vai_tro_id, ngay_upload
            ) VALUES (
                :tieu_de, :mo_ta, :loai_tai_lieu, :file_url, 'da_duyet', :tai_khoan_id, :vai_tro_id, NOW()
            )
        ");
        $stmt->execute([
            ':tieu_de' => $tieu_de,
            ':mo_ta' => $mo_ta,
            ':loai_tai_lieu' => $loai_tai_lieu,
            ':file_url' => $file_url,
            ':tai_khoan_id' => $tai_khoan_id,
            ':vai_tro_id' => $vai_tro_id
        ]);
        json_ok(["message" => "Thêm tài liệu thành công 🎉"]);
    }
} catch (PDOException $e) {
    json_err("Lỗi CSDL: " . $e->getMessage());
}
?>
