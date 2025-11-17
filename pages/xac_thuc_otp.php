<?php
require_once '../config.php';
$conn = $db->connect();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_GET['email'] ?? '';
    $otp = trim($_POST['otp']);
    $new_pass = trim($_POST['mat_khau']);

    if ($email && $otp && $new_pass) {
        $stmt = $conn->prepare("SELECT otp_code, otp_expire FROM tai_khoan WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if ($user['otp_code'] === $otp && strtotime($user['otp_expire']) > time()) {
                $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE tai_khoan 
                                        SET mat_khau = ?, otp_code = NULL, otp_expire = NULL 
                                        WHERE email = ?");
                $stmt->execute([$hashed, $email]);

                // ✅ Thông báo + tự động quay lại đăng nhập
                $message = "
                <p style='color:green'>✅ Đổi mật khẩu thành công!</p>
                <p>Hệ thống sẽ tự chuyển bạn về trang đăng nhập sau 3 giây...</p>
                <meta http-equiv='refresh' content='3;url=../pages/dang_nhap.php'>
                <a href='../pages/dang_nhap.php' 
                   style='color:#fff;background:#007bff;padding:8px 16px;border-radius:6px;text-decoration:none'>
                   Đăng nhập ngay
                </a>";
            } else {
                $message = "<p style='color:red'>❌ Mã OTP không hợp lệ hoặc đã hết hạn.</p>";
            }
        } else {
            $message = "<p style='color:red'>❌ Không tìm thấy email này.</p>";
        }
    } else {
        $message = "<p style='color:red'>⚠️ Vui lòng nhập đầy đủ thông tin.</p>";
    }
}
?>

<h2>🔑 Xác thực OTP</h2>
<form method="post">
    <label>Nhập mã OTP:</label>
    <input type="text" name="otp" required>

    <label>Mật khẩu mới:</label>
    <input type="password" name="mat_khau" required>

    <button type="submit">Xác nhận đổi mật khẩu</button>
</form>

<?= $message ?>
