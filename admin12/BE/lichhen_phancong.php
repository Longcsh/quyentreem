<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/them_thong_bao.php'; // để gửi thông báo
session_start();

global $conn; // ✅ sử dụng kết nối có sẵn từ db.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lich_id = $_POST['lich_id'] ?? null;
    $chuyen_gia_id = $_POST['chuyen_gia_id'] ?? null;

    // 🧩 Kiểm tra dữ liệu đầu vào
    if (!$lich_id || !$chuyen_gia_id) {
        header("Location: ../pages/admin_lichhen.php?msg=Thiếu dữ liệu.");
        exit;
    }

    // 🔍 Lấy thông tin lịch đang được gán
    $stmt = $conn->prepare("SELECT ngay_gio, nguoi_dung_id FROM lich_hen WHERE id = ?");
    $stmt->execute([$lich_id]);
    $lich = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lich) {
        header("Location: ../pages/admin_lichhen.php?msg=Không tìm thấy lịch hẹn.");
        exit;
    }

    $ngay_gio = $lich['ngay_gio'];
    $nguoi_dung_id = $lich['nguoi_dung_id'];

    // 🔒 Kiểm tra chuyên gia đã có lịch trùng chưa
    $check = $conn->prepare("
        SELECT COUNT(*) FROM lich_hen 
        WHERE chuyen_gia_id = ? 
          AND DATE(ngay_gio) = DATE(?) 
          AND trang_thai IN ('cho_xac_nhan', 'da_xac_nhan')
          AND id != ?
    ");
    $check->execute([$chuyen_gia_id, $ngay_gio, $lich_id]);
    $count = $check->fetchColumn();

    if ($count > 0) {
        header("Location: ../pages/admin_lichhen.php?msg=Chuyên gia này đã có lịch trong ngày đó.");
        exit;
    }

    // ✅ Cập nhật lịch sang chuyên gia mới
    $stmt = $conn->prepare("
        UPDATE lich_hen 
        SET chuyen_gia_id = ?, trang_thai = 'cho_xac_nhan' 
        WHERE id = ?
    ");
    $stmt->execute([$chuyen_gia_id, $lich_id]);

    // 🔔 Gửi thông báo cho chuyên gia mới
    guiThongBao($chuyen_gia_id, "Lịch tư vấn mới", "Bạn vừa được admin gán lịch tư vấn mới. Vui lòng xác nhận lịch trên hệ thống.");

    // 🔔 Gửi thông báo cho người dùng
    guiThongBao($nguoi_dung_id, "Lịch tư vấn cập nhật", "Lịch tư vấn của bạn đã được cập nhật với chuyên gia mới.");

    // ✅ Quay lại trang quản lý
    header("Location: ../pages/admin_lichhen.php?msg=Phân công thành công");
    exit;
}
?>
