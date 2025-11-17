<?php
require_once __DIR__ . '/../config.php';


$db = new Database();
$conn = $db->connect();

// Nếu chưa đăng nhập thì chuyển hướng
if (!isset($_SESSION['user_id'])) {
    header("Location: dang_nhap.php");
    exit();
}

$message = "";

// 🟢 Lấy danh sách chuyên môn
$stmt = $conn->prepare("SELECT id, ten_chuyen_mon FROM chuyen_mon ORDER BY ten_chuyen_mon ASC");
$stmt->execute();
$chuyen_mon_list = $stmt->fetchAll(PDO::FETCH_ASSOC);


// 🟢 Xử lý khi người dùng gửi form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $chuyen_mon_id = $_POST['chuyen_mon_id'];
    $ngay_gio = $_POST['ngay_gio'];
    $noi_dung = trim($_POST['noi_dung']);
    $nguoi_dung_id = $_SESSION['user_id'];

    // 1️⃣ Tìm chuyên gia cùng chuyên môn và rảnh trong khung giờ này
   $stmt = $conn->prepare("
    SELECT id, ho_ten
    FROM tai_khoan
    WHERE vai_tro_id = 2
      AND chuyen_mon_id = ?
      AND (xac_thuc = 1 OR xac_thuc = 0 OR xac_thuc IS NULL)
      AND (LOWER(trang_thai) LIKE '%hoat%' OR LOWER(trang_thai) LIKE '%active%' OR trang_thai IS NULL)
      AND id NOT IN (
          SELECT chuyen_gia_id
          FROM lich_hen
          WHERE chuyen_gia_id IS NOT NULL
            AND TIMESTAMPDIFF(MINUTE, ngay_gio, ?) BETWEEN -60 AND 60
            AND trang_thai IN ('da_xac_nhan', 'cho_xac_nhan')
      )
    ORDER BY RAND()
    LIMIT 1
");
$stmt->execute([$chuyen_mon_id, $ngay_gio]);
$chuyen_gia = $stmt->fetch(PDO::FETCH_ASSOC);


$stmt->execute([$chuyen_mon_id, $ngay_gio]);

    $stmt->execute([$chuyen_mon_id, $ngay_gio]);
    $chuyen_gia = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2️⃣ Nếu có chuyên gia rảnh → gán tự động
    if ($chuyen_gia) {
        $chuyen_gia_id = $chuyen_gia['id'];
        $stmt = $conn->prepare("
            INSERT INTO lich_hen 
            (nguoi_dung_id, chuyen_gia_id, chuyen_mon_id, ngay_gio, noi_dung, trang_thai, ngay_dat)
            VALUES (?, ?, ?, ?, ?, 'cho_xac_nhan', NOW())
        ");
        $stmt->execute([$nguoi_dung_id, $chuyen_gia_id, $chuyen_mon_id, $ngay_gio, $noi_dung]);

    require_once __DIR__ . '/../admin12/BE/them_thong_bao.php';
    $noi_dung_tb = "Người dùng {$_SESSION['ho_ten']} đã đặt lịch tư vấn vào {$ngay_gio}.";
    guiThongBao($chuyen_gia_id, 'lich_hen', 'Có lịch hẹn mới', $noi_dung_tb);
    
        $message = "✅ Lịch hẹn của bạn đã được gửi, chờ chuyên gia xác nhận.";
    }
  //3️⃣ Nếu không có chuyên gia rảnh → báo luôn cho người dùng, không cần admin
else {
    $message = "⚠️ Hiện không có chuyên gia rảnh trong chuyên môn này. 
    Vui lòng chọn thời gian khác hoặc chuyên môn khác để đặt lịch.";
}

}


// 🟢 Lấy lịch hẹn gần đây của user
$stmt = $conn->prepare("
    SELECT lh.*, cg.ho_ten AS ten_chuyen_gia, cm.ten_chuyen_mon
    FROM lich_hen lh
    LEFT JOIN tai_khoan cg ON lh.chuyen_gia_id = cg.id
    LEFT JOIN chuyen_mon cm ON lh.chuyen_mon_id = cm.id
    WHERE lh.nguoi_dung_id = ?
    ORDER BY lh.ngay_dat DESC LIMIT 5
");
$stmt->execute([$_SESSION['user_id']]);
$lich_hen = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Đặt lịch hẹn</title>
  <link rel="stylesheet" href="../css/datlich.css">
</head>
<body>

<section class="datlich">
  <h2>📅 Đặt lịch hẹn với chuyên gia</h2>

  <?php if (!empty($message)): ?>
    <p class="message"><?php echo $message; ?></p>
  <?php endif; ?>

  <!-- FORM ĐẶT LỊCH -->
  <form method="post" class="form-datlich">
    <label for="chuyen_mon_id">🎓 Chọn chuyên môn:</label>
    <select name="chuyen_mon_id" id="chuyen_mon_id" required>
      <option value="">-- Chọn chuyên môn --</option>
      <?php foreach ($chuyen_mon_list as $cm): ?>
        <option value="<?php echo $cm['id']; ?>">
          <?php echo htmlspecialchars($cm['ten_chuyen_mon']); ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label for="ngay_gio">🕓 Ngày giờ hẹn:</label>
    <input type="datetime-local" name="ngay_gio" id="ngay_gio" required>

    <label for="noi_dung">💬 Nội dung:</label>
    <textarea name="noi_dung" id="noi_dung" placeholder="Nhập nội dung cuộc hẹn..." required></textarea>

    <button type="submit">📌 Đặt lịch</button>
  </form>

  <hr>
  <h3>📖 Lịch hẹn gần đây</h3>
  <?php if ($lich_hen): ?>
    <ul class="lichhen-list">
      <?php foreach ($lich_hen as $lh): ?>
        <li class="lichhen-item">
          <strong>🎓 Chuyên môn:</strong> <?php echo htmlspecialchars($lh['ten_chuyen_mon']); ?><br>
          <strong>👩‍⚕️ Chuyên gia:</strong> <?php echo htmlspecialchars($lh['ten_chuyen_gia'] ?? 'Chưa phân công'); ?><br>
          <strong>🕓 Thời gian:</strong> <?php echo htmlspecialchars($lh['ngay_gio']); ?><br>
          <strong>💬 Nội dung:</strong> <?php echo htmlspecialchars($lh['noi_dung']); ?><br>
          <strong>📌 Trạng thái:</strong> <?php echo htmlspecialchars($lh['trang_thai']); ?><br>
          <small>📅 Đặt lúc: <?php echo htmlspecialchars($lh['ngay_dat']); ?></small>
        </li>
        <hr>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p class="empty">⏳ Bạn chưa có lịch hẹn nào.</p>
  <?php endif; ?>
</section>

</body>
</html>
