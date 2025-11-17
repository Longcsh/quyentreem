<?php
require_once __DIR__ . '/db.php';

/* ==========================================================
   🧱 1️⃣ Tạo slug (SEO link)
   ========================================================== */
function create_slug($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9-]+/u', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

/* ==========================================================
   🧩 2️⃣ Xác định trạng thái mặc định theo vai trò
   ========================================================== */
function default_status($vai_tro_id) {
    if ($vai_tro_id == 2 || $vai_tro_id == 3) {
        return 'da_duyet'; // admin + chuyên gia -> được duyệt luôn
    }
    return 'khong_duoc_phep'; // user thường -> không được đăng
}

/* ==========================================================
   📁 3️⃣ Upload file
   ========================================================== */
function upload_file($input_name, $folder = 'uploads/tailieu') {
    if (!isset($_FILES[$input_name]) || $_FILES[$input_name]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    // ✅ Danh sách định dạng cho phép
    $allowed_ext = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($_FILES[$input_name]['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_ext)) {
        json_err("Định dạng file không hợp lệ! Cho phép: " . implode(', ', $allowed_ext));
    }

    // ✅ Tạo thư mục nếu chưa có
    $target_dir = __DIR__ . '/../' . $folder . '/';
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $new_name = time() . '_' . uniqid() . '.' . $ext;
    $target_path = $target_dir . $new_name;

    if (move_uploaded_file($_FILES[$input_name]['tmp_name'], $target_path)) {
        // Trả về đường dẫn tương đối (để FE có thể truy cập)
        return $folder . '/' . $new_name;
    }

    json_err("Không thể tải file lên máy chủ!");
}


/* ==========================================================
   ✳️ 4️⃣ Thêm tài liệu
   ========================================================== */
function save_post($type, $data, $file_field = null) {
    global $pdo;
    if ($type !== 'tai_lieu') json_err("Loại không hợp lệ");

    $tai_khoan_id = intval($data['tai_khoan_id'] ?? 0);
    $tieu_de      = trim($data['tieu_de'] ?? '');
    $mo_ta        = trim($data['mo_ta'] ?? '');
    $loai_tai_lieu= trim($data['loai_tai_lieu'] ?? '');
    $vai_tro_id   = intval($data['vai_tro_id'] ?? 3);
    $vai_tro_id   = intval($data['vai_tro_id'] ?? 3);

// ❌ Chặn người dùng thường (vai_tro_id = 3) đăng tài liệu
if ($vai_tro_id === 1) {
    json_err("Người dùng không có quyền đăng tài liệu.", 403);
}

$trang_thai   = default_status($vai_tro_id);

    $trang_thai   = default_status($vai_tro_id);

    $file_url = null;
    if ($file_field && isset($_FILES[$file_field]) && $_FILES[$file_field]['error'] === UPLOAD_ERR_OK) {
        $file_url = upload_file($file_field, 'uploads/tailieu');
    }

    $duong_dan = create_slug($tieu_de);

    $sql = "INSERT INTO tai_lieu (
                tai_khoan_id, tieu_de, mo_ta, file_url, duong_dan, trang_thai, loai_tai_lieu, ngay_upload
            ) VALUES (:tai_khoan_id, :tieu_de, :mo_ta, :file_url, :duong_dan, :trang_thai, :loai_tai_lieu, NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':tai_khoan_id' => $tai_khoan_id,
        ':tieu_de' => $tieu_de,
        ':mo_ta' => $mo_ta,
        ':file_url' => $file_url,
        ':duong_dan' => $duong_dan,
        ':trang_thai' => $trang_thai,
        ':loai_tai_lieu' => $loai_tai_lieu
    ]);

    return $pdo->lastInsertId();
}

/* ==========================================================
   🟩 5️⃣ Duyệt tài liệu
   ========================================================== */
function approve_post($type, $id) {
    global $pdo;
    $table = ($type === 'tai_lieu') ? 'tai_lieu' : 'bai_viet';
    $stmt = $pdo->prepare("UPDATE $table SET trang_thai = 'duyet' WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount();
}

/* ==========================================================
   ❌ 6️⃣ Xóa tài liệu
   ========================================================== */
function delete_post($type, $id) {
    global $pdo;
    $table = ($type === 'tai_lieu') ? 'tai_lieu' : 'bai_viet';
    $stmt = $pdo->prepare("DELETE FROM $table WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount();
}

/* ==========================================================
   📝 7️⃣ Cập nhật tài liệu
   ========================================================== */
function update_post($type, $id, $data, $file_field = null) {
    global $pdo;
    $table = ($type === 'tai_lieu') ? 'tai_lieu' : 'bai_viet';

    if ($file_field && isset($_FILES[$file_field]) && $_FILES[$file_field]['error'] === UPLOAD_ERR_OK) {
        $upload_path = upload_file($file_field, "uploads/$type");
        $data['file_url'] = $upload_path;
    }

    $fields = [];
    foreach ($data as $col => $val) {
        $fields[] = "$col = :$col";
    }

    $sql = "UPDATE $table SET " . implode(', ', $fields) . " WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $data['id'] = $id;
    $stmt->execute($data);

    return $stmt->rowCount();
}

/* ==========================================================
   🌐 8️⃣ Trả JSON chuẩn
   ========================================================== */
if (!function_exists('json_ok')) {
    function json_ok($data = []) {
        echo json_encode(array_merge(["success" => true], $data), JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if (!function_exists('json_err')) {
    function json_err($msg, $code = 400) {
        http_response_code($code);
        echo json_encode(["success" => false, "error" => $msg], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/* ==========================================================
   🔐 9️⃣ Kiểm tra quyền theo vai_tro_id
   ========================================================== */
function requireRole($vai_tro_id_can) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['vai_tro_id'])) json_err("Bạn chưa đăng nhập");

    $current = intval($_SESSION['vai_tro_id']);
    if ($current !== $vai_tro_id_can && $current !== 3) {
        json_err("Không có quyền thực hiện hành động này");
    }
}

/* ==========================================================
   💬 10️⃣ Quản lý bình luận
   ========================================================== */

// 🟩 Lấy danh sách bình luận
function get_comments($loai_noi_dung = null, $tai_lieu_id = null) {
    global $pdo;

    $sql = "SELECT * FROM binh_luan WHERE 1";
    $params = [];

    if ($loai_noi_dung) {
        $sql .= " AND loai_noi_dung = :loai_noi_dung";
        $params[':loai_noi_dung'] = $loai_noi_dung;
    }
    if ($tai_lieu_id) {
        $sql .= " AND tai_lieu_id = :tai_lieu_id";
        $params[':tai_lieu_id'] = $tai_lieu_id;
    }

    $sql .= " ORDER BY ngay_gio DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 🟦 Thêm bình luận mới
function add_comment($user_id, $loai_noi_dung, $tai_lieu_id, $ten, $email, $noi_dung, $tra_loi_id = null, $trang_thai = 'cho_duyet') {
    global $pdo;

    $sql = "INSERT INTO binh_luan 
            (user_id, loai_noi_dung, tai_lieu_id, ten, email, noi_dung, tra_loi_id, ngay_gio, trang_thai)
            VALUES (:user_id, :loai_noi_dung, :tai_lieu_id, :ten, :email, :noi_dung, :tra_loi_id, NOW(), :trang_thai)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id' => $user_id,
        ':loai_noi_dung' => $loai_noi_dung,
        ':tai_lieu_id' => $tai_lieu_id,
        ':ten' => $ten,
        ':email' => $email,
        ':noi_dung' => $noi_dung,
        ':tra_loi_id' => $tra_loi_id,
        ':trang_thai' => $trang_thai
    ]);

    return $pdo->lastInsertId();
}

// 🟧 Cập nhật trạng thái bình luận
function set_comment_status($id, $trang_thai) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE binh_luan SET trang_thai = :trang_thai WHERE id = :id");
    $stmt->execute([':trang_thai' => $trang_thai, ':id' => $id]);
    return true;
}

// 🟪 Admin trả lời bình luận
function reply_comment_admin($id, $admin_tra_loi) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE binh_luan SET admin_tra_loi = :admin_tra_loi WHERE id = :id");
    $stmt->execute([':admin_tra_loi' => $admin_tra_loi, ':id' => $id]);
    return true;
}
/* ==========================================================
   📰 11️⃣ Quản lý bài viết
   ========================================================== */

// 🟩 Lấy danh sách bài viết (có lọc)
function get_bai_viet($search = '', $loai = '', $vai_tro_id = '') {
    global $pdo;

    $sql = "SELECT 
                b.id,
                b.tieu_de,
                b.noi_dung,
                b.file_url,
                b.loai_bai_viet,
                b.trang_thai,
                b.ngay_dang,
                t.ho_ten AS nguoi_dang,
                v.ten_vai_tro AS vai_tro
            FROM bai_viet b
            LEFT JOIN tai_khoan t ON b.tai_khoan_id = t.id
            LEFT JOIN vai_tro v ON b.vai_tro_id = v.id
            WHERE 1=1";
    $params = [];

    if ($search !== '') {
        $sql .= " AND b.tieu_de LIKE :search";
        $params['search'] = "%$search%";
    }
    if ($loai !== '') {
        $sql .= " AND b.loai_bai_viet = :loai";
        $params['loai'] = $loai;
    }
    if ($vai_tro_id !== '') {
        $sql .= " AND b.vai_tro_id = :vai_tro_id";
        $params['vai_tro_id'] = $vai_tro_id;
    }

    $sql .= " ORDER BY b.id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 🟦 Lấy chi tiết bài viết
function get_bai_viet_by_id($id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT b.*, t.ho_ten AS nguoi_dang, v.ten_vai_tro AS vai_tro
        FROM bai_viet b
        LEFT JOIN tai_khoan t ON b.tai_khoan_id = t.id
        LEFT JOIN vai_tro v ON b.vai_tro_id = v.id
        WHERE b.id = :id
    ");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// 🟨 Thêm bài viết mới
function add_bai_viet($data, $file_field = null) {
    global $pdo;

    $tai_khoan_id = intval($data['tai_khoan_id'] ?? 0);
    $tieu_de      = trim($data['tieu_de'] ?? '');
    $noi_dung     = trim($data['noi_dung'] ?? '');
    $loai_bai_viet= trim($data['loai_bai_viet'] ?? '');
    $vai_tro_id   = intval($data['vai_tro_id'] ?? 3);
    $trang_thai   = ($vai_tro_id == 3 || $vai_tro_id == 2) ? 'Đã duyệt' : 'Chờ duyệt';

    $file_url = null;
    if ($file_field && isset($_FILES[$file_field]) && $_FILES[$file_field]['error'] === UPLOAD_ERR_OK) {
        $file_url = upload_file($file_field, 'admin12/assets/baiviet');
    }

    $sql = "INSERT INTO bai_viet (tai_khoan_id, vai_tro_id, tieu_de, noi_dung, file_url, loai_bai_viet, trang_thai, ngay_dang)
            VALUES (:tai_khoan_id, :vai_tro_id, :tieu_de, :noi_dung, :file_url, :loai_bai_viet, :trang_thai, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':tai_khoan_id' => $tai_khoan_id,
        ':vai_tro_id' => $vai_tro_id,
        ':tieu_de' => $tieu_de,
        ':noi_dung' => $noi_dung,
        ':file_url' => $file_url,
        ':loai_bai_viet' => $loai_bai_viet,
        ':trang_thai' => $trang_thai
    ]);

    return $pdo->lastInsertId();
}

// 🟥 Xóa bài viết
function delete_bai_viet($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM bai_viet WHERE id = ?");
    return $stmt->execute([$id]);
}
/* ==========================================================
   📚 12️⃣ Class TaiLieuFunction cho giao diện User
   ========================================================== */
/* ==========================================================
   📚 12️⃣ Class TaiLieuFunction cho giao diện User
   ========================================================== */
class TaiLieuFunction {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // 🟩 Lấy danh sách tài liệu đã duyệt theo loại
    public function getTaiLieu($loai) {
        $sql = "SELECT * FROM tai_lieu 
                WHERE loai_tai_lieu = :loai 
                AND trang_thai = 'da_duyet' 
                ORDER BY ngay_upload DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':loai' => $loai]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🟦 Lấy danh sách bình luận theo loại nội dung
    public function getBinhLuan($loai) {
        $sql = "SELECT * FROM binh_luan 
                WHERE loai_noi_dung = :loai 
                ORDER BY ngay_gio DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':loai' => $loai]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🟨 Thêm bình luận mới (chờ duyệt hoặc hiển thị luôn)
    public function themBinhLuan($loai, $ten, $email, $noi_dung) {
        $sql = "INSERT INTO binh_luan (loai_noi_dung, ten, email, noi_dung, ngay_gio, trang_thai)
                VALUES (:loai, :ten, :email, :noi_dung, NOW(), 'da_duyet')";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':loai' => $loai,
            ':ten' => $ten,
            ':email' => $email,
            ':noi_dung' => $noi_dung
        ]);
        return $this->conn->lastInsertId();
    }

    // 🧮 ➕ Đếm tổng số tài liệu (dành cho phân trang user)
    public function countTaiLieu($loai) {
        $sql = "SELECT COUNT(*) FROM tai_lieu 
                WHERE loai_tai_lieu = :loai 
                AND trang_thai = 'da_duyet'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':loai' => $loai]);
        return (int)$stmt->fetchColumn();
    }

    // 📄 ➕ Lấy tài liệu có phân trang
    public function getTaiLieuPhanTrang($loai, $limit, $offset) {
        $sql = "SELECT * FROM tai_lieu 
                WHERE loai_tai_lieu = :loai 
                AND trang_thai = 'da_duyet'
                ORDER BY ngay_upload DESC 
                LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':loai', $loai, PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
