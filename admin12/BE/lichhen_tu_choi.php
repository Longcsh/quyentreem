<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/them_thong_bao.php';
session_start();

global $conn;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lich_id = $_POST['lich_id'] ?? null;
    $ly_do = trim($_POST['ly_do_tu_choi'] ?? '');

    if (!$lich_id) {
        header("Location: ../pages/admin_lichhen.php?msg=Thiếu mã lịch.");
        exit;
    }

    // 🔍 Lấy thông tin người dùng & chuyên gia để gửi thông báo
    $stmt = $conn->prepare("
        SELECT nguoi_dung_id, chuyen_gia_id, ngay_gio 
        FROM lich_hen 
        WHERE id = ?
    ");
    $stmt->execute([$lich_id]);
    $lich = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lich) {
        header("Location: ../pages/admin_lichhen.php?msg=Lịch hẹn không tồn tại.");
        exit;
    }

    $nguoi_dung_id = $lich['nguoi_dung_id'];
    $chuyen_gia_id = $lich['chuyen_gia_id'];
    $ngay_gio = $lich['ngay_gio'];

    // ✅ Cập nhật trạng thái và lý do
    $stmt = $conn->prepare("UPDATE lich_hen SET trang_thai = 'bi_tu_choi', ly_do_tu_choi = ? WHERE id = ?");
    $stmt->execute([$ly_do, $lich_id]);

    // 🔔 Gửi thông báo cho người dùng
    $noi_dung_user = "Lịch tư vấn vào lúc $ngay_gio đã bị từ chối";
    if ($ly_do) $noi_dung_user .= " (Lý do: $ly_do).";
    $noi_dung_user .= " Hệ thống sẽ phân công chuyên gia khác sớm nhất.";
    guiThongBao($nguoi_dung_id, "Lịch tư vấn bị từ chối", $noi_dung_user);

    // 🔔 Gửi thông báo cho chuyên gia (nếu có)
    if ($chuyen_gia_id) {
        $noi_dung_cg = "Lịch tư vấn lúc $ngay_gio đã bị admin hủy";
        if ($ly_do) $noi_dung_cg .= " (Lý do: $ly_do).";
        guiThongBao($chuyen_gia_id, "Lịch tư vấn bị hủy", $noi_dung_cg);
    }

    // ✅ Quay lại trang quản lý
    header("Location: ../pages/admin_lichhen.php?msg=Từ chối thành công");
    exit;
}
?>
