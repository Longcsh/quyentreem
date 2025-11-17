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

$conn = (new Database())->connect();
$chuyen_gia_id = $_SESSION['expert_id'];

// ✅ Lấy thông tin chuyên gia
$stmtInfo = $conn->prepare("SELECT ho_ten FROM tai_khoan WHERE id = ?");
$stmtInfo->execute([$chuyen_gia_id]);
$expert = $stmtInfo->fetch(PDO::FETCH_ASSOC);
$ten_chuyen_gia = $expert['ho_ten'] ?? 'Chuyên gia';

// ✅ Lấy danh sách bài viết
$sql = "SELECT id, tieu_de, loai_bai_viet, ngay_dang, trang_thai 
        FROM bai_viet 
        WHERE tai_khoan_id = ? 
        ORDER BY ngay_dang DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([$chuyen_gia_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Danh sách bài viết của <?= htmlspecialchars($ten_chuyen_gia) ?></title>

  <!-- ✅ CSS -->
<!-- ✅ CSS -->
  <link rel="stylesheet" href="/php/bvte/experts/css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="/php/bvte/experts/css/sidebar-expert.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="/php/bvte/experts/css/style_baiviet.css?v=<?php echo time(); ?>">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
  <!-- 🧭 Thanh sidebar + navbar -->
  <?php include '../partials/navbar.php'; ?>

  <div class="main-content">
    <div class="baiviet-container">
      <h2>📰 Danh sách bài viết của <?= htmlspecialchars($ten_chuyen_gia) ?></h2>

      <table class="table-bv">
        <thead>
          <tr>
            <th>Tiêu đề</th>
            <th>Chuyên mục</th>
            <th>Ngày đăng</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($posts): ?>
            <?php foreach ($posts as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row['tieu_de']) ?></td>
                <td>
                  <?php
                    $loai = [
                      "tin_tuc_su_kien" => "Tin tức – Sự kiện",
                      "tieng_noi_cua_tre" => "Tiếng nói của trẻ",
                      "chia_se" => "Chia sẻ cộng đồng",
                      "kien_thuc_ki_nang" => "Kiến thức – Kĩ năng",
                      "sang_kien_cho_tre" => "Sáng kiến cho trẻ"
                    ];
                    echo htmlspecialchars($loai[$row['loai_bai_viet']] ?? $row['loai_bai_viet']);
                  ?>
                </td>
                <td><?= date("d/m/Y H:i", strtotime($row['ngay_dang'])) ?></td>
                <td>
                  <?php if (strtolower($row['trang_thai']) === 'da_duyet' || strtolower($row['trang_thai']) === 'đã duyệt'): ?>
                    <span class="status active">Đã duyệt</span>
                  <?php else: ?>
                    <span class="status pending">Chờ duyệt</span>
                  <?php endif; ?>
                </td>
                <td class="actions">
                  <a href="sua_baiviet.php?id=<?= $row['id'] ?>" class="btn-view">
                    ✏️ Sửa
                  </a>
                  <a href="xoa_baiviet.php?id=<?= $row['id'] ?>" 
                     onclick="return confirm('Bạn có chắc muốn xóa bài viết này không?')" 
                     class="btn-delete">
                    🗑 Xóa
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="5" style="text-align:center;">📭 Chưa có bài viết nào.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ✅ JS -->
  <script src="./../js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
