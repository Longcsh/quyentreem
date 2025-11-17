<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bảo vệ trẻ em khỏi xâm hại</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* ==================== RESET CSS ==================== */
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      background: #f4f7fc;
      color: #1e293b;
      line-height: 1.8;
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
      font-size: 1.5rem;
      font-weight: 700;
      border-bottom: 3px solid #003080;
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

    /* ==================== NÚT QUAY LẠI ==================== */
    .back {
      display: inline-block;
      color: #004aad;
      font-weight: 600;
      padding: 10px 16px;
      border-radius: 8px;
      background: rgba(0, 74, 173, 0.08);
      border: 1px solid rgba(0, 74, 173, 0.2);
      transition: all 0.3s ease;
      margin-bottom: 24px;
    }

    .back:hover {
      background: #004aad;
      color: #fff;
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0, 74, 173, 0.2);
    }

    /* ==================== BÀI VIẾT ==================== */
    .article {
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 12px 28px rgba(0,0,0,0.08);
      padding: 32px;
      animation: fadeIn 0.6s ease;
    }

    .article h1 {
      font-size: 32px;
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
    }

    .article p {
      margin-bottom: 20px;
      font-size: 1.08rem;
      text-align: justify;
      line-height: 1.9;
      color: #1e293b;
    }

    /* ==================== CHIA MỤC ==================== */
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

    .article ul {
      margin-left: 20px;
      margin-bottom: 20px;
      list-style: disc inside;
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

    .sidebar .project-card {
      background: #fff;
      padding: 12px;
      border-radius: 12px;
      box-shadow: 0 6px 16px rgba(0,0,0,0.08);
      text-align: center;
      transition: all 0.3s ease;
    }

    .project-card img {
      width: 100%;
      height: auto;
      object-fit: cover;
      border-radius: 10px;
      margin-bottom: 10px;
      transition: transform 0.3s, box-shadow 0.3s;
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

    .project-card:hover img {
      transform: scale(1.03);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .project-card a { text-decoration: none; color: inherit; }

    /* ==================== VIDEO ==================== */
    .video-module .title-line {
      font-weight: 700;
      font-size: 1.2rem;
      margin-bottom: 12px;
      padding-bottom: 4px;
      border-bottom: 3px solid #8e44ad;
      display: inline-block;
      color: #8e44ad;
    }

    .video-item {
      margin-bottom: 16px;
      overflow: hidden;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    /* ==================== LIÊN HỆ ==================== */
    .contact-box .title-line {
      font-weight: 700;
      font-size: 1.2rem;
      margin-bottom: 12px;
      padding-bottom: 4px;
      border-bottom: 3px solid #4b0082;
      display: inline-block;
      color: #4b0082;
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
    }

    footer a {
      color: #ffdd57;
      margin: 0 8px;
      font-weight: 500;
    }

    /* ==================== ANIMATION ==================== */
    @keyframes fadeIn {
      from {opacity:0; transform: translateY(20px);}
      to {opacity:1; transform: translateY(0);}
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- BÀI VIẾT -->
    <div>
      <a href="../index.php" class="back"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
      <div class="article">
        <h1>Bảo vệ trẻ em khỏi xâm hại</h1>
        <div class="meta">
          <i class="fa-regular fa-calendar"></i> Ngày đăng: 20/10/2025 |
          <i class="fa-solid fa-pen"></i> Ban biên tập |
          <i class="fa-solid fa-eye"></i> 156 lượt xem
        </div>

        <img src="../igm/baovexamhaitreem.jpg" alt="Bảo vệ trẻ em khỏi xâm hại">

        <p>Trẻ em là mầm non của đất nước, là đối tượng cần được yêu thương, chăm sóc và bảo vệ tuyệt đối. Tuy nhiên, trong xã hội hiện đại, tình trạng xâm hại trẻ em vẫn còn diễn biến phức tạp, để lại hậu quả nghiêm trọng về thể chất và tinh thần cho các em.</p>

        <div class="section-title"><i class="fa-solid fa-hand-holding-heart"></i> Quyền được bảo vệ</div>
        <p>Mọi trẻ em đều có quyền được bảo vệ khỏi mọi hình thức bạo lực, bóc lột và xâm hại. Đây là quyền cơ bản được ghi nhận trong Công ước Quốc tế về Quyền trẻ em của Liên Hợp Quốc mà Việt Nam là một trong những quốc gia đầu tiên phê chuẩn.</p>

        <div class="section-title"><i class="fa-solid fa-triangle-exclamation"></i> Các hình thức xâm hại phổ biến</div>
        <ul>
          <li>Bạo lực thể chất: đánh đập, hành hạ, gây tổn thương cơ thể.</li>
          <li>Xâm hại tinh thần: nhục mạ, cô lập, đe dọa hoặc bắt nạt.</li>
          <li>Xâm hại tình dục: mọi hành vi lạm dụng, quấy rối, tấn công tình dục.</li>
          <li>Bóc lột lao động: ép buộc trẻ em làm việc nặng, nguy hiểm.</li>
        </ul>

        <div class="section-title"><i class="fa-solid fa-user-shield"></i> Trách nhiệm của gia đình và xã hội</div>
        <p>Gia đình đóng vai trò quan trọng nhất trong việc bảo vệ trẻ em. Cha mẹ cần quan tâm, lắng nghe, hướng dẫn con nhận biết và tự bảo vệ bản thân. Trường học và cộng đồng cần tạo môi trường an toàn, thân thiện và có cơ chế xử lý kịp thời khi phát hiện hành vi xâm hại.</p>

        <div class="section-title"><i class="fa-solid fa-people-group"></i> Các biện pháp phòng ngừa</div>
        <ul>
          <li>Giáo dục kỹ năng sống và kỹ năng tự bảo vệ cho trẻ.</li>
          <li>Tăng cường tuyên truyền, nâng cao nhận thức cho phụ huynh, giáo viên.</li>
          <li>Xây dựng đường dây nóng, mạng lưới hỗ trợ trẻ em bị xâm hại.</li>
          <li>Thực thi nghiêm minh pháp luật đối với người phạm tội xâm hại trẻ em.</li>
        </ul>

        <div class="section-title"><i class="fa-solid fa-heart"></i> Chung tay hành động</div>
        <p>Mỗi người trong cộng đồng đều có thể góp phần bảo vệ trẻ em bằng cách quan tâm, lên tiếng, và báo cáo kịp thời những hành vi nghi ngờ xâm hại. Bảo vệ trẻ em không chỉ là trách nhiệm của Nhà nước mà còn là bổn phận của toàn xã hội.</p>
      </div>
    </div>

    <!-- SIDEBAR -->
    <aside class="sidebar">
      <article class="project-card">
        <a href="pagesidebar1.php">
          <img src="../igm/hoctap_page1.jpg" alt="Quyền học tập và phát triển">
          <h4>Quyền học tập và phát triển</h4>
        </a>
        <p>Mỗi trẻ em đều có quyền được học tập và phát triển toàn diện.</p>
      </article>

      <article class="project-card">
        <a href="pagesidebar2.php">
          <img src="../igm/quyenchamsocsuckhoe.jpg" alt="Quyền chăm sóc sức khỏe">
          <h4>Quyền chăm sóc sức khỏe</h4>
        </a>
        <p>Bảo đảm mọi trẻ em được chăm sóc sức khỏe đầy đủ và an toàn.</p>
      </article>

      <div class="module video-module">
        <div class="title-line"><span>Video</span></div>
        <div class="video-item">
          <iframe width="100%" height="180"
            src="https://www.youtube.com/embed/aecD84txPqk"
            title="Video 1 - Giới thiệu"
            frameborder="0"
            allowfullscreen></iframe>
        </div>
        <div class="video-item">
          <iframe width="100%" height="180"
            src="https://www.youtube.com/embed/dTDcUHGvbKI"
            title="Video 2 - Hướng dẫn"
            frameborder="0"
            allowfullscreen></iframe>
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
