<?php
require_once __DIR__ . '/db.php';

// ======= Thiết lập header tải file CSV =======
header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=baocao_donate_" . date('Ymd_His') . ".csv");

// ======= Ghi BOM UTF-8 để Excel hiển thị đúng tiếng Việt =======
echo "\xEF\xBB\xBF";

// ======= Mở luồng ghi ra output =======
$out = fopen('php://output', 'w');

// ======= Ghi tiêu đề cột =======
fputcsv($out, ['Họ tên', 'Email', 'Số tiền (₫)', 'Lời nhắn', 'Ngày ủng hộ', 'Ẩn danh']);

// ======= Lấy dữ liệu từ DB =======
try {
    $sql = "SELECT ho_ten, email, so_tien, loi_nhan, ngay_ung_ho, an_danh
            FROM donate
            ORDER BY ngay_ung_ho DESC";
    
    // Dùng wrapper $db (hoặc trực tiếp $conn đều được)
    $rows = $db->query($sql);

    foreach ($rows as $row) {
        fputcsv($out, [
            $row['ho_ten'],
            $row['email'],
            number_format($row['so_tien'], 0, ',', '.'), // định dạng số tiền
            $row['loi_nhan'],
            $row['ngay_ung_ho'],
            $row['an_danh'] ? 'Có' : 'Không'
        ]);
    }

} catch (PDOException $e) {
    // Nếu lỗi, ghi dòng thông báo lỗi vào file CSV
    fputcsv($out, ['Lỗi khi xuất dữ liệu:', $e->getMessage()]);
}

fclose($out);
exit;
?>
