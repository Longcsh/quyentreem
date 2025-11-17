<?php
require_once __DIR__ . '/../../config.php';
if (!isset($_SESSION['expert_id'])) exit;

$chuyen_gia_id = $_SESSION['expert_id'];
$conn->prepare("UPDATE thong_bao SET da_xem = 1 WHERE tai_khoan_id = ?")->execute([$chuyen_gia_id]);
header("Location: thongbao.php");
exit;
