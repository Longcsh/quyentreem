<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json; charset=UTF-8');

$conn = (new Database())->connect();
$id = $_GET['id'] ?? null;

if (!$id) {
  echo json_encode(['success' => false, 'message' => 'Thiếu ID tài liệu']);
  exit;
}

$stmt = $conn->prepare("SELECT id, tieu_de, mo_ta, file_url, loai_tai_lieu, 
                        DATE_FORMAT(ngay_upload, '%d/%m/%Y') AS ngay_upload
                        FROM tai_lieu WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($data) {
  echo json_encode(['success' => true, 'data' => $data]);
} else {
  echo json_encode(['success' => false, 'message' => 'Không tìm thấy tài liệu']);
}
