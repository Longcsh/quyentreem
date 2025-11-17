<?php
session_start();

// Giả lập quyền admin (nếu chưa có session)
if (empty($_SESSION['admin_id'])) {
    $_SESSION['admin_id'] = 1;
    $_SESSION['vai_tro_id'] = 3;
}

if (!in_array($_SESSION['vai_tro_id'], [3])) {
    echo "<script>alert('Bạn không có quyền truy cập trang này!'); window.location.href='/'</script>";
    exit;
}

$scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$basePath = '/php/bvte/admin12/BE/';
$apiBase = "{$scheme}://{$host}{$basePath}";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>💬 Quản lý bình luận</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/binhluan.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>💬 Quản lý bình luận</h3>
    <a href="baidang.php" class="btn btn-secondary btn-sm">⬅ Quay lại</a>
  </div>

  <!-- Bộ lọc -->
  <div class="filter-card">
    <label class="form-label mb-1 fw-semibold text-primary">📁 Lọc theo loại nội dung:</label>
    <select id="loai_noi_dung" class="form-select form-select-sm">
      <option value="">Tất cả</option>
      <optgroup label="📰 Bài viết">
        <option value="tin_tuc_su_kien">Tin tức – Sự kiện</option>
        <option value="tieng_noi_cua_tre">Tiếng nói của trẻ</option>
        <option value="chia_se_tu_cong_dong">Chia sẻ từ cộng đồng</option>
        <option value="kien_thuc_ki_nang">Kiến thức – Kĩ năng</option>
        <option value="sang_kien_cho_tre">Sáng kiến cho trẻ</option>
      </optgroup>
      <optgroup label="📚 Tài liệu">
        <option value="quyen_tre_em">Quyền trẻ em</option>
        <option value="chong_bao_hanh">Chống bạo hành</option>
        <option value="giao_duc_an_toan">Giáo dục an toàn</option>
      </optgroup>
    </select>
  </div>

  <!-- Danh sách bình luận -->
  <div id="commentList"></div>
</div>

<script>
const API = "<?= $apiBase ?>";

function loadComments() {
  const params = { loai_noi_dung: $('#loai_noi_dung').val() };
  $('#commentList').html('<div class="text-center text-secondary py-3">⏳ Đang tải bình luận...</div>');

  $.get(API + 'binh_luan_ds.php', params, function(res) {
    if (!res || !res.success) {
      $('#commentList').html('<div class="alert alert-danger text-center">❌ Lỗi tải dữ liệu!</div>');
      return;
    }

    const list = res.data || [];
    if (list.length === 0) {
      $('#commentList').html('<div class="no-comment">📭 Chưa có bình luận nào.</div>');
      return;
    }

    let html = '';
    list.forEach(c => {
      const avatarLetter = c.ten?.charAt(0)?.toUpperCase() || '?';
      html += `
      <div class="comment-card">
        <div class="comment-header">
          <div class="comment-info">
            <div class="avatar">${avatarLetter}</div>
            <div>
              <b>${c.ten}</b> <small class="text-muted">(${c.email})</small><br>
              <small class="text-secondary">${c.ngay_gio}</small>
            </div>
          </div>
          <div>
            <span class="badge bg-success">${c.trang_thai || 'hiện'}</span>
            <button class="btn btn-sm btn-outline-danger ms-2" onclick="deleteComment(${c.id})">🗑 Xóa</button>
          </div>
        </div>
        <hr>
        <p class="mb-2">${c.noi_dung}</p>
        ${c.admin_tra_loi ? `<div class="admin-reply"><b>Admin:</b> ${c.admin_tra_loi}</div>` : ""}
        <textarea id="reply_${c.id}" placeholder="✍️ Nhập phản hồi..."></textarea>
        <div class="text-end mt-2">
          <button class="btn btn-success btn-sm" onclick="replyAdmin(${c.id})">💬 Trả lời</button>
        </div>
      </div>`;
    });

    $('#commentList').html(html);
  }, 'json');
}

function replyAdmin(id) {
  const reply = $(`#reply_${id}`).val().trim();
  if (!reply) return alert("Vui lòng nhập nội dung phản hồi!");

  $.post(API + 'binh_luan_traloi.php', { id, admin_tra_loi: reply }, function(res) {
    alert(res.message || "✅ Đã phản hồi!");
    loadComments();
  }, 'json');
}

function deleteComment(id) {
  if (!confirm("Bạn có chắc muốn xóa bình luận này?")) return;
  $.post(API + 'binh_luan_xoa.php', { id }, function(res) {
    alert(res.message || "🗑 Đã xóa!");
    loadComments();
  }, 'json');
}

$('#loai_noi_dung').on('change', loadComments);
$(document).ready(loadComments);
</script>

</body>
</html>
