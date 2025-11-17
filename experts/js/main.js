
document.addEventListener("DOMContentLoaded", () => {
  // Chờ DOM sẵn sàng
  const menuLinks = document.querySelectorAll(".sidebar .dropdown > a");
  if (!menuLinks.length) {
    console.error("Không tìm thấy menu dropdown nào!");
    return;
  }

  menuLinks.forEach(link => {
    link.addEventListener("click", e => {
      e.preventDefault();
      const li = e.target.closest("li");
      li.classList.toggle("open");
    });
  });
});


// ===========================
// Hồ sơ cá nhân - Preview avatar (giữ lại)
// ===========================
document.addEventListener("change", e => {
  if (e.target && e.target.id === "avatarUpload") {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = event => {
        document.getElementById("avatarPreview").src = event.target.result;
        const lbl = document.querySelector(".label-upload");
        if (lbl) lbl.classList.add("hidden");
      };
      reader.readAsDataURL(file);
    }
  }
});
