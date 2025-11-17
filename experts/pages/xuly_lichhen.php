<?php
require_once __DIR__ . '/../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lich_hen_id = $_POST['lich_hen_id'];
    $action = $_POST['action'];

    if ($action === 'accept') {
        // ✅ Chuyên gia nhận lịch
        $stmt = $conn->prepare("UPDATE lich_hen SET trang_thai = 'da_xac_nhan' WHERE id = ?");
        $stmt->execute([$lich_hen_id]);
        $_SESSION['msg'] = "✅ Bạn đã nhận lịch thành công!";

       require_once __DIR__ . '/../../admin12/be/them_thong_bao.php';

        guiThongBao($chuyen_gia_id, 'lich_hen', 'Đã nhận lịch', "Bạn đã xác nhận lịch tư vấn ID #{$lich_hen_id}.");

    } elseif ($action === 'reject') {
        // ❌ Từ chối — gửi lại cho admin
        $stmt = $conn->prepare("UPDATE lich_hen SET trang_thai = 'cho_phan_cong' WHERE id = ?");
        $stmt->execute([$lich_hen_id]);
        $_SESSION['msg'] = "❌ Bạn đã từ chối lịch. Hệ thống sẽ gửi lại cho admin.";
require_once __DIR__ . '/../../admin12/be/them_thong_bao.php';

        guiThongBao($chuyen_gia_id, 'lich_hen', 'Từ chối lịch', "Bạn đã từ chối lịch tư vấn ID #{$lich_hen_id}. Lịch sẽ chuyển lại cho admin.");
    }
}
header("Location: lichhen.php");
exit;

?>
