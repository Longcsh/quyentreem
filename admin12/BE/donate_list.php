<?php
require_once __DIR__ . '/db.php';

try {
    // ======= Lấy tham số tìm kiếm =======
    $q = trim($_GET['q'] ?? '');
    $sql = "SELECT * FROM donate WHERE 1";
    $params = [];

    // ======= Nếu có từ khóa tìm kiếm =======
    if ($q !== '') {
        $sql .= " AND (ho_ten LIKE :kw OR email LIKE :kw OR loi_nhan LIKE :kw)";
        $params[':kw'] = "%$q%";
    }

    $sql .= " ORDER BY ngay_ung_ho DESC";

    // ======= Chuẩn bị và thực thi =======
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    // ======= Trả kết quả JSON =======
    json_ok($rows);

} catch (PDOException $e) {
    json_err("Lỗi SQL: " . $e->getMessage(), 500);
} catch (Exception $e) {
    json_err($e->getMessage(), 500);
}
?>
