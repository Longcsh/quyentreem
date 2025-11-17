<?php
// File: admin12/pages/chuyengia.php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý chuyên gia</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/chuyengia.css">
</head>
<body>

<header>
  <h1>👨‍⚕️ Quản lý chuyên gia</h1>
  <div class="header-right">
    <button class="back-btn" onclick="window.history.back()">⬅️ Quay lại</button>
    <div>Xin chào, Admin 👋</div>
  </div>
</header>

<div class="container">
  <div class="actions">
    <div class="actions-left">
      <input id="searchInput" type="text" placeholder="🔍 Tìm kiếm chuyên gia...">
      <select id="filterChuyenMon" class="filter-select">
        <option value="">Tất cả chuyên môn</option>
      </select>
      <select id="statusFilter" class="filter-select">
        <option value="">Tất cả trạng thái</option>
        <option value="Hoạt động">Hoạt động</option>
        <option value="Bị khóa">Bị khóa</option>
      </select>
    </div>
    <div class="actions-right">
      <button class="add-btn" onclick="openAddModal()">➕ Thêm chuyên gia</button>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Họ tên</th>
        <th>Email</th>
        <th>Điện thoại</th>
        <th>Chuyên môn</th>
        <th>Trạng thái</th>
        <th>Ngày tạo</th>
        <th>Hành động</th>
      </tr>
    </thead>
    <tbody id="expertTableBody" class="loading">⏳ Đang tải dữ liệu...</tbody>
  </table>
</div>

<!-- 🔽 Modal thêm chuyên gia -->
<div id="addModal" class="modal">
  <div class="modal-content">
    <h2>➕ Thêm chuyên gia</h2>
    <form id="addExpertForm">
      <label>Họ tên *</label>
      <input name="ho_ten" required>

      <label>Email *</label>
      <input type="email" name="email" required>

      <label>Tên đăng nhập *</label>
      <input name="ten_dang_nhap" required>

      <label>Mật khẩu *</label>
      <input type="password" name="mat_khau" required>

      <label>Điện thoại</label>
      <input name="so_dien_thoai">

      <label>Địa chỉ</label>
      <input name="dia_chi">

      <label>Chuyên môn *</label>
      <select name="chuyen_mon_id" id="chuyenMonSelect" required></select>

      <div style="text-align:center; margin-top:12px;">
        <button type="submit" class="btn-submit">💾 Lưu</button>
        <button type="button" class="btn-cancel" onclick="closeAddModal()">Hủy</button>
      </div>
    </form>
  </div>
</div>

<script>
function showToast(msg, type = 'info') {
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerText = msg;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 3000);
}

/* ==== Modal ==== */
function openAddModal() { document.getElementById('addModal').style.display = 'flex'; }
function closeAddModal() { document.getElementById('addModal').style.display = 'none'; }

/* ==== Load danh sách chuyên môn ==== */
async function loadChuyenMon() {
  try {
    const res = await fetch('../BE/chuyengia_lcm.php');
    const data = await res.json();
    if (data.success) {
      const selects = [document.getElementById('filterChuyenMon'), document.getElementById('chuyenMonSelect')];
      data.data.forEach(cm => {
        selects.forEach(sel => {
          const opt = document.createElement('option');
          opt.value = cm.id;
          opt.textContent = cm.ten_chuyen_mon;
          sel.appendChild(opt);
        });
      });
    }
  } catch (e) { console.error(e); }
}

/* ==== Load danh sách chuyên gia ==== */
async function loadExperts(search = '', chuyenmon = '', status = '') {
  const tbody = document.getElementById('expertTableBody');
  tbody.innerHTML = `<tr><td colspan="8" class="loading">⏳ Đang tải dữ liệu...</td></tr>`;

  try {
    const res = await fetch(`../BE/chuyengia_action.php?action=list&timkiem=${encodeURIComponent(search)}&chuyenmon=${encodeURIComponent(chuyenmon)}&trangthai=${encodeURIComponent(status)}`);
    const data = await res.json();

    if (!data.success) throw new Error(data.message || 'Không thể tải dữ liệu.');
    if (data.data.length === 0) {
      tbody.innerHTML = `<tr><td colspan="8" class="loading">Không có chuyên gia nào phù hợp.</td></tr>`;
      return;
    }

    tbody.innerHTML = data.data.map(cg => `
      <tr>
        <td>${cg.id}</td>
        <td><b>${cg.ho_ten}</b></td>
        <td>${cg.email}</td>
        <td>${cg.so_dien_thoai || '-'}</td>
        <td>${cg.ten_chuyen_mon}</td>
        <td><span class="badge ${cg.trang_thai === 'Hoạt động' ? 'badge-success' : 'badge-secondary'}">${cg.trang_thai}</span></td>
        <td>${cg.ngay_tao || '-'}</td>
        <td>
          <button class="action-btn" title="Đổi trạng thái" onclick="toggleStatus(${cg.id}, '${cg.trang_thai}')">🔁</button>
          <button class="action-btn delete" title="Xóa chuyên gia" onclick="deleteExpert(${cg.id})">🗑️</button>
        </td>
      </tr>
    `).join('');
  } catch (err) {
    tbody.innerHTML = `<tr><td colspan="8" class="loading">${err.message}</td></tr>`;
  }
}

/* ==== Đổi trạng thái chuyên gia ==== */
async function toggleStatus(id, currentStatus) {
  const newStatus = currentStatus === 'Hoạt động' ? 'Bị khóa' : 'Hoạt động';
  if (!confirm(`Bạn có chắc muốn chuyển sang trạng thái "${newStatus}" không?`)) return;

  try {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('trangthai', currentStatus);

    const res = await fetch('../BE/chuyengia_action.php?action=update', { method: 'POST', body: formData });
    const data = await res.json();

    if (data.success) {
      showToast(data.message, 'success');
      reloadData();
    } else showToast(data.message, 'error');
  } catch (e) {
    console.error(e);
    showToast('❌ Lỗi khi cập nhật trạng thái', 'error');
  }
}

/* ==== Thêm chuyên gia ==== */
document.getElementById('addExpertForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const formData = new FormData(e.target);
  try {
    const res = await fetch('../BE/chuyengia_action.php?action=add', { method: 'POST', body: formData });
    const data = await res.json();

    if (data.success) {
      showToast('✅ ' + data.message, 'success');
      closeAddModal();
      e.target.reset();
      reloadData();
    } else showToast('⚠️ ' + data.message, 'error');
  } catch (err) {
    console.error(err);
    showToast('❌ Lỗi khi thêm chuyên gia', 'error');
  }
});

/* ==== Xóa chuyên gia ==== */
async function deleteExpert(id) {
  if (!confirm("❗Bạn có chắc chắn muốn xóa chuyên gia này không?")) return;

  try {
    const res = await fetch("../BE/chuyengia_action.php?action=delete", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id })
    });
    const data = await res.json();

    if (data.success) {
      showToast(data.message || "✅ Đã xóa chuyên gia", "success");
      reloadData();
    } else showToast(data.message || "⚠️ Không thể xóa chuyên gia", "error");
  } catch (e) {
    console.error(e);
    showToast("❌ Lỗi khi xóa chuyên gia", "error");
  }
}

/* ==== Reload dữ liệu ==== */
function reloadData() {
  const search = document.getElementById('searchInput').value.trim();
  const chuyenmon = document.getElementById('filterChuyenMon').value;
  const status = document.getElementById('statusFilter').value;
  loadExperts(search, chuyenmon, status);
}

/* ==== Sự kiện ==== */
document.getElementById('searchInput').addEventListener('input', reloadData);
document.getElementById('filterChuyenMon').addEventListener('change', reloadData);
document.getElementById('statusFilter').addEventListener('change', reloadData);

/* ==== Khởi tạo ==== */
loadChuyenMon();
loadExperts();
</script>

</body>
</html>
