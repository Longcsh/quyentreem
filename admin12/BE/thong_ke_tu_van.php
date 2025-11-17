<?php
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

try {
    $data = [];

    $data['tong'] = $conn->query("SELECT COUNT(*) FROM tu_van")->fetchColumn();
    $data['cho_phan_cong'] = $conn->query("SELECT COUNT(*) FROM tu_van WHERE trang_thai = 'cho_phan_cong'")->fetchColumn();
    $data['cho_tra_loi'] = $conn->query("SELECT COUNT(*) FROM tu_van WHERE trang_thai = 'cho_tra_loi'")->fetchColumn();
    $data['da_tra_loi'] = $conn->query("SELECT COUNT(*) FROM tu_van WHERE trang_thai = 'da_tra_loi'")->fetchColumn();

    $stmt = $conn->query("
        SELECT cm.ten_chuyen_mon, COUNT(t.id) AS so_cau_hoi
        FROM tu_van t
        JOIN chuyen_mon cm ON t.chuyen_mon_id = cm.id
        GROUP BY cm.id
    ");
    $data['theo_chuyen_mon'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'data' => $data]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
