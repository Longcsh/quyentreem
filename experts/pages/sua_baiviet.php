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
$id = $_GET['id'] ?? null;

if (!$id) {
    die("<h3>Thiếu ID bài viết!</h3>");
}

$thongbao = "";

// ✅ Lấy thông tin bài viết
$stmt = $conn->prepare("SELECT * FROM bai_viet WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) {
    die("<h3>Bài viết không tồn tại!</h3>");
}

// ✅ Khi chuyên gia bấm cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tieu_de = trim($_POST['tieu_de']);
    $noi_dung = trim($_POST['noi_dung']);
    $loai_bai_viet = $_POST['loai_bai_viet'];
    $anh_dai_dien = $post['anh_dai_dien'] ?? null;
    $file_url = $post['file_url'] ?? null;

    // 🖼️ Thư mục lưu mới (trong admin12/assets/baiviet)
    $upload_dir = __DIR__ . '/../../admin12/assets/baiviet/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    // Ảnh mới
    if (!empty($_FILES['anh_dai_dien']['name'])) {
        $img_name = time() . "_" . basename($_FILES['anh_dai_dien']['name']);
        if (move_uploaded_file($_FILES['anh_dai_dien']['tmp_name'], $upload_dir . $img_name)) {
            if (!empty($post['anh_dai_dien']) && file_exists($upload_dir . $post['anh_dai_dien'])) {
                unlink($upload_dir . $post['anh_dai_dien']);
            }
            $anh_dai_dien = $img_name;
        }
    }

    // File đính kèm mới (nếu có)
    if (!empty($_FILES['file_url']['name'])) {
        $file_name = time() . "_" . basename($_FILES['file_url']['name']);
        if (move_uploaded_file($_FILES['file_url']['tmp_name'], $upload_dir . $file_name)) {
            if (!empty($post['file_url']) && file_exists($upload_dir . $post['file_url'])) {
                unlink($upload_dir . $post['file_url']);
            }
            $file_url = $file_name;
        }
    }

    // ✅ Cập nhật vào CSDL
    $sql = "UPDATE bai_viet 
            SET tieu_de = ?, noi_dung = ?, loai_bai_viet = ?, anh_dai_dien = ?, ngay_dang = NOW()
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$tieu_de, $noi_dung, $loai_bai_viet, $anh_dai_dien, $id]);

    // ✅ Chuyển về danh sách
    header("Location: danhsachbaiviet.php?success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title> Sửa bài viết</title>
  <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/sidebar-expert.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="../css/style_baiviet.css?v=<?php echo time(); ?>">
</head>
  
<body>
 <?php include '../partials/navbar.php'; ?> 
<div class="edit-container">
  <h2>✏️ Sửa bài viết</h2>

  <form method="POST" enctype="multipart/form-data" class="edit-form">
    <label>Tiêu đề:</label>
    <input type="text" name="tieu_de" value="<?= htmlspecialchars($post['tieu_de']) ?>" required>

    <label>Nội dung:</label>
    <textarea name="noi_dung" rows="7" required><?= htmlspecialchars($post['noi_dung']) ?></textarea>

    <label>Chuyên mục:</label>
    <select name="loai_bai_viet" required>
      <option value="">-- Chọn danh mục --</option>
      <option value="tin_tuc_su_kien" <?= $post['loai_bai_viet']=='tin_tuc_su_kien'?'selected':'' ?>>📰 Tin tức – Sự kiện</option>
      <option value="tieng_noi_cua_tre" <?= $post['loai_bai_viet']=='tieng_noi_cua_tre'?'selected':'' ?>>🎤 Tiếng nói của trẻ</option>
      <option value="chia_se" <?= $post['loai_bai_viet']=='chia_se'?'selected':'' ?>>🤝 Chia sẻ</option>
      <option value="kien_thuc_ki_nang" <?= $post['loai_bai_viet']=='kien_thuc_ki_nang'?'selected':'' ?>>📚 Kiến thức – Kĩ năng</option>
      <option value="sang_kien_cho_tre" <?= $post['loai_bai_viet']=='sang_kien_cho_tre'?'selected':'' ?>>💡 Sáng kiến cho trẻ</option>
    </select>

    <div class="file-section">
      <label>Ảnh minh họa hiện tại:</label>
      <?php if (!empty($post['anh_dai_dien'])): ?>
        <img src="../../admin12/assets/baiviet/<?= htmlspecialchars($post['anh_dai_dien']) ?>" class="preview-img">
      <?php endif; ?>
      <input type="file" name="anh_dai_dien" accept="image/*">
    </div>

    <button type="submit" class="btn-save">💾 Lưu thay đổi</button>
    <a href="danhsachbaiviet.php" class="btn-cancel">⬅ Hủy</a>
  </form>
</div>
  <script src="../js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
