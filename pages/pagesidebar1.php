<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quyền học tập và phát triển</title>
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

    /* ==================== BANNER ==================== */
    .banner {
      width: 100%;
      max-height: 380px;
      overflow: hidden;
      position: relative;
      margin-top: 10px;
    }

    .banner img {
      width: 100%;
      object-fit: cover;
      height: 100%;
      transition: transform 0.5s;
    }

    .banner img:hover {
      transform: scale(1.05);
    }

    /* ==================== CONTAINER CHÍNH ==================== */
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

    /* ==================== CHIA MỤC BÀI VIẾT ==================== */
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

    .article ol {
      margin-left: 20px;
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

    /* -------------------- PROJECT CARD -------------------- */
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

    /* -------------------- VIDEO MODULE -------------------- */
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

    /* -------------------- CONTACT BOX -------------------- */
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

    /* ==================== RESPONSIVE ==================== */
    @media (max-width: 992px) {
      .container { grid-template-columns: 1fr; }
      .sidebar { position: relative; top: 0; }
    }

    @media (max-width: 600px) {
      body { padding: 0 10px; }
      .article { padding: 24px; }
      .article h1 { font-size: 24px; }
      .project-card h4 { font-size: 1em; }
      .video-module .title-line,
      .contact-box .title-line { font-size: 1.1rem; }
    }

  </style>
</head>
<body>

  <div class="container">
    <!-- BÀI VIẾT -->
    <div>
      <a href="../index.php" class="back"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
      <div class="article">
        <h1>Quyền học tập và phát triển</h1>
        <div class="meta">
          <i class="fa-regular fa-calendar"></i> Ngày đăng: 20/10/2025 |
          <i class="fa-solid fa-pen"></i> Ban biên tập |
          <i class="fa-solid fa-eye"></i> 123 lượt xem
        </div>
        <img src="../igm/hoctap_page1.jpg" alt="Quyền học tập và phát triển">

        <p><b>Giáo dục</b> là nền tảng của mọi sự tiến bộ xã hội. Từ những bước đầu tiên của hành trình học chữ, đến việc tiếp cận tri thức toàn cầu, quyền được học tập không chỉ là đặc quyền, mà là một trong những quyền cơ bản nhất của con người. Mỗi đứa trẻ đều có tiềm năng vô hạn để phát triển — và giáo dục chính là chiếc chìa khóa mở cánh cửa đó.</p>

        <div class="section-title"><i class="fa-solid fa-graduation-cap"></i> Tầm quan trọng của giáo dục</div>
        <p>Trong thời đại 4.0, tri thức không chỉ còn nằm trong sách vở, mà đã trở thành sức mạnh thúc đẩy đổi mới sáng tạo và phát triển kinh tế – xã hội. Một quốc gia chỉ có thể phát triển bền vững khi đảm bảo mọi trẻ em, dù ở thành thị hay nông thôn, đều được tiếp cận nền giáo dục chất lượng.</p>
        <p>Giáo dục giúp trẻ:</p>
        <ul>
          <li>Phát triển tư duy phản biện và sáng tạo.</li>
          <li>Học cách làm việc nhóm và tôn trọng sự khác biệt.</li>
          <li>Hình thành nhân cách, lòng nhân ái và tinh thần trách nhiệm.</li>
        </ul>

        <div class="section-title"><i class="fa-solid fa-lightbulb"></i> Lợi ích lâu dài của việc học</div>
        <p>Học tập không chỉ mang lại tri thức mà còn tạo cơ hội cho sự thay đổi. Một đứa trẻ được học hành có thể giúp cả gia đình thoát khỏi vòng luẩn quẩn của đói nghèo. Theo báo cáo của UNESCO, mỗi năm học thêm có thể giúp tăng thu nhập của một người trung bình 10%. Điều đó cho thấy, đầu tư vào giáo dục là đầu tư cho tương lai của cả quốc gia.</p>

        <img src="../igm/education_children.jpg" alt="Trẻ em học tập vui vẻ">

        <div class="section-title"><i class="fa-solid fa-children"></i> Giáo dục toàn diện – phát triển cả trí và tâm</div>
        <p>Giáo dục hiện đại không chỉ hướng đến kiến thức mà còn chú trọng phát triển kỹ năng sống, cảm xúc, đạo đức và nhân cách. Một đứa trẻ biết đồng cảm, biết yêu thương và biết hành động vì cộng đồng — đó mới là thành công thật sự của nền giáo dục.</p>

        <p>Nhiều trường học ở Việt Nam đã đưa vào chương trình các hoạt động trải nghiệm sáng tạo, học ngoài trời, làm việc nhóm hoặc học thông qua trò chơi. Cách tiếp cận này giúp trẻ chủ động tiếp thu tri thức, đồng thời rèn luyện tinh thần hợp tác và khả năng lãnh đạo.</p>

        <div class="section-title"><i class="fa-solid fa-earth-asia"></i> Hướng tới nền giáo dục bình đẳng</div>
        <p>Không phải mọi trẻ em đều có cơ hội đến trường. Ở vùng sâu, vùng xa, nhiều em vẫn phải vượt hàng chục cây số để học chữ. Chính vì vậy, các chương trình “Cặp sách đến trường”, “Nâng bước em tới trường” hay các dự án cộng đồng đang đóng vai trò quan trọng trong việc thu hẹp khoảng cách này.</p>
        <blockquote style="background:#f9fafb;padding:16px;border-left:4px solid #004aad;margin:20px 0;border-radius:8px;">
          “Một đứa trẻ, một cây bút, một cuốn sách và một giáo viên – có thể thay đổi thế giới.”  
          <br><em>– Malala Yousafzai, nhà hoạt động vì quyền được học tập.</em>
        </blockquote>

        <div class="section-title"><i class="fa-solid fa-heart"></i> Kết luận</div>
        <p>Quyền học tập và phát triển là nền tảng của một xã hội công bằng và nhân văn. Khi một đứa trẻ được học tập đầy đủ, được khuyến khích phát triển khả năng của mình, xã hội sẽ có thêm một công dân tự tin, có tri thức và có khát vọng cống hiến.</p>
        <p>Để đảm bảo quyền ấy, cần sự chung tay của cả cộng đồng — từ gia đình, nhà trường, cho tới các tổ chức xã hội. Bởi mỗi khi ta giúp một đứa trẻ được đến trường, là ta đang gieo một hạt mầm của hy vọng và tương lai.</p>
      </div>
    </div>

    <!-- SIDEBAR -->
    <aside class="sidebar">
      <article class="project-card">
        <a href="pagesidebar2.php">
         <img src="../igm/quyenchamsocsuckhoe.jpg" alt="Quyền chăm sóc sức khỏe">
          <h4>Quyền chăm sóc sức khỏe</h4>
        </a>
        <p>Bảo đảm mọi trẻ em được chăm sóc sức khỏe đầy đủ và an toàn.</p>
      </article>

      <article class="project-card">
        <a href="pagesidebar3.php">
        <img src="../igm/baovexamhaitreem.jpg" alt="Bảo vệ trẻ em khỏi xâm hại">
          <h4>Bảo vệ trẻ em khỏi xâm hại</h4>
        </a>
        <p>Trẻ em cần được bảo vệ khỏi bạo lực, xâm hại và môi trường nguy hiểm.</p>
      </article>

      <div class="module video-module">
        <div class="title-line purple"><span>Video</span></div>
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
        <div class="title-line indigo"><span>Liên hệ nhanh</span></div>
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
