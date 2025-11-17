<?php
// === HÀM GỌI API === //
function callAPI($url) {
    $res = @file_get_contents($url);
    if ($res === false) return [];
    $json = json_decode($res, true);
    if (isset($json['data'])) return $json['data'];
    return $json ?? [];
}

// === TỰ ĐỘNG XÁC ĐỊNH API BASE URL === //
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);

// Lùi một cấp để tìm thư mục BE
$apiBase = rtrim($protocol . "://" . $host . $scriptDir, "/") . "/../BE/";
$apiBase = str_replace('\\', '/', $apiBase); // tránh lỗi Windows path

// === GỌI API === //
$donations = callAPI($apiBase . "donate_list.php");
$statsResponse = callAPI($apiBase . "donate_stats.php");

$stats = [
    'tong_so_tien' => $statsResponse['donations_total'] ?? 0,
    'hom_nay'      => $statsResponse['donations_today'] ?? 0,
    'tong_luot'    => $statsResponse['donations_count'] ?? 0
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>💰 Quản lý tài chính - Quyền Trẻ Em</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/taichinh.css?v=<?= time() ?>"> <!-- ✅ GỌI CSS RIÊNG -->
</head>
<body>

<header>
  <h1>💰 Quản lý tài chính - Quyền Trẻ Em</h1>
  <div style="display:flex;align-items:center;gap:15px;">
    <button class="back-btn" onclick="window.location.href='../../admin.php'">⬅️ Quay lại</button>
    <span>Xin chào, Admin 👋</span>
  </div>
</header>

<div class="container">

  <!-- ===== Thống kê ===== -->
  <div class="stats-box">
    <div class="stat">
      <h3>Tổng số tiền đã nhận</h3>
      <p><?= number_format($stats['tong_so_tien'], 0, ',', '.') ?> ₫</p>
    </div>
    <div class="stat">
      <h3>Số tiền hôm nay</h3>
      <p><?= number_format($stats['hom_nay'], 0, ',', '.') ?> ₫</p>
    </div>
    <div class="stat">
      <h3>Tổng lượt ủng hộ</h3>
      <p><?= $stats['tong_luot'] ?></p>
    </div>
  </div>

  <!-- ===== Thanh tìm kiếm & hành động ===== -->
  <div class="actions">
    <input type="text" id="searchInput" placeholder="🔍 Tìm kiếm theo tên hoặc email...">
    <div style="display:flex;gap:10px;">
      <button onclick="showAddForm()">➕ Thêm ủng hộ</button>
      <button onclick="exportCSV()">📄 Xuất báo cáo</button>
    </div>
  </div>

  <!-- ===== Bảng danh sách ===== -->
  <table>
    <thead>
      <tr>
        <th>Họ tên</th>
        <th>Email</th>
        <th>Số tiền (₫)</th>
        <th>Lời nhắn</th>
        <th>Ngày ủng hộ</th>
        <th>Ẩn danh</th>
      </tr>
    </thead>
    <tbody id="donateBody">
      <?php if (empty($donations)): ?>
        <tr><td colspan="6" class="loading">Chưa có ủng hộ nào</td></tr>
      <?php else: foreach ($donations as $d): ?>
        <tr>
          <td><?= htmlspecialchars($d['ho_ten']) ?></td>
          <td><?= htmlspecialchars($d['email']) ?></td>
          <td><?= number_format($d['so_tien'], 0, ',', '.') ?></td>
          <td><?= htmlspecialchars($d['loi_nhan']) ?></td>
          <td><?= htmlspecialchars($d['ngay_ung_ho']) ?></td>
          <td><?= $d['an_danh'] ? '✅ Có' : '❌ Không' ?></td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>

<!-- ===== Form thêm mới ===== -->
<div id="addForm" class="overlay-form">
  <div class="form-container">
    <h3>➕ Thêm ủng hộ mới</h3>
    <form id="donateForm">
      <label>Họ tên:</label><br>
      <input type="text" name="ho_ten" required><br><br>

      <label>Email:</label><br>
      <input type="email" name="email"><br><br>

      <label>Số tiền (₫):</label><br>
      <input type="number" name="so_tien" required><br><br>

      <label>Lời nhắn:</label><br>
      <textarea name="loi_nhan" rows="3"></textarea><br><br>

      <label><input type="checkbox" name="an_danh" value="1"> Ẩn danh</label><br><br>

      <div style="text-align:right;">
        <button type="button" onclick="hideAddForm()">Hủy</button>
        <button type="submit">Lưu</button>
      </div>
    </form>
  </div>
</div>

<script>
const API_BASE = <?= json_encode($apiBase) ?>;

// --- Xuất CSV ---
function exportCSV() {
  window.open(API_BASE + "donate_export.php", "_blank");
}

// --- Tìm kiếm theo tên hoặc email ---
document.getElementById("searchInput").addEventListener("keyup", e => {
  const val = e.target.value.trim().toLowerCase();

  fetch(API_BASE + "donate_list.php")
    .then(r => r.json())
    .then(rows => {
      const tb = document.getElementById("donateBody");
      tb.innerHTML = "";
      if (!rows.length) {
        tb.innerHTML = "<tr><td colspan='6' class='loading'>Không có dữ liệu</td></tr>";
        return;
      }

      const filtered = rows.filter(d => 
        d.ho_ten.toLowerCase().includes(val) ||
        (d.email && d.email.toLowerCase().includes(val))
      );

      if (!filtered.length) {
        tb.innerHTML = "<tr><td colspan='6' class='loading'>Không có kết quả phù hợp</td></tr>";
        return;
      }

      filtered.forEach(d => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${d.ho_ten}</td>
          <td>${d.email}</td>
          <td>${Number(d.so_tien).toLocaleString('vi-VN')}</td>
          <td>${d.loi_nhan ?? ''}</td>
          <td>${d.ngay_ung_ho}</td>
          <td>${d.an_danh == 1 ? '✅ Có' : '❌ Không'}</td>`;
        tb.appendChild(tr);
      });
    });
});

// --- Hiển thị form thêm mới ---
function showAddForm() {
  document.getElementById("addForm").style.display = "flex";
}
function hideAddForm() {
  document.getElementById("addForm").style.display = "none";
}

// --- Thêm ủng hộ mới ---
document.getElementById("donateForm").addEventListener("submit", e => {
  e.preventDefault();
  const formData = new FormData(e.target);
  fetch(API_BASE + "donate_add.php", { method: "POST", body: formData })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        alert("✅ Thêm ủng hộ thành công!");
        hideAddForm();
        location.reload();
      } else {
        alert("❌ Lỗi: " + (res.error || "Không rõ"));
      }
    })
    .catch(err => alert("Lỗi kết nối: " + err));
});
</script>
</body>
</html>
