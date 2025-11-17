<?php
require_once __DIR__ . '/../../config.php';

if (isset($_SESSION['user_id'], $_SESSION['vai_tro_id']) && $_SESSION['vai_tro_id'] == 2) {
    if (!isset($_SESSION['expert_id'])) {
        $_SESSION['expert_id'] = $_SESSION['user_id'];
        $_SESSION['expert_name'] = $_SESSION['ho_ten'] ?? $_SESSION['ten_dang_nhap'];
        $_SESSION['role_id'] = 2;
    }
}

// 🧩 Kiểm tra đăng nhập
if (!isset($_SESSION['expert_id'])) { 
  header('Location: ../../pages/dang_nhap.php'); 
  exit; 
}

$conn = (new Database())->connect();
$id = $_GET['id'] ?? 0;
$expert = $_SESSION['expert_id'];

// 🔹 Lấy dữ liệu cũ
$stmt = $conn->prepare("SELECT * FROM tai_lieu WHERE id=? AND tai_khoan_id=?");
$stmt->execute([$id, $expert]);
$doc = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$doc) die("❌ Không tìm thấy tài liệu hoặc bạn không có quyền sửa.");

// 🔹 Khi gửi form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tieu_de = trim($_POST['tieu_de']);
  $mo_ta = trim($_POST['mo_ta']);
  $loai = $_POST['loai_tai_lieu'];

  $upload_dir = "../../uploads/tailieu/";
  if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

  // Giữ lại file cũ nếu không upload mới
  $file_url = $doc['file_url'];

  // 📎 File mới
  if (!empty($_FILES['file_url']['name'])) {
    $file = $_FILES['file_url'];
    $file_name = time() . "_" . basename($file['name']);
    if (move_uploaded_file($file['tmp_name'], $upload_dir . $file_name)) {
      $file_url = "uploads/tailieu/" . $file_name;
    }
  }

  // 🔄 Cập nhật DB
  $sql = "UPDATE tai_lieu 
          SET tieu_de=?, mo_ta=?, loai_tai_lieu=?, file_url=? 
          WHERE id=? AND tai_khoan_id=?";
  $stmt = $conn->prepare($sql);
  $stmt->execute([$tieu_de, $mo_ta, $loai, $file_url, $id, $expert]);

  header("Location: danhsach_tailieu.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Sửa tài liệu</title>
  <link rel="stylesheet" href="../css/sidebar-expert.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/style_suatailieu.css?v=<?php echo time(); ?>">
</head>
<body>
  <?php include '../partials/navbar.php'; ?>

  <div class="main-content">
    <div class="edit-container">
      <h2>📘 Sửa tài liệu</h2>

      <form method="POST" enctype="multipart/form-data" class="edit-form">
        <label>Tiêu đề tài liệu:</label>
        <input type="text" name="tieu_de" value="<?= htmlspecialchars($doc['tieu_de']) ?>" required>

        <label>Mô tả ngắn:</label>
        <textarea name="mo_ta" rows="5" required><?= htmlspecialchars($doc['mo_ta']) ?></textarea>

        <label>Chọn danh mục:</label>
        <select name="loai_tai_lieu" required>
          <option value="">-- Chọn danh mục --</option>
          <option value="quyen_tre_em" <?= $doc['loai_tai_lieu']=='quyen_tre_em'?'selected':'' ?>>📘 Quyền trẻ em</option>
          <option value="chong_bao_hanh" <?= $doc['loai_tai_lieu']=='chong_bao_hanh'?'selected':'' ?>>🚫 Chống bạo hành</option>
          <option value="giao_duc_an_toan" <?= $doc['loai_tai_lieu']=='giao_duc_an_toan'?'selected':'' ?>>🏫 Giáo dục an toàn</option>
        </select>

        <label>File đính kèm (PDF, DOC, v.v):</label>
        <?php if (!empty($doc['file_url'])): ?>
          <a href="../../<?= htmlspecialchars($doc['file_url']) ?>" target="_blank" class="download-link">📎 Xem file hiện tại</a>
        <?php endif; ?>
        <input type="file" name="file_url" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt">

        <div class="btn-group">
          <button type="submit" class="btn-save">💾 Lưu thay đổi</button>
          <a href="danhsach_tailieu.php" class="btn-cancel">⬅ Hủy</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
