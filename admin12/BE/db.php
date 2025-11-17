<?php

// ======= Cấu hình DB =======
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'baovetreem';
$DB_CHARSET = 'utf8mb4';

// ======= Kết nối PDO =======
$dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=$DB_CHARSET";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // tự động throw lỗi
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // mặc định trả về mảng key
    PDO::ATTR_EMULATE_PREPARES   => false                   // dùng prepare thực
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);

    // ✅ Dòng này cho phép sử dụng $pdo ở mọi file require db.php
    $GLOBALS['pdo'] = $pdo;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Kết nối cơ sở dữ liệu thất bại',
        'detail' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ======= Helpers =======
function json_ok($data) {
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function json_err($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['error' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

function json_res($data, $code = 200) {
    http_response_code($code);
    echo json_encode([
        'status' => 'success',
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ======= Wrapper cho PDO =======
class MyPDOStatementWrapper {
    private $stmt;
    public function __construct($stmt) { $this->stmt = $stmt; }

    public function execute($params = []) {
    // ✅ Nếu có tham số thì mới truyền vào
    if (!empty($params)) {
        return $this->stmt->execute($params);
    } else {
        return $this->stmt->execute();
    }
}

    public function fetch() {
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchAll() {
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function rowCount() {
        return $this->stmt->rowCount();
    }
}

class MyPDOWrapper {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function prepare($query) {
        $stmt = $this->pdo->prepare($query);
        if (!$stmt) throw new Exception("SQL prepare failed");
        return new MyPDOStatementWrapper($stmt);
    }

    public function query($query, $params = []) {
        if (empty($params)) {
            $stmt = $this->pdo->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}

// ✅ Luôn luôn gán biến $db cuối cùng
$db = new MyPDOWrapper($pdo);
$conn = $pdo;
?>
