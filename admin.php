<?php
session_start();

if (empty($_SESSION['user_id']) || $_SESSION['vai_tro_id'] != 3) {
    header("Location:  pages/dang_nhap.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Admin - Website bảo vệ quyền trẻ em</title>

  <!-- CSS -->
  <link rel="stylesheet" href="admin12/css/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <!-- Header -->
  <?php include 'admin12/partials/header.html'; ?>

  <div class="container-fluid mt-3">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-3">
        <?php include 'admin12/partials/sidebar.html'; ?>
      </div>

      <!-- Nội dung chính -->
      <main class="col-9">
        <h2>Chào mừng Admin</h2>
        <p>Chọn chức năng từ menu bên trái để bắt đầu.</p>
      </main>
    </div>
  </div>

  <!-- JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
  // 🧭 Xử lý đăng xuất (dùng file ở folder pages ngoài)
  $(document).on('click', '#logoutBtn', function() {
    if (confirm('Bạn có chắc muốn đăng xuất không?')) {
      $.get('pages/dangxuat.php', function(res) {
        if (res.status === 'success') {
          window.location.href = 'pages/dangnhap.php';
        } else {
          alert('Lỗi đăng xuất!');
        }
      }, 'json');
    }
  });
  </script>
</body>
</html>
