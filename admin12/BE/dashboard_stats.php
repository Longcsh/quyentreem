<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/db.php'; // db.php nên trả về $pdo là đối tượng PDO

$stats = [
    'users' => 0,
    'experts' => 0,
    'posts' => 0,
    'donations' => 0,
    'appointments' => 0,
    'consultations' => 0,
    'posts_monthly' => [],
    'donations_daily' => []
];

try {
    // ===== Tổng người dùng =====
    $q = $pdo->query("SELECT COUNT(*) AS total FROM tai_khoan");
    $stats['users'] = (int)$q->fetch(PDO::FETCH_ASSOC)['total'];

    // ===== Tổng chuyên gia (vai_tro_id = 2) =====
    $q = $pdo->prepare("SELECT COUNT(*) AS total FROM tai_khoan WHERE vai_tro_id = ?");
    $q->execute([2]);
    $stats['experts'] = (int)$q->fetch(PDO::FETCH_ASSOC)['total'];

    // ===== Tổng bài viết =====
    $q = $pdo->query("SELECT COUNT(*) AS total FROM bai_viet");
    $stats['posts'] = (int)$q->fetch(PDO::FETCH_ASSOC)['total'];

    // ===== Tổng số tiền quyên góp =====
    $q = $pdo->query("SELECT SUM(so_tien) AS total FROM donate");
    $stats['donations'] = (int)$q->fetch(PDO::FETCH_ASSOC)['total'];

    // ===== Tổng lịch hẹn =====
    $q = $pdo->query("SELECT COUNT(*) AS total FROM lich_hen");
    $stats['appointments'] = (int)$q->fetch(PDO::FETCH_ASSOC)['total'];

    // ===== Thống kê bài viết theo tháng =====
    $q = $pdo->query("
        SELECT DATE_FORMAT(ngay_dang, '%Y-%m') AS thang, COUNT(*) AS tong
        FROM bai_viet
        GROUP BY thang
        ORDER BY thang ASC
    ");
    while ($r = $q->fetch(PDO::FETCH_ASSOC)) {
        $stats['posts_monthly'][$r['thang']] = (int)$r['tong'];
    }

    // ===== Thống kê donate theo ngày (7 ngày gần nhất) =====
    $q = $pdo->query("
        SELECT DATE(ngay_ung_ho) AS ngay, SUM(so_tien) AS tong
        FROM donate
        WHERE ngay_ung_ho >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY ngay
        ORDER BY ngay ASC
    ");
    while ($r = $q->fetch(PDO::FETCH_ASSOC)) {
        $stats['donations_daily'][$r['ngay']] = (int)$r['tong'];
    }

    // ===== Trả JSON =====
    echo json_encode(['success' => true, 'data' => $stats], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
