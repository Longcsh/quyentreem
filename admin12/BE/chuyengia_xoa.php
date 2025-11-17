<?php
require_once("db.php");

header('Content-Type: application/json; charset=UTF-8');

// Đọc dữ liệu JSON từ body
$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? 0;

$sql = "DELETE FROM tai_khoan WHERE id = ? AND vai_tro_id = 2";

try {
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$id]);

    echo json_encode(["success" => $success]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>
