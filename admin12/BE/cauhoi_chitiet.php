<?php
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'Thiếu ID câu hỏi']);
    exit;
}

try {
    // ✅ Lấy đầy đủ thông tin câu hỏi
    $stmt = $conn->prepare("
        SELECT 
            t.id,
            t.cau_hoi,
            t.tra_loi,
            t.ngay_gui,
            t.ngay_tra_loi,
            t.trang_thai,
            t.anh_minh_hoa,
            u.ho_ten AS nguoi_dung,
            cm.ten_chuyen_mon,
            cg.ho_ten AS chuyen_gia
        FROM tu_van t
        LEFT JOIN tai_khoan u ON t.nguoi_dung_id = u.id
        LEFT JOIN tai_khoan cg ON t.chuyen_gia_id = cg.id
        LEFT JOIN chuyen_mon cm ON t.chuyen_mon_id = cm.id
        WHERE t.id = ?
    ");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // ✅ Gắn đường dẫn đúng đến file ảnh
        if (!empty($row['anh_minh_hoa'])) {
            // chỉ lưu tên file trong DB (vd: treem.png)
          $d['anh_minh_hoa'] = $row['anh_minh_hoa'];


        }

        echo json_encode(['status' => 'success', 'data' => $row], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy câu hỏi']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
