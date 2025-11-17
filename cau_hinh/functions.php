<?php
require_once __DIR__ . '/db.php';

class TaiLieuFunction {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }
    public function timKiemTaiLieu($keyword) {
    $sql = "SELECT id, ten_tai_lieu, mo_ta, file_url 
            FROM tai_lieu 
            WHERE trang_thai = 'da_duyet'
            AND (ten_tai_lieu LIKE :kw OR mo_ta LIKE :kw)
            ORDER BY ngay_upload DESC";
    $stmt = $this->conn->prepare($sql);
    $kw = "%" . $keyword . "%";
    $stmt->bindParam(':kw', $kw);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 🟢 Đếm tổng số tài liệu theo loại
public function countTaiLieu($loai) {
    $sql = "SELECT COUNT(*) FROM tai_lieu WHERE loai_tai_lieu = ? AND trang_thai = 'da_duyet'";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$loai]);
    return (int) $stmt->fetchColumn();
}

// 🟢 Lấy tài liệu theo trang (phân trang)
public function getTaiLieuPhanTrang($loai, $limit, $offset) {
    $sql = "SELECT * FROM tai_lieu 
            WHERE loai_tai_lieu = ? 
            AND trang_thai = 'da_duyet' 
            ORDER BY ngay_upload DESC 
            LIMIT ? OFFSET ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(1, $loai, PDO::PARAM_STR);
    $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // 🟢 Lấy danh sách tài liệu theo loại
public function getTaiLieu($loai) {
    $sql = "SELECT * FROM tai_lieu 
            WHERE loai_tai_lieu = ? 
            AND trang_thai = 'da_duyet' 
            ORDER BY ngay_upload DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$loai]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    // 🟢 Lấy danh sách bình luận theo loại
    public function getBinhLuan($loai) {
        $sql = "SELECT * FROM binh_luan WHERE loai_noi_dung = ? ORDER BY ngay_gio DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$loai]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🟢 Thêm bình luận mới
    public function themBinhLuan($loai, $ten, $email, $noi_dung) {
        $sql = "INSERT INTO binh_luan (loai_noi_dung, ten, email, noi_dung) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$loai, $ten, $email, $noi_dung]);
    }
}
class BaiVietFunction {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }
public function countBaiViet($loai) {
    $sql = "SELECT COUNT(*) FROM bai_viet 
            WHERE loai_bai_viet = ? AND trang_thai = 'da_duyet'";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$loai]);
    return (int)$stmt->fetchColumn();
}

public function getBaiVietPhanTrang($loai, $limit, $offset) {
    $sql = "SELECT * FROM bai_viet 
            WHERE loai_bai_viet = ? AND trang_thai = 'da_duyet'
            ORDER BY ngay_dang DESC
            LIMIT ? OFFSET ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(1, $loai, PDO::PARAM_STR);
    $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // 🟣 Lấy danh sách bài viết theo loại
  // 🟣 Lấy danh sách bài viết theo loại
public function getBaiViet($loai) {
    $sql = "SELECT * FROM bai_viet 
            WHERE loai_bai_viet = ? 
              AND trang_thai = 'da_duyet'
            ORDER BY ngay_dang DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$loai]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // 🟣 Thêm bình luận cho bài viết
    public function themBinhLuan($loai, $ten, $email, $noi_dung) {
        $sql = "INSERT INTO binh_luan (loai_noi_dung, ten, email, noi_dung) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$loai, $ten, $email, $noi_dung]);
    }

    // 🟣 Lấy bình luận
    public function getBinhLuan($loai) {
        $sql = "SELECT * FROM binh_luan WHERE loai_noi_dung = ? ORDER BY ngay_gio DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$loai]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function timKiemBaiViet($keyword) {
    $sql = "SELECT id, tieu_de, noi_dung, anh_dai_dien 
            FROM bai_viet 
            WHERE trang_thai = 'da_duyet' 
            AND (tieu_de LIKE :kw OR noi_dung LIKE :kw)
            ORDER BY ngay_dang DESC";
    $stmt = $this->conn->prepare($sql);
    $kw = "%" . $keyword . "%";
    $stmt->bindParam(':kw', $kw, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);

}
}

?>
