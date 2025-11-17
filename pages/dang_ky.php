<?php
require_once __DIR__ . '/../config.php';

$db = new Database();
$conn = $db->connect();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten_dang_nhap = trim($_POST['ten_dang_nhap']);
    $mat_khau = password_hash($_POST['mat_khau'], PASSWORD_DEFAULT);
    $email = trim($_POST['email']);
    $ho_ten = trim($_POST['ho_ten']);
    $ngay_sinh = !empty($_POST['ngay_sinh']) ? $_POST['ngay_sinh'] : null;
    $dia_chi = trim($_POST['dia_chi']);
    $so_dien_thoai = trim($_POST['so_dien_thoai']);

    // Mặc định vai_tro_id = 1 (Người dùng)
    $vai_tro_id = 1;
    $xac_thuc = 0;
    $trang_thai = "Hoạt động";

    // Kiểm tra trùng tên đăng nhập hoặc email
    $check = $conn->prepare("SELECT id FROM tai_khoan WHERE ten_dang_nhap = ? OR email = ?");
    $check->execute([$ten_dang_nhap, $email]);

    if ($check->rowCount() > 0) {
        $error = "Tên đăng nhập hoặc email đã tồn tại!";
    } else {
        $stmt = $conn->prepare("INSERT INTO tai_khoan 
            (ten_dang_nhap, mat_khau, email, ho_ten, ngay_sinh, dia_chi, so_dien_thoai, vai_tro_id, xac_thuc, trang_thai, ngay_tao)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

        $stmt->execute([$ten_dang_nhap, $mat_khau, $email, $ho_ten, $ngay_sinh, $dia_chi, $so_dien_thoai, $vai_tro_id, $xac_thuc, $trang_thai]);

        $success = "Đăng ký thành công! Hãy <a href='dang_nhap.php'>đăng nhập</a>.";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Đăng ký tài khoản</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../css/style_dangky.css">
</head>
<body>

<div class="register-box">
  <h2>Đăng ký tài khoản</h2>

  <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
  <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>

  <form method="POST">
    <div class="input-group">
      <i class="fa fa-user"></i>
      <input type="text" name="ten_dang_nhap" placeholder="Tên đăng nhập" required>
    </div>

    <div class="input-group">
      <i class="fa fa-lock"></i>
      <input type="password" name="mat_khau" placeholder="Mật khẩu" required>
    </div>

    <div class="input-group">
      <i class="fa fa-envelope"></i>
      <input type="email" name="email" placeholder="Email" required>
    </div>

    <div class="input-group">
      <i class="fa fa-id-card"></i>
      <input type="text" name="ho_ten" placeholder="Họ tên" required>
    </div>

    <div class="input-group">
      <i class="fa fa-calendar"></i>
      <input type="date" name="ngay_sinh" placeholder="Ngày sinh">
    </div>

    <div class="input-group">
      <i class="fa fa-map-marker-alt"></i>
      <input type="text" name="dia_chi" placeholder="Địa chỉ">
    </div>

    <div class="input-group">
      <i class="fa fa-phone"></i>
      <input type="text" name="so_dien_thoai" placeholder="Số điện thoại">
    </div>

    <div class="buttons">
      <button type="submit" class="btn-register"><i class="fa fa-user-plus"></i> Đăng ký</button>
      <button type="button" class="btn-home" onclick="window.location.href='../index.php'">
        <i class="fa fa-home"></i> Trang chủ
      </button>
    </div>
  </form>

  <div class="login-link">
    <p>Đã có tài khoản? <a href="dang_nhap.php">Đăng nhập ngay</a></p>
  </div>
</div>

</body>
</html>
