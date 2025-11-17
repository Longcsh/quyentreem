<?php
require_once __DIR__ . '/../../config.php';

if (!isset($_SESSION['expert_id']) || ($_SESSION['role_id'] ?? 0) != 2) {
    header('Location: ../../pages/dang_nhap.php');

    exit;
}

$conn = (new Database())->connect();
$id = $_GET['id'] ?? 0;

if ($id) {
    // 🔹 Lấy đường dẫn file để xóa vật lý
    $stmt = $conn->prepare("SELECT file_url FROM tai_lieu WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file && !empty($file['file_url'])) {
        $file_path = __DIR__ . '/../../' . $file['file_url'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    // 🔹 Xóa bản ghi trong database
    $stmt = $conn->prepare("DELETE FROM tai_lieu WHERE id = ?");
    $stmt->execute([$id]);
}

// ✅ Quay lại danh sách sau khi xóa
header("Location: danhsach_tailieu.php");
exit;
ob_end_flush();
?>
