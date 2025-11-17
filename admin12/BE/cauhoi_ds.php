<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/db.php';

try {
    global $conn;
    if (!isset($conn) || !$conn instanceof PDO) {
        if (isset($pdo) && $pdo instanceof PDO) {
            $conn = $pdo;
        } else {
            throw new Exception("Không thể khởi tạo kết nối CSDL");
        }
    }

    // ✅ Truy vấn dữ liệu từ bảng tư vấn, có JOIN chuyên môn
    $sql = "
        SELECT 
            t.id,
            u.ho_ten AS ten_nguoi_dung,
            t.cau_hoi,
            t.ngay_gui,
            t.trang_thai,
            cm.ten_chuyen_mon,
            cg.ho_ten AS ten_chuyen_gia
        FROM tu_van t
        LEFT JOIN tai_khoan u ON t.nguoi_dung_id = u.id
        LEFT JOIN tai_khoan cg ON t.chuyen_gia_id = cg.id
        LEFT JOIN chuyen_mon cm ON t.chuyen_mon_id = cm.id
        ORDER BY t.ngay_gui DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
}
catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi PDO: ' . $e->getMessage()]);
}
catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>
