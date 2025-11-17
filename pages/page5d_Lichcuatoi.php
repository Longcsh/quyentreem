<?php

require_once __DIR__ . '/../config.php';


$db = new Database();
$conn = $db->connect();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: dang_nhap.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy lịch hẹn của người dùng
$stmt = $conn->prepare("
    SELECT lh.*, cg.ho_ten AS ten_chuyen_gia
    FROM lich_hen lh
    LEFT JOIN tai_khoan cg ON lh.chuyen_gia_id = cg.id
    WHERE lh.nguoi_dung_id = ?
    ORDER BY lh.ngay_dat DESC
");
$stmt->execute([$user_id]);
$lich_hen = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="lichhen-toi">
  <h2> Lịch hẹn của tôi</h2>

  <?php if ($lich_hen): ?>
    <ul class="lichhen-list">
      <?php foreach ($lich_hen as $lich): ?>
        <li class="lichhen-item">
          <strong>⏰ Thời gian:</strong> <?php echo htmlspecialchars($lich['ngay_gio']); ?><br>
          <strong>👩‍⚕️ Chuyên gia:</strong> <?php echo htmlspecialchars($lich['ten_chuyen_gia'] ?? 'Chưa phân công'); ?><br>
          <strong>📄 Nội dung:</strong> <?php echo htmlspecialchars($lich['noi_dung']); ?><br>
          <strong>📌 Trạng thái:</strong> 
          <span class="status <?php echo htmlspecialchars($lich['trang_thai']); ?>">
            <?php echo htmlspecialchars($lich['trang_thai']); ?>
          </span><br>
          <em>📅 Ngày đặt: <?php echo htmlspecialchars($lich['ngay_dat']); ?></em>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p class="empty">⏳ Bạn chưa có lịch hẹn nào.</p>
  <?php endif; ?>
</section>

<link rel="stylesheet" href="../css/lichhen.css">
