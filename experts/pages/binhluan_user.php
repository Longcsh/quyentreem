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
$conn = (new Database())->connect();

// 🧭 Lấy loại nội dung chính (bài viết / tài liệu)
$loai_noi_dung = $_GET['type'] ?? 'bai_viet';
// 🧩 Lấy loại mục con (nếu có)
$muc_con = $_GET['muc_con'] ?? '';

$comments = [];

// ✅ Lấy bình luận tùy theo loại
if ($loai_noi_dung === 'bai_viet') {
    $sql = "SELECT b.noi_dung, b.ngay_gio, b.ten, b.email, v.tieu_de, v.loai_bai_viet AS loai
            FROM binh_luan b
            JOIN bai_viet v 
              ON b.loai_noi_dung COLLATE utf8mb4_general_ci = v.loai_bai_viet COLLATE utf8mb4_general_ci
            WHERE v.tai_khoan_id = :cg_id";
    if ($muc_con) {
        $sql .= " AND v.loai_bai_viet = :muc_con";
    }
    $sql .= " ORDER BY b.ngay_gio DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':cg_id', $chuyen_gia_id);
    if ($muc_con) $stmt->bindValue(':muc_con', $muc_con);
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else { // nếu là tài liệu
    $sql = "SELECT b.noi_dung, b.ngay_gio, b.ten, b.email, t.tieu_de, t.loai_tai_lieu AS loai
            FROM binh_luan b
            JOIN tai_lieu t 
              ON b.loai_noi_dung COLLATE utf8mb4_general_ci = t.loai_tai_lieu COLLATE utf8mb4_general_ci
            WHERE t.tai_khoan_id = :cg_id";
    if ($muc_con) {
        $sql .= " AND t.loai_tai_lieu = :muc_con";
    }
    $sql .= " ORDER BY b.ngay_gio DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':cg_id', $chuyen_gia_id);
    if ($muc_con) $stmt->bindValue(':muc_con', $muc_con);
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 🧾 Danh sách mục con (để đổ vào dropdown)
$muc_con_bai_viet = [
  "kien_thuc_ki_nang" => "Kiến thức – Kĩ năng",
  "tieng_noi_cua_tre" => "Tiếng nói của trẻ",
  "chia_se" => "Chia sẻ cộng đồng",
  "sang_kien_cho_tre" => "Sáng kiến cho trẻ",
  "tin_tuc_su_kien" => "Tin tức – Sự kiện"
];

$muc_con_tai_lieu = [
  "quyen_tre_em" => "Quyền trẻ em",
  "chong_bao_hanh" => "Chống bạo hành",
  "giao_duc_an_toan" => "Giáo dục an toàn"
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Bình luận người dùng</title>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/sidebar-expert.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/style_binhluan.css?v=<?php echo time(); ?>">
   <?php include '../partials/navbar.php'; ?>
</head>
<body>
  <div class="comment-container">
  
    <h2>💬 Bình luận từ người dùng</h2>

  <div class="filter-bar">
  <form method="get">
    <div class="filter-group">
      <label for="type">Loại nội dung:</label>
      <select id="type" name="type" onchange="this.form.submit()">
        <option value="bai_viet" <?= $loai_noi_dung === 'bai_viet' ? 'selected' : '' ?>>Bài viết</option>
        <option value="tai_lieu" <?= $loai_noi_dung === 'tai_lieu' ? 'selected' : '' ?>>Tài liệu</option>
      </select>

      <label for="muc_con">Mục con:</label>
      <select id="muc_con" name="muc_con" onchange="this.form.submit()">
        <option value="">-- Tất cả --</option>
        <?php
        $mucs = ($loai_noi_dung === 'bai_viet') ? $muc_con_bai_viet : $muc_con_tai_lieu;
        foreach ($mucs as $key => $label) {
            $sel = ($muc_con === $key) ? 'selected' : '';
            echo "<option value='$key' $sel>$label</option>";
        }
        ?>
      </select>
    </div>
  </form>
</div>


    <table class="comment-table">
      <tr>
        <th>Loại</th>
        <th>Tiêu đề</th>
        <th>Người bình luận</th>
        <th>Email</th>
        <th>Nội dung</th>
        <th>Thời gian</th>
      </tr>

      <?php if (count($comments) > 0): ?>
        <?php foreach ($comments as $c): ?>
          <tr>
            <td><?= htmlspecialchars($c['loai']) ?></td>
            <td><?= htmlspecialchars($c['tieu_de']) ?></td>
            <td><?= htmlspecialchars($c['ten']) ?></td>
            <td><?= htmlspecialchars($c['email']) ?></td>
            <td><?= nl2br(htmlspecialchars($c['noi_dung'])) ?></td>
            <td><?= htmlspecialchars($c['ngay_gio']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="6" style="text-align:center;">Không có bình luận nào.</td></tr>
      <?php endif; ?>
    </table>
  </div>
    <script src="../js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
