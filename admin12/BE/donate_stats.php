<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/db.php';

try {
    $stats = [
        'donations_total' => 0,
        'donations_today' => 0,
        'donations_count' => 0
    ];

    // Tổng số tiền đã nhận
    $rows = $db->query("SELECT COALESCE(SUM(so_tien), 0) AS total FROM donate");
    $stats['donations_total'] = (float) ($rows[0]['total'] ?? 0);

    // Số tiền hôm nay
    $rows = $db->query("SELECT COALESCE(SUM(so_tien), 0) AS total FROM donate WHERE DATE(ngay_ung_ho) = CURDATE()");
    $stats['donations_today'] = (float) ($rows[0]['total'] ?? 0);

    // Tổng lượt ủng hộ
    $rows = $db->query("SELECT COUNT(*) AS c FROM donate");
    $stats['donations_count'] = (int) ($rows[0]['c'] ?? 0);

    echo json_encode(['success' => true, 'data' => $stats], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
