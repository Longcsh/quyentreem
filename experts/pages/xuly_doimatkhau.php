<?php
session_start();
require_once __DIR__ . '/../../config.php';

if (isset($_SESSION['user_id'], $_SESSION['vai_tro_id']) && $_SESSION['vai_tro_id'] == 2) {
    if (!isset($_SESSION['expert_id'])) {
        $_SESSION['expert_id'] = $_SESSION['user_id'];
        $_SESSION['expert_name'] = $_SESSION['ho_ten'] ?? $_SESSION['ten_dang_nhap'];
        $_SESSION['role_id'] = 2;
    }
}
header('Content-Type: application/json; charset=UTF-8');

try {
    // ✅ Kiểm tra đăng nhập chuyên gia
    if (empty($_SESSION['expert_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Bạn chưa đăng nhập!']);
        exit;
    }

    $id = $_SESSION['expert_id'];
    $matkhau_cu = trim($_POST['matkhau_cu'] ?? '');
    $matkhau_moi = trim($_POST['matkhau_moi'] ?? '');
    $xacnhan = trim($_POST['xacnhan'] ?? '');

    // ✅ Kiểm tra dữ liệu đầu vào
    if ($matkhau_cu === '' || $matkhau_moi === '' || $xacnhan === '') {
        echo json_encode(['status' => 'error', 'message' => 'Vui lòng nhập đầy đủ thông tin.']);
        exit;
    }

    if ($matkhau_moi !== $xacnhan) {
        echo json_encode(['status' => 'error', 'message' => 'Mật khẩu mới và xác nhận không khớp.']);
        exit;
    }

    // ✅ Lấy mật khẩu hiện tại từ DB
    $stmt = $conn->prepare("SELECT mat_khau FROM tai_khoan WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy tài khoản.']);
        exit;
    }

    if (!password_verify($matkhau_cu, $user['mat_khau'])) {
        echo json_encode(['status' => 'error', 'message' => 'Mật khẩu hiện tại không đúng.']);
        exit;
    }

    // 🚫 Không cho trùng mật khẩu cũ
    if (password_verify($matkhau_moi, $user['mat_khau'])) {
        echo json_encode(['status' => 'error', 'message' => 'Mật khẩu mới không được trùng với mật khẩu cũ.']);
        exit;
    }

    // ✅ Cập nhật mật khẩu mới
    $newHash = password_hash($matkhau_moi, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE tai_khoan SET mat_khau = ? WHERE id = ?");
    $update->execute([$newHash, $id]);

    echo json_encode(['status' => 'success', 'message' => '✅ Đổi mật khẩu thành công!']);
    exit;

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
    exit;
}
?>
