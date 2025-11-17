<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? ''; // phân biệt hành động qua ?action=...

try {
    /* ============================================================
       🟢 1️⃣ LẤY DANH SÁCH / LỌC CHUYÊN GIA  (GET)
       ============================================================ */
    if ($action === 'list' && $method === 'GET') {
        $timkiem = $_GET['timkiem'] ?? '';
        $chuyenmon = $_GET['chuyenmon'] ?? '';
        $trangthai = $_GET['trangthai'] ?? '';

        $sql = "
            SELECT 
                tk.id,
                tk.ho_ten,
                tk.email,
                tk.so_dien_thoai,
                cm.ten_chuyen_mon,
                tk.trang_thai,
                tk.ngay_tao
            FROM tai_khoan tk
            JOIN chuyen_mon cm ON tk.chuyen_mon_id = cm.id
            WHERE tk.vai_tro_id = 2
        ";

        $params = [];

        if ($timkiem !== '') {
            $sql .= " AND (tk.ho_ten LIKE ? OR tk.email LIKE ?)";
            $params[] = "%$timkiem%";
            $params[] = "%$timkiem%";
        }

        if ($chuyenmon !== '') {
            $sql .= " AND tk.chuyen_mon_id = ?";
            $params[] = $chuyenmon;
        }

        if ($trangthai !== '') {
            if ($trangthai === 'Hoạt động' || $trangthai === 'Bị khóa') {
                $sql .= " AND tk.trang_thai = ?";
                $params[] = $trangthai;
            }
        }

        $sql .= " ORDER BY tk.ngay_tao DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }

    /* ============================================================
       🟡 2️⃣ THÊM CHUYÊN GIA  (POST, action=add)
       ============================================================ */
    if ($action === 'add' && $method === 'POST') {
        $ten_dang_nhap = trim($_POST['ten_dang_nhap'] ?? '');
        $mat_khau      = trim($_POST['mat_khau'] ?? '');
        $email         = trim($_POST['email'] ?? '');
        $ho_ten        = trim($_POST['ho_ten'] ?? '');
        $ngay_sinh     = trim($_POST['ngay_sinh'] ?? '');
        $dia_chi       = trim($_POST['dia_chi'] ?? '');
        $so_dien_thoai = trim($_POST['so_dien_thoai'] ?? '');
        $chuyen_mon_id = intval($_POST['chuyen_mon_id'] ?? 0);

        $vai_tro_id = 2; // chuyên gia
        $trang_thai = 'Hoạt động';

        if ($ten_dang_nhap === '' || $mat_khau === '' || $email === '' || $ho_ten === '') {
            throw new Exception('Vui lòng nhập đầy đủ thông tin bắt buộc.');
        }

        // kiểm tra trùng tên đăng nhập
        $stmt = $conn->prepare("SELECT id FROM tai_khoan WHERE ten_dang_nhap = ?");
        $stmt->execute([$ten_dang_nhap]);
        if ($stmt->fetch()) throw new Exception('Tên đăng nhập đã tồn tại.');

        // kiểm tra trùng email
        $stmt = $conn->prepare("SELECT id FROM tai_khoan WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) throw new Exception('Email đã tồn tại.');

        $mat_khau_mahoa = password_hash($mat_khau, PASSWORD_BCRYPT);

        $sql = "INSERT INTO tai_khoan 
            (ten_dang_nhap, mat_khau, email, ho_ten, ngay_sinh, dia_chi, so_dien_thoai, chuyen_mon_id, vai_tro_id, trang_thai, ngay_tao)
            VALUES (:ten_dang_nhap, :mat_khau, :email, :ho_ten, :ngay_sinh, :dia_chi, :so_dien_thoai, :chuyen_mon_id, :vai_tro_id, :trang_thai, NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':ten_dang_nhap' => $ten_dang_nhap,
            ':mat_khau'      => $mat_khau_mahoa,
            ':email'         => $email,
            ':ho_ten'        => $ho_ten,
            ':ngay_sinh'     => $ngay_sinh ?: null,
            ':dia_chi'       => $dia_chi ?: null,
            ':so_dien_thoai' => $so_dien_thoai ?: null,
            ':chuyen_mon_id' => $chuyen_mon_id ?: null,
            ':vai_tro_id'    => $vai_tro_id,
            ':trang_thai'    => $trang_thai
        ]);

        echo json_encode([
            'success' => true,
            'message' => '✅ Thêm chuyên gia thành công!',
            'id' => $conn->lastInsertId()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /* ============================================================
       🔵 3️⃣ CẬP NHẬT TRẠNG THÁI  (POST, action=update)
       ============================================================ */
    if ($action === 'update' && $method === 'POST') {
        $id = $_POST['id'] ?? 0;
        $hientai = $_POST['trangthai'] ?? '';

        if (!$id || $hientai === '') {
            echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu gửi lên.']);
            exit;
        }

        $newTrangThai = ($hientai === 'Hoạt động') ? 'Bị khóa' : 'Hoạt động';

        $stmt = $conn->prepare("UPDATE tai_khoan SET trang_thai = ? WHERE id = ? AND vai_tro_id = 2");
        $stmt->execute([$newTrangThai, $id]);

        echo json_encode([
            'success' => true,
            'message' => "✅ Đã đổi trạng thái thành: $newTrangThai",
            'newStatus' => $newTrangThai
        ]);
        exit;
    }

    /* ============================================================
       🔴 4️⃣ XÓA CHUYÊN GIA  (POST, action=delete)
       ============================================================ */
    if ($action === 'delete' && $method === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'] ?? 0;

        $stmt = $conn->prepare("DELETE FROM tai_khoan WHERE id = ? AND vai_tro_id = 2");
        $success = $stmt->execute([$id]);

        echo json_encode([
            'success' => $success,
            'message' => $success ? '✅ Đã xóa chuyên gia thành công.' : '⚠️ Không thể xóa chuyên gia.'
        ]);
        exit;
    }

    /* ============================================================
       ❌ Không khớp hành động nào
       ============================================================ */
    echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ hoặc phương thức sai.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => '⚠️ ' . $e->getMessage()]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => '❌ Lỗi CSDL: ' . $e->getMessage()]);
}
?>
