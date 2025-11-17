<?php
require_once __DIR__ . '/../config.php';
require_once DB_FILE;

// 🔒 Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: dang_nhap.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

// ✅ Lấy bình luận của người dùng hiện tại (kể cả khi không có tài liệu), kèm phản hồi admin
$stmt = $conn->prepare("
    SELECT 
        b.noi_dung, 
        b.ngay_gio, 
        b.admin_tra_loi, 
        t.tieu_de
    FROM binh_luan b
    LEFT JOIN tai_lieu t ON b.tai_lieu_id = t.id
    WHERE b.user_id = ?
    ORDER BY b.ngay_gio DESC
");
$stmt->execute([$_SESSION['user_id']]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>💬 Bình luận của bạn</h2>
<ul>
<?php if ($comments): ?>
    <?php foreach ($comments as $c): ?>
        <li style="margin-bottom: 15px; line-height:1.6;">
            <b>📘 <?= htmlspecialchars($c['tieu_de'] ?? 'Không có tiêu đề') ?>:</b>
            <?= htmlspecialchars($c['noi_dung']) ?>
            <i style="color:#555;">(<?= $c['ngay_gio'] ?>)</i>

            <?php if (!empty($c['admin_tra_loi'])): ?>
                <div style="
                    margin-top: 6px; 
                    background: #eaf1ff; 
                    border-left: 3px solid #0d6efd; 
                    padding: 6px 10px; 
                    border-radius: 6px; 
                    color: #0b3e9d;
                ">
                    <b>👨‍💼 Admin:</b> <?= htmlspecialchars($c['admin_tra_loi']) ?>
                </div>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
<?php else: ?>
    <li>Bạn chưa có bình luận nào.</li>
<?php endif; ?>
</ul>
