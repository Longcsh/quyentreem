<?php

require_once __DIR__ . '/../config.php';


// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) || $_SESSION['vai_tro_id'] != 1) {
    header("Location: dang_nhap.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

$user_id = $_SESSION['user_id'];

// Lấy danh sách câu hỏi của người dùng
$stmt = $conn->prepare("
    SELECT * FROM tu_van 
    WHERE nguoi_dung_id = ? 
    ORDER BY ngay_gui DESC
");
$stmt->execute([$user_id]);
$cauhoi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title> Trả lời của tôi</title>
  <link rel="stylesheet" href="../css/traloi.css">
</head>
<body>

<section class="traloi-section">
  <h2>📩 Trả lời của tôi</h2>

  <?php if ($cauhoi): ?>
    <ul class="traloi-list">
      <?php foreach ($cauhoi as $item): ?>
        <li class="traloi-item">
          <p class="question">
            <strong>❓ Hỏi:</strong> <?php echo htmlspecialchars($item['cau_hoi']); ?>
          </p>
          <em class="date">Ngày gửi: <?php echo htmlspecialchars($item['ngay_gui']); ?></em><br>

          <?php if ($item['trang_thai'] == 'da_tra_loi'): ?>
            <div class="answer-box">
              <p><strong>✅ Trả lời:</strong> <?php echo nl2br(htmlspecialchars($item['tra_loi'])); ?></p>
              <?php if (!empty($item['ngay_tra_loi'])): ?>
                <em>Ngày trả lời: <?php echo htmlspecialchars($item['ngay_tra_loi']); ?></em>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <p class="pending"><strong>⌛ Trạng thái:</strong> Chờ chuyên gia phản hồi...</p>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p class="empty">Bạn chưa gửi câu hỏi nào.</p>
  <?php endif; ?>
</section>

</body>
</html>
