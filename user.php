<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) || $_SESSION['vai_tro_id'] != 1) {
    header("Location: pages/dang_nhap.php");
    exit();
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Website Dự án - Người dùng</title>
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
    <?php include("partials/header-user.html"); ?>
  </div>

  <!-- Nội dung chính -->
  <main class="container" role="main">
    <div class="layout">
      <div id="content">
        <?php
          // Router theo tham số page
          $page = isset($_GET['page']) ? $_GET['page'] : 'home';

          switch ($page) {
            // Giới Thiệu
                    case 'tamnhin':
              include("pages/page1a_tam_nhin.php");
              break;
          case 'lichsu':
              include("pages/page1b_lich_su.php");
              break;
          case 'cocautochuc':
              include("pages/page1c_co_cau_to_chuc.php");
              break;
          case 'chienluoc':
              include("pages/page1d_chien_luoc_hd.php");
              break;
            // Tài Liệu
              case 'quyentreem':
                  include("pages/page2a_Quyen_tre_em.php");
                  break;
              case 'chongbaohanh':
                  include("pages/page2b_Chong_bh.php");
                  break;
              case 'giaoducantoan':
                  include("pages/page2c_Gdat.php"); 
                  break;
                    // CHUYÊN MỤC
              case 'tintuc':
                  include("pages/page3a_Tintuc_sukien.php");
                  break;
              case 'tiengnoi':
                  include("pages/page3b_Tiengnoi_cuatre.php");
                  break;
              case 'chiase':
                  include("pages/page3c_Chiase.php");
                  break;
              case 'kienthuc':
                  include("pages/page3d_Kienthuc_kinang.php");
                  break;
              case 'sangkien':
                  include("pages/page3e_Sangkien_chotre.php");
                  break;
                // Ủng Hộ
                case 'ungho':
                  include("pages/page4_ungho.php");
                  break;
                 
                // Tư Vấn
                  case 'hoichuyengia':
                include("pages/page5a_Hoichuyengia.php");
                break;
            case 'traloi':
                include("pages/page5b_Traloicuatoi.php");
                break;
            case 'datlich':
                include("pages/page5c_Datlich.php");
                break;
            case 'lich':
                include("pages/page5d_Lichcuatoi.php");
                break;
                 // liên hệ
                  case 'lienhe':
                  include("pages/page6_lienhe.php");
                  break;
                  //cá nhân
                case 'hoso':
                include("pages/ho_so.php");
                break;
                case 'doimk':
                include("pages/Doi_mat_khau.php");
                break;
              default:
                  include("pages/content-user.html"); 
                  break;
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
let slideIndex = 0;
showSlides();

function plusSlides(n) {
  slideIndex += n - 1;
  showSlides();
}

function showSlides() {
  const slides = document.getElementsByClassName("slide");
  for (let i = 0; i < slides.length; i++) slides[i].style.display = "none";

  slideIndex++;
  if (slideIndex > slides.length) slideIndex = 1;

  slides[slideIndex - 1].style.display = "block";
  setTimeout(showSlides, 5000); // đổi ảnh mỗi 5 giây
}
</script>
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
