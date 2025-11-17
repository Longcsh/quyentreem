<?php

require_once __DIR__ . '/../config.php'; 

$db = new Database();
$conn = $db->connect();

// Nếu chưa đăng nhập thì chuyển hướng
if (!isset($_SESSION['user_id'])) {
    header("Location: dang_nhap.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Cập nhật hồ sơ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $ho_ten = trim($_POST['ho_ten']);
    $email = trim($_POST['email']);
    $ngay_sinh = $_POST['ngay_sinh'] ?: null;
    $dia_chi = trim($_POST['dia_chi']);
    $so_dien_thoai = trim($_POST['so_dien_thoai']);

    $sql_update = "UPDATE tai_khoan 
                   SET ho_ten=?, email=?, ngay_sinh=?, dia_chi=?, so_dien_thoai=? 
                   WHERE id=?";
    $stmt = $conn->prepare($sql_update);
    $stmt->execute([$ho_ten, $email, $ngay_sinh, $dia_chi, $so_dien_thoai, $user_id]);

       // 🔁 Tránh gửi lại form khi reload
    echo "<script>window.location.href='" . $_SERVER['REQUEST_URI'] . "';</script>";
exit;
}

// ✅ Lấy thông tin người dùng
$sql = "SELECT ten_dang_nhap, ho_ten, email, ngay_sinh, dia_chi, so_dien_thoai, vai_tro_id, ngay_tao 
        FROM tai_khoan WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$roles = [
    1 => "Người dùng",
    2 => "Chuyên gia",
    3 => "Quản trị viên"
];
?>

<section class="hoso-container">
  <h2>📋 Hồ sơ cá nhân</h2>

  <?php if (isset($_GET['success'])): ?>
      <p class="alert success">✅ Cập nhật thông tin thành công!</p>
  <?php endif; ?>

  <?php if ($user): ?>
    <?php if (!isset($_GET['edit'])): ?>
      <!-- Chế độ xem -->
      <div class="hoso-view">
        <p><strong>Tên đăng nhập:</strong> <?= htmlspecialchars($user['ten_dang_nhap']) ?></p>
        <p><strong>Họ tên:</strong> <?= htmlspecialchars($user['ho_ten']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Ngày sinh:</strong> <?= htmlspecialchars($user['ngay_sinh']) ?></p>
        <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($user['dia_chi']) ?></p>
        <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($user['so_dien_thoai']) ?></p>
        <p><strong>Vai trò:</strong> <?= htmlspecialchars($roles[$user['vai_tro_id']] ?? "Không xác định") ?></p>
        <p><strong>Ngày tạo:</strong> <?= htmlspecialchars($user['ngay_tao']) ?></p>
      </div>

      <a href="?page=hoso&edit=1" class="btn-primary">✏️ Sửa hồ sơ</a>

    <?php else: ?>
      <!-- Chế độ chỉnh sửa -->
      <form method="post" class="hoso-form">
        <label>Họ tên:</label>
        <input type="text" name="ho_ten" value="<?= htmlspecialchars($user['ho_ten']) ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>Ngày sinh:</label>
        <input type="date" name="ngay_sinh" value="<?= htmlspecialchars($user['ngay_sinh']) ?>">

        <label>Địa chỉ:</label>
        <input type="text" name="dia_chi" value="<?= htmlspecialchars($user['dia_chi']) ?>">

        <label>Số điện thoại:</label>
        <input type="text" name="so_dien_thoai" value="<?= htmlspecialchars($user['so_dien_thoai']) ?>">

        <div class="hoso-buttons">
          <button type="submit" name="update" class="btn-save">💾 Cập nhật</button>
          <a href="?page=hoso" class="btn-cancel">❌ Hủy</a>
        </div>
      </form>
    <?php endif; ?>
  <?php else: ?>
    <p class="alert error">❌ Không tìm thấy thông tin người dùng.</p>
  <?php endif; ?>
</section>

