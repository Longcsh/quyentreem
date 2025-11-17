<?php
session_start();
require_once __DIR__ . '/../BE/db.php';

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("
    SELECT lh.*, 
           u.ho_ten AS ten_user,
           cg.ho_ten AS ten_chuyen_gia,
           cm.ten_chuyen_mon
    FROM lich_hen lh
    LEFT JOIN tai_khoan u ON lh.nguoi_dung_id = u.id
    LEFT JOIN tai_khoan cg ON lh.chuyen_gia_id = cg.id
    LEFT JOIN chuyen_mon cm ON lh.chuyen_mon_id = cm.id
    WHERE lh.id = ?
");
$stmt->execute([$id]);
$lich = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chi tiết lịch hẹn</title>
<link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<h2>📖 Chi tiết lịch hẹn #<?= $lich['id'] ?></h2>

<p><strong>Người đặt:</strong> <?= htmlspecialchars($lich['ten_user']) ?></p>
<p><strong>Chuyên môn:</strong> <?= htmlspecialchars($lich['ten_chuyen_mon']) ?></p>
<p><strong>Chuyên gia:</strong> <?= htmlspecialchars($lich['ten_chuyen_gia'] ?? 'Chưa phân công') ?></p>
<p><strong>Thời gian:</strong> <?= htmlspecialchars($lich['ngay_gio']) ?></p>
<p><strong>Nội dung:</strong> <?= nl2br(htmlspecialchars($lich['noi_dung'])) ?></p>
<p><strong>Trạng thái:</strong> <?= htmlspecialchars($lich['trang_thai']) ?></p>

<a href="quanly_lichhen.php">⬅ Quay lại danh sách</a>

</body>
</html>
