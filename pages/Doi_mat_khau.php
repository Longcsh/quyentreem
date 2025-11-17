<?php

require_once __DIR__ . '/../config.php'; 


$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? 0;
    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];

    // Lấy mật khẩu cũ trong DB
    $stmt = $conn->prepare("SELECT mat_khau FROM tai_khoan WHERE id=?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($old_pass, $row['mat_khau'])) {
        if ($new_pass === $confirm_pass) {
            $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE tai_khoan SET mat_khau=? WHERE id=?");
            $update->execute([$new_hash, $user_id]);
            $message = "<p style='color:green;'>✅ Đổi mật khẩu thành công!</p>";
        } else {
            $message = "<p style='color:red;'>❌ Mật khẩu mới không khớp.</p>";
        }
    } else {
        $message = "<p style='color:red;'>❌ Mật khẩu cũ không đúng.</p>";
    }
}
?>

<div class="change-pass" style="max-width:500px;margin:20px auto;padding:20px;border:1px solid #ddd;border-radius:10px;background:#fff;">
    <h2>🔑 Đổi mật khẩu</h2>
    <?= $message ?>
    <form method="post">
        <label>Mật khẩu cũ:</label><br>
        <input type="password" name="old_pass" required><br><br>
        
        <label>Mật khẩu mới:</label><br>
        <input type="password" name="new_pass" required><br><br>
        
        <label>Xác nhận mật khẩu mới:</label><br>
        <input type="password" name="confirm_pass" required><br><br>
        
        <button type="submit">Đổi mật khẩu</button>
    </form>
</div>
