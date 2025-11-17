<?php
// 📂 admin12/BE/them_thong_bao.php
require_once __DIR__ . '/db.php';  // giữ nguyên kết nối PDO

// ❌ Không cần header JSON vì file này không phải API độc lập
// header('Content-Type: application/json; charset=UTF-8');

/**
 * Gửi thông báo tới người dùng / chuyên gia
 * @param int $tai_khoan_id - ID người nhận
 * @param string $tieu_de - Tiêu đề thông báo
 * @param string $noi_dung - Nội dung chi tiết
 * @return bool
 */
function guiThongBao($tai_khoan_id, $tieu_de, $noi_dung) {
    global $conn; // $conn là đối tượng PDO
    if (!$tai_khoan_id) return false;

    try {
        // Lưu thông báo vào bảng thong_bao
        $sql = "INSERT INTO thong_bao 
                (tai_khoan_id, loai_thong_bao, tieu_de, noi_dung, da_xem, ngay_gui)
                VALUES (?, 'lich_hen', ?, ?, 0, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$tai_khoan_id, $tieu_de, $noi_dung]);
        return true;
    } catch (PDOException $e) {
        error_log('Lỗi khi gửi thông báo: ' . $e->getMessage());
        return false;
    }
}

/**
 * 🔧 Alias tương thích với các file cũ gọi themThongBao()
 * Giúp tránh lỗi “Call to undefined function themThongBao()”
 */
function themThongBao($tai_khoan_id, $noi_dung) {
    // Gọi lại hàm chính guiThongBao với tiêu đề mặc định
    guiThongBao($tai_khoan_id, 'Thông báo hệ thống', $noi_dung);
}
?>
