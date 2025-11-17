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
$thongbao = "";

// ✅ Khi chuyên gia bấm Đăng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tieu_de = trim($_POST['tieu_de']);
    $noi_dung = trim($_POST['noi_dung']);
    $loai_bai_viet = $_POST['loai_bai_viet'];
    $hinh_anh = null;

    // 🖼️ Upload ảnh minh họa — ĐƯA ẢNH VÀO admin12/assets/baiviet/
    $upload_dir = __DIR__ . '/../../admin12/assets/baiviet/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    if (!empty($_FILES['hinh_anh']['name'])) {
        $img = $_FILES['hinh_anh'];
        $img_name = time() . "_" . basename($img['name']);

        if (move_uploaded_file($img['tmp_name'], $upload_dir . $img_name)) {
            // Lưu vào DB chỉ tên file
            $hinh_anh = $img_name;
        }
    }

    try {
        // ✅ Lưu vào bảng bai_viet
        $sql = "INSERT INTO bai_viet 
                (tai_khoan_id, tieu_de, noi_dung, anh_dai_dien, loai_bai_viet, ngay_dang, trang_thai)
                VALUES (?, ?, ?, ?, ?, NOW(), 'da_duyet')";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$chuyen_gia_id, $tieu_de, $noi_dung, $hinh_anh, $loai_bai_viet]);

        $thongbao = "🎉 Đăng bài thành công! Ảnh đã được lưu trong admin12/assets/baiviet/";
    } catch (PDOException $e) {
        $thongbao = "❌ Lỗi khi đăng bài: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng bài viết chuyên mục</title>
  <link rel="stylesheet" href="../css/style_dangbaiviet.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/sidebar-expert.css?v=<?php echo time(); ?>">
  <?php include '../partials/navbar.php'; ?>
</head>
<body>
  <div class="main-wrapper">
    <div class="main-content">
      <div class="dangbai-container">
        <h2>📰 Đăng bài viết chuyên mục</h2>

        <?php if ($thongbao): ?>
          <p class="msg"><?= htmlspecialchars($thongbao) ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="dangbai-form">
          <label>Tiêu đề bài viết:</label>
          <input type="text" name="tieu_de" required>

          <label>Nội dung bài chia sẻ:</label>
          <textarea name="noi_dung" rows="6" required></textarea>

          <label>Đăng bài vào mục:</label>
          <select name="loai_bai_viet" required>
            <option value="">-- Chọn danh mục --</option>
            <option value="tin_tuc_su_kien">📰 Tin tức – Sự kiện</option>
            <option value="tieng_noi_cua_tre">🎤 Tiếng nói của trẻ</option>
            <option value="chia_se">🤝 Chia sẻ từ cộng đồng</option>
            <option value="kien_thuc_ki_nang">📚 Kiến thức – Kĩ năng</option>
            <option value="sang_kien_cho_tre">💡 Sáng kiến cho trẻ</option>
          </select>

          <label>Ảnh minh họa (tùy chọn):</label>
          <input type="file" name="hinh_anh" accept="image/*">

          <button type="submit" class="btn-submit">Đăng bài</button>
        </form>
      </div>
    </div>
  </div>
  <script src="../js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
