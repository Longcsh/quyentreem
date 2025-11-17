<?php
require_once __DIR__ . '/../config.php'; 


$db = new Database();
$conn = $db->connect();

// --- Lấy id user hiện tại ---
$user_id = $_SESSION['user_id'] ?? null;

$success = "";
$error = "";

// ==========================
//  GỬI CÂU HỎI
// ==========================
if ($_SERVER["REQUEST_METHOD"] === "POST" && $user_id) {
    $cau_hoi = trim($_POST['cau_hoi']);
    $chuyen_mon_id = $_POST['chuyen_mon_id'] ?? null;
    $anh_path = null;

    // Tạo thư mục lưu file nếu chưa có
    $upload_dir = __DIR__ . '/../uploads/tu_van/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // --- Upload ảnh minh họa (tùy chọn) ---
    if (!empty($_FILES['anh_minh_hoa']['name'])) {
        $ext = pathinfo($_FILES['anh_minh_hoa']['name'], PATHINFO_EXTENSION);
        $newName = 'img_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['anh_minh_hoa']['tmp_name'], $upload_dir . $newName)) {
            $anh_path = 'uploads/tu_van/' . $newName;
        }
    }

        if (!empty($cau_hoi) && !empty($chuyen_mon_id)) {
    try {
            // --- Chọn chuyên gia công bằng theo số lượng câu hỏi đang chờ ---
            // 🔁 Phân công chuyên gia công bằng: chọn người có ít câu hỏi nhất
        $stmt_cg = $conn->prepare("
            SELECT tk.id
            FROM tai_khoan tk
            LEFT JOIN tu_van tv 
                ON tk.id = tv.chuyen_gia_id AND tv.trang_thai = 'dang_cho_tra_loi'
            WHERE tk.vai_tro_id = 2 AND tk.chuyen_mon_id = ?
            GROUP BY tk.id
            ORDER BY COUNT(tv.id) ASC, RAND()
            LIMIT 1
        ");
        $stmt_cg->execute([$chuyen_mon_id]);
        $chuyen_gia_id = $stmt_cg->fetchColumn();

    // 🔁 Nếu chưa có chuyên gia nào thuộc chuyên môn → chọn ngẫu nhiên chuyên gia khác (fallback)
        if (!$chuyen_gia_id) {
            $stmt_fallback = $conn->query("
                SELECT id FROM tai_khoan 
                WHERE vai_tro_id = 2 
                ORDER BY RAND() LIMIT 1
            ");
            $chuyen_gia_id = $stmt_fallback->fetchColumn();
            }
        // --- Nếu có chuyên gia thì chèn vào bảng tu_van ---
        if ($chuyen_gia_id) {
            $stmt = $conn->prepare("
                INSERT INTO tu_van 
                    (nguoi_dung_id, chuyen_mon_id, chuyen_gia_id, cau_hoi, anh_minh_hoa, ngay_gui, trang_thai)
                VALUES 
                    (?, ?, ?, ?, ?, NOW(), 'dang_cho_tra_loi')
            ");
            $stmt->execute([$user_id, $chuyen_mon_id, $chuyen_gia_id, $cau_hoi, $anh_path]);

            // ✅ Gợi ý: thêm thông tin chuyên gia để hiển thị cho user biết ai nhận câu hỏi
            $stmt_ten = $conn->prepare("SELECT ho_ten FROM tai_khoan WHERE id = ?");
            $stmt_ten->execute([$chuyen_gia_id]);
            $ten_cg = $stmt_ten->fetchColumn();

            $success = "✅ Câu hỏi đã được gửi đến chuyên gia <strong style='color:#2c7be5;'>$ten_cg</strong>.";
        } else {
            $error = "❌ Hiện chưa có chuyên gia nào trong lĩnh vực này.";
        }
    } catch (PDOException $e) {
        $error = "❌ Lỗi khi gửi câu hỏi: " . $e->getMessage();
    }
} else {
    $error = "❌ Vui lòng nhập câu hỏi và chọn chuyên môn.";
}


}
?>
<link rel="stylesheet" href="/php/bvte/css/hoichuyengia.css">



<!-- ==========================
     GIAO DIỆN HỎI CHUYÊN GIA
     ========================== -->
<section class="tu-van">
  <h2>🧠 Hỏi chuyên gia</h2>

  <?php if (!empty($success)): ?>
      <p style="color: green;"><?php echo $success; ?></p>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
      <p style="color: red;"><?php echo $error; ?></p>
  <?php endif; ?>

  <!-- FORM GỬI CÂU HỎI -->
  <form method="post" enctype="multipart/form-data">
      <label for="cau_hoi">💬 Nội dung câu hỏi:</label>
      <textarea name="cau_hoi" id="cau_hoi" placeholder="Nhập câu hỏi của bạn..." required></textarea>

      <label for="chuyen_mon_id">📚 Chọn chuyên môn:</label>
      <select name="chuyen_mon_id" id="chuyen_mon_id" required>
          <option value="">-- Chọn lĩnh vực bạn muốn hỏi --</option>
          <?php
          // Lấy danh sách chuyên môn từ DB
          $stmt = $conn->query("SELECT id, ten_chuyen_mon FROM chuyen_mon ORDER BY ten_chuyen_mon ASC");
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              echo "<option value='{$row['id']}'>" . htmlspecialchars($row['ten_chuyen_mon']) . "</option>";
          }
          ?>
      </select>

      <label for="anh_minh_hoa">🖼️ Ảnh minh họa (tùy chọn):</label>
      <input type="file" name="anh_minh_hoa" id="anh_minh_hoa" accept="image/*">

      <img id="preview-image" src="#" alt="Xem trước ảnh" 
           style="display:none; max-width:200px; margin-top:10px; border-radius:8px;">

      <button type="submit">📨 Gửi câu hỏi</button>
  </form>

  <!-- DANH SÁCH CÂU HỎI -->
  <h3>📋 Các câu hỏi đã gửi</h3>
  <ul>
    <?php
      if ($user_id) {
       $stmt = $conn->prepare("
    SELECT t.cau_hoi, t.tra_loi, t.ngay_gui, t.trang_thai, 
           t.anh_minh_hoa, t.ly_do_tu_choi, 
           cm.ten_chuyen_mon, cg.ho_ten AS ten_chuyen_gia
    FROM tu_van t
    LEFT JOIN chuyen_mon cm ON t.chuyen_mon_id = cm.id
    LEFT JOIN tai_khoan cg ON t.chuyen_gia_id = cg.id
    WHERE t.nguoi_dung_id = ?
    ORDER BY t.ngay_gui DESC
");

          $stmt->execute([$user_id]);
          $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if ($list) {
              foreach ($list as $row) {
                  echo "<li>";
                  echo "<strong>📌 Câu hỏi:</strong> " . htmlspecialchars($row['cau_hoi']) . "<br>";
                  echo "<em>🕒 Gửi lúc:</em> " . htmlspecialchars($row['ngay_gui']) . "<br>";
                  echo "<strong>📚 Chuyên môn:</strong> " . htmlspecialchars($row['ten_chuyen_mon']) . "<br>";
               if (!empty($row['ly_do_tu_choi']) && 
    in_array($row['trang_thai'], ['tu_choi', 'bi_tu_choi'])) {
    echo "<br><span style='color:red;'><strong>❗ Lý do từ chối:</strong> " 
         . htmlspecialchars($row['ly_do_tu_choi']) . "</span>";
}

                  if (!empty($row['ten_chuyen_gia'])) {
                      echo " | 👨‍⚕️ <em>" . htmlspecialchars($row['ten_chuyen_gia']) . "</em>";
                  }

                  if (!empty($row['anh_minh_hoa'])) {
                      echo "<br><img src='" . htmlspecialchars($row['anh_minh_hoa']) . "' alt='Ảnh minh họa' 
                            style='max-width:150px; margin-top:5px; border-radius:6px;'>";
                  }

                  echo "</li><hr>";
              }
          } else {
              echo "<li>Chưa có câu hỏi nào được gửi.</li>";
          }
      } else {
          echo "<li>Vui lòng đăng nhập để gửi và xem câu hỏi.</li>";
      }
    ?>
  </ul>
</section>

<!-- ==========================
     JS xem trước ảnh
     ========================== -->
<script>
document.getElementById('anh_minh_hoa').addEventListener('change', function(event) {
  const img = document.getElementById('preview-image');
  const file = event.target.files[0];
  if (file) {
    img.src = URL.createObjectURL(file);
    img.style.display = 'block';
  } else {
    img.style.display = 'none';
  }
});
</script>
