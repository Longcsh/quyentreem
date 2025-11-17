<?php
require 'db.php';

header('Content-Type: application/json; charset=utf-8');
$action = $_GET['action'] ?? ($_POST['action'] ?? 'list');

switch ($action) {
    // === LẤY DANH SÁCH ===
    case 'list':
        try {
            $stmt = $pdo->query("SELECT * FROM chuyen_mon ORDER BY id ASC");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    // === THÊM MỚI ===
    case 'add':
        $ten = trim($_POST['ten_chuyen_mon'] ?? '');
        $mo_ta = trim($_POST['mo_ta'] ?? '');

        if ($ten === '') {
            echo json_encode(['status' => 'error', 'message' => 'Tên chuyên môn không được để trống']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO chuyen_mon (ten_chuyen_mon, mo_ta) VALUES (?, ?)");
            $stmt->execute([$ten, $mo_ta]);
            echo json_encode(['status' => 'success', 'message' => 'Đã thêm chuyên môn mới']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    // === XÓA ===
    case 'delete':
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Thiếu ID']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM chuyen_mon WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['status' => 'success', 'message' => 'Đã xóa chuyên môn']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    // === SỬA ===
    case 'update':
        $id = intval($_POST['id'] ?? 0);
        $ten = trim($_POST['ten_chuyen_mon'] ?? '');
        $mo_ta = trim($_POST['mo_ta'] ?? '');

        if ($id <= 0 || $ten === '') {
            echo json_encode(['status' => 'error', 'message' => 'Thiếu dữ liệu hợp lệ']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE chuyen_mon SET ten_chuyen_mon = ?, mo_ta = ? WHERE id = ?");
            $stmt->execute([$ten, $mo_ta, $id]);
            echo json_encode(['status' => 'success', 'message' => 'Cập nhật thành công']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Hành động không hợp lệ']);
        break;
}
