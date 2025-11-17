<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');
require_once '../admin12/BE/db.php';

if (!isset($_SESSION['expert_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Chưa đăng nhập']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Dữ liệu gửi không hợp lệ']);
    exit;
}

$id = $_SESSION['expert_id'];

$stmt = $conn->prepare("UPDATE tai_khoan 
  SET ho_ten=?, cccd=?, ngay_sinh=?, so_dien_thoai=?, email=?, noi_lam_viec=?, dia_chi=?, mo_ta=? 
  WHERE id=?");

$stmt->bind_param(
  "ssssssssi",
  $data['ho_ten'], 
  $data['cccd'], 
  $data['nam_sinh'], 
  $data['so_dien_thoai'], 
  $data['email'], 
  $data['noi_lam_viec'], 
  $data['thuong_tru'], 
  $data['mo_ta'],
  $id
);

if ($stmt->execute()) {
  echo json_encode(['status' => 'success', 'message' => 'Cập nhật hồ sơ thành công!'], JSON_UNESCAPED_UNICODE);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Lỗi khi lưu dữ liệu'], JSON_UNESCAPED_UNICODE);
}
