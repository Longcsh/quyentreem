<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quyền chăm sóc sức khỏe</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* ==================== RESET ==================== */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      background: #f5f8ff; /* nền tĩnh nhẹ, thay cho mây trôi */
      color: #1e293b;
      line-height: 1.8;
      overflow-x: hidden;
      min-height: 100vh;
    }
    a { text-decoration: none; color: inherit; }
    img { display: block; max-width: 100%; }

    /* ==================== HEADER ==================== */
    header {
      background: #004aad;
      color: #fff;
      padding: 16px 24px;
      text-align: center;
      font-size: 1.6rem;
      font-weight: 700;
      border-bottom: 3px solid #002e7a;
      letter-spacing: 0.5px;
      box-shadow: 0 3px 12px rgba(0,0,0,0.15);
    }

    /* ==================== NÚT QUAY LẠI ==================== */
    .back {
      display: inline-block;
      color: #004aad;
      font-weight: 600;
      padding: 10px 16px;
      border-radius: 8px;
      background: rgba(0,74,173,0.08);
      border: 1px solid rgba(0,74,173,0.2);
      transition: all 0.3s ease;
      margin-bottom: 24px;
    }
    .back:hover {
      background: #004aad;
      color: #fff;
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0,74,173,0.2);
    }

    /* ==================== CONTAINER ==================== */
    .container {
      max-width: 1200px;
      margin: 40px auto;
      display: grid;
      grid-template-columns: 1fr 360px;
      gap: 40px;
    }
    @media (max-width: 992px) {
      .container { grid-template-columns: 1fr; gap: 24px; }
    }

    /* ==================== BÀI VIẾT ==================== */
    .article {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 12px 28px rgba(0,0,0,0.1);
      padding: 36px;
      animation: fadeIn 0.6s ease;
      backdrop-filter: blur(10px);
    }
    .article h1 {
      font-size: 2rem;
      color: #004aad;
      margin-bottom: 16px;
      font-weight: 700;
      line-height: 1.3;
    }
    .meta {
      font-size: 0.95rem;
      color: #6b7280;
      margin-bottom: 24px;
    }
    .meta i { color: #004aad; margin-right: 6px; }

    .article img {
      width: 100%;
      max-height: 500px;
      object-fit: cover;
      border-radius: 12px;
      margin: 24px 0;
      box-shadow: 0 8px 20px rgba(0,0,0,0.08);
      transition: transform 0.4s ease;
    }
    .article img:hover { transform: scale(1.03); }

    .article p {
      margin-bottom: 20px;
      font-size: 1.08rem;
      text-align: justify;
      color: #1e293b;
      line-height: 1.9;
    }

    .section-title {
      display: flex;
      align-items: center;
      margin: 32px 0 16px;
      font-size: 1.25rem;
      font-weight: 700;
      color: #004aad;
    }
    .section-title i {
      margin-right: 10px;
      color: #ff6f61;
      font-size: 1.3rem;
    }

    .article ul, .article ol {
      margin-left: 24px;
      margin-bottom: 20px;
    }
    .article li {
      margin-bottom: 10px;
      line-height: 1.6;
    }

    /* ==================== SIDEBAR ==================== */
    .sidebar {
      position: sticky;
      top: 20px;
      display: flex;
      flex-direction: column;
      gap: 24px;
    }
    .project-card {
      background: #fff;
      padding: 12px;
      border-radius: 14px;
      box-shadow: 0 6px 16px rgba(0,0,0,0.08);
      text-align: center;
      transition: all 0.3s ease;
    }
    .project-card img {
      width: 100%;
      border-radius: 10px;
      margin-bottom: 10px;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .project-card:hover img {
      transform: scale(1.05);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .project-card h4 {
      font-size: 1.1em;
      color: #00796b;
      margin-bottom: 6px;
    }
    .project-card p {
      font-size: 0.95em;
      color: #555;
      line-height: 1.4;
    }

    /* ==================== VIDEO & LIÊN HỆ ==================== */
    .video-module .title-line,
    .contact-box .title-line {
      font-weight: 700;
      font-size: 1.2rem;
      margin-bottom: 12px;
      padding-bottom: 4px;
      display: inline-block;
    }
    .video-module .title-line { border-bottom: 3px solid #8e44ad; color: #8e44ad; }
    .contact-box .title-line { border-bottom: 3px solid #4b0082; color: #4b0082; }

    .video-item {
      margin-bottom: 16px;
      overflow: hidden;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .contact-box p.muted {
      color: #555;
      font-size: 0.95rem;
      line-height: 1.5;
      margin-bottom: 12px;
    }
    .contact-box .btn.donate-btn {
      display: inline-block;
      background: #ff6f61;
      color: #fff;
      padding: 10px 16px;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.3s ease;
      margin-bottom: 12px;
    }
    .contact-box .btn.donate-btn:hover {
      background: #e65b4f;
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(0,0,0,0.2);
    }
    .contact-box .social-links {
      display: flex;
      gap: 12px;
    }
    .contact-box .social-links a {
      color: #fff;
      display: inline-flex;
      justify-content: center;
      align-items: center;
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: #004aad;
      transition: all 0.3s ease;
    }
    .contact-box .social-links a:hover {
      background: #ffdd57;
      color: #004aad;
    }

    /* ==================== FOOTER ==================== */
    footer {
      background: #004aad;
      color: #fff;
      padding: 24px 20px;
      text-align: center;
      margin-top: 60px;
      box-shadow: 0 -3px 12px rgba(0,0,0,0.15);
    }
    footer a {
      color: #ffdd57;
      margin: 0 8px;
      font-weight: 500;
    }

    /* ==================== ANIMATION ==================== */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 600px) {
      .container { grid-template-columns: 1fr; padding: 0 10px; }
      .article { padding: 24px; }
      .article h1 { font-size: 1.5rem; }
    }
  </style>
</head>
<body>

  <!-- Bỏ phần mây trôi -->

  <div class="container">
    <div>
      <a href="../index.php" class="back"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
      <div class="article">
        <h1>Quyền chăm sóc sức khỏe</h1>
        <div class="meta">
          <i class="fa-regular fa-calendar"></i> Ngày đăng: 20/10/2025 |
          <i class="fa-solid fa-pen"></i> Ban biên tập |
          <i class="fa-solid fa-eye"></i> 98 lượt xem
        </div>

        <img src="../igm/suckhoe_page2.jpg" alt="Quyền chăm sóc sức khỏe">

        <p>Mỗi trẻ em đều có quyền được chăm sóc sức khỏe đầy đủ và an toàn...</p>

        <div class="section-title"><i class="fa-solid fa-heart-pulse"></i> Tầm quan trọng của chăm sóc sức khỏe</div>
        <p>Trẻ em khỏe mạnh có khả năng học tập tốt hơn...</p>
        <ul>
          <li>Khám sức khỏe định kỳ để phát hiện sớm bệnh lý.</li>
          <li>Tiêm chủng đầy đủ giúp phòng chống bệnh truyền nhiễm nguy hiểm.</li>
          <li>Cung cấp dinh dưỡng đầy đủ, cân đối và hợp lý.</li>
          <li>Giáo dục vệ sinh cá nhân, phòng chống bệnh tật.</li>
          <li>Tạo môi trường học đường và gia đình an toàn, không bạo lực.</li>
        </ul>

        <div class="section-title"><i class="fa-solid fa-stethoscope"></i> Dinh dưỡng và thể chất</div>
        <p>Dinh dưỡng hợp lý đóng vai trò quan trọng trong sự phát triển của trẻ...</p>

        <div class="section-title"><i class="fa-solid fa-shield-halved"></i> Bảo vệ sức khỏe toàn diện</div>
        <p>Cần đảm bảo trẻ em được chăm sóc toàn diện, bao gồm sức khỏe thể chất và tinh thần...</p>

        <div class="section-title"><i class="fa-solid fa-lightbulb"></i> Lợi ích lâu dài</div>
        <p>Đầu tư cho sức khỏe trẻ em là đầu tư cho tương lai của xã hội...</p>
      </div>
    </div>

    <aside class="sidebar">
      <article class="project-card">
        <a href="pagesidebar1.php">
          <img src="../igm/hoctap_page1.jpg" alt="Quyền học tập và phát triển">
          <h4>Quyền học tập và phát triển</h4>
        </a>
        <p>Mỗi trẻ em đều có quyền được học tập và phát triển toàn diện.</p>
      </article>

      <article class="project-card">
        <a href="pagesidebar3.php">
          <img src="../igm/baovexamhaitreem.jpg" alt="Bảo vệ trẻ em khỏi xâm hại">
          <h4>Bảo vệ trẻ em khỏi xâm hại</h4>
        </a>
        <p>Trẻ em cần được bảo vệ khỏi bạo lực, xâm hại và môi trường nguy hiểm.</p>
      </article>

      <div class="module video-module">
        <div class="title-line"><span>Video</span></div>
        <div class="video-item">
          <iframe width="100%" height="180" src="https://www.youtube.com/embed/aecD84txPqk"
            title="Video 1 - Giới thiệu" frameborder="0" allowfullscreen></iframe>
        </div>
        <div class="video-item">
          <iframe width="100%" height="180" src="https://www.youtube.com/embed/dTDcUHGvbKI"
            title="Video 2 - Hướng dẫn" frameborder="0" allowfullscreen></iframe>
        </div>
      </div>

      <div class="module contact-box">
        <div class="title-line"><span>Liên hệ nhanh</span></div>
        <p class="muted">
          📍 Địa chỉ: Cầu Giấy, Hà Nội<br>
          ☎ Hotline: 0123 456 789<br>
          ✉ Email: bvte@gmail.com
        </p>
        <a class="btn donate-btn" href="user.php?page=ungho">Ủng hộ</a>
        <div class="social-links">
          <a href="https://facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a>
          <a href="mailto:bvte@gmail.com"><i class="fas fa-envelope"></i></a>
        </div>
      </div>
    </aside>
  </div>

  <footer>
    <p>&copy; 2025 Quyền Trẻ Em. Bản quyền thuộc về tổ chức.</p>
  </footer>

</body>
</html>
