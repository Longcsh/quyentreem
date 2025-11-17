<?php
// admin_tuvan.php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>📋 Quản lý tư vấn - Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <style>
    :root {
      --main-blue: #2563eb;
      --blue-gradient: linear-gradient(135deg, #2563eb, #1d4ed8);
      --white: #ffffff;
      --shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
      --radius: 14px;
    }
    body { background: #f8f9fa; margin:0; padding:0; font-family: 'Segoe UI', sans-serif; }
    header {
      background: var(--blue-gradient);
      color: white;
      padding: 18px 40px;
      display: flex; justify-content: space-between; align-items: center;
      box-shadow: var(--shadow);
      border-bottom-left-radius: var(--radius);
      border-bottom-right-radius: var(--radius);
    }
    header h1 { font-size: 1.6rem; font-weight: 600; margin: 0; }
    .header-right { display: flex; align-items: center; gap: 12px; font-weight: 500; }
    .back-btn {
      background: var(--white);
      color: var(--main-blue);
      border: none; padding: 8px 16px; border-radius: 10px;
      font-weight: 600; cursor: pointer;
      box-shadow: 0 3px 8px rgba(0,0,0,0.15);
      transition: all 0.25s ease;
    }
    .back-btn:hover { background: #eff6ff; transform: translateY(-2px); }
    .table-wrapper { overflow-x: auto; }
    table th, table td { vertical-align: middle !important; }
    .btn { min-width: 90px; }
    .filter-bar {
      display:flex; flex-wrap:wrap; gap:10px;
      justify-content:space-between; align-items:center;
      margin-bottom:15px;
    }
    .filter-bar select, .filter-bar input { min-width:200px; }
  </style>
</head>
<body>

<header>
  <h1>📋 Quản lý tư vấn</h1>
  <div class="header-right">
    <button class="back-btn" onclick="window.location.href='../../admin.php'">⬅️ Quay lại</button>
    <span>Xin chào, <b>Admin</b> 👋</span>
  </div>
</header>

<div class="container mt-4">

  <!-- Bộ lọc + tìm kiếm -->
  <div class="filter-bar">
    <div class="input-group" style="max-width: 320px;">
      <input type="text" id="searchInput" class="form-control" placeholder="🔍 Tìm theo người dùng hoặc câu hỏi...">
      <button class="btn btn-outline-primary" onclick="filterQuestions()">Tìm</button>
    </div>
    <div class="d-flex gap-2">
      <select id="statusFilter" class="form-select" onchange="filterQuestions()">
        <option value="">-- Trạng thái --</option>
        <option value="dang_cho_tra_loi">Đang Chờ trả lời</option>
        <option value="da_tra_loi">Đã trả lời</option>
      </select>
      <select id="chuyenmonFilter" class="form-select" onchange="filterQuestions()">
        <option value="">-- Chuyên môn --</option>
      </select>
    </div>
  </div>

  <!-- Bảng câu hỏi chính -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white fw-bold">Câu hỏi người dùng</div>
    <div class="card-body table-wrapper">
      <table class="table table-bordered table-hover" id="tblQuestions">
        <thead class="table-secondary">
          <tr>
            <th>ID</th><th>Người dùng</th><th>Câu hỏi</th>
            <th>Ngày gửi</th><th>Trạng thái</th><th>Chuyên môn</th>
            <th>Chuyên gia</th><th>Hành động</th>
          </tr>
        </thead>
        <tbody><tr><td colspan="8" class="text-center">Đang tải dữ liệu...</td></tr></tbody>
      </table>
    </div>
  </div>

  <!-- 🟨 Câu hỏi cần phân công lại -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-warning fw-bold text-dark">🌀 Câu hỏi cần phân công lại</div>
    <div class="card-body table-wrapper">
      <table class="table table-bordered table-hover align-middle" id="tblReassign">
        <thead class="table-light">
          <tr>
          <th>ID</th><th>Người dùng</th><th>Chuyên môn</th><th>Câu hỏi</th><th>Lý do từ chối</th><th>Phân công</th>
          </tr>
        </thead>
        <tbody><tr><td colspan="6" class="text-center text-muted">Không có câu hỏi cần phân công lại</td></tr></tbody>
      </table>
    </div>
  </div>

  <!-- 🟥 Câu hỏi bị từ chối -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-danger text-white fw-bold">❌ Câu hỏi bị từ chối (Không có chuyên gia phù hợp)</div>
    <div class="card-body table-wrapper">
      <table class="table table-bordered table-hover align-middle" id="tblRejected">
        <thead class="table-light">
          <tr>
            <th>ID</th><th>Người dùng</th><th>Chuyên môn</th><th>Câu hỏi</th>
            <th>Lý do</th><th>Ngày gửi</th>
          </tr>
        </thead>
        <tbody><tr><td colspan="6" class="text-center text-muted">Không có câu hỏi bị từ chối</td></tr></tbody>
      </table>
    </div>
  </div>

  <!-- Thống kê -->
  <div class="card shadow-sm mb-5">
    <div class="card-header bg-success text-white fw-bold">Thống kê theo chuyên môn</div>
    <div class="card-body table-wrapper">
      <table class="table table-bordered table-hover" id="tblSessions">
        <thead class="table-secondary"><tr><th>Chuyên môn</th><th>Số câu hỏi</th></tr></thead>
        <tbody><tr><td colspan="2" class="text-center">Đang tải dữ liệu...</td></tr></tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal chọn chuyên gia -->
<div class="modal fade" id="assignModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">🔄 Chọn chuyên gia để phân công</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <<div class="modal-body">
  <table class="table table-bordered align-middle">
    <thead><tr><th>Họ tên</th><th>Email</th><th>Đang xử lý</th><th>Chọn</th></tr></thead>
    <tbody id="tblExperts"><tr><td colspan="4" class="text-center text-muted">Đang tải...</td></tr></tbody>
  </table>
</div>
<div class="modal-footer d-flex justify-content-between">
  <button class="btn btn-danger" id="btnRejectAll">❌ Từ chối phân công</button>
  <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
</div>

    </div>
  </div>
</div>

<!-- Modal xem chi tiết -->
<div class="modal fade" id="detailModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">Chi tiết câu hỏi</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="detailContent"><p>Đang tải dữ liệu...</p></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const BASE_URL = '../BE/';
let allQuestions = [];
let currentTuvanId = null;

function safeJSON(raw) {
  try { return typeof raw === 'object' ? raw : JSON.parse(raw); }
  catch { return { success: false, message: 'Phản hồi không hợp lệ từ server' }; }
}

/* ================== DANH SÁCH CHÍNH ================== */
function loadQuestions() {
  $.get(BASE_URL + 'cauhoi_ds.php', function(raw) {
    const res = safeJSON(raw);
    if (!(res.success || res.status === 'success')) {
      return $('#tblQuestions tbody').html(`<tr><td colspan="8" class="text-danger text-center">${res.message || 'Không có dữ liệu'}</td></tr>`);
    }
    allQuestions = res.data; renderQuestions(allQuestions);
  }).fail(() => {
    $('#tblQuestions tbody').html(`<tr><td colspan="8" class="text-danger text-center">Không thể kết nối máy chủ</td></tr>`);
  });
}

function renderQuestions(data) {
  // 🧩 Lọc bỏ các câu hỏi có trạng thái 'tu_choi' hoặc 'bi_tu_choi'
  const filtered = data.filter(r => r.trang_thai !== 'tu_choi' && r.trang_thai !== 'bi_tu_choi');

  const rows = filtered.map(r => `
    <tr>
      <td>${r.id}</td>
      <td>${r.ten_nguoi_dung || ''}</td>
      <td>${r.cau_hoi || ''}</td>
      <td>${r.ngay_gui || ''}</td>
      <td>${r.trang_thai || ''}</td>
      <td>${r.ten_chuyen_mon || ''}</td>
      <td>${r.ten_chuyen_gia || 'Chưa có'}</td>
      <td>
        <button class='btn btn-outline-info btn-sm' onclick='viewDetail(${r.id})'>Xem</button>
        <button class='btn btn-outline-danger btn-sm' onclick='deleteQuestion(${r.id})'>Xóa</button>
      </td>
    </tr>`).join('');

  $('#tblQuestions tbody').html(
    rows || `<tr><td colspan="8" class="text-center text-muted">Không có câu hỏi nào</td></tr>`
  );
}

/* ================== LỌC ================== */
function filterQuestions() {
  const text = $('#searchInput').val().toLowerCase();
  const status = $('#statusFilter').val();
  const cm = $('#chuyenmonFilter').val();
  const filtered = allQuestions.filter(q => {
    const matchText = q.cau_hoi?.toLowerCase().includes(text) || q.ten_nguoi_dung?.toLowerCase().includes(text);
    const matchStatus = status ? q.trang_thai === status : true;
    const matchCM = cm ? (q.ten_chuyen_mon === cm) : true;
    return matchText && matchStatus && matchCM;
  });
  renderQuestions(filtered);
}

function loadChuyenMon() {
  $.get(BASE_URL + 'chuyenmon_list.php', function(raw) {
    const res = safeJSON(raw);
    if (!res.success || !res.data) return;
    const options = res.data.map(c => `<option value="${c.ten_chuyen_mon}">${c.ten_chuyen_mon}</option>`).join('');
    $('#chuyenmonFilter').append(options);
  });
}

/* ================== CHI TIẾT / XÓA ================== */
function viewDetail(id) {
  $.get(BASE_URL + 'cauhoi_chitiet.php', { id }, function(raw) {
    const res = safeJSON(raw);
    if (!(res.success || res.status === 'success') || !res.data)
      return alert('❌ Không tải được chi tiết câu hỏi.');
    const d = res.data;
    let html = `
      <div class="row">
        <div class="col-md-7">
          <p><strong>📌 Câu hỏi:</strong> ${d.cau_hoi || '(Không có nội dung)'}</p>
          <p><strong>👤 Người hỏi:</strong> ${d.nguoi_dung || 'Ẩn danh'}</p>
          <p><strong>📚 Chuyên môn:</strong> ${d.ten_chuyen_mon || 'Không xác định'}</p>
          <p><strong>⚙️ Trạng thái:</strong> ${d.trang_thai || 'Không rõ'}</p>
          <p><strong>👨‍⚕️ Chuyên gia:</strong> ${d.chuyen_gia || 'Chưa có'}</p>
        </div>
        <div class="col-md-5 text-center">
          ${d.anh_minh_hoa ? `<img src="../../${d.anh_minh_hoa}" class="img-fluid rounded shadow-sm border" style="max-height:300px;object-fit:cover;">` : '<em>Không có ảnh minh họa</em>'}
        </div>
      </div>`;
    $('#detailContent').html(html);
    new bootstrap.Modal(document.getElementById('detailModal')).show();
  });
}

function deleteQuestion(id) {
  if (!confirm('Bạn có chắc muốn xóa câu hỏi này không?')) return;
  $.post(BASE_URL + 'cauhoi_xoa.php', { id }, function(res) {
    const data = safeJSON(res);
    alert(data.message || 'Đã xử lý');
    if (data.success) loadQuestions();
  }, 'json');
}

/* ================== DANH SÁCH PHÂN CÔNG LẠI ================== */
function loadReassignList() {
  $.get(BASE_URL + 'tuvan_action.php?action=list_canphancong', function(raw) {
    const res = safeJSON(raw);
    if (!(res.success || res.status === 'success')) {
      return $('#tblReassign tbody').html(`<tr><td colspan="6" class="text-danger text-center">${res.message || 'Không có dữ liệu'}</td></tr>`);
    }
    const rows = res.data.map(r => `
      <tr>
        <td>${r.id}</td>
        <td>${r.ten_nguoi_dung || ''}</td>
        <td>${r.ten_chuyen_mon || ''}</td>
        <td>${r.cau_hoi || ''}</td>
        <td>${r.ten_chuyen_gia_tu_choi || '(Chưa có)'}</td>
<td>${r.ly_do_tu_choi || '(Không có)'}</td>

        <td><button class='btn btn-sm btn-success' onclick='openAssignModal(${r.id})'>🔄 Phân công lại</button></td>
      </tr>`).join('');
    $('#tblReassign tbody').html(rows || `<tr><td colspan="6" class="text-center text-muted">Không có câu hỏi cần phân công lại</td></tr>`);
  });
}

/* ================== MODAL CHỌN CHUYÊN GIA ================== */
function openAssignModal(id) {
  currentTuvanId = id;
  $('#assignModal').modal('show');
  $('#tblExperts').html(`<tr><td colspan="4" class="text-center text-muted">Đang tải...</td></tr>`);
  $.get(BASE_URL + 'tuvan_action.php?action=list_chuyengia&tuvan_id=' + id, function(raw) {
    const res = safeJSON(raw);
    if (!res.success || !res.data.length)
      return $('#tblExperts').html('<tr><td colspan="4" class="text-center text-danger">Không có chuyên gia phù hợp</td></tr>');
    const rows = res.data.map(e => `
  <tr>
    <td>${e.ho_ten}</td>
    <td>${e.email}</td>
    <td>${e.so_cau_hoi}</td>
    <td>
    <td><button class='btn btn-sm btn-primary' onclick='assignExpert(${e.id})'>Chọn</button></td>

    </td>
  </tr>`).join('');
$('#tblExperts').html(rows);

  });
}

function assignExpert(expertId) {
  if (!confirm('Xác nhận phân công câu hỏi này cho chuyên gia đã chọn?')) return;
  $.post(BASE_URL + 'tuvan_action.php?action=phancong', { tuvan_id: currentTuvanId, chuyen_gia_id: expertId }, function(res) {
    const data = safeJSON(res);
    alert(data.message || 'Đã xử lý');
    if (data.success) {
      $('#assignModal').modal('hide');
      loadReassignList();
      loadQuestions();
    }
  });
}
function rejectExpert(expertId) {
  const reason = prompt("Nhập lý do từ chối (ví dụ: Chuyên gia đang xử lý quá nhiều câu hỏi):", "Quá tải công việc");
  if (reason === null) return; // user bấm hủy
  if (!reason.trim()) return alert("Bạn cần nhập lý do cụ thể!");
  
  $.post(BASE_URL + 'tuvan_action.php?action=tu_choi_phan_cong', 
    { tuvan_id: currentTuvanId, chuyen_gia_id: expertId, ly_do_tu_choi: reason }, 
    function(res) {
      const data = safeJSON(res);
      alert(data.message || 'Đã xử lý');
      if (data.success) {
        $('#assignModal').modal('hide');
        loadReassignList();
        loadRejectedList();
      }
    }, 'json'
  );
}

/* ================== DANH SÁCH TỪ CHỐI ================== */
function loadRejectedList() {
  $.get(BASE_URL + 'tuvan_action.php?action=list_bitu_choi', function(raw) {
    const res = safeJSON(raw);
    const rows = res.success && res.data?.length
      ? res.data.map(r => `
        <tr><td>${r.id}</td><td>${r.ten_nguoi_dung}</td>
        <td>${r.ten_chuyen_mon}</td><td>${r.cau_hoi}</td>
        <td>${r.ly_do_tu_choi}</td><td>${r.ngay_gui}</td></tr>`).join('')
      : `<tr><td colspan="6" class="text-center text-muted">Không có câu hỏi bị từ chối</td></tr>`;
    $('#tblRejected tbody').html(rows);
  });
}

/* ================== THỐNG KÊ ================== */
function loadSessions() {
  $.get(BASE_URL + 'thong_ke_tu_van.php', function(raw) {
    const res = safeJSON(raw);
    if (!(res.success || res.status === 'success')) {
      return $('#tblSessions tbody').html(`<tr><td colspan="2" class="text-danger text-center">${res.message || 'Không có dữ liệu thống kê'}</td></tr>`);
    }
    const stats = res.data?.theo_chuyen_mon || res.theo_chuyen_mon || [];
    const rows = stats.map(r => `<tr><td>${r.ten_chuyen_mon}</td><td>${r.so_cau_hoi}</td></tr>`).join('');
    $('#tblSessions tbody').html(rows || `<tr><td colspan="2" class="text-center text-muted">Không có dữ liệu</td></tr>`);
  });
}
// 🟥 Nút "Từ chối phân công" trong modal
$('#btnRejectAll').click(function() {
  const reason = prompt("Nhập lý do từ chối phân công (ví dụ: Không có chuyên gia phù hợp hoặc tất cả đều quá tải):", "Không có chuyên gia phù hợp");
  if (reason === null) return;
  if (!reason.trim()) return alert("Bạn cần nhập lý do cụ thể!");

  $.post(BASE_URL + 'tuvan_action.php?action=tu_choi_phan_cong', 
    { tuvan_id: currentTuvanId, chuyen_gia_id: 0, ly_do_tu_choi: reason },
    function(res) {
      const data = safeJSON(res);
      alert(data.message || 'Đã xử lý');
      if (data.success) {
        $('#assignModal').modal('hide');
        loadReassignList();
        loadRejectedList();
      }
    }, 'json'
  );
});


/* ================== KHỞI CHẠY ================== */
$(document).ready(function() {
  loadQuestions();
  loadChuyenMon();
  loadSessions();
  loadReassignList();
  loadRejectedList();
});
</script>
</body>
</html>
