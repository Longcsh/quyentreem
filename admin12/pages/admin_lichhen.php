<?php 
session_start();
require_once __DIR__ . '/../BE/db.php';
global $conn;

// 🔹 Lịch đã gán (Chờ hoặc đã xác nhận)
$stmt1 = $conn->prepare("
  SELECT lh.*, 
         u.ho_ten AS ten_user, 
         cg.ho_ten AS ten_chuyen_gia, 
         cm.ten_chuyen_mon
  FROM lich_hen lh
  LEFT JOIN tai_khoan u  ON lh.nguoi_dung_id = u.id
  LEFT JOIN tai_khoan cg ON lh.chuyen_gia_id = cg.id
  LEFT JOIN chuyen_mon cm ON lh.chuyen_mon_id = cm.id
  WHERE lh.trang_thai IN ('cho_xac_nhan', 'da_xac_nhan')
  ORDER BY lh.ngay_dat DESC
");
$stmt1->execute();
$lich_gan = $stmt1->fetchAll(PDO::FETCH_ASSOC);

// 🔸 Lịch chưa phân công hoặc bị từ chối
$stmt2 = $conn->prepare("
  SELECT lh.*, 
         u.ho_ten AS ten_user, 
         cm.ten_chuyen_mon,
         cg.ho_ten AS ten_chuyen_gia_cu
  FROM lich_hen lh
  LEFT JOIN tai_khoan u  ON lh.nguoi_dung_id = u.id
  LEFT JOIN chuyen_mon cm ON lh.chuyen_mon_id = cm.id
  LEFT JOIN tai_khoan cg ON lh.chuyen_gia_id = cg.id
  WHERE lh.trang_thai IN ('cho_phan_cong', 'bi_tu_choi')
  ORDER BY lh.ngay_dat DESC
");
$stmt2->execute();
$lich_chua = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>📅 Quản lý lịch hẹn</title>
<link rel="stylesheet" href="../css/admin.css">
<style>
:root {
  --main-blue: #2563eb;
  --blue-gradient: linear-gradient(135deg, #2563eb, #1d4ed8);
  --bg: #f5f7fa;
  --white: #ffffff;
  --shadow: 0 6px 18px rgba(0,0,0,0.08);
  --radius: 14px;
}

body { font-family: "Segoe UI", sans-serif; background: var(--bg); margin:0; padding:0; }

/* ===== HEADER GIỐNG DASHBOARD ===== */
header {
  background: var(--blue-gradient);
  color: white;
  padding: 18px 40px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: var(--shadow);
  border-bottom-left-radius: var(--radius);
  border-bottom-right-radius: var(--radius);
}

header h1 {
  font-size: 1.6rem;
  font-weight: 600;
  letter-spacing: 0.4px;
  margin: 0;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 12px;
  font-weight: 500;
}

.back-btn {
  background: var(--white);
  color: var(--main-blue);
  border: none;
  padding: 8px 16px;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 3px 8px rgba(0,0,0,0.15);
  transition: all 0.25s ease;
}
.back-btn:hover { background: #eff6ff; transform: translateY(-2px); }

/* ===== CONTENT GỐC (giữ nguyên logic) ===== */
.container { padding: 30px 40px; }

h2 { color: #2c3e50; border-left: 6px solid #3498db; padding-left: 10px; margin-top: 40px; }
table { width: 100%; border-collapse: collapse; margin-top: 15px; background: white; box-shadow: 0 3px 6px rgba(0,0,0,0.1); }
th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
th { background: #3498db; color: white; }
tr:hover { background: #f1f1f1; transition: 0.2s; }
button { border: none; padding: 6px 12px; border-radius: 4px; color: white; cursor: pointer; }
button:hover { opacity: 0.9; }
.btn-green { background-color: #2ecc71; }
.btn-yellow { background-color: #f1c40f; color: black; }
.btn-red { background-color: #e74c3c; }
select { padding: 6px; border-radius: 4px; border: 1px solid #ccc; }
.badge { padding: 4px 8px; border-radius: 6px; color: white; font-weight: 600; font-size: 13px; }
.bg-wait { background: #f1c40f; color: #000; }
.bg-confirm { background: #2ecc71; }
.bg-reject { background: #e74c3c; }
.bg-pending { background: #95a5a6; }
.filter-bar {
  display:flex; justify-content:center; gap:20px;
  margin-bottom:25px; flex-wrap:wrap;
}
.filter-bar select {
  padding:8px 10px; border-radius:6px; border:1px solid #ccc;
}
.filter-bar select:hover { border-color:#3498db; box-shadow:0 0 4px #3498db; }
.reset-btn {
  background:#95a5a6; color:white; border:none; border-radius:6px;
  padding:8px 12px; cursor:pointer;
}
.reset-btn:hover { opacity:0.85; }
</style>

<script>
// 🔹 Tải danh sách chuyên gia rảnh
async function loadChuyenGia(select, chuyenMonId, ngayGio, chuyenGiaCu) {
  try {
    const url = `../BE/lichhen_kiemtra.php?chuyen_mon_id=${chuyenMonId}&ngay_gio=${encodeURIComponent(ngayGio)}${chuyenGiaCu ? `&chuyen_gia_cu=${chuyenGiaCu}` : ''}`;
    const res = await fetch(url);
    if (!res.ok) throw new Error('Không thể tải danh sách chuyên gia');
    const data = await res.json();

    select.innerHTML = '<option value="">-- Chọn chuyên gia --</option>';
    if (data.success && data.count > 0) {
      data.data.forEach(cg => {
        select.innerHTML += `<option value="${cg.id}">${cg.ho_ten}</option>`;
      });
    } else {
      select.innerHTML = '<option value="">❌ Không có chuyên gia rảnh</option>';
    }
  } catch (err) {
    console.error(err);
    alert('Lỗi khi tải danh sách chuyên gia');
  }
}

function handleReject(event, form) {
  event.preventDefault();
  const reason = prompt("Nhập lý do từ chối lịch hẹn này:");
  if (reason === null) return false;
  form.querySelector('input[name="ly_do_tu_choi"]').value = reason.trim();
  form.submit();
  return true;
}

// 🔍 Bộ lọc
function applyFilter() {
  const mon = document.getElementById('filterChuyenMon').value.toLowerCase();
  const tt  = document.getElementById('filterTrangThai').value.toLowerCase();
  const rows = document.querySelectorAll("table tr[data-mon]");
  rows.forEach(row => {
    const monCell = row.dataset.mon.toLowerCase();
    const ttCell  = row.dataset.tt.toLowerCase();
    const matchMon = !mon || monCell.includes(mon);
    const matchTT  = !tt || ttCell.includes(tt);
    row.style.display = (matchMon && matchTT) ? "" : "none";
  });
}
function resetFilter() {
  document.getElementById('filterChuyenMon').value = "";
  document.getElementById('filterTrangThai').value = "";
  applyFilter();
}
</script>
</head>
<body>

<!-- ===== HEADER (giống dashboard) ===== -->
<header>
  <h1>📅 Quản lý lịch hẹn</h1>
  <div class="header-right">
    <button class="back-btn" onclick="window.location.href='../../admin.php'">⬅️ Quay lại</button>

    <span>Xin chào, <b>Admin</b> 👋</span>
  </div>
</header>

<div class="container">

<!-- 🔎 Thanh lọc -->
<div class="filter-bar">
  <div>
    <label><strong>🔍 Lọc theo chuyên môn:</strong></label>
    <select id="filterChuyenMon" onchange="applyFilter()">
      <option value="">-- Tất cả chuyên môn --</option>
      <?php
      $monStmt = $conn->query("SELECT DISTINCT ten_chuyen_mon FROM chuyen_mon ORDER BY ten_chuyen_mon ASC");
      while ($mon = $monStmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<option value="' . htmlspecialchars($mon['ten_chuyen_mon']) . '">' . htmlspecialchars($mon['ten_chuyen_mon']) . '</option>';
      }
      ?>
    </select>
  </div>

  <div>
    <label><strong>🧩 Lọc theo trạng thái:</strong></label>
    <select id="filterTrangThai" onchange="applyFilter()">
      <option value="">-- Tất cả trạng thái --</option>
      <option value="cho_xac_nhan">Chờ xác nhận</option>
      <option value="da_xac_nhan">Đã xác nhận</option>
      <option value="cho_phan_cong">Chờ phân công</option>
      <option value="bi_tu_choi">Bị từ chối</option>
    </select>
  </div>

  <button class="reset-btn" onclick="resetFilter()">🔁 Đặt lại lọc</button>
</div>

<!-- 🟩 Lịch đã gán -->
<h2>🔹 Lịch đã gán (Chờ hoặc đã xác nhận)</h2>
<table>
  <tr><th>ID</th><th>Người dùng</th><th>Chuyên môn</th><th>Chuyên gia</th><th>Thời gian</th><th>Trạng thái</th></tr>
  <?php if ($lich_gan): foreach ($lich_gan as $row): ?>
  <tr data-mon="<?= htmlspecialchars($row['ten_chuyen_mon']) ?>" data-tt="<?= htmlspecialchars($row['trang_thai']) ?>">
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['ten_user']) ?></td>
    <td><?= htmlspecialchars($row['ten_chuyen_mon']) ?></td>
    <td><?= htmlspecialchars($row['ten_chuyen_gia'] ?? '—') ?></td>
    <td><?= htmlspecialchars($row['ngay_gio']) ?></td>
    <td>
      <?php
      $cls = match($row['trang_thai']) {
        'cho_xac_nhan' => 'bg-wait',
        'da_xac_nhan'  => 'bg-confirm',
        default        => 'bg-pending',
      };
      ?>
      <span class="badge <?= $cls ?>"><?= $row['trang_thai'] ?></span>
    </td>
  </tr>
  <?php endforeach; else: ?>
  <tr><td colspan="6" style="text-align:center;">Không có lịch nào</td></tr>
  <?php endif; ?>
</table>

<!-- 🟦 Lịch chưa phân công -->
<h2>🟦 Lịch chưa phân công</h2>
<table>
  <tr><th>ID</th><th>Người dùng</th><th>Chuyên môn</th><th>Thời gian</th><th>Phân công thủ công</th><th>Hành động</th></tr>
  <?php
  $lich_cho = array_filter($lich_chua, fn($r) => $r['trang_thai'] === 'cho_phan_cong');
  if ($lich_cho): foreach ($lich_cho as $row): ?>
  <tr data-mon="<?= htmlspecialchars($row['ten_chuyen_mon']) ?>" data-tt="cho_phan_cong">
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['ten_user']) ?></td>
    <td><?= htmlspecialchars($row['ten_chuyen_mon']) ?></td>
    <td><?= htmlspecialchars($row['ngay_gio']) ?></td>
    <td>
      <form method="post" action="../BE/lichhen_phancong.php">
        <input type="hidden" name="lich_id" value="<?= $row['id'] ?>">
        <select name="chuyen_gia_id" id="cg_<?= $row['id'] ?>" required></select>
        <button type="button" class="btn-yellow" 
          onclick="loadChuyenGia(document.getElementById('cg_<?= $row['id'] ?>'),
          <?= $row['chuyen_mon_id'] ?>,'<?= $row['ngay_gio'] ?>',<?= $row['chuyen_gia_id'] ? $row['chuyen_gia_id'] : 'null' ?>)">
          🔄 Tải chuyên gia
        </button>
        <button type="submit" class="btn-green">✅ Gán</button>
      </form>
    </td>
    <td>
      <form method="post" action="../BE/lichhen_tu_choi.php" onsubmit="return handleReject(event,this)">
        <input type="hidden" name="lich_id" value="<?= $row['id'] ?>">
        <input type="hidden" name="ly_do_tu_choi" value="">
        <button type="submit" class="btn-red">❌ Từ chối</button>
      </form>
    </td>
  </tr>
  <?php endforeach; else: ?>
  <tr><td colspan="6" style="text-align:center;">Không có lịch nào chờ phân công</td></tr>
  <?php endif; ?>
</table>

<!-- 🟥 Lịch bị từ chối -->
<h2>🟥 Lịch bị từ chối</h2>
<table>
  <tr><th>ID</th><th>Người dùng</th><th>Chuyên môn</th><th>Chuyên gia cũ</th><th>Thời gian</th><th>Lý do từ chối</th></tr>
  <?php
  $lich_tuchoi = array_filter($lich_chua, fn($r) => $r['trang_thai'] === 'bi_tu_choi');
  if ($lich_tuchoi): foreach ($lich_tuchoi as $row): ?>
  <tr data-mon="<?= htmlspecialchars($row['ten_chuyen_mon']) ?>" data-tt="bi_tu_choi" style="background:#ffeaea;">
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['ten_user']) ?></td>
    <td><?= htmlspecialchars($row['ten_chuyen_mon']) ?></td>
    <td><?= htmlspecialchars($row['ten_chuyen_gia_cu'] ?? '—') ?></td>
    <td><?= htmlspecialchars($row['ngay_gio']) ?></td>
    <td><?= htmlspecialchars($row['ly_do_tu_choi'] ?? 'Không rõ') ?></td>
  </tr>
  <?php endforeach; else: ?>
  <tr><td colspan="6" style="text-align:center;">Không có lịch bị từ chối</td></tr>
  <?php endif; ?>
</table>

</div>
</body>
</html>
