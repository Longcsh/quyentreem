<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../cau_hinh/functions.php';

$tl = new TaiLieuFunction();

// 🧭 Phân trang
$page  = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

// Tổng số tài liệu và tổng số trang
$totalDocs   = $tl->countTaiLieu('chong_bao_hanh');
$totalPages  = max(4, ceil($totalDocs / $limit));

// Lấy dữ liệu theo trang
$documents = $tl->getTaiLieuPhanTrang('chong_bao_hanh', $limit, $offset);

// Xử lý thêm bình luận
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['noi_dung'])) {
    $ten = trim($_POST['ten']);
    $email = trim($_POST['email']);
    $noi_dung = trim($_POST['noi_dung']);
    $tl->themBinhLuan('chong_bao_hanh', $ten, $email, $noi_dung);
   echo "<script>window.location.href='" . $_SERVER['REQUEST_URI'] . "';</script>";
exit;

}

// Lấy bình luận
$comments = $tl->getBinhLuan('chong_bao_hanh');
?>

<link rel="stylesheet" href="css/page_content.css">

<div class="page-content">
  <h2>CHỐNG BẠO HÀNH TRẺ EM</h2>

  <div class="doclist-container">
    <?php if ($documents): ?>
      <?php foreach ($documents as $index => $doc): ?>
        <div class="doc-item">
          <h3><?= htmlspecialchars($doc['tieu_de']) ?></h3>
          <p><?= nl2br(htmlspecialchars($doc['mo_ta'])) ?></p>

          <div class="doc-actions">
            <button class="btn btn-success xem-tl" data-id="<?= $doc['id'] ?>">📖 Xem tài liệu</button>
            <button class="comment-toggle-btn" data-target="comment-<?= $index ?>">💬 Xem bình luận</button>
          </div>

          <!-- 💬 PHẦN BÌNH LUẬN -->
          <div class="comment-section" id="comment-<?= $index ?>" style="display:none;">
            <h4>💬 Bình luận cho tài liệu này</h4>

            <?php if (isset($_SESSION['user_id'])): ?>
              <form method="post">
                <textarea name="noi_dung" placeholder="Nhập bình luận..." required></textarea>
                <input type="text" name="ten" placeholder="Tên của bạn" required>
                <input type="email" name="email" placeholder="Email của bạn" required>
                <button type="submit">Gửi</button>
              </form>
            <?php else: ?>
              <form onsubmit="alert('Bạn cần đăng nhập để bình luận'); return false;">
                <textarea placeholder="Nhập bình luận..." disabled></textarea>
                <input type="text" placeholder="Tên của bạn" disabled>
                <input type="email" placeholder="Email của bạn" disabled>
                <button type="submit" disabled>Gửi</button>
              </form>
              <p>Bạn cần <a href="pages/dang_nhap.php" style="background:none; border:none; color:#007bff; text-decoration:underline; padding:0;">đăng nhập</a> để bình luận.</p>
            <?php endif; ?>

            <ul class="comment-list">
  <?php if ($comments): ?>
    <?php foreach ($comments as $cmt): ?>
      <li style="margin-bottom:15px;">
        <strong><?= htmlspecialchars($cmt['ten']) ?></strong>
        <i style="color:#666;">(<?= htmlspecialchars($cmt['ngay_gio']) ?>)</i>:
        <p><?= nl2br(htmlspecialchars($cmt['noi_dung'])) ?></p>

        <?php if (!empty($cmt['admin_tra_loi'])): ?>
          <div style="
            margin-left:15px;
            background:#eaf1ff;
            border-left:3px solid #0d6efd;
            padding:6px 10px;
            border-radius:6px;
            color:#0b3e9d;
          ">
            <b>👨‍💼 Admin:</b> <?= htmlspecialchars($cmt['admin_tra_loi']); ?>
          </div>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  <?php else: ?>
    <li>Chưa có bình luận nào.</li>
  <?php endif; ?>
</ul>

          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="no-docs">
        <p>📭 Hiện chưa có tài liệu nào trong mục này.</p>
      </div>
    <?php endif; ?>
  </div>

  <!-- 📘 KHU HIỂN THỊ CHI TIẾT TÀI LIỆU -->
  <div id="tailieuDetail" class="tailieu-detail" style="display:none;"></div>

  <!-- 🔢 PHÂN TRANG -->
  <div class="pagination">
    <?php if ($page > 1): ?>
      <a href="?page=chongbaohanh&p=<?= $page - 1 ?>" class="prev">« Trước</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <a href="?page=chongbaohanh&p=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>">
        <?= $i ?>
      </a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
      <a href="?page=chongbaohanh&p=<?= $page + 1 ?>" class="next">Sau »</a>
    <?php endif; ?>
  </div>
</div>

<!-- ================== SCRIPT ================== -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.querySelectorAll('.comment-toggle-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const target = document.getElementById(btn.dataset.target);
    const isVisible = target.style.display === 'block';
    document.querySelectorAll('.comment-section').forEach(el => el.style.display = 'none');
    target.style.display = isVisible ? 'none' : 'block';
    btn.textContent = isVisible ? '💬 Xem bình luận' : 'Ẩn bình luận ▲';
  });
});

// 📥 Xem tài liệu ngay trong trang
$(document).on("click", ".xem-tl", function() {
  const id = $(this).data("id");
  $.get("BE/get_tailieu.php", { id: id }, function(res) {
    if (res.success) {
      const t = res.data;
      const fileUrl = t.file_url ? t.file_url.replace(/^(\.\.\/)*/, '') : '';

      const fileEmbed = `<p class="text-center">
        <a href="${fileUrl}" target="_blank" class="btn btn-primary">📥 Tải xuống tài liệu</a>
      </p>`;

      const html = `
        <div class="news-article">
          <h2 class="article-title">${t.tieu_de}</h2>
          <p class="article-meta">📅 ${t.ngay_upload} | 📁 ${t.loai_tai_lieu}</p>
          <div class="article-content">
            <p>${t.mo_ta}</p>
            ${fileEmbed}
          </div>
          <div class="article-footer">
            <button id="btnBack" class="btn-back">← Quay lại danh sách</button>
          </div>
        </div>
      `;
      $(".doclist-container").hide();
      $("#tailieuDetail").html(html).fadeIn(300);
    } else {
      alert(res.message);
    }
  }, "json");
});

$(document).on("click", "#btnBack", function() {
  $("#tailieuDetail").fadeOut(function() {
    $(".doclist-container").fadeIn(300);
  });
});
</script>
