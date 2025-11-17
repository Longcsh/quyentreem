<?php
require_once __DIR__ . '/../config.php'; 
require_once __DIR__ . '/../cau_hinh/functions.php';

$bv = new BaiVietFunction();
$tl = new TaiLieuFunction();

$keyword = trim($_GET['q'] ?? '');
$results_bv = $results_tl = $results_cm = [];

if ($keyword !== '') {
    // 🔍 Tìm bài viết
    $results_bv = $bv->timKiemBaiViet($keyword);

    // 🔍 Tìm tài liệu
    $results_tl = $tl->timKiemTaiLieu($keyword);

    // 🔍 Tìm chuyên môn
    $conn = (new Database())->connect();
    $stmt = $conn->prepare("SELECT * FROM chuyen_mon WHERE ten_chuyen_mon LIKE :kw OR mo_ta LIKE :kw");
    $kw = "%" . $keyword . "%";
    $stmt->bindParam(':kw', $kw);
    $stmt->execute();
    $results_cm = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Kết quả tìm kiếm</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#f4f6f9; font-family:'Segoe UI', sans-serif;">
<div class="container py-4">
  <h3>Kết quả tìm kiếm cho: <span class="text-primary"><?= htmlspecialchars($keyword) ?></span></h3>
  <hr>

  <?php if (empty($results_bv) && empty($results_tl) && empty($results_cm)): ?>
    <p>Không tìm thấy kết quả nào phù hợp.</p>
  <?php endif; ?>

  <!-- Bài viết -->
  <?php if (!empty($results_bv)): ?>
    <h4 class="mt-4 text-success">📰 Bài viết</h4>
    <div class="row">
      <?php foreach ($results_bv as $r): ?>
        <div class="col-md-4 mb-3">
          <div class="card h-100 shadow-sm">
            <?php if (!empty($r['anh_dai_dien'])): ?>
              <img src="uploads/baiviet/<?= htmlspecialchars($r['anh_dai_dien']) ?>" class="card-img-top" style="height:180px; object-fit:cover;">
            <?php endif; ?>
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($r['tieu_de']) ?></h5>
              <p class="card-text"><?= htmlspecialchars(mb_substr(strip_tags($r['noi_dung']), 0, 120)) ?>...</p>
              <a href="user.php?page=chitiet&id=<?= $r['id'] ?>" class="btn btn-sm btn-primary">Xem chi tiết</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- Tài liệu -->
  <?php if (!empty($results_tl)): ?>
    <h4 class="mt-4 text-warning">📚 Tài liệu</h4>
    <div class="row">
      <?php foreach ($results_tl as $t): ?>
        <div class="col-md-4 mb-3">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($t['ten_tai_lieu']) ?></h5>
              <p class="card-text"><?= htmlspecialchars(mb_substr(strip_tags($t['mo_ta']), 0, 120)) ?>...</p>
              <a href="uploads/tailieu/<?= htmlspecialchars($t['file_url']) ?>" class="btn btn-sm btn-success" target="_blank">Tải về</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- Chuyên môn -->
  <?php if (!empty($results_cm)): ?>
    <h4 class="mt-4 text-info">👩‍🏫 Chuyên môn</h4>
    <ul class="list-group">
      <?php foreach ($results_cm as $c): ?>
        <li class="list-group-item">
          <strong><?= htmlspecialchars($c['ten_chuyen_mon']) ?></strong><br>
          <?= htmlspecialchars($c['mo_ta']) ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

</div>
</body>
</html>
