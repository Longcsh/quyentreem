<?php
require_once __DIR__ . '/../../config.php';

// 🔒 Kiểm tra đăng nhập
if (!isset($_SESSION['expert_id']) || ($_SESSION['role_id'] ?? 0) != 2) {
    header('Location: ../../pages/dang_nhap.php');
    exit;
}

$conn = (new Database())->connect();
$id = $_GET['id'] ?? null;

if ($id) {
    try {
        // 🔹 Lấy thông tin ảnh (nếu có)
        $stmt = $conn->prepare("SELECT anh_dai_dien FROM bai_viet WHERE id = ?");
        $stmt->execute([$id]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($file && !empty($file['anh_dai_dien'])) {
            // Xóa ảnh khỏi thư mục uploads/baiviet/
            $baseDir = realpath(__DIR__ . '/../../uploads/baiviet/') . '/';
            $filePath = $baseDir . basename($file['anh_dai_dien']);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // 🔹 Xóa bài viết trong CSDL
        $sql = "DELETE FROM bai_viet WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        header("Location: danhsachbaiviet.php");
        exit;
    } catch (PDOException $e) {
        echo "❌ Lỗi khi xóa: " . htmlspecialchars($e->getMessage());
    }
} else {
    echo "⚠️ Thiếu ID bài viết.";
}
?>
