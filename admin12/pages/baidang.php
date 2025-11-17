<?php
// ===============================
// 📂 File: admin12/pages/noidung.php
// ===============================
$apiBase = "http://localhost:8081/php/bvte/admin12/BE/";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>📂 Quản lý nội dung</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --main-blue: #2563eb;
      --blue-gradient: linear-gradient(135deg, #2563eb, #1d4ed8);
      --white: #ffffff;
      --shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
      --radius: 14px;
    }

    body { background: #f8f9fa; margin: 0; font-family: "Segoe UI", sans-serif; }

    /* ===== HEADER ===== */
    header {
      background: var(--blue-gradient);
      color: white;
      padding: 18px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: var(--shadow);
      border-bottom-left-radius: var(--radius);
      border-bottom-right-radius: var(--radius);
    }
    header h1 {
      font-size: 1.6rem;
      font-weight: 600;
      margin: 0;
    }
    .header-right {
      display: flex;
      align-items: center;
      gap: 12px;
      font-weight: 500;
    }
    .back-btn {
      background: var(--white);
      color: var(--main-blue);
      border: none;
      padding: 8px 16px;
      border-radius: 10px;
      font-weight: 600;
      cursor: pointer;
      box-shadow: 0 3px 8px rgba(0,0,0,0.15);
      transition: all 0.25s ease;
    }
    .back-btn:hover { background: #eff6ff; transform: translateY(-2px); }

    /* ===== CARD ===== */
    .card-select {
      transition: all 0.3s ease;
      cursor: pointer;
      border-radius: 16px;
      border: 2px solid transparent;
    }
    .card-select:hover {
      transform: translateY(-6px);
      box-shadow: 0 4px 14px rgba(0,0,0,0.15);
    }
    .icon { font-size: 60px; }
  </style>
</head>
<body>

<header>
  <h1>📂 Quản lý nội dung</h1>
  <div class="header-right">
    <button class="back-btn" onclick="window.location.href='../../admin.php'">⬅️ Quay lại</button>
    <span>Xin chào, <b>Admin</b> 👋</span>
  </div>
</header>

<div class="container py-5">
  <h2 class="text-center mb-5 fw-semibold">Chọn loại nội dung cần quản lý</h2>
  <div class="row justify-content-center g-4">
    
    <!-- 📰 Quản lý bài viết -->
    <div class="col-md-4">
      <div class="card card-select text-center p-4 border-primary" onclick="window.location.href='baiviet.php'">
        <div class="icon text-primary mb-3">📰</div>
        <h4>Quản lý Bài viết</h4>
        <p class="text-muted">Xem, duyệt, chỉnh sửa và quản lý các bài đăng từ chuyên gia hoặc người dùng.</p>
        <button class="btn btn-primary mt-2">Đi đến Bài viết</button>
      </div>
    </div>

    <!-- 📘 Quản lý tài liệu -->
    <div class="col-md-4">
      <div class="card card-select text-center p-4 border-success" onclick="window.location.href='tailieu.php'">
        <div class="icon text-success mb-3">📘</div>
        <h4>Quản lý Tài liệu</h4>
        <p class="text-muted">Quản lý các tài liệu chia sẻ, báo cáo, tài nguyên từ các vai trò khác nhau.</p>
        <button class="btn btn-success mt-2">Đi đến Tài liệu</button>
      </div>
    </div>

    <!-- 💬 Quản lý bình luận -->
    <div class="col-md-4">
      <div class="card card-select text-center p-4 border-info" onclick="window.location.href='admin_binhluan.php'">
        <div class="icon text-info mb-3">💬</div>
        <h4>Quản lý Bình luận</h4>
        <p class="text-muted">Xem, phản hồi và kiểm duyệt bình luận từ người dùng trên hệ thống.</p>
        <button class="btn btn-info text-white mt-2">Đi đến Bình luận</button>
      </div>
    </div>

  </div>
</div>

</body>
</html>
