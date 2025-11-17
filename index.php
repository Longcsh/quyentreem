<?php
session_start();
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Website Dự án</title>
  <link rel="stylesheet" href="css/banner.css">
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/style_login.css" />
  <link rel="stylesheet" href="css/style_content.css" />
  <link rel="stylesheet" href="css/style_tuvan.css" />
  <link rel="stylesheet" href="css/content_pages.css" />
  <link rel="stylesheet" href="css/style_donate_tuvan.css" />  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <!-- Header -->
  <div id="header">
    <?php
      if (isset($_SESSION['vai_tro'])) {
          // Nếu đã đăng nhập
          if ($_SESSION['vai_tro'] === 'admin') {
              include("partials/header-admin.html");
          } elseif ($_SESSION['vai_tro'] === 'expert') {
              include("partials/header-expert.html");
          } else {
              include("partials/header-user.html");
          }
      } else {
          // Nếu chưa đăng nhập thì dùng header-guest
          include("partials/header-guest.html");
      }
    ?>
  </div>

  <!-- Nội dung chính -->
  <main class="container" role="main">
    <div class="layout">
      <!-- Content -->
      <div id="content">
        <?php
          $page = isset($_GET['page']) ? $_GET['page'] : 'home';

          $pages = [
            "tamnhin" => "pages/page1a_tam_nhin.php",
            "lichsu" => "pages/page1b_lich_su.php",
            "cocautochuc" => "pages/page1c_co_cau_to_chuc.php",
            "chienluoc" => "pages/page1d_chien_luoc_hd.php",

            "quyentreem" => "pages/page2a_Quyen_tre_em.php",
            "chongbaohanh" => "pages/page2b_Chong_bh.php",
            "giaoducantoan" => "pages/page2c_Gdat.php",

   
            "tintucsukien"  => "pages/page3a_Tintuc_sukien.php",      
            "tiengnoitre"   => "pages/page3b_Tiengnoi_cuatre.php", 
            "chiase"        => "pages/page3c_Chiase.php",          
            "kienthuc"      => "pages/page3d_Kienthuc_kinang.php",    
            "sangkien"      => "pages/page3e_Sangkien_chotre.php",   

            "lienhe" => "pages/page6_Lienhe.php",
          ];

          if(array_key_exists($page, $pages)) {
              include $pages[$page];
          } else {
              include("pages/content-home.html");
          }
        ?>
      </div>

      <!-- Sidebar -->
      <aside id="sidebar">
        <?php include("partials/sidebar.html"); ?>
      </aside>
    </div>
  </main>

  <!-- Footer -->
  <div id="footer">
    <?php include("partials/footer.html"); ?>
  </div>
  <script>
window.addEventListener("scroll", function() {
  const nav = document.querySelector(".site-nav");
  if (window.scrollY > 150) {
    nav.classList.add("nav-fixed");
  } else {
    nav.classList.remove("nav-fixed");
  }
});
</script>
</body>
</html>
