<?php
require_once __DIR__ . '/../../config.php';

if (isset($_SESSION['user_id'], $_SESSION['vai_tro_id']) && $_SESSION['vai_tro_id'] == 2) {
    if (!isset($_SESSION['expert_id'])) {
        $_SESSION['expert_id'] = $_SESSION['user_id'];
        $_SESSION['expert_name'] = $_SESSION['ho_ten'] ?? $_SESSION['ten_dang_nhap'];
        $_SESSION['role_id'] = 2;
    }
}

// 🟢 Kiểm tra đăng nhập chuyên gia
if (!isset($_SESSION['expert_id']) || ($_SESSION['role_id'] ?? 0) != 2) {
    header('Location: ../../pages/dang_nhap.php');
    exit;
}

$db = new Database();
$conn = $db->connect();

$chuyen_gia_id = $_SESSION['expert_id'];
$ho_ten = $_SESSION['expert_name'] ?? 'Chuyên gia';

// ✅ Lấy danh sách lịch đã được xác nhận
$sql = "SELECT lh.id, 
               u.ho_ten AS ten_nguoi_dung, 
               cm.ten_chuyen_mon, 
               lh.ngay_gio, 
               lh.noi_dung, 
               lh.trang_thai
        FROM lich_hen lh
        JOIN tai_khoan u ON lh.nguoi_dung_id = u.id
        JOIN chuyen_mon cm ON lh.chuyen_mon_id = cm.id
        WHERE lh.chuyen_gia_id = ? 
          AND lh.trang_thai = 'da_xac_nhan'
        ORDER BY lh.ngay_gio DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([$chuyen_gia_id]);
$lichhen = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>📅 Lịch của <?= htmlspecialchars($ho_ten) ?></title>

  <!-- CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/sidebar-expert.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/style_lichhen_cuatoi.css?v=<?php echo time(); ?>">
</head>

<body>
  <!-- Sidebar -->
  <?php include '../partials/navbar.php'; ?>

  <!-- Nội dung chính -->
  <main class="main-content">
    <div class="lichcuatoi-container">
      <h2>📅 Lịch hẹn đã xác nhận của <?= htmlspecialchars($ho_ten) ?></h2>

      <?php if (count($lichhen) > 0): ?>
        <table class="lichcuatoi-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Người dùng</th>
              <th>Chuyên môn</th>
              <th>Ngày giờ</th>
              <th>Nội dung</th>
              <th>Trạng thái</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($lichhen as $lh): ?>
              <tr>
                <td><?= htmlspecialchars($lh['id']) ?></td>
                <td><?= htmlspecialchars($lh['ten_nguoi_dung']) ?></td>
                <td><?= htmlspecialchars($lh['ten_chuyen_mon']) ?></td>
                <td><?= htmlspecialchars($lh['ngay_gio']) ?></td>
                <td><?= nl2br(htmlspecialchars($lh['noi_dung'])) ?></td>
                <td><span class="status success">Đã duyệt</span></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="empty">Chưa có lịch hẹn nào được xác nhận.</p>
      <?php endif; ?>
    </div>
  </main>

  <script src="../js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
