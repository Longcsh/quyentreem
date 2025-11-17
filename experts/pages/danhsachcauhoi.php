<?php
require_once __DIR__ . '/../../config.php';

if (isset($_SESSION['user_id'], $_SESSION['vai_tro_id']) && $_SESSION['vai_tro_id'] == 2) {
    if (!isset($_SESSION['expert_id'])) {
        $_SESSION['expert_id'] = $_SESSION['user_id'];
        $_SESSION['expert_name'] = $_SESSION['ho_ten'] ?? $_SESSION['ten_dang_nhap'];
        $_SESSION['role_id'] = 2;
    }
}

// ✅ Kiểm tra đăng nhập
if (!isset($_SESSION['expert_id'])) {
    header('Location: ../../pages/dang_nhap.php');
    exit;
}

$db = new Database();
$conn = $db->connect();

$chuyen_gia_id = $_SESSION['expert_id'];
$ho_ten = $_SESSION['expert_name'] ?? 'Chuyên gia';

// ✅ Lấy danh sách câu hỏi được giao
$sql = "SELECT t.id, t.cau_hoi, t.ngay_gui, u.ho_ten AS ten_nguoi_dung, 
               cm.ten_chuyen_mon, t.tra_loi, t.trang_thai, t.ly_do_tu_choi
        FROM tu_van t
        JOIN tai_khoan u ON t.nguoi_dung_id = u.id
        JOIN chuyen_mon cm ON t.chuyen_mon_id = cm.id
        WHERE t.chuyen_gia_id = ?
        ORDER BY t.ngay_gui DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([$chuyen_gia_id]);
$cau_hoi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>💬 Danh sách câu hỏi</title>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/sidebar-expert.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/danhsachcauhoi.css?v=<?php echo time(); ?>">

  <style>
    .status.reject {
      background-color: #f44336;
      color: #fff;
      padding: 5px 10px;
      border-radius: 6px;
      font-size: 0.9rem;
    }
    .reason {
      font-size: 0.85rem;
      color: #555;
      margin-top: 4px;
      display: block;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <?php include '../partials/navbar.php'; ?>

  <!-- Nội dung chính -->
  <main class="main-content">
    <div class="question-container">
      <h2>💬 Câu hỏi được giao cho <?= htmlspecialchars($ho_ten) ?></h2>

      <?php if (isset($_GET['success'])): ?>
        <p style="color: green; margin-bottom: 10px;"> Cập nhật thành công!</p>
      <?php elseif (isset($_GET['error'])): ?>
        <p style="color: red; margin-bottom: 10px;"> Có lỗi xảy ra hoặc không có quyền thao tác.</p>
      <?php endif; ?>

      <?php if ($cau_hoi): ?>
        <table class="question-table">
          <thead>
            <tr>
              <th>Người hỏi</th>
              <th>Câu hỏi</th>
              <th>Chuyên môn</th>
              <th>Ngày gửi</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($cau_hoi as $row): ?>
            <tr>
              <td><?= htmlspecialchars($row['ten_nguoi_dung']) ?></td>
              <td><?= nl2br(htmlspecialchars($row['cau_hoi'])) ?></td>
              <td><?= htmlspecialchars($row['ten_chuyen_mon']) ?></td>
              <td><?= htmlspecialchars($row['ngay_gui']) ?></td>

              <td>
                <?php if ($row['trang_thai'] === 'da_tra_loi'): ?>
                  <span class="status success">Đã trả lời</span>

                <?php elseif ($row['trang_thai'] === 'tu_choi'): ?>
                  <span class="status reject">Đã từ chối</span>
                  <?php if (!empty($row['ly_do_tu_choi'])): ?>
                    <span class="reason">📋 Lý do: <?= htmlspecialchars($row['ly_do_tu_choi']) ?></span>
                  <?php endif; ?>

                <?php else: ?>
                  <span class="status pending">Chưa trả lời</span>
                <?php endif; ?>
              </td>

              <td>
                <?php if ($row['trang_thai'] === 'dang_cho_tra_loi' || $row['trang_thai'] === null): ?>
                  <!-- Nút trả lời -->
                  <a href="traloi.php?id=<?= $row['id'] ?>" class="btn btn-primary">Trả lời</a>

                  <!-- Nút từ chối -->
                  <button type="button" class="btn btn-danger" onclick="toggleRejectForm(<?= $row['id'] ?>)">
                    Từ chối
                  </button>

                  <!-- Form nhập lý do -->
                  <form id="reject-form-<?= $row['id'] ?>" 
                        action="xuly_tu_choi.php" method="post"
                        style="display:none; margin-top:6px;">
                    <input type="hidden" name="id_tuvan" value="<?= $row['id'] ?>">
                    <input type="text" name="ly_do" placeholder="Nhập lý do từ chối..." 
                          style="padding:4px;border-radius:5px;border:1px solid #ccc;width:180px;margin-right:5px;">
                    <button type="submit" class="btn btn-warning"
                            style="background:#ff9800;border:none;color:#fff;padding:6px 10px;border-radius:5px;">
                      Xác nhận
                    </button>
                  </form>
                <?php else: ?>
                  <em class="done">Đã xử lý</em>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="empty">Chưa có câu hỏi nào được giao.</p>
      <?php endif; ?>
    </div>
  </main>

  <script src="../js/main.js?v=<?php echo time(); ?>"></script>
  <script>
    function toggleRejectForm(id) {
      const form = document.getElementById('reject-form-' + id);
      form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
    }
  </script>
</body>
</html>
