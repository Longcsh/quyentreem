<?php
session_start();
require_once __DIR__ . '/../../config.php';

if (isset($_SESSION['user_id'], $_SESSION['vai_tro_id']) && $_SESSION['vai_tro_id'] == 2) {
    if (!isset($_SESSION['expert_id'])) {
        $_SESSION['expert_id'] = $_SESSION['user_id'];
        $_SESSION['expert_name'] = $_SESSION['ho_ten'] ?? $_SESSION['ten_dang_nhap'];
        $_SESSION['role_id'] = 2;
    }
}
// ✅ Kiểm tra đăng nhập chuyên gia
if (!isset($_SESSION['expert_id'])) {
    header('Location: ../../pages/dang_nhap.php');
    exit;
}

$chuyen_gia_id = $_SESSION['expert_id'];

// 🔹 Lấy thông tin chuyên gia
$stmt = $conn->prepare("
    SELECT ho_ten, email, ngay_sinh, dia_chi, so_dien_thoai, chuyen_mon_id
    FROM tai_khoan 
    WHERE id = ?
");
$stmt->execute([$chuyen_gia_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
$ho_ten = $profile['ho_ten'] ?? 'Chuyên gia';

// 🔹 Lấy danh sách chuyên môn
$stmtCM = $conn->prepare("SELECT id, ten_chuyen_mon FROM chuyen_mon ORDER BY ten_chuyen_mon ASC");
$stmtCM->execute();
$ds_chuyen_mon = $stmtCM->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Hồ sơ cá nhân</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- ✅ CSS layout chung cho giao diện chuyên gia -->
<link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="../css/sidebar-expert.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="../css/home-expert.css?v=<?php echo time(); ?>">
<!-- ✅ CSS riêng cho trang hồ sơ -->
<link rel="stylesheet" href="../css/style_canhan.css?v=<?php echo time(); ?>">
</head>

<body>
  <!-- ✅ Sidebar chuyên gia -->
  <?php include '../partials/navbar.php'; ?>

  <!-- ✅ Nội dung chính -->
  <div class="main-content">
    <section class="profile-card">
      <h2>👋 Xin chào, <?= htmlspecialchars($ho_ten) ?></h2>
      <div id="message" class="message"></div>

      <form id="profileForm" method="POST">
        <div class="form-group">
          <label>Họ và tên:</label>
          <input type="text" name="ho_ten" value="<?= htmlspecialchars($profile['ho_ten'] ?? '') ?>" readonly>
        </div>
        <div class="form-group">
          <label>Email:</label>
          <input type="email" name="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label>Ngày sinh:</label>
          <input type="date" name="ngay_sinh" value="<?= htmlspecialchars($profile['ngay_sinh'] ?? '') ?>" readonly>
        </div>
        <div class="form-group">
          <label>Số điện thoại:</label>
          <input type="tel" name="so_dien_thoai" value="<?= htmlspecialchars($profile['so_dien_thoai'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Địa chỉ:</label>
          <input type="text" name="dia_chi" value="<?= htmlspecialchars($profile['dia_chi'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Chuyên môn:</label>
          <select name="chuyen_mon_id" disabled>
            <option value="">-- Chọn chuyên môn --</option>
            <?php foreach ($ds_chuyen_mon as $cm): ?>
              <option value="<?= $cm['id'] ?>" <?= ($profile['chuyen_mon_id'] == $cm['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cm['ten_chuyen_mon']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit" class="btn-save">💾 Lưu thông tin</button>
      </form>
    </section>
  </div>
  <!-- Script dropdown của sidebar -->
<script src="../js/main.js?v=<?php echo time(); ?>"></script>

</body>
</html>

<?php
// =======================
// XỬ LÝ CẬP NHẬT HỒ SƠ
// =======================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $so_dien_thoai = trim($_POST['so_dien_thoai']);
    $dia_chi = trim($_POST['dia_chi']);

    try {
        $stmt = $conn->prepare("
            UPDATE tai_khoan 
            SET email = ?, so_dien_thoai = ?, dia_chi = ?
            WHERE id = ?
        ");
        $stmt->execute([$email, $so_dien_thoai, $dia_chi, $chuyen_gia_id]);

        echo "<script>
            document.getElementById('message').className = 'message success';
            document.getElementById('message').textContent = '✅ Cập nhật thành công!';
        </script>";
    } catch (PDOException $e) {
        echo "<script>
            document.getElementById('message').className = 'message error';
            document.getElementById('message').textContent = '❌ Lỗi: ".addslashes($e->getMessage())."';
        </script>";
    }
}
?>
