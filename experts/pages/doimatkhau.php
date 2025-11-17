<?php
require_once __DIR__ . '/../../config.php';
include '../partials/navbar.php';

if (isset($_SESSION['user_id'], $_SESSION['vai_tro_id']) && $_SESSION['vai_tro_id'] == 2) {
    if (!isset($_SESSION['expert_id'])) {
        $_SESSION['expert_id'] = $_SESSION['user_id'];
        $_SESSION['expert_name'] = $_SESSION['ho_ten'] ?? $_SESSION['ten_dang_nhap'];
        $_SESSION['role_id'] = 2;
    }
}

// ✅ Kiểm tra đăng nhập chuyên gia
if (empty($_SESSION['expert_id'])) {
    header('Location: ../../pages/dang_nhap.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>🔐 Đổi mật khẩu</title>

  <!-- CSS -->
  <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/sidebar-expert.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/doimatkhau.css?v=<?php echo time(); ?>">
</head>
<body>

  <!-- Sidebar cố định -->
  <?php include '../partials/navbar.php'; ?>

  <!-- Nội dung chính -->
  <main class="main-content">
    <div class="change-password-container">
      <h2>Đổi mật khẩu</h2>

      <form id="formDoiMatKhau" method="POST">
        <label>Mật khẩu hiện tại:</label>
        <input type="password" id="matkhau_cu" name="matkhau_cu" required>

        <label>Mật khẩu mới:</label>
        <input type="password" id="matkhau_moi" name="matkhau_moi" required>

        <label>Xác nhận mật khẩu mới:</label>
        <input type="password" id="xacnhan" name="xacnhan" required>

        <button type="submit">Cập nhật</button>
      </form>
    </div>
  </main>

  <!-- Popup -->
  <div id="popup" class="popup">
    <div class="popup-content">
      <div id="popup-icon"></div>
      <p id="popup-message"></p>
    </div>
  </div>

  <script>
  document.getElementById('formDoiMatKhau').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('../pages/xuly_doimatkhau.php', {  // đúng cấp thư mục
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      const popup = document.getElementById('popup');
      const message = document.getElementById('popup-message');
      const icon = document.getElementById('popup-icon');

      message.textContent = data.message;
      icon.innerHTML = data.status === 'success' ? '✅' : '❌';

      popup.classList.add('show', data.status);
      if (data.status === 'success') this.reset();

      setTimeout(() => popup.classList.remove('show', 'success', 'error'), 3000);
    })
    .catch(() => alert("Lỗi kết nối máy chủ!"));
  });
  </script>
 <!-- JS dropdown sidebar -->
  <script src="../js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
