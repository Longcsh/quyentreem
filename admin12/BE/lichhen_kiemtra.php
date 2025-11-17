<?php
require_once __DIR__ . '/db.php';
header('Content-Type: application/json; charset=UTF-8');

try {
    global $conn;

    $chuyen_mon_id = $_GET['chuyen_mon_id'] ?? null;
    $ngay_gio = $_GET['ngay_gio'] ?? null;
    $chuyen_gia_cu = $_GET['chuyen_gia_cu'] ?? null;

    if (!$chuyen_mon_id || !$ngay_gio) {
        echo json_encode(['success' => false, 'error' => 'Thiếu tham số']);
        exit;
    }

    $sql = "
        SELECT tk.id, tk.ho_ten, tk.trang_thai
        FROM tai_khoan tk
        WHERE tk.vai_tro_id = 2
          AND tk.chuyen_mon_id = ?
          AND tk.trang_thai LIKE 'Hoạt%'
          AND tk.id != COALESCE(?, 0)
          AND tk.id NOT IN (
              SELECT DISTINCT chuyen_gia_id 
              FROM lich_hen 
              WHERE chuyen_gia_id IS NOT NULL
                AND DATE(ngay_gio) = DATE(?)
                AND trang_thai IN ('cho_xac_nhan', 'da_xac_nhan')
          )
        ORDER BY tk.ho_ten ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$chuyen_mon_id, $chuyen_gia_cu, $ngay_gio]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'count' => count($rows),
        'data' => $rows,
        'debug' => [
            'input_mon' => $chuyen_mon_id,
            'old_expert' => $chuyen_gia_cu,
            'ngay_gio' => $ngay_gio
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
