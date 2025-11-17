<?php
require_once __DIR__ . '/../../config.php';

if (!isset($_SESSION['expert_id'])) {
    header("Location: dang_nhap.php");
    exit;
}

$db = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_tuvan'])) {
    $id_tuvan = intval($_POST['id_tuvan']);
    $chuyen_gia_id = $_SESSION['expert_id'];
    $ly_do = trim($_POST['ly_do'] ?? 'Không phù hợp chuyên môn');

    // ✅ Kiểm tra quyền
    $stmt = $conn->prepare("SELECT chuyen_gia_id FROM tu_van WHERE id = ?");
    $stmt->execute([$id_tuvan]);
    $cg = $stmt->fetchColumn();

    if ($cg == $chuyen_gia_id) {
       $stmt_upd = $conn->prepare("
    UPDATE tu_van 
    SET trang_thai = 'cho_phan_cong',
        ly_do_tu_choi = ?,
        chuyen_gia_id = NULL,
        ngay_tra_loi = NOW()
    WHERE id = ?
");
$stmt_upd->execute([$ly_do, $id_tuvan]);

        header("Location: danhsachcauhoi.php?success=1");
        exit;
    } else {
        header("Location: danhsachcauhoi.php?error=unauthorized");
        exit;
    }
}
?>
