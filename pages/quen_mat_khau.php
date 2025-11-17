<?php
require_once __DIR__ . '/../config.php';

$db = new Database();
$conn = $db->connect();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $stmt = $conn->prepare("SELECT * FROM tai_khoan WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Tạo mã OTP ngẫu nhiên 6 chữ số
        $otp = rand(100000, 999999);
        $expire = date('Y-m-d H:i:s', time() + 300); // hết hạn sau 5 phút

        // Lưu vào DB
        $conn->prepare("UPDATE tai_khoan SET otp_code=?, otp_expire=? WHERE email=?")
             ->execute([$otp, $expire, $email]);

        // Thông báo OTP (mô phỏng “gửi email”)
        $message = "✅ Mã OTP của bạn là: <b>$otp</b><br>
                    (Có hiệu lực trong 5 phút).<br>
                    Nhập OTP tại đây: <a href='xac_thuc_otp.php?email=$email'>Xác thực OTP</a>";
    } else {
        $message = "❌ Email không tồn tại trong hệ thống.";
    }
}
?>
<h2>🔐 Quên mật khẩu</h2>
<form method="post">
    <label>Nhập email của bạn:</label>
    <input type="email" name="email" required>
    <button type="submit">Gửi mã OTP</button>
</form>
<p><?= $message ?></p>
