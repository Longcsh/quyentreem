<?php
// 🧩 Xác định base URL động, tương thích mọi môi trường
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$apiBase = rtrim($protocol . $host . $scriptDir, '/') . '/../BE/';
$apiBase = preg_replace('#(?<!:)//#', '/', $apiBase);

session_start(); // để lấy user_id & vai_tro_id
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>📚 Quản lý Tài Liệu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/tailieu.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-4">
  <div class="header-btn">
    <h3>📚 Quản lý Tài Liệu</h3>
    <a href="baidang.php" class="btn btn-back">🔙 Quay lại</a>
  </div>

  <!-- 🟢 Form thêm tài liệu -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">Thêm tài liệu mới</div>
    <div class="card-body">
      <form id="formThemTaiLieu" enctype="multipart/form-data">
        <input type="hidden" name="tai_khoan_id" value="<?= $_SESSION['user_id'] ?? 1 ?>">
        <input type="hidden" name="vai_tro_id" value="<?= $_SESSION['vai_tro_id'] ?? 3 ?>">

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <input type="text" name="tieu_de" class="form-control" placeholder="Tiêu đề tài liệu" required>
          </div>
          <div class="col-md-6">
            <select name="loai_tai_lieu" class="form-select" required>
              <option value="">-- Chọn loại tài liệu --</option>
              <option value="quyen_tre_em">Quyền trẻ em</option>
              <option value="chong_bao_hanh">Chống bạo hành</option>
              <option value="giao_duc_an_toan">Giáo dục an toàn</option>
            </select>
          </div>
        </div>

        <div class="mb-3">
          <textarea name="mo_ta" class="form-control" rows="3" placeholder="Mô tả ngắn..." required></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">📎 File đính kèm (PDF / DOCX / Ảnh)</label>
          <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg" required>
        </div>

        <div class="text-end">
          <button type="submit" class="btn btn-success">+ Thêm tài liệu</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 📋 Danh sách tài liệu -->
  <div class="card shadow-sm">
    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
      <span>Danh sách Tài Liệu</span>
      <div class="filter-bar">
        <select id="filterVaiTro" class="form-select form-select-sm">
          <option value="">-- Vai trò --</option>
          <option value="2">Chuyên gia</option>
          <option value="3">Quản trị viên</option>
        </select>
        <select id="filterLoai" class="form-select form-select-sm">
          <option value="">-- Loại tài liệu --</option>
          <option value="quyen_tre_em">Quyền trẻ em</option>
          <option value="chong_bao_hanh">Chống bạo hành</option>
          <option value="giao_duc_an_toan">Giáo dục an toàn</option>
        </select>
      </div>
    </div>
    <div class="card-body p-0">
      <table class="table table-bordered table-hover m-0" id="tblTaiLieu">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Tiêu đề</th>
            <th>Người đăng</th>
            <th>Vai trò</th>
            <th>Loại tài liệu</th>
            <th>File</th>
            <th>Ngày đăng</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody id="listTaiLieu">
          <tr><td colspan="8" class="text-center py-3">⏳ Đang tải...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- 🔵 Modal SỬA -->
<div class="modal fade" id="modalSua" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="formSuaTaiLieu" enctype="multipart/form-data">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title">✏️ Sửa tài liệu</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="edit_id">
          <div class="mb-3">
            <label>Tiêu đề</label>
            <input type="text" class="form-control" name="tieu_de" id="edit_tieu_de" required>
          </div>
          <div class="mb-3">
            <label>Loại tài liệu</label>
            <select name="loai_tai_lieu" id="edit_loai_tai_lieu" class="form-select" required>
              <option value="quyen_tre_em">Quyền trẻ em</option>
              <option value="chong_bao_hanh">Chống bạo hành</option>
              <option value="giao_duc_an_toan">Giáo dục an toàn</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Mô tả</label>
            <textarea class="form-control" name="mo_ta" id="edit_mo_ta" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label>File mới (nếu muốn thay)</label>
            <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg">
            <small id="edit_file_link" class="d-block mt-2 text-muted"></small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">💾 Lưu thay đổi</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const API_BASE = "<?= $apiBase ?>";

// 🟩 Load danh sách tài liệu
function loadTaiLieu() {
  $("#listTaiLieu").html('<tr><td colspan="8" class="text-center py-3">⏳ Đang tải...</td></tr>');

  const vai_tro = $("#filterVaiTro").val();
  const loai = $("#filterLoai").val();

  $.ajax({
    url: API_BASE + "tailieu_list.php",
    method: "GET",
    dataType: "json",
    data: { vai_tro, loai_tai_lieu: loai },
    success: function(res) {
      const items = res.items || [];

      if (!items.length) {
        $("#listTaiLieu").html('<tr><td colspan="8" class="text-center text-muted py-3">Không có tài liệu nào</td></tr>');
        return;
      }

      const loaiMap = {
        "quyen_tre_em": "Quyền trẻ em",
        "chong_bao_hanh": "Chống bạo hành",
        "giao_duc_an_toan": "Giáo dục an toàn"
      };

      const roleMap = { 2: "Chuyên gia", 3: "Quản trị viên" };

      const rows = items.map(item => `
        <tr>
          <td>${item.id}</td>
          <td>${item.tieu_de}</td>
          <td>${item.nguoi_dang || 'Không rõ'}</td>
          <td>${roleMap[item.vai_tro_id] || 'Không xác định'}</td>
          <td>${loaiMap[item.loai_tai_lieu] || item.loai_tai_lieu}</td>
          <td>${item.file_url ? `<a href="${item.file_url}" target="_blank">📄 Xem</a>` : '—'}</td>
          <td>${item.ngay_upload}</td>
          <td>
            <button class="btn btn-sm btn-warning" onclick="edit(${item.id})">✏️</button>
            <button class="btn btn-sm btn-danger" onclick="xoa(${item.id})">🗑</button>
          </td>
        </tr>`).join('');

      $("#listTaiLieu").html(rows);
    },
    error: function() {
      $("#listTaiLieu").html('<tr><td colspan="8" class="text-center text-danger py-3">⚠️ Lỗi tải dữ liệu</td></tr>');
    }
  });
}

// 🟦 Lọc tự động
$("#filterVaiTro, #filterLoai").on("change", loadTaiLieu);

// 🟨 Sửa
function edit(id) {
  $.getJSON(API_BASE + "tailieu_list.php", res => {
    const item = (res.items || []).find(x => x.id == id);
    if (!item) return alert("Không tìm thấy tài liệu!");

    $("#edit_id").val(item.id);
    $("#edit_tieu_de").val(item.tieu_de);
    $("#edit_loai_tai_lieu").val(item.loai_tai_lieu);
    $("#edit_mo_ta").val(item.mo_ta);
    $("#edit_file_link").html(`📄 <a href="${item.file_url}" target="_blank">${item.file_name || 'Xem file hiện tại'}</a>`);

    new bootstrap.Modal(document.getElementById('modalSua')).show();
  });
}

// 🟢 Lưu cập nhật
$("#formSuaTaiLieu").on("submit", e => {
  e.preventDefault();
  $.ajax({
    url: API_BASE + "tailieu_save.php", // ✅ gộp thêm + sửa
    type: "POST",
    data: new FormData(e.target),
    processData: false,
    contentType: false,
    success: res => {
      if (typeof res === "string") res = JSON.parse(res);
      alert(res.message || "Cập nhật thành công");
      loadTaiLieu();
      bootstrap.Modal.getInstance(document.getElementById('modalSua')).hide();
    },
    error: () => alert("Không thể cập nhật tài liệu!")
  });
});

// 🟥 Xóa
function xoa(id) {
  if (!confirm("Bạn có chắc muốn xóa tài liệu này?")) return;
  $.post(API_BASE + "tailieu_xoa.php", { id }, res => {
    if (typeof res === "string") res = JSON.parse(res);
    alert(res.message || res.error || "Đã xóa");
    loadTaiLieu();
  });
}

// 🟢 Thêm mới
$("#formThemTaiLieu").on("submit", e => {
  e.preventDefault();
  $.ajax({
    url: API_BASE + "tailieu_save.php", // ✅ dùng chung file save
    type: "POST",
    data: new FormData(e.target),
    processData: false,
    contentType: false,
    success: res => {
      if (typeof res === "string") res = JSON.parse(res);
      alert(res.message || "Thêm thành công");
      e.target.reset();
      loadTaiLieu();
    },
    error: () => alert("Không thể gửi dữ liệu lên máy chủ!")
  });
});

// 🟦 Load danh sách ban đầu
loadTaiLieu();
</script>
</body>
</html>
