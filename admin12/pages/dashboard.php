<?php 
// File: admin12/pages/dashboard.php

// ✅ Tự động tính base URL (không hardcode)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$apiBase = $protocol . "://" . $host . $scriptDir . "/../BE/";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>📊 Bảng điều khiển thống kê</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <!-- 🔗 Gọi file CSS riêng -->
  <link rel="stylesheet" href="../css/dashboard.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<header>
  <h1>📊 Bảng điều khiển thống kê</h1>
  <div class="header-right">
    <button class="back-btn" onclick="window.history.back()">⬅️ Quay lại</button>
    <span>Xin chào, <b>Admin</b> 👋</span>
  </div>
</header>

<div class="container fade-in">
  <div id="stats-cards" class="cards loading">⏳ Đang tải dữ liệu...</div>

  <!-- Hai biểu đồ nằm ngang -->
  <div class="charts">
    <div class="chart-box">
      <h3>👥 Tỷ lệ Người dùng & Chuyên gia</h3>
      <canvas id="userChart" height="180"></canvas>
    </div>

    <div class="chart-box">
      <h3>💰 Tài chính quyên góp theo ngày</h3>
      <canvas id="donateChart" height="180"></canvas>
    </div>
  </div>
</div>

<footer>© <?= date('Y') ?> Trung tâm Bảo vệ Trẻ em — Dashboard thống kê</footer>

<script>
document.addEventListener("DOMContentLoaded", async () => {
  const apiUrl = "<?= $apiBase ?>dashboard_stats.php";
  const statsDiv = document.getElementById("stats-cards");

  try {
    const res = await fetch(apiUrl);
    const json = await res.json();
    if (!json.success) throw new Error("Không lấy được dữ liệu dashboard");

    const d = json.data;

    statsDiv.classList.remove('loading');
    statsDiv.innerHTML = `
      <div class="card"><h2>${d.users}</h2><p>Người dùng</p></div>
      <div class="card"><h2>${d.experts}</h2><p>Chuyên gia</p></div>
      <div class="card"><h2>${d.posts}</h2><p>Bài viết</p></div>
      <div class="card"><h2>${d.appointments}</h2><p>Lịch hẹn</p></div>
      <div class="card"><h2>${d.donations.toLocaleString()} ₫</h2><p>Tổng quyên góp</p></div>
    `;

    // ===== BIỂU ĐỒ NGƯỜI DÙNG =====
    new Chart(document.getElementById('userChart'), {
      type: 'doughnut',
      data: {
        labels: ['Người dùng', 'Chuyên gia'],
        datasets: [{
          data: [d.users - d.experts, d.experts],
          backgroundColor: ['#3b82f6', '#f97316'],
          borderWidth: 0
        }]
      },
      options: {
        plugins: { legend: { position: 'bottom' } }
      }
    });

    // ===== BIỂU ĐỒ TÀI CHÍNH =====
    const donateLabels = Object.keys(d.donations_daily);
    const donateValues = Object.values(d.donations_daily);

    new Chart(document.getElementById('donateChart'), {
      type: 'bar',
      data: {
        labels: donateLabels,
        datasets: [{
          label: 'Số tiền (VNĐ)',
          data: donateValues,
          backgroundColor: donateValues.map(() => randomColor()),
          borderRadius: 8
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString() + ' ₫' } }
        }
      }
    });

  } catch (e) {
    statsDiv.innerHTML = `<div class="loading">❌ ${e.message}</div>`;
    console.error(e);
  }
});

function randomColor() {
  const colors = [
    'rgba(59,130,246,0.8)',
    'rgba(16,185,129,0.8)',
    'rgba(234,179,8,0.8)',
    'rgba(239,68,68,0.8)',
    'rgba(147,51,234,0.8)',
    'rgba(249,115,22,0.8)'
  ];
  return colors[Math.floor(Math.random() * colors.length)];
}
</script>

</body>
</html>
