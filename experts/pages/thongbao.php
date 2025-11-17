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

// ✅ Kiểm tra đăng nhập
if (!isset($_SESSION['expert_id'])) {
    header('Location: ../../pages/dang_nhap.php');
    exit;
}

$chuyen_gia_id = $_SESSION['expert_id'];

// ✅ Lấy danh sách thông báo
$stmt = $conn->prepare("SELECT * FROM thong_bao WHERE tai_khoan_id = ? ORDER BY ngay_gui DESC");
$stmt->execute([$chuyen_gia_id]);
$thong_bao = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>🔔 Thông báo của tôi</title>

  <!-- CSS chung -->
  <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/sidebar-expert.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/home-expert.css?v=<?php echo time(); ?>">

  <!-- CSS riêng cho trang này -->
  <link rel="stylesheet" href="../css/style_thongbao.css?v=<?php echo time(); ?>">
</head>

<body>
  <!-- Thanh sidebar -->
  <?php include '../partials/navbar.php'; ?>

  <!-- Nội dung chính -->
  <div class="main-content">
    <div class="notification-container">
      <h2>🔔 Danh sách thông báo</h2>

      <?php if ($thong_bao): ?>
        <a href="xuly_doc_thongbao.php" class="btn-mark">✅ Đánh dấu tất cả đã đọc</a>

        <?php foreach ($thong_bao as $tb): ?>
          <div class="notice <?= $tb['da_xem'] ? '' : 'unread' ?>">
            <div class="title">📘 <?= htmlspecialchars($tb['tieu_de']) ?></div>
            <div class="content"><?= htmlspecialchars($tb['noi_dung']) ?></div>
            <div class="time">🕓 <?= htmlspecialchars($tb['ngay_gui']) ?></div>
          </div>
        <?php endforeach; ?>

      <?php else: ?>
        <p class="empty">Không có thông báo nào.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- JS dropdown sidebar -->
  <script src="../js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
