<?php
// ===============================
// ⚙️ CẤU HÌNH HỆ THỐNG CHUNG
// ===============================

// 🧩 Khởi tạo session an toàn (chỉ một lần cho toàn project)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Định nghĩa đường dẫn gốc tuyệt đối (tự động nhận đúng khi deploy)
define("BASE_PATH", __DIR__);

// ✅ Đường dẫn đến file kết nối CSDL
define("DB_FILE", BASE_PATH . "/cau_hinh/db.php");

// ✅ Nạp file db.php
require_once DB_FILE;

// ✅ Tạo kết nối database dùng chung
$db = new Database();
$conn = $db->connect();

// 🧠 Giải thích:
//  Sau khi require file này, bạn có thể dùng:
//   - $conn để truy vấn DB
//   - $_SESSION để truy cập thông tin người dùng
//   - Không cần gọi session_start() thêm lần nào nữa
