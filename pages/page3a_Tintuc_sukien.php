<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../cau_hinh/functions.php';

$bv = new BaiVietFunction();

// 🧭 Thiết lập phân trang
$page  = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$limit = 5; // số bài viết mỗi trang
$offset = ($page - 1) * $limit;

// Tổng số bài và tổng số trang
$totalPosts  = $bv->countBaiViet('tin_tuc_su_kien');
$totalPages  = max(4, ceil($totalPosts / $limit));

// Lấy bài viết theo trang
$articles = $bv->getBaiVietPhanTrang('tin_tuc_su_kien', $limit, $offset);

// 🟢 Xử lý thêm bình luận
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['noi_dung'])) {
    $ten = trim($_POST['ten']);
    $email = trim($_POST['email']);
    $noi_dung = trim($_POST['noi_dung']);
    $bv->themBinhLuan('tin_tuc_su_kien', $ten, $email, $noi_dung);

    // 🔁 Tránh gửi lại form khi reload
    echo "<script>window.location.href='" . $_SERVER['REQUEST_URI'] . "';</script>";
exit;
}

// 🟢 Lấy danh sách bình luận
$comments = $bv->getBinhLuan('tin_tuc_su_kien');
?>

<link rel="stylesheet" href="css/page_content.css">

<div class="page-content">
  <h2>TIN TỨC – SỰ KIỆN</h2>

  <?php if ($articles): ?>
    <?php foreach ($articles as $index => $art): ?>
      <div class="doc-item">
        <h3><?= htmlspecialchars($art['tieu_de']); ?></h3>
        <div class="article-body">
  <?= nl2br($art['noi_dung']); ?>
</div>

        <!-- 🖼️ Ảnh minh họa nếu có -->
        <?php if (!empty($art['anh_dai_dien'])): ?>
          <?php
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
            $host = $_SERVER['HTTP_HOST'];
            $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
            $imgPath = $protocol . $host . $basePath . '/admin12/assets/baiviet/' . $art['anh_dai_dien'];
          ?>
          <img src="<?= htmlspecialchars($imgPath); ?>" alt="Ảnh minh họa"
               style="max-width:100%;margin:15px 0;border-radius:10px;
                      box-shadow:0 0 10px rgba(0,0,0,0.1);display:block;">
        <?php endif; ?>

        <div class="doc-actions">
          <button class="comment-toggle-btn" data-target="comment-<?= $index ?>">💬 Xem bình luận</button>
        </div>

        <!-- 💬 PHẦN BÌNH LUẬN ẨN -->
        <div class="comment-section" id="comment-<?= $index ?>" style="display:none;">
          <h4>💬 Bình luận cho bài viết này</h4>

          <?php if (isset($_SESSION['user_id'])): ?>
            <form method="post">
              <textarea name="noi_dung" placeholder="Nhập bình luận..." required></textarea>
              <input type="text" name="ten" placeholder="Tên của bạn" required>
              <input type="email" name="email" placeholder="Email của bạn" required>
              <button type="submit">Gửi</button>
            </form>
          <?php else: ?>
            <form method="post" onsubmit="alert('Bạn cần đăng nhập để bình luận'); return false;">
              <textarea placeholder="Nhập bình luận..." disabled></textarea>
              <input type="text" placeholder="Tên của bạn" disabled>
              <input type="email" placeholder="Email của bạn" disabled>
              <button type="submit" disabled>Gửi</button>
            </form>
            <p>Bạn cần <a href="pages/dang_nhap.php">đăng nhập</a> để bình luận.</p>
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
    <p>📭 Hiện chưa có bài viết nào trong mục này.</p>
    <div class="doc-placeholder-list">
      <?php for ($i = 0; $i < 3; $i++): ?>
        <div class="doc-placeholder">
          <div class="ph-title"></div>
          <div class="ph-desc"></div>
          <div class="ph-desc"></div>
        </div>
      <?php endfor; ?>
    </div>
  </div>
<?php endif; ?>

<!-- 🔢 PHÂN TRANG -->
<div class="pagination">
  <?php if ($page > 1): ?>
    <a href="user.php?page=tintuc&p=<?= $page - 1 ?>" class="prev">« Trước</a>
  <?php endif; ?>

  <?php
    $maxPages = 4;
    $start = max(1, $page - floor($maxPages / 2));
    $end = min($totalPages, $start + $maxPages - 1);
    if ($end - $start < $maxPages - 1) {
        $start = max(1, $end - $maxPages + 1);
    }

    for ($i = $start; $i <= $end; $i++):
  ?>
    <a href="user.php?page=tintuc&p=<?= $i ?>" 
       class="<?= $i == $page ? 'active' : '' ?>">
       <?= $i ?>
    </a>
  <?php endfor; ?>

  <?php if ($page < $totalPages): ?>
    <a href="user.php?page=tintuc&p=<?= $page + 1 ?>" class="next">Sau »</a>
  <?php endif; ?>
</div>

</div>

<!-- ================== SCRIPT ẨN/HIỆN BÌNH LUẬN ================== -->
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
</script>
