<?php
require_once __DIR__ . '/../../config.php';

$conn = (new Database())->connect();

$id = $_GET['id'] ?? 0;
$sql = "SELECT * FROM tai_lieu WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$doc = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doc) {
    echo "<p>Không tìm thấy tài liệu.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($doc['tieu_de']) ?></title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="detail-container">
  <h2>📘 <?= htmlspecialchars($doc['tieu_de']) ?></h2>
  <p><strong>Mô tả:</strong> <?= nl2br(htmlspecialchars($doc['mo_ta'])) ?></p>
  <p><strong>Ngày đăng:</strong> <?= htmlspecialchars($doc['ngay_upload']) ?></p>
  <p><strong>Loại tài liệu:</strong> <?= htmlspecialchars($doc['loai_tai_lieu']) ?></p>
  <p><strong>Trạng thái:</strong> <?= htmlspecialchars($doc['trang_thai']) ?></p>
  <?php if (!empty($doc['file_url'])): ?>
    <p><a href="../../<?= htmlspecialchars($doc['file_url']) ?>" target="_blank">📂 Tải file đính kèm</a></p>
  <?php endif; ?>
  <a href="danhsach_tailieu.php" class="back-btn">⬅ Quay lại danh sách</a>
</div>
</body>
</html>
