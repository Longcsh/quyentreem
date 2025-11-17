console.log("✅ ungho.js đã chạy!");

document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("donateForm");
  const popup = document.getElementById("donate-popup");
  const btnClose = document.getElementById("close-popup");
  const btnPreview = document.getElementById("btnPreview");
  const confirmForm = document.getElementById("confirmForm");

  if (!form || !btnPreview) return;

  // Khi bấm “Ủng hộ ngay”
  btnPreview.addEventListener("click", function () {
    const hoTen = form.querySelector('input[name="ho_ten"]').value.trim();
    const email = form.querySelector('input[name="email"]').value.trim();
    const soTien = form.querySelector('input[name="so_tien"]').value.trim();
    const loiNhan = form.querySelector('textarea[name="loi_nhan"]').value.trim();
    const anDanh = form.querySelector('input[name="an_danh"]').checked ? 1 : 0;

    if (!hoTen || !email || !soTien) {
      alert("⚠️ Vui lòng nhập đầy đủ thông tin trước khi ủng hộ!");
      return;
    }

    const gmailPattern = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
    if (!gmailPattern.test(email)) {
      alert("⚠️ Email không hợp lệ! Vui lòng nhập đúng dạng @gmail.com");
      return;
    }

    // Gán dữ liệu sang form xác nhận
    confirmForm.querySelector('input[name="ho_ten"]').value = hoTen;
    confirmForm.querySelector('input[name="email"]').value = email;
    confirmForm.querySelector('input[name="so_tien"]').value = soTien;
    confirmForm.querySelector('input[name="loi_nhan"]').value = loiNhan;
    confirmForm.querySelector('input[name="an_danh"]').value = anDanh;

    // Hiện popup
    popup.style.display = "flex";
  });

  // Đóng popup
  if (btnClose) {
    btnClose.addEventListener("click", function (e) {
      e.preventDefault();
      popup.style.display = "none";
    });
  }
});
