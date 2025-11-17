<?php
require_once __DIR__ . '/../../config.php';

if (!isset($_SESSION['expert_id']) || ($_SESSION['role_id'] ?? 0) != 2) {
  header('Location: ../../pages/dang_nhap.php');
  exit;
}

$db = new Database();
$conn = $db->connect();

$chuyen_gia_id = $_SESSION['expert_id'];
$id = $_GET['id'] ?? null;

if (!$id) {
  echo "<script>alert('Thiếu ID câu hỏi!'); window.location='danhsachcauhoi.php';</script>";
  exit;
}

// ✅ Gửi phản hồi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tra_loi = trim($_POST['tra_loi'] ?? '');
  if ($tra_loi !== '') {
    $stmt = $conn->prepare("
      UPDATE tu_van
      SET tra_loi = ?, trang_thai = 'da_tra_loi', ngay_tra_loi = NOW()
      WHERE id = ? AND chuyen_gia_id = ?
    ");
    $stmt->execute([$tra_loi, $id, $chuyen_gia_id]);
    echo "<script>alert('✅ Đã gửi phản hồi thành công!'); window.location='danhsachcauhoi.php';</script>";
    exit;
  } else {
    echo "<script>alert('❌ Vui lòng nhập nội dung trả lời.');</script>";
  }
}

// ✅ Lấy câu hỏi
$stmt = $conn->prepare("
  SELECT t.*, u.ho_ten AS ten_user, cm.ten_chuyen_mon
  FROM tu_van t
  JOIN tai_khoan u ON t.nguoi_dung_id = u.id
  JOIN chuyen_mon cm ON t.chuyen_mon_id = cm.id
  WHERE t.id = ? AND t.chuyen_gia_id = ?
");
$stmt->execute([$id, $chuyen_gia_id]);
$cauhoi = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cauhoi) {
  echo "<script>alert('Không tìm thấy câu hỏi này hoặc không thuộc quyền của bạn!'); window.location='danhsachcauhoi.php';</script>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Trả lời câu hỏi</title>
  <!-- Font chữ Google -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/sidebar-expert.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/style_traloi.css?v=<?php echo time(); ?>">
</head>
<body>
  <div class="layout">

 <!-- Sidebar -->
  <?php include '../partials/navbar.php'; ?>
    <!-- Nội dung chính -->
    <div class="main-content">
      <div class="answer-container">
        <h2>💬 Trả lời câu hỏi của 
          <span style="color:#3498db;">
            <?= htmlspecialchars($cauhoi['ten_user']) ?>
          </span>
        </h2>

        <p class="chuyenmon">📚 Chuyên môn: <?= htmlspecialchars($cauhoi['ten_chuyen_mon']) ?></p>

        <div class="question-box">
          <strong>💡 Câu hỏi:</strong>
          <p><?= nl2br(htmlspecialchars($cauhoi['cau_hoi'])) ?></p>
        </div>

        <?php if (!empty($cauhoi['anh_minh_hoa'])): ?>
          <div class="image-box">
            <img src="../../<?= htmlspecialchars($cauhoi['anh_minh_hoa']) ?>" alt="Ảnh minh họa">
          </div>
        <?php endif; ?>

        <form method="post" class="answer-form">
          <label for="tra_loi">🪄 Nhập câu trả lời của bạn:</label>
          <textarea name="tra_loi" id="tra_loi" rows="6" placeholder="Nhập nội dung trả lời chi tiết..." required></textarea>
          <button type="submit">📤 Gửi phản hồi</button>
          <a href="danhsachcauhoi.php" class="back-btn"> Quay lại</a>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
