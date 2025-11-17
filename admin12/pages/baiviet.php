<?php
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$apiBase = $protocol . $host . "/php/bvte/admin12/BE/";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý Bài Viết</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/baiviet.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-4">
  <div class="header-btn">
    <h3>📰 Quản lý Bài Viết</h3>
    <a href="baidang.php" class="btn btn-back">🔙 Quay lại</a>
  </div>

  <!-- Form thêm bài viết -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">Thêm bài viết mới</div>
    <div class="card-body">
      <form id="formThemBaiViet" enctype="multipart/form-data">
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <input type="text" name="tieu_de" class="form-control" placeholder="Tiêu đề bài viết" required>
          </div>
          <div class="col-md-6">
            <input type="file" name="anh_dai_dien" class="form-control" accept="image/*" required>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Loại bài viết</label>
          <select name="loai_bai_viet" class="form-select" required>
            <option value="">-- Chọn loại bài viết --</option>
            <option value="tin_tuc_su_kien">Tin tức - Sự kiện</option>
            <option value="tieng_noi_cua_tre">Tiếng nói của trẻ</option>
            <option value="chia_se">Chia sẻ câu chuyện</option>
            <option value="kien_thuc_ki_nang">Kiến thức - Kĩ năng</option>
            <option value="sang_kien_cho_tre">Sáng kiến cho trẻ</option>
          </select>
        </div>
        <div class="mb-3">
          <textarea name="noi_dung" class="form-control" rows="4" placeholder="Nội dung bài viết..." required></textarea>
        </div>
        <div class="text-end">
          <button type="submit" class="btn btn-success">+ Thêm bài viết</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Bộ lọc -->
  <div class="filter-bar card p-3 mb-3 shadow-sm">
    <form id="filterForm" class="row g-3 align-items-center">
      <div class="col-md-4">
        <input type="text" id="searchInput" name="search" class="form-control" placeholder="🔍 Tìm kiếm theo tiêu đề...">
      </div>
      <div class="col-md-4">
        <select id="filterLoai" name="loai_bai_viet" class="form-select">
          <option value="">-- Tất cả loại bài viết --</option>
          <option value="tin_tuc_su_kien">Tin tức - Sự kiện</option>
          <option value="tieng_noi_cua_tre">Tiếng nói của trẻ</option>
          <option value="chia_se">Chia sẻ câu chuyện</option>
          <option value="kien_thuc_ki_nang">Kiến thức - Kĩ năng</option>
          <option value="sang_kien_cho_tre">Sáng kiến cho trẻ</option>
        </select>
      </div>
      <div class="col-md-3">
        <select id="filterVaiTro" name="vai_tro" class="form-select">
          <option value="">-- Tất cả vai trò --</option>
          <option value="2">Chuyên gia</option>
          <option value="3">Admin</option>
        </select>
      </div>
      <div class="col-md-1 d-flex">
        <button type="button" class="btn btn-secondary w-100" id="btnReset">↻</button>
      </div>
    </form>
  </div>

  <!-- Danh sách -->
  <div class="card shadow-sm">
    <div class="card-header bg-secondary text-white">Danh sách bài viết</div>
    <div class="card-body p-0">
      <table class="table table-bordered table-hover m-0">
        <thead class="table-light">
          <tr>
            <th>ID</th><th>Tiêu đề</th><th>Người đăng</th><th>Vai trò</th>
            <th>Ảnh đại diện</th><th>Loại bài viết</th><th>Ngày đăng</th><th>Hành động</th>
          </tr>
        </thead>
        <tbody id="listBaiViet">
          <tr><td colspan="8" class="text-center py-3">⏳ Đang tải...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal xem/sửa -->
<div class="modal fade" id="modalBaiViet" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="formSuaBaiViet" enctype="multipart/form-data">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title">📄 Chi tiết / Sửa bài viết</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="edit_id">
          <div class="mb-3">
            <label class="form-label">Tiêu đề</label>
            <input type="text" class="form-control" name="tieu_de" id="edit_tieu_de" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Loại bài viết</label>
            <select name="loai_bai_viet" id="edit_loai_bai_viet" class="form-select" required>
              <option value="">-- Chọn loại bài viết --</option>
              <option value="tin_tuc_su_kien">Tin tức - Sự kiện</option>
              <option value="tieng_noi_cua_tre">Tiếng nói của trẻ</option>
              <option value="chia_se">Chia sẻ câu chuyện</option>
              <option value="kien_thuc_ki_nang">Kiến thức - Kĩ năng</option>
              <option value="sang_kien_cho_tre">Sáng kiến cho trẻ</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Nội dung</label>
            <textarea class="form-control" name="noi_dung" id="edit_noi_dung" rows="5" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Ảnh đại diện hiện tại:</label><br>
            <img id="edit_anh_preview" src="" width="150" class="rounded mb-2 border">
            <input type="file" name="anh_dai_dien" class="form-control mt-2" accept="image/*">
          </div>
          <div id="edit_meta" class="small text-muted"></div>
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
const API_BASE = "<?= $apiBase ?>baiviet_action.php";

// ======== DANH SÁCH ========
function loadBaiViet() {
  $("#listBaiViet").html('<tr><td colspan="8" class="text-center py-3">⏳ Đang tải...</td></tr>');
  const search = $("#searchInput").val().trim();
  const loai = $("#filterLoai").val();
  const vai_tro = $("#filterVaiTro").val();

  $.getJSON(`${API_BASE}?action=list`, { search, loai, vai_tro }, function(res) {
    const items = res.data || [];
    if (!items.length)
      return $("#listBaiViet").html('<tr><td colspan="8" class="text-center text-muted py-3">Không có bài viết nào</td></tr>');

    const loaiMap = {
      "tin_tuc_su_kien": "Tin tức - Sự kiện",
      "tieng_noi_cua_tre": "Tiếng nói của trẻ",
      "chia_se": "Chia sẻ câu chuyện",
      "kien_thuc_ki_nang": "Kiến thức - Kĩ năng",
      "sang_kien_cho_tre": "Sáng kiến cho trẻ"
    };

    const rows = items.map(item => {
      const anh = item.anh_dai_dien ? `<img src="${item.anh_dai_dien}" class="img-thumbnail" style="max-height:60px;">` : '—';
      const badgeClass = item.vai_tro === 'Admin' ? 'bg-danger' : 'bg-info';
      return `
        <tr id="bv_${item.id}">
          <td>${item.id}</td>
          <td>${item.tieu_de}</td>
          <td>${item.nguoi_dang || 'Không rõ'}</td>
          <td><span class="badge ${badgeClass}">${item.vai_tro}</span></td>
          <td>${anh}</td>
          <td>${loaiMap[item.loai_bai_viet] || item.loai_bai_viet}</td>
          <td>${item.ngay_dang}</td>
          <td>
            <button class="btn btn-sm btn-primary" onclick="xemChiTiet(${item.id})">👁 Xem</button>
            <button class="btn btn-sm btn-danger" onclick="xoa(${item.id})">🗑 Xóa</button>
          </td>
        </tr>`;
    }).join('');
    $("#listBaiViet").html(rows);
  }).fail(() => $("#listBaiViet").html('<tr><td colspan="8" class="text-center text-danger">❌ Lỗi kết nối API</td></tr>'));
}

// ======== XEM CHI TIẾT ========
function xemChiTiet(id) {
  $.getJSON(`${API_BASE}?action=detail&id=${id}`, function(res) {
    if (!res || res.error) return alert(res.message || "Không thể tải chi tiết bài viết.");
    const b = res.data;
    $("#edit_id").val(b.id);
    $("#edit_tieu_de").val(b.tieu_de);
    $("#edit_noi_dung").val(b.noi_dung);
    $("#edit_loai_bai_viet").val(b.loai_bai_viet);
    $("#edit_anh_preview").attr("src", b.anh_dai_dien || "");
    $("#edit_meta").html(`Người đăng: <b>${b.nguoi_dang}</b> | Vai trò: ${b.vai_tro} | Ngày đăng: ${b.ngay_dang}`);
    new bootstrap.Modal('#modalBaiViet').show();
  }).fail(() => alert("❌ Không thể kết nối API chi tiết bài viết."));
}

// ======== THÊM BÀI VIẾT ========
$("#formThemBaiViet").on("submit", e => {
  e.preventDefault();
  $.ajax({
    url: `${API_BASE}?action=add`,
    type: "POST",
    data: new FormData(e.target),
    processData: false,
    contentType: false,
    success: res => {
      if (typeof res === "string") res = JSON.parse(res);
      alert(res.message);
      if (res.status === "success") { e.target.reset(); loadBaiViet(); }
    },
    error: () => alert("❌ Lỗi kết nối khi thêm bài viết!")
  });
});

// ======== SỬA BÀI VIẾT ========
$("#formSuaBaiViet").on("submit", e => {
  e.preventDefault();
  $.ajax({
    url: `${API_BASE}?action=update`,
    type: "POST",
    data: new FormData(e.target),
    processData: false,
    contentType: false,
    success: res => {
      if (typeof res === "string") res = JSON.parse(res);
      alert(res.message);
      if (res.status === "success") { loadBaiViet(); bootstrap.Modal.getInstance('#modalBaiViet').hide(); }
    }
  });
});

// ======== XÓA ========
function xoa(id) {
  if (!confirm("Bạn có chắc muốn xóa bài viết này?")) return;
  $.post(`${API_BASE}?action=delete`, { id }, res => {
    if (typeof res === "string") res = JSON.parse(res);
    alert(res.message);
    loadBaiViet();
  });
}

// ======== LỌC / RESET ========
$("#searchInput").on("input", function () {
  clearTimeout(window._searchTimeout);
  window._searchTimeout = setTimeout(() => loadBaiViet(), 400);
});
$("#filterLoai, #filterVaiTro").on("change", () => loadBaiViet());
$("#btnReset").on("click", () => { $("#searchInput").val(''); $("#filterLoai").val(''); $("#filterVaiTro").val(''); loadBaiViet(); });

// ======== KHỞI ĐỘNG ========
loadBaiViet();
</script>
</body>
</html>
