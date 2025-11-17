<?php
// File: admin12/pages/nguoidung.php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý người dùng</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/nguoidung.css">
</head>
<body>

<header>
  <h1>👥 Quản lý người dùng</h1>
  <div class="header-right">
    <button class="back-btn" onclick="window.history.back()">⬅️ Quay lại</button>
    <div>Xin chào, Admin 👋</div>
  </div>
</header>

<div class="container">
  <div class="actions">
    <input id="searchInput" type="text" placeholder="🔍 Tìm kiếm người dùng...">

    <!-- 🔽 Dropdown lọc trạng thái -->
    <select id="statusFilter" class="filter-select">
      <option value="">Tất cả trạng thái</option>
      <option value="Hoạt động">Hoạt động</option>
      <option value="Bị khóa">Bị khóa</option>
    </select>
  </div>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Tên đăng nhập</th>
        <th>Họ tên</th>
        <th>Email</th>
        <th>Điện thoại</th>
        <th>Vai trò</th>
        <th>Trạng thái</th>
        <th>Ngày tạo</th>
        <th>Hành động</th>
      </tr>
    </thead>
    <tbody id="userTableBody" class="loading">⏳ Đang tải dữ liệu...</tbody>
  </table>
</div>

<script>
function showToast(msg, type = 'info') {
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerText = msg;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 3000);
}

// ==== Load danh sách người dùng ====
async function loadUsers(search = '', status = '') {
  const tbody = document.getElementById('userTableBody');
  tbody.innerHTML = `<tr><td colspan="9" class="loading">⏳ Đang tải dữ liệu...</td></tr>`;

  try {
    const res = await fetch(`../BE/nguoidung_ds.php?search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}`);
    const data = await res.json();

    if (!data.success) throw new Error(data.error || 'Không thể tải dữ liệu.');

    if (data.items.length === 0) {
      tbody.innerHTML = `<tr><td colspan="9" class="loading">Không có người dùng nào phù hợp.</td></tr>`;
      return;
    }

    tbody.innerHTML = data.items.map(u => `
      <tr>
        <td>${u.id}</td>
        <td><b>${u.ten_dang_nhap}</b></td>
        <td>${u.ho_ten || '-'}</td>
        <td>${u.email}</td>
        <td>${u.so_dien_thoai || '-'}</td>
        <td><span class="badge badge-info">${u.ten_vai_tro}</span></td>
        <td><span class="badge ${u.trang_thai === 'Hoạt động' ? 'badge-success' : 'badge-secondary'}">${u.trang_thai}</span></td>
        <td>${u.ngay_tao || '-'}</td>
        <td>
          <button class="action-btn" title="Đổi trạng thái" onclick="toggleStatus(${u.id})">🔁</button>
          <button class="action-btn" title="Xóa" onclick="deleteUser(${u.id})">🗑️</button>
        </td>
      </tr>
    `).join('');
  } catch (err) {
    tbody.innerHTML = `<tr><td colspan="9" class="loading">${err.message}</td></tr>`;
  }
}

async function toggleStatus(id) {
  if (!confirm('Bạn có chắc muốn thay đổi trạng thái người dùng này?')) return;
  try {
    const res = await fetch(`../BE/nguoidung_trangthai.php?id=${id}`);
    const data = await res.json();
    if (data.success) {
      showToast(`✅ Trạng thái mới: ${data.newStatus}`, 'success');
      reloadData();
    } else throw new Error(data.error);
  } catch (e) {
    showToast(`❌ ${e.message}`, 'error');
  }
}

async function deleteUser(id) {
  if (!confirm('⚠️ Bạn có chắc muốn xóa người dùng này không?')) return;
  try {
    const res = await fetch(`../BE/nguoidung_xoa.php?id=${id}`);
    const data = await res.json();
    if (data.success) {
      showToast('🗑️ Người dùng đã bị xóa.', 'success');
      reloadData();
    } else throw new Error(data.error);
  } catch (e) {
    showToast(`❌ ${e.message}`, 'error');
  }
}

// === Cập nhật khi thay đổi ô tìm kiếm hoặc trạng thái ===
function reloadData() {
  const search = document.getElementById('searchInput').value.trim();
  const status = document.getElementById('statusFilter').value;
  loadUsers(search, status);
}

document.getElementById('searchInput').addEventListener('input', reloadData);
document.getElementById('statusFilter').addEventListener('change', reloadData);

// === Lần đầu tải ===
loadUsers();
</script>

</body>
</html>
