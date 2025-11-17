<?php
class Database {
    private $host = "localhost";
    private $dbname = "baovetreem"; // tên database của bạn
    private $username = "root";     // mặc định XAMPP
    private $password = "";         // mặc định rỗng
    public $conn;

   public function connect() {
    $this->conn = null;
    try {
        $this->conn = new PDO(
            "mysql:host=" . $this->host . ";dbname=" . $this->dbname . ";charset=utf8",
            $this->username,
            $this->password
        );
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // ⚙️ Thêm dòng này để set múi giờ Việt Nam
        $this->conn->exec("SET time_zone = '+07:00'");
    } catch (PDOException $e) {
        die("❌ Lỗi kết nối CSDL: " . $e->getMessage());
    }
    return $this->conn;
}

}
