<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php'; // File này cần khởi tạo biến $pdo (đối tượng PDO)

// Lấy dữ liệu từ POST
$ho_ten = trim($_POST['ho_ten'] ?? '');
$email = trim($_POST['email'] ?? '');
$so_tien = floatval($_POST['so_tien'] ?? 0);
$loi_nhan = trim($_POST['loi_nhan'] ?? '');
$an_danh = intval($_POST['an_danh'] ?? 0);

// Kiểm tra dữ liệu đầu vào
if (!$ho_ten || $so_tien <= 0) {
    echo json_encode(['success' => false, 'message' => 'Thiếu họ tên hoặc số tiền hợp lệ']);
    exit;
}

try {
    // Câu SQL
    $sql = "INSERT INTO donate (ho_ten, email, so_tien, loi_nhan, an_danh, ngay_ung_ho)
            VALUES (:ho_ten, :email, :so_tien, :loi_nhan, :an_danh, NOW())";

    // Chuẩn bị truy vấn
    $stmt = $pdo->prepare($sql);

    // Gán giá trị
    $stmt->bindParam(':ho_ten', $ho_ten, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':so_tien', $so_tien);
    $stmt->bindParam(':loi_nhan', $loi_nhan, PDO::PARAM_STR);
    $stmt->bindParam(':an_danh', $an_danh, PDO::PARAM_INT);

    // Thực thi
    $stmt->execute();

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi SQL: ' . $e->getMessage(),
        'sql' => $sql
    ]);
}
?>
