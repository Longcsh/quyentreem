<?php
require_once __DIR__ . '/../config.php';


$db = new Database();
$conn = $db->connect();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten_dang_nhap = trim($_POST['ten_dang_nhap']);
    $mat_khau = trim($_POST['mat_khau']);
    $vai_tro = (int)$_POST['vai_tro'];

    $stmt = $conn->prepare("SELECT * FROM tai_khoan WHERE ten_dang_nhap = ?");
    $stmt->execute([$ten_dang_nhap]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // 🔒 Kiểm tra trạng thái tài khoản (ngoại trừ admin)
        if ($user['vai_tro_id'] != 3 && isset($user['trang_thai']) && mb_strtolower($user['trang_thai'], 'UTF-8') !== 'hoạt động') {
            $error = "Tài khoản của bạn đã bị khóa hoặc tạm dừng. Vui lòng liên hệ quản trị viên.";

        // ⚙️ Kiểm tra vai trò khớp với loại tài khoản
        } elseif ($user['vai_tro_id'] != $vai_tro) {
            $error = "Vai trò không khớp với tài khoản. Vui lòng chọn đúng loại tài khoản.";

        // ✅ Kiểm tra mật khẩu
        } elseif (password_verify($mat_khau, $user['mat_khau'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['vai_tro_id'] = $user['vai_tro_id'];
            $_SESSION['ho_ten'] = $user['ho_ten'];
            $_SESSION['ten_dang_nhap'] = $user['ten_dang_nhap'];

            // 🎯 Điều hướng theo vai trò
            if ($user['vai_tro_id'] == 1) {
                header("Location: ../user.php");
            } elseif ($user['vai_tro_id'] == 2) {
                header("Location: ../chuyengia.php");
            } elseif ($user['vai_tro_id'] == 3) {
                header("Location: ../admin.php");
            }
            exit();
        } else {
            $error = "Sai tài khoản hoặc mật khẩu!";
        }
    } else {
        $error = "Tài khoản không tồn tại!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng nhập tài khoản</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style_dangnhap.css">
</head>
<body>

<div class="login-box">
  <h2>Đăng nhập tài khoản</h2>
  <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

  <form method="POST">
    <div>
      <select name="vai_tro" required>
        <option value="1">Người dùng</option>
        <option value="2">Chuyên gia</option>
        <option value="3">Quản trị viên</option>
      </select>
    </div>

    <div class="input-group">
      <i class="fa fa-user"></i>
      <input type="text" name="ten_dang_nhap" placeholder="Tên đăng nhập" required>
    </div>

    <div class="input-group">
      <i class="fa fa-lock"></i>
      <input type="password" name="mat_khau" placeholder="Mật khẩu" required>
    </div>

    <div class="forgot">
      <a href="quen_mat_khau.php">[ Quên mật khẩu người dùng ]</a>
    </div>

    <div class="buttons">
      <button type="submit" class="btn-login"><i class="fa fa-sign-in-alt"></i> Đăng nhập</button>
      <button type="button" class="btn-home" onclick="window.location.href='../index.php'">
        <i class="fa fa-home"></i> Trang chủ
      </button>
    </div>
  </form>

  <div class="login-link">
    <p>Bạn chưa có tài khoản? <a href="dang_ky.php">Đăng ký ngay</a></p>
  </div>
</div>

</body>
</html>
