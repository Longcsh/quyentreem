<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🟢 Nếu session từ BVTE đã có, chuyển đổi sang dạng chuyên gia
if (isset($_SESSION['user_id'], $_SESSION['vai_tro_id']) && $_SESSION['vai_tro_id'] == 2) {
    if (!isset($_SESSION['expert_id'])) {
        $_SESSION['expert_id'] = $_SESSION['user_id'];
        $_SESSION['expert_name'] = $_SESSION['ho_ten'] ?? $_SESSION['ten_dang_nhap'];
        $_SESSION['role_id'] = 2;
    }
}

// 🔒 Nếu vẫn chưa có session chuyên gia => chặn truy cập
if (!isset($_SESSION['expert_id']) || $_SESSION['role_id'] != 2) {
    header('Location: http://localhost/php/bvte/pages/dang_nhap.php');
    exit;
}
?>
