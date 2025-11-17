<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý chuyên môn</title>

<!-- Font Inter -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

<!-- Gọi file CSS riêng -->
<link rel="stylesheet" href="../css/chuyenmon.css">
</head>

<body>
  <div class="container">

    <div class="header-bar">
      <h2>Quản lý chuyên môn</h2>
      <a href="../../admin.php" class="back-btn">⬅️ Quay lại trang Admin</a>
    </div>

    <div class="form-section">
      <label>Tên chuyên môn:</label>
      <input id="ten" type="text" placeholder="Nhập tên chuyên môn">

      <label>Mô tả:</label>
      <textarea id="mo_ta" rows="3" placeholder="Nhập mô tả..."></textarea>

      <button onclick="addItem()">➕ Thêm chuyên môn</button>
    </div>

    <hr>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Tên chuyên môn</th>
          <th>Mô tả</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody id="tbody"></tbody>
    </table>

  </div>

  <script>
  async function loadData() {
    const res = await fetch('../be/chuyenmon_action.php?action=list');
    const json = await res.json();
    const tbody = document.getElementById('tbody');
    tbody.innerHTML = '';
    if (json.status === 'success') {
      json.data.forEach(cm => {
        tbody.innerHTML += `
          <tr id="row_${cm.id}">
            <td>${cm.id}</td>
            <td><input id="ten_${cm.id}" value="${cm.ten_chuyen_mon}" disabled></td>
            <td><textarea id="mo_${cm.id}" rows="2" disabled>${cm.mo_ta || ''}</textarea></td>
            <td>
              <button class="btn-edit" id="edit_${cm.id}" onclick="enableEdit(${cm.id})">✏️ Sửa</button>
              <button class="btn-delete" onclick="deleteItem(${cm.id})">🗑️ Xóa</button>
            </td>
          </tr>`;
      });
    }
  }

  function enableEdit(id) {
    const ten = document.getElementById('ten_'+id);
    const mo_ta = document.getElementById('mo_'+id);
    const btn = document.getElementById('edit_'+id);
    ten.disabled = false;
    mo_ta.disabled = false;
    ten.focus();
    btn.textContent = '💾 Lưu';
    btn.className = 'btn-save';
    btn.onclick = () => updateItem(id);
  }

  async function addItem() {
    const ten = document.getElementById('ten').value.trim();
    const mo_ta = document.getElementById('mo_ta').value.trim();
    if (!ten) return alert('Nhập tên chuyên môn!');
    const form = new FormData();
    form.append('action', 'add');
    form.append('ten_chuyen_mon', ten);
    form.append('mo_ta', mo_ta);
    const res = await fetch('../be/chuyenmon_action.php', { method: 'POST', body: form });
    const json = await res.json();
    alert(json.message);
    document.getElementById('ten').value = '';
    document.getElementById('mo_ta').value = '';
    loadData();
  }

  async function updateItem(id) {
    const ten = document.getElementById('ten_'+id).value.trim();
    const mo_ta = document.getElementById('mo_'+id).value.trim();
    const form = new FormData();
    form.append('action', 'update');
    form.append('id', id);
    form.append('ten_chuyen_mon', ten);
    form.append('mo_ta', mo_ta);
    const res = await fetch('../be/chuyenmon_action.php', { method: 'POST', body: form });
    const json = await res.json();
    alert(json.message);
    document.getElementById('ten_'+id).disabled = true;
    document.getElementById('mo_'+id).disabled = true;
    const btn = document.getElementById('edit_'+id);
    btn.textContent = '✏️ Sửa';
    btn.className = 'btn-edit';
    btn.onclick = () => enableEdit(id);
  }

  async function deleteItem(id) {
    if (!confirm('Xóa chuyên môn này?')) return;
    const res = await fetch(`../be/chuyenmon_action.php?action=delete&id=${id}`);
    const json = await res.json();
    alert(json.message);
    loadData();
  }

  window.onload = loadData;
  </script>
</body>
</html>
