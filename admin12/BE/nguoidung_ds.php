<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/db.php'; // $pdo có sẵn ở đây

try {
    // ✅ Dùng PDO gốc từ db.php
    $conn = $GLOBALS['pdo'];

    // Lấy dữ liệu lọc
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $status = isset($_GET['status']) ? trim($_GET['status']) : '';

    // Câu SQL gốc
    $sql = "SELECT 
                t.id,
                t.ten_dang_nhap,
                t.ho_ten,
                t.email,
                t.so_dien_thoai,
                v.ten_vai_tro,
                t.trang_thai,
                t.ngay_tao
            FROM tai_khoan t
            JOIN vai_tro v ON t.vai_tro_id = v.id
            WHERE t.vai_tro_id = 1";

    // Cờ kiểm tra
    $hasSearch = ($search !== '');
    $hasStatus = ($status === 'Hoạt động' || $status === 'Bị khóa');

    // Điều kiện tìm kiếm
    if ($hasSearch) {
        $sql .= " AND (
            CAST(t.id AS CHAR) LIKE :kw1
            OR t.ten_dang_nhap LIKE :kw2
            OR t.ho_ten LIKE :kw3
            OR t.email LIKE :kw4
            OR t.so_dien_thoai LIKE :kw5
        )";
    }

    // Điều kiện lọc trạng thái
    if ($hasStatus) {
        $sql .= " AND t.trang_thai = :status";
    }

    $sql .= " ORDER BY t.id DESC";

    // Chuẩn bị truy vấn
    $stmt = $conn->prepare($sql);

    // Bind an toàn (chỉ khi có)
   if ($hasSearch) {
    foreach (['kw1', 'kw2', 'kw3', 'kw4', 'kw5'] as $key) {
        $stmt->bindValue(":$key", "%$search%", PDO::PARAM_STR);
    }
}

    if ($hasStatus) {
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    }

    // Thực thi
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Chuẩn hóa dữ liệu
    foreach ($users as &$u) {
        $u['ho_ten'] = $u['ho_ten'] ?: '-';
        $u['so_dien_thoai'] = $u['so_dien_thoai'] ?: '-';
        $u['ngay_tao'] = $u['ngay_tao'] ?: '';
    }

    echo json_encode([
        'success' => true,
        'items' => $users
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
        // , 'sql' => $sql // bật nếu muốn xem SQL thực tế
    ], JSON_UNESCAPED_UNICODE);
}
?>
