<?php
session_start();

// ✅ Kiểm tra đăng nhập và vai trò
if (empty($_SESSION['user_id']) || $_SESSION['vai_tro_id'] != 2) {
    header("Location: /php/bvte/pages/dang_nhap.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Hệ thống Chuyên Gia</title>

 <!-- CSS -->
<link rel="stylesheet" href="experts/css/style.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="experts/css/home-expert.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="experts/css/sidebar-expert.css?v=<?php echo time(); ?>"> <!-- cái này luôn cuối -->


  <!-- Font Google -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
</head>

<body>
  <div class="layout">
    <!-- Sidebar -->
    <?php include __DIR__ . '/experts/partials/navbar.php'; ?>

    <!-- Nội dung chính -->
    <div class="main-content">
  <section class="welcome-card">
    <img src="/php/bvte/experts/assets/logo.png" alt="Chuyên gia avatar">
    <h1>🌞 Hệ thống Chuyên Gia</h1>
   <p>Xin chào, <strong>Chuyên gia <?php echo htmlspecialchars($_SESSION['ho_ten'] ?? ''); ?></strong></p>
    <p>Chọn chức năng ở menu bên trái để bắt đầu quản lý tư vấn, lịch hẹn và bài viết.</p>
  </section>
</div>

  </div>

  <script src="experts/js/main.js"></script>
</body>
</html>
