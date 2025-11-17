<?php
// ===============================================
// 📂 File: admin12/BE/baiviet_action.php
// 👉 Hợp nhất toàn bộ CRUD (list, detail, add, update, delete)
// ===============================================

header('Content-Type: application/json; charset=UTF-8');

// ✅ CORS động cho đa môi trường
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
}
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/function.php';

// ✅ Kết nối PDO toàn cục
$pdo = $GLOBALS['pdo'] ?? null;
if (!$pdo) {
    echo json_encode(['error' => true, 'message' => 'Không thể kết nối CSDL.']);
    exit;
}

// ✅ Khởi tạo session
if (session_status() === PHP_SESSION_NONE) session_start();

// ===================================================
// 🧩 Thông tin người đăng nhập
// ===================================================
$user_id    = $_SESSION['user_id'] ?? 0;
$vai_tro_id = $_SESSION['vai_tro_id'] ?? 0;

$isAdmin  = ($vai_tro_id == 3);
$isExpert = ($vai_tro_id == 2);
$isUser   = ($vai_tro_id == 1);

// ===================================================
// 🧭 Xác định action
// ===================================================
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    // ===================================================
    // 📜 1️⃣ Lấy danh sách bài viết
    // ===================================================
    case 'list':
        $search        = $_GET['search'] ?? ($_GET['tu_khoa'] ?? '');
        $loai_bai_viet = $_GET['loai'] ?? ($_GET['loai_bai_viet'] ?? '');
        $vai_tro       = $_GET['vai_tro'] ?? '';

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host     = $_SERVER['HTTP_HOST'];
        $assetBase = $protocol . $host . '/php/bvte/admin12/assets/baiviet/';

        $sql = "
            SELECT 
                bv.id, bv.tieu_de, bv.noi_dung, bv.anh_dai_dien, 
                bv.loai_bai_viet, bv.trang_thai, bv.ngay_dang,
                tk.ho_ten AS nguoi_dang, vt.ten_vai_tro AS vai_tro, tk.vai_tro_id
            FROM bai_viet bv
            LEFT JOIN tai_khoan tk ON bv.tai_khoan_id = tk.id
            LEFT JOIN vai_tro vt ON tk.vai_tro_id = vt.id
            WHERE 1
        ";

        $params = [];
        if ($isAdmin) {
            // thấy hết
        } elseif ($isExpert) {
            $sql .= " AND bv.tai_khoan_id = :uid";
            $params['uid'] = $user_id;
        } else {
            $sql .= " AND bv.trang_thai = 'da_duyet'";
        }

        if ($search) {
            $sql .= " AND bv.tieu_de LIKE :search";
            $params['search'] = "%$search%";
        }
        if ($loai_bai_viet) {
            $sql .= " AND bv.loai_bai_viet = :loai_bai_viet";
            $params['loai_bai_viet'] = $loai_bai_viet;
        }
        if ($vai_tro) {
            $sql .= " AND tk.vai_tro_id = :vai_tro";
            $params['vai_tro'] = $vai_tro;
        }

        $sql .= " ORDER BY bv.ngay_dang DESC";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $data = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $row['anh_dai_dien'] = !empty($row['anh_dai_dien'])
                    ? $assetBase . $row['anh_dai_dien']
                    : null;
                $row['nguoi_dang'] = $row['nguoi_dang'] ?: 'Không rõ';
                $row['vai_tro']    = $row['vai_tro'] ?: 'Không rõ';
                $data[] = $row;
            }
            echo json_encode(['status' => 'success', 'data' => $data]);
        } catch (PDOException $e) {
            echo json_encode(['error' => true, 'message' => 'Lỗi truy vấn: ' . $e->getMessage()]);
        }
        break;

    // ===================================================
    // 📄 2️⃣ Lấy chi tiết bài viết
    // ===================================================
    case 'detail':
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) json_err('Thiếu ID bài viết.');

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host     = $_SERVER['HTTP_HOST'];
        $assetBase = $protocol . $host . '/php/bvte/admin12/assets/baiviet/';

        try {
            $sql = "SELECT bv.*, tk.ho_ten AS nguoi_dang, tk.vai_tro_id
                    FROM bai_viet bv
                    LEFT JOIN tai_khoan tk ON bv.tai_khoan_id = tk.id
                    WHERE bv.id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$res) json_err('Không tìm thấy bài viết.');

            if (!empty($res['anh_dai_dien']) && !preg_match('/^https?:\/\//', $res['anh_dai_dien'])) {
                $res['anh_dai_dien'] = $assetBase . $res['anh_dai_dien'];
            }

            switch ((int)$res['vai_tro_id']) {
                case 3: $res['vai_tro'] = 'Admin'; break;
                case 2: $res['vai_tro'] = 'Chuyên gia'; break;
                default: $res['vai_tro'] = 'Người dùng';
            }

            echo json_encode(['status' => 'success', 'data' => $res]);
        } catch (PDOException $e) {
            json_err('Lỗi truy vấn: ' . $e->getMessage());
        }
        break;

    // ===================================================
    // 🟢 3️⃣ Thêm bài viết
    // ===================================================
    case 'add':
        if ($isUser) json_err("Tài khoản người dùng không được phép đăng bài.");

        $tieu_de = trim($_POST['tieu_de'] ?? '');
        $noi_dung = trim($_POST['noi_dung'] ?? '');
        $loai_bai_viet = trim($_POST['loai_bai_viet'] ?? '');
        if ($tieu_de === '' || $noi_dung === '' || $loai_bai_viet === '') {
            json_err("Thiếu tiêu đề, nội dung hoặc loại bài viết.");
        }

        $anh_dai_dien = null;
        if (!empty($_FILES['anh_dai_dien']['name']) && $_FILES['anh_dai_dien']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = dirname(__DIR__) . '/assets/baiviet/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $ext = pathinfo($_FILES['anh_dai_dien']['name'], PATHINFO_EXTENSION);
            $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array(strtolower($ext), $allowedExt)) json_err("Định dạng ảnh không hợp lệ.");

            $fileName = time() . '_' . uniqid() . '.' . strtolower($ext);
            if (!move_uploaded_file($_FILES['anh_dai_dien']['tmp_name'], $uploadDir . $fileName))
                json_err("Không thể tải ảnh lên server.");
            $anh_dai_dien = $fileName;
        }

        $trang_thai = ($vai_tro_id == 3) ? 'da_duyet' : 'cho_duyet';

        try {
            $sql = "INSERT INTO bai_viet 
                    (vai_tro_id, tai_khoan_id, tieu_de, noi_dung, anh_dai_dien, loai_bai_viet, trang_thai, ngay_dang)
                    VALUES 
                    (:vai_tro_id, :tai_khoan_id, :tieu_de, :noi_dung, :anh_dai_dien, :loai_bai_viet, :trang_thai, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'vai_tro_id' => $vai_tro_id,
                'tai_khoan_id' => $user_id,
                'tieu_de' => $tieu_de,
                'noi_dung' => $noi_dung,
                'anh_dai_dien' => $anh_dai_dien,
                'loai_bai_viet' => $loai_bai_viet,
                'trang_thai' => $trang_thai
            ]);
            json_ok(['status' => 'success', 'message' => '✅ Thêm bài viết thành công!', 'id' => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            json_err("Lỗi khi thêm bài viết: " . $e->getMessage());
        }
        break;

    // ===================================================
    // ✏️ 4️⃣ Sửa bài viết
    // ===================================================
    case 'update':
        if ($isUser) json_err("Người dùng không được phép chỉnh sửa bài viết.");

        $id = intval($_POST['id'] ?? 0);
        $tieu_de = trim($_POST['tieu_de'] ?? '');
        $noi_dung = trim($_POST['noi_dung'] ?? '');
        $loai_bai_viet = trim($_POST['loai_bai_viet'] ?? '');
        if ($id <= 0 || $tieu_de === '' || $noi_dung === '') json_err("Thiếu dữ liệu.");

        $stmt = $pdo->prepare("SELECT * FROM bai_viet WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $old = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$old) json_err("Bài viết không tồn tại.");

        if ($isExpert && $user_id != $old['tai_khoan_id'] && !$isAdmin)
            json_err("Bạn không có quyền chỉnh sửa bài viết này.");

        $anh_dai_dien = $old['anh_dai_dien'];
        $uploadDir = dirname(__DIR__) . '/assets/baiviet/';
        if (!empty($_FILES['anh_dai_dien']['name']) && $_FILES['anh_dai_dien']['error'] === UPLOAD_ERR_OK) {
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $ext = pathinfo($_FILES['anh_dai_dien']['name'], PATHINFO_EXTENSION);
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array(strtolower($ext), $allowed)) json_err("Định dạng ảnh không hợp lệ.");
            $fileName = time() . '_' . uniqid() . '.' . strtolower($ext);
            if (move_uploaded_file($_FILES['anh_dai_dien']['tmp_name'], $uploadDir . $fileName)) {
                if (!empty($anh_dai_dien) && file_exists($uploadDir . basename($anh_dai_dien))) {
                    unlink($uploadDir . basename($anh_dai_dien));
                }
                $anh_dai_dien = $fileName;
            }
        }

        $trang_thai = $isAdmin ? ($old['trang_thai'] ?: 'da_duyet') : 'da_duyet';
        $stmt = $pdo->prepare("UPDATE bai_viet SET tieu_de=:t, noi_dung=:n, loai_bai_viet=:l, anh_dai_dien=:a, trang_thai=:tt WHERE id=:id");
        $stmt->execute([
            't' => $tieu_de, 'n' => $noi_dung, 'l' => $loai_bai_viet,
            'a' => $anh_dai_dien, 'tt' => $trang_thai, 'id' => $id
        ]);
        json_ok(['status' => 'success', 'message' => '✅ Đã cập nhật bài viết thành công!']);
        break;

    // ===================================================
    // 🗑️ 5️⃣ Xóa bài viết
    // ===================================================
    case 'delete':
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) json_err("Thiếu ID bài viết.");

        $stmt = $pdo->prepare("SELECT tai_khoan_id, trang_thai, anh_dai_dien FROM bai_viet WHERE id=:id");
        $stmt->execute(['id' => $id]);
        $bv = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$bv) json_err("Bài viết không tồn tại.");

        if (!$isAdmin) {
            if (!$isExpert || $user_id != $bv['tai_khoan_id'])
                json_err("Bạn không có quyền xóa bài viết này.");
            if ($bv['trang_thai'] === 'da_duyet')
                json_err("Không thể xóa bài đã được duyệt.");
        }

        $pdo->prepare("DELETE FROM bai_viet WHERE id=:id")->execute(['id' => $id]);
        if (!empty($bv['anh_dai_dien'])) {
            $file = dirname(__DIR__) . '/assets/baiviet/' . basename($bv['anh_dai_dien']);
            if (file_exists($file)) unlink($file);
        }
        json_ok(['status' => 'success', 'message' => '🗑️ Đã xóa bài viết thành công.']);
        break;

    // ===================================================
    // 🚫 Mặc định
    // ===================================================
    default:
        echo json_encode(['error' => true, 'message' => 'Hành động không hợp lệ.']);
        break;
}
?>
