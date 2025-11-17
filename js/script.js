// Hàm load file HTML vào phần tử theo id
async function loadHTML(id, file) {
  try {
    const res = await fetch(file);
    const text = await res.text();
    document.getElementById(id).innerHTML = text;
  } catch (err) {
    document.getElementById(id).innerHTML = "<p>Lỗi load " + file + "</p>";
  }
}
// Router: ánh xạ hash -> content
function router() {
  let hash = location.hash || "#/home";

  switch (hash) {
    case "#/home":
      loadHTML("content", "pages/content-home.html");
      break;
//Giới Thiệu
case "#/tamnhin":
  loadHTML("content", "pages/page1a_tam_nhin.html");
  break;
case "#/lichsu":
  loadHTML("content", "pages/page1b_lich_su.html");
  break;
case "#/cocautochuc":
  loadHTML("content", "pages/page1c_co_cau_to_chuc.html");
  break;
case "#/chienluochd":
  loadHTML("content", "pages/page1d_chien_luoc_hd.html");
  break;
case "#/quychehd":
  loadHTML("content", "pages/page1e_quy_che_hd.html");
  break;

//Tài Liệu
case "#/quyentreem":
  loadHTML("content", "pages/page2a_Quyen_tre_em.html");
  break;
case "#/chongbaohanh":
  loadHTML("content", "pages/page2b_Chong_bh.html");
  break;
case "#/giaoducantoan":
  loadHTML("content", "pages/page2c_Gdat.html");
  break;

//Chuyên mục
case "#/tintucsk":
  loadHTML("content", "pages/page3a_Tintuc_sukien.html");
  break;
case "#/tiengnoicuatre":
  loadHTML("content", "pages/page3b_Tiengnoi_cuatre.html");
  break;
case "#/chiasetucd":
  loadHTML("content", "pages/page3c_Chiase.html");
  break;
case "#/kienthuckinang":
  loadHTML("content", "pages/page3d_Kienthuc_kinang.html");
  break;
case "#/sangkienchotre":
  loadHTML("content", "pages/page3e_Sangkien_chotre.html");
  break;

//Ủng hộ
case "#/donate":
  loadHTML("content", "pages/page4_donate.html");
  break;

//Tư Vấn
case "#/hoichuyengia":
  loadHTML("content", "pages/page5a_Hoi_chuyengia.html");
  break;
case "#/traloi":
  loadHTML("content", "pages/page5b_Traloi.html");
  break;
case "#/lich":
  loadHTML("content", "pages/page5c_Lich.html");
  break;
case "#/lstrochuyen":
  loadHTML("content", "pages/page5d_Lichsu_trochuyen.html");
  break;
case "#/datlich":
  loadHTML("content", "pages/page5e_Datlich.html");
  break;

//Liên hệ
case "#/lienhe":
  loadHTML("content", "pages/page6_Lienhe.html");
  break;
//Tài khoản
case "#/hoso":
  loadHTML("content", "page/page7a_Hoso_canhan");
  break;

    
    default:
      loadHTML("content", "pages/content-home.html");
  }
}

// Tự động load header, content, footer theo trang
window.addEventListener("DOMContentLoaded", () => {
  if (location.pathname.endsWith("index.html")) {
    loadHTML("header", "partials/header-guest.html");
    loadHTML("content", "pages/content-home.html"); // load nội dung trang khách
  } else if (location.pathname.endsWith("user.html")) {
    loadHTML("header", "partials/header-user.html");
    loadHTML("content", "pages/content-home.html"); // tạm dùng chung content  
  }
  loadHTML("sidebar", "pages/sidebar.html");   
  loadHTML("footer", "partials/footer.html");
  router(); // load content lần đầu
  });

  // Khi đổi hash (click menu)
  window.addEventListener("hashchange", router);
  
document.addEventListener("DOMContentLoaded", () => {
  // Dropdown mobile
  const dropdowns = document.querySelectorAll(".site-nav .dropdown > a");

  dropdowns.forEach(link => {
    link.addEventListener("click", e => {
      if (window.innerWidth <= 900) {
        e.preventDefault();
        const parent = link.parentElement;

        // Đóng các menu khác khi mở 1 menu
        document.querySelectorAll(".site-nav .dropdown")
          .forEach(d => { if (d !== parent) d.classList.remove("open"); });

        parent.classList.toggle("open");
      }
    });
  });
});
const popup = document.getElementById('popup');
const btnDonate = document.getElementById('btnDonate');
const closePopup = document.getElementById('close-popup');

btnDonate.addEventListener('click', () => {
  // Kiểm tra form có dữ liệu hợp lệ chưa
  const form = document.getElementById('donateForm');
  if (form.checkValidity()) {
    popup.style.display = 'flex'; // chỉ hiện popup, không reload
  } else {
    form.reportValidity(); // báo lỗi nhập thiếu
  }
});

closePopup.addEventListener('click', () => {
  popup.style.display = 'none'; // ẩn popup, form vẫn giữ nguyên dữ liệu
});


