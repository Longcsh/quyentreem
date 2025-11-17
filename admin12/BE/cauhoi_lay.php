<?php
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

try {
    $trang_thai = $_GET['trang_thai'] ?? 'all';
    $chuyen_mon = $_GET['chuyen_mon'] ?? 'all';

    $sql = "
        SELECT t.id, u.ho_ten AS nguoi_dung, cm.ten_chuyen_mon, cg.ho_ten AS chuyen_gia,
               t.cau_hoi, t.trang_thai, t.ngay_gui, t.ngay_tra_loi
        FROM tu_van t
        LEFT JOIN tai_khoan u ON t.nguoi_dung_id = u.id
        LEFT JOIN tai_khoan cg ON t.chuyen_gia_id = cg.id
        LEFT JOIN chuyen_mon cm ON t.chuyen_mon_id = cm.id
        WHERE 1
    ";

    $params = [];
    if ($trang_thai !== 'all') {
        $sql .= " AND t.trang_thai = ?";
        $params[] = $trang_thai;
    }
    if ($chuyen_mon !== 'all') {
        $sql .= " AND cm.id = ?";
        $params[] = $chuyen_mon;
    }

    $sql .= " ORDER BY t.ngay_gui DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
