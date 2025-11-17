<?php 
require_once __DIR__ . '/../../config.php';

// 🧭 Gán session chuyên gia nếu đã đăng nhập
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
$thongbao = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tieu_de = trim($_POST['tieu_de']);
    $mo_ta = trim($_POST['noi_dung']);
    $loai_tai_lieu = $_POST['danh_muc'] ?? '';
    $file_url = null;
    $duong_dan = strtolower(preg_replace('/\s+/', '-', $tieu_de));

    // 📎 Upload file đính kèm (bắt buộc)
    if (!empty($_FILES['tep_dinh_kem']['name'])) {
        $file = $_FILES['tep_dinh_kem'];
        $dir = "../../uploads/tailieu/";

        // Tạo thư mục nếu chưa có
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $file_ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $allowed_ext = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip'];

        if (!in_array($file_ext, $allowed_ext)) {
            $thongbao = "❌ Định dạng file không hợp lệ. Chỉ chấp nhận: PDF, DOC, DOCX, PPT, ZIP.";
        } else {
            $file_name = time() . "_" . basename($file["name"]);
            $file_path = $dir . $file_name;
            if (move_uploaded_file($file["tmp_name"], $file_path)) {
                $file_url = "uploads/tailieu/" . $file_name;
            } else {
                $thongbao = "❌ Không thể tải lên file.";
            }
        }
    }

    // ✅ Nếu file hợp lệ thì lưu DB
    if ($file_url) {
       $sql = "INSERT INTO tai_lieu 
        (tai_khoan_id, vai_tro_id, tieu_de, mo_ta, file_url, trang_thai, ngay_upload, loai_tai_lieu)
        VALUES (?, 2, ?, ?, ?, 'da_duyet', NOW(), ?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$chuyen_gia_id, $tieu_de, $mo_ta, $file_url, $loai_tai_lieu]);
        $thongbao = "🎉 Tài liệu đã được đăng thành công!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>📘 Đăng tài liệu</title>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/sidebar-expert.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/style_dangtailieu.css?v=<?php echo time(); ?>">
</head>

<body>
  <!-- Sidebar -->
  <?php include '../partials/navbar.php'; ?>

  <!-- Nội dung chính -->
  <main class="main-content">
    <div class="upload-container">
      <h2>📘 Đăng tài liệu</h2>

      <?php if (!empty($thongbao)): ?>
        <div class="alert success"><?= htmlspecialchars($thongbao) ?></div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="upload-form">
        <div class="form-group">
          <label for="tieu_de">Tiêu đề tài liệu:</label>
          <input type="text" name="tieu_de" id="tieu_de" required>
        </div>

        <div class="form-group">
          <label for="noi_dung">Mô tả ngắn:</label>
          <textarea name="noi_dung" id="noi_dung" rows="4" required></textarea>
        </div>

        <div class="form-group">
          <label for="danh_muc">Chọn danh mục:</label>
          <select name="danh_muc" id="danh_muc" required>
            <option value="">-- Chọn danh mục --</option>
            <option value="quyen_tre_em">📘 Quyền trẻ em</option>
            <option value="chong_bao_hanh">🛡️ Chống bạo hành</option>
            <option value="giao_duc_an_toan">🎓 Giáo dục an toàn</option>
          </select>
        </div>

        <div class="form-group">
          <label for="tep_dinh_kem">File đính kèm (PDF, DOC, ZIP,...):</label>
          <input type="file" name="tep_dinh_kem" id="tep_dinh_kem" accept=".pdf,.doc,.docx,.ppt,.pptx,.zip" required>
        </div>

        <button type="submit" class="btn-submit">Đăng tài liệu</button>
      </form>
    </div>
  </main>
  
  <script src="../js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
