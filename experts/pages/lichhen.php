<?php
require_once __DIR__ . '/../../config.php';


// 🟢 Đồng bộ session nếu chuyên gia đăng nhập từ trang BVTE
if (isset($_SESSION['user_id'], $_SESSION['vai_tro_id']) && $_SESSION['vai_tro_id'] == 2) {
    if (!isset($_SESSION['expert_id'])) {
        $_SESSION['expert_id'] = $_SESSION['user_id'];
        $_SESSION['expert_name'] = $_SESSION['ho_ten'] ?? $_SESSION['ten_dang_nhap'];
        $_SESSION['role_id'] = 2;
    }
}

// 🔒 Kiểm tra đăng nhập
if (!isset($_SESSION['expert_id']) || ($_SESSION['role_id'] ?? 0) != 2) {
    header('Location: ../../pages/dang_nhap.php');
    exit;
}

$chuyen_gia_id = $_SESSION['expert_id'];
$ho_ten = $_SESSION['expert_name'] ?? 'Chuyên gia';

// ✅ Lấy danh sách lịch hẹn
$stmt = $conn->prepare("
    SELECT lh.id, u.ho_ten AS ten_user, cm.ten_chuyen_mon, lh.ngay_gio, lh.trang_thai, lh.noi_dung
    FROM lich_hen lh
    JOIN tai_khoan u ON lh.nguoi_dung_id = u.id
    JOIN chuyen_mon cm ON lh.chuyen_mon_id = cm.id
    WHERE lh.chuyen_gia_id = ?
    ORDER BY lh.ngay_gio DESC
");
$stmt->execute([$chuyen_gia_id]);
$lich_hen = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>📅 Lịch hẹn của <?= htmlspecialchars($ho_ten) ?></title>

  <!-- CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/sidebar-expert.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="../css/style_lichhen.css?v=<?php echo time(); ?>">

</head>
<body>
  <!-- Sidebar -->
  <?php include '../partials/navbar.php'; ?>

  <!-- Nội dung chính -->
  <main class="main-content">
    <div class="lichhen-container">
      <h2>📅 Lịch hẹn của Chuyên gia <?= htmlspecialchars($ho_ten) ?></h2>

      <?php if ($lich_hen): ?>
        <table class="schedule-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Người dùng</th>
              <th>Chuyên môn</th>
              <th>Ngày giờ</th>
              <th>Trạng thái</th>
              <th>Nội dung</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($lich_hen as $lh): ?>
              <tr>
                <td><?= $lh['id'] ?></td>
                <td><?= htmlspecialchars($lh['ten_user']) ?></td>
                <td><?= htmlspecialchars($lh['ten_chuyen_mon']) ?></td>
                <td><?= htmlspecialchars($lh['ngay_gio']) ?></td>
                <td>
                  <?php
                    $trang_thai = $lh['trang_thai'];
                    if ($trang_thai == 'cho_xac_nhan') echo '<span class="status pending">Chờ xác nhận</span>';
                    elseif ($trang_thai == 'da_xac_nhan') echo '<span class="status success">Đã nhận</span>';
                    elseif ($trang_thai == 'cho_phan_cong') echo '<span class="status info">Chờ phân công</span>';
                    else echo '<span class="status neutral">'.htmlspecialchars($trang_thai).'</span>';
                  ?>
                </td>
                <td><?= htmlspecialchars($lh['noi_dung']) ?></td>
                <td>
                  <?php if ($lh['trang_thai'] == 'cho_xac_nhan'): ?>
                    <form method="post" action="xuly_lichhen.php" class="inline-form">
                      <input type="hidden" name="lich_hen_id" value="<?= $lh['id'] ?>">
                      <button type="submit" name="action" value="accept" class="btn btn-accept">✅ Nhận</button>
                      <button type="submit" name="action" value="reject" class="btn btn-reject">❌ Từ chối</button>
                    </form>
                  <?php else: ?>
                    <em class="done">—</em>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="empty">Không có lịch hẹn nào.</p>
      <?php endif; ?>
    </div>
  </main>

  <script src="../js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
