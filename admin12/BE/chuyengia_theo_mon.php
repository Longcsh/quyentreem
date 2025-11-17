<?php
// 📄 BE/chuyengia_theo_mon.php
header('Content-Type: application/json; charset=UTF-8');

// ⚙️ Cho phép gọi API từ mọi frontend (tự động nhận domain)
$allowed_origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header("Access-Control-Allow-Origin: $allowed_origin");
header("Access-Control-Allow-Credentials: true");

require_once __DIR__ . '/db.php';

try {
    // 📥 Nhận tham số từ FE
    $chuyen_mon_id = $_GET['chuyen_mon_id'] ?? null;
    $ngay_gio = $_GET['ngay_gio'] ?? null;

    if (!$chuyen_mon_id || !$ngay_gio) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Thiếu tham số chuyên môn hoặc ngày giờ'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ✅ Lấy danh sách chuyên gia cùng chuyên môn, và kiểm tra ai rảnh
    $sql = "
        SELECT 
            cg.id,
            cg.ho_ten,
            cm.ten_chuyen_mon,
            CASE 
                WHEN EXISTS (
                    SELECT 1 
                    FROM lich_hen lh
                    WHERE lh.chuyen_gia_id = cg.id
                      AND lh.ngay_gio = ?
                      AND lh.trang_thai IN ('da_duyet','cho_xac_nhan')
                )
                THEN 'bận'
                ELSE 'rảnh'
            END AS trang_thai_lich
        FROM chuyen_gia cg
        JOIN chuyen_mon cm ON cg.chuyen_mon_id = cm.id
        WHERE cg.chuyen_mon_id = ?
        ORDER BY trang_thai_lich DESC, cg.ho_ten ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$ngay_gio, $chuyen_mon_id]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'chuyen_gia' => $data
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi CSDL: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
