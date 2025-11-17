<?php
require_once __DIR__ . '/../config.php';


$db = new Database();
$conn = $db->connect();

// Hàm hiển thị tên (ẩn danh nếu người ủng hộ chọn)
function displayName($name, $isAnonymous) {
    if ($isAnonymous) return "Ẩn danh";
    $first = mb_substr($name, 0, 1, 'UTF-8');
    return $first . '***';
}

// ✅ Khi người dùng xác nhận “Đã chuyển khoản” — ghi vào DB trực tiếp
if (!empty($_POST) && isset($_POST['confirm'])) {
    $ho_ten  = trim($_POST['ho_ten'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $so_tien = floatval($_POST['so_tien'] ?? 0);
    $loi_nhan = trim($_POST['loi_nhan'] ?? '');
    $an_danh  = intval($_POST['an_danh'] ?? 0);

    if ($ho_ten && $email && $so_tien > 0) {
        try {
            $stmt = $conn->prepare("INSERT INTO donate (ho_ten, email, so_tien, loi_nhan, an_danh) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$ho_ten, $email, $so_tien, $loi_nhan, $an_danh]);

            // Hiện hiệu ứng cảm ơn
            echo "<script>
            window.onload = function(){
                const eff = document.getElementById('thanks-effect');
                if (eff) eff.style.display='block';
                for (let i=0;i<25;i++){
                    let h=document.createElement('div');
                    h.className='heart';
                    h.style.left=Math.random()*100+'%';
                    h.style.animationDuration=(2+Math.random()*3)+'s';
                    eff.appendChild(h);
                    setTimeout(()=>h.remove(),4000);
                }
                setTimeout(()=>{window.location.href='user.php?page=ungho';},3500);
            };
            </script>";
        } catch (Exception $e) {
            echo "<p style='color:red'>Lỗi ghi dữ liệu: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}

// ✅ Lấy danh sách người ủng hộ gần đây
$stmt = $conn->query("SELECT ho_ten, so_tien, ngay_ung_ho, an_danh FROM donate ORDER BY ngay_ung_ho DESC LIMIT 5");
$donations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Đường dẫn QR
$qrPath = 'igm/qr.jpg';
?>

<!-- HTML -->
<div class="donate-container">
  <h2>🤝 ỦNG HỘ QUỸ BẢO VỆ TRẺ EM</h2>
  <p>Mọi đóng góp của bạn sẽ giúp chúng tôi tiếp tục hành trình bảo vệ quyền trẻ em Việt Nam. Xin chân thành cảm ơn!</p>

  <!-- FORM CHÍNH -->
  <form id="donateForm" class="donate-form" novalidate>
      <label>Họ tên:</label>
      <input type="text" name="ho_ten" required>

      <label>Email:</label>
      <input type="email" name="email" required>

      <label>Số tiền ủng hộ (VNĐ):</label>
      <input type="number" name="so_tien" min="1000" required>

      <label>Lời nhắn (tuỳ chọn):</label>
      <textarea name="loi_nhan" rows="3"></textarea>

      <label style="margin-top:10px;">
          <input type="checkbox" name="an_danh" value="1">
          Giữ ẩn danh (không hiển thị tên trên danh sách ủng hộ)
      </label>

      <!-- Không gửi form thật -->
      <button type="button" id="btnPreview" class="btn-donate">Ủng hộ ngay</button>
  </form>

  <!-- POPUP QR -->
  <div id="donate-popup" class="popup-overlay" style="display:none;">
    <div class="popup-content">
        <button id="close-popup" class="btn-close-x" title="Đóng">&times;</button>

        <h3>Vui lòng chuyển khoản theo thông tin dưới đây 💚</h3>
        <img src="<?= htmlspecialchars($qrPath) ?>" alt="QR Donate" class="qr-img">
        <p><strong>Ngân hàng:</strong> VPBank</p>
        <p><strong>Số tài khoản:</strong> 0383471869</p>
        <p><strong>Chủ tài khoản:</strong> Lê Hồng Sơn</p>

        <!-- FORM ẨN để gửi dữ liệu thật -->
        <form method="post" id="confirmForm">
            <input type="hidden" name="ho_ten">
            <input type="hidden" name="email">
            <input type="hidden" name="so_tien">
            <input type="hidden" name="loi_nhan">
            <input type="hidden" name="an_danh">
            <button type="submit" name="confirm" class="btn-close">Đã chuyển khoản</button>
        </form>
    </div>
  </div>

  <!-- Hiệu ứng trái tim -->
  <div id="thanks-effect" class="thanks-effect"></div>

  <hr>
  <h3>🕊️ Những tấm lòng gần đây</h3>
  <ul class="donate-list">
      <?php foreach ($donations as $don): ?>
          <?php
            $display = displayName($don['ho_ten'], $don['an_danh']);
            $amount = number_format($don['so_tien'], 0, ',', '.');
          ?>
          <li><strong><?= htmlspecialchars($display) ?></strong> – <?= $amount ?> VNĐ <br>
              <small><?= htmlspecialchars($don['ngay_ung_ho']) ?></small>
          </li>
      <?php endforeach; ?>
  </ul>
</div>

<script src="js/ungho.js" defer></script>
