<?php
require_once __DIR__ . '/db.php';
header('Content-Type: application/json; charset=UTF-8');

try {
    // 🟨 Lấy tham số GET
    $chuyen_mon = $_GET['chuyen_mon'] ?? '';
    $ngay_gio   = $_GET['ngay_gio'] ?? '';

    if (!$chuyen_mon || !$ngay_gio) {
        echo json_encode(['error' => true, 'message' => 'Thiếu tham số']);
        exit;
    }

    // 🟩 Xử lý thời gian
    $ngay = date('Y-m-d', strtotime($ngay_gio));
    $gio  = date('H:i:s', strtotime($ngay_gio));

    // 🟩 Truy vấn chuyên gia cùng chuyên môn, kiểm tra bận/rảnh
    $sql = "
        SELECT 
            id, 
            ho_ten, 
            chuyen_mon,
            CASE 
                WHEN EXISTS (
                    SELECT 1 
                    FROM lich_hen h 
                    WHERE h.chuyen_gia_id = t.id 
                      AND DATE(h.ngay_gio) = ?
                      AND TIME(h.ngay_gio) BETWEEN SUBTIME(?, '01:00:00') AND ADDTIME(?, '01:00:00')
                      AND h.trang_thai IN ('cho_duyet', 'da_duyet')
                ) THEN 'bận'
                ELSE 'rảnh'
            END AS trang_thai_lich
        FROM tai_khoan t
        WHERE t.vai_tro_id = 2 
          AND t.chuyen_mon = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$ngay, $gio, $gio, $chuyen_mon]);

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'error' => false,
        'data'  => $data
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo json_encode([
        'error' => true,
        'message' => 'Lỗi CSDL: ' . $e->getMessage()
    ]);
}
?>
