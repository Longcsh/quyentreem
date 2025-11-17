<?php
require_once __DIR__ . '/../../config.php';

if (isset($_SESSION['user_id'], $_SESSION['vai_tro_id']) && $_SESSION['vai_tro_id'] == 2) {
    if (!isset($_SESSION['expert_id'])) {
        $_SESSION['expert_id'] = $_SESSION['user_id'];
        $_SESSION['expert_name'] = $_SESSION['ho_ten'] ?? $_SESSION['ten_dang_nhap'];
        $_SESSION['role_id'] = 2;
    }
}

// 🔒 Kiểm tra đăng nhập chuyên gia
if (!isset($_SESSION['expert_id']) || ($_SESSION['role_id'] ?? 0) != 2) {
  header('Location: ../../pages/dang_nhap.php');
  exit;
}

$chuyen_gia_id = $_SESSION['expert_id'];
$ho_ten = $_SESSION['expert_name'] ?? 'Chuyên gia';
$conn = (new Database())->connect();

// ✅ Lấy danh sách tài liệu của chuyên gia
$sql = "SELECT id, tieu_de, mo_ta, file_url, ngay_upload, trang_thai, loai_tai_lieu
        FROM tai_lieu 
        WHERE tai_khoan_id = ?
        ORDER BY ngay_upload DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([$chuyen_gia_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>📚 Danh sách tài liệu</title>

  <!-- CSS -->
  <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/sidebar-expert.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/style_danhsachtailieu.css?v=<?php echo time(); ?>">
</head>
<body>

  <!-- Sidebar -->
  <?php include '../partials/navbar.php'; ?>

  <!-- Nội dung chính -->
  <main class="main-content">
    <div class="doclist-container">
      <h2>📚 Danh sách tài liệu của <?= htmlspecialchars($ho_ten) ?></h2>

      <?php if (count($posts) > 0): ?>
        <table class="doc-table">
          <thead>
            <tr>
              <th>Tiêu đề</th>
              <th>Loại tài liệu</th>
              <th>Ngày đăng</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($posts as $p): ?>
              <tr>
                <td><?= htmlspecialchars($p['tieu_de']) ?></td>
                <td><?= htmlspecialchars($p['loai_tai_lieu']) ?></td>
                <td><?= date("d/m/Y H:i", strtotime($p['ngay_upload'])) ?></td>
                <td>
                  <?php if ($p['trang_thai'] == 'da_duyet'): ?>
                    <span class="status success">Đã duyệt</span>
                  <?php else: ?>
                    <span class="status pending">Chờ duyệt</span>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="sua_tailieu.php?id=<?= $p['id'] ?>" class="btn btn-view">Sửa</a>
                  <a href="xoa_tailieu.php?id=<?= $p['id'] ?>" onclick="return confirm('Bạn có chắc muốn xóa tài liệu này không?')" class="btn btn-delete"> Xóa</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="empty">Bạn chưa đăng tài liệu nào.</p>
      <?php endif; ?>
    </div>
  </main>

  <script src="../js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
