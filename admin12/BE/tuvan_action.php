<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/them_thong_bao.php';
header('Content-Type: application/json; charset=UTF-8');

$action = $_REQUEST['action'] ?? null;
if (!$action) {
    echo json_encode(['success' => false, 'message' => 'Thiếu tham số action']);
    exit;
}

try {
    switch ($action) {

        /* ==============================================================
         🟦 1️⃣ AUTO ASSIGN – Hệ thống tự động gán chuyên gia khi user gửi câu hỏi
        ============================================================== */
        case 'auto_assign':
            $tuvan_id = $_POST['tuvan_id'] ?? null;
            if (!$tuvan_id) {
                echo json_encode(['success' => false, 'message' => 'Thiếu ID câu hỏi']);
                exit;
            }

            // Lấy chuyên môn của câu hỏi
            $stmt = $conn->prepare("SELECT chuyen_mon_id FROM tu_van WHERE id = ?");
            $stmt->execute([$tuvan_id]);
            $cm_id = $stmt->fetchColumn();

            if (!$cm_id) {
                echo json_encode(['success' => false, 'message' => 'Không xác định được chuyên môn của câu hỏi']);
                exit;
            }

            // Tìm chuyên gia có cùng chuyên môn, ít bận nhất
            $stmt = $conn->prepare("
                SELECT tk.id
                FROM tai_khoan tk
                JOIN chuyen_mon cm ON tk.chuyen_mon_id = cm.id
                LEFT JOIN tu_van tv 
                    ON tv.chuyen_gia_id = tk.id
                    AND tv.trang_thai IN ('dang_cho_tra_loi','cho_phan_cong')
                WHERE tk.chuyen_mon_id = ?
                  AND tk.vai_tro_id = 2
                  AND tk.trang_thai = 'hoat_dong'
                GROUP BY tk.id
                ORDER BY COUNT(tv.id) ASC
                LIMIT 1
            ");
            $stmt->execute([$cm_id]);
            $cg = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cg) {
                echo json_encode(['success' => false, 'message' => 'Không có chuyên gia phù hợp để gán']);
                exit;
            }

            // Gán chuyên gia
            $cg_id = $cg['id'];
            $update = $conn->prepare("
                UPDATE tu_van 
                SET chuyen_gia_id = ?, trang_thai = 'dang_cho_tra_loi'
                WHERE id = ?
            ");
            $update->execute([$cg_id, $tuvan_id]);

            themThongBao($cg_id, "Bạn vừa được gán một câu hỏi mới để tư vấn.");
            echo json_encode(['success' => true, 'message' => '✅ Đã tự động gán chuyên gia phù hợp']);
            break;


        /* ==============================================================
         🟩 2️⃣ PHÂN CÔNG LẠI – Admin gán chuyên gia mới
        ============================================================== */
        case 'phancong':
            $tuvan_id = $_POST['tuvan_id'] ?? null;
            $chuyen_gia_id = $_POST['chuyen_gia_id'] ?? null;

            if (!$tuvan_id) {
                echo json_encode(['success' => false, 'message' => 'Thiếu ID câu hỏi']);
                exit;
            }

            // Nếu admin không chỉ định cụ thể → tìm chuyên gia ít bận nhất
            if (!$chuyen_gia_id) {
                $auto = $conn->prepare("
                    SELECT tk.id
                    FROM tai_khoan tk
                    JOIN chuyen_mon cm ON tk.chuyen_mon_id = cm.id
                    LEFT JOIN tu_van tv 
                        ON tv.chuyen_gia_id = tk.id
                        AND tv.trang_thai IN ('dang_cho_tra_loi','cho_phan_cong')
                    WHERE tk.chuyen_mon_id = (SELECT chuyen_mon_id FROM tu_van WHERE id = ?)
                      AND tk.vai_tro_id = 2
                      AND tk.trang_thai = 'hoat_dong'
                    GROUP BY tk.id
                    ORDER BY COUNT(tv.id) ASC
                    LIMIT 1
                ");
                $auto->execute([$tuvan_id]);
                $res = $auto->fetch(PDO::FETCH_ASSOC);
                $chuyen_gia_id = $res['id'] ?? null;
            }

            if (!$chuyen_gia_id) {
                // ❌ Nếu không có chuyên gia phù hợp → đánh dấu bị từ chối (kết thúc)
                $conn->prepare("
                    UPDATE tu_van 
                    SET trang_thai = 'bi_tu_choi',
                        ly_do_tu_choi = 'Không có chuyên gia phù hợp'
                    WHERE id = ?
                ")->execute([$tuvan_id]);

                echo json_encode(['success' => false, 'message' => '❌ Không có chuyên gia phù hợp, đã chuyển sang Bị từ chối']);
                exit;
            }

            // Gán chuyên gia mới thành công
            $update = $conn->prepare("
                UPDATE tu_van 
                SET chuyen_gia_id = ?, trang_thai = 'dang_cho_tra_loi', ly_do_tu_choi = NULL
                WHERE id = ?
            ");
            $update->execute([$chuyen_gia_id, $tuvan_id]);

            themThongBao($chuyen_gia_id, "Bạn vừa được phân công lại một câu hỏi để tư vấn.");
            echo json_encode(['success' => true, 'message' => '✅ Đã phân công lại chuyên gia mới']);
            break;
        /* ==============================================================
         🟥 2.1️⃣ ADMIN TỪ CHỐI PHÂN CÔNG CHUYÊN GIA (quá tải)
        ============================================================== */
        /* ==============================================================
 🟥 2.1️⃣ ADMIN TỪ CHỐI PHÂN CÔNG CHUYÊN GIA (quá tải hoặc không phù hợp)
============================================================== */
case 'tu_choi_phan_cong':
    $tuvan_id = $_POST['tuvan_id'] ?? null;
    $chuyen_gia_id = $_POST['chuyen_gia_id'] ?? null; // có thể là 0 nếu từ chối toàn bộ
    $ly_do = trim($_POST['ly_do_tu_choi'] ?? 'Admin từ chối phân công do quá tải hoặc không có chuyên gia phù hợp');

    // ✅ chỉ bắt buộc có tuvan_id
    if (!$tuvan_id) {
        echo json_encode(['success' => false, 'message' => 'Thiếu tham số tuvan_id']);
        exit;
    }

    // Nếu admin từ chối toàn bộ chuyên gia → để chuyen_gia_id = NULL
    $stmt = $conn->prepare("
        UPDATE tu_van 
        SET trang_thai = 'bi_tu_choi',
            chuyen_gia_id = NULL,
            ly_do_tu_choi = ?
        WHERE id = ?
    ");
    $stmt->execute([$ly_do, $tuvan_id]);

    echo json_encode([
        'success' => true,
        'message' => '❌ Đã từ chối phân công cho câu hỏi này.'
    ]);
    break;



        /* ==============================================================
         🟥 3️⃣ TỪ CHỐI – Chuyên gia từ chối → Admin phân công lại
        ============================================================== */
        case 'tu_choi':
            $tuvan_id = $_POST['tuvan_id'] ?? null;
            $ly_do = trim($_POST['ly_do_tu_choi'] ?? '');

            if (!$tuvan_id) {
                echo json_encode(['success' => false, 'message' => 'Thiếu ID câu hỏi']);
                exit;
            }

            // 🟡 Chuyên gia từ chối → chuyển sang "cho_phan_cong" để admin xử lý
            $update = $conn->prepare("
                UPDATE tu_van 
                SET trang_thai = 'cho_phan_cong',
                    ly_do_tu_choi = ?,
                    chuyen_gia_id = NULL
                WHERE id = ?
            ");
            $update->execute([$ly_do, $tuvan_id]);

            echo json_encode(['success' => true, 'message' => '⏳ Câu hỏi đã được chuyển lại cho admin để phân công chuyên gia khác']);
            break;


        /* ==============================================================
         🟨 4️⃣ DANH SÁCH CẦN PHÂN CÔNG LẠI
        ============================================================== */
       case 'list_canphancong':
    $sql = "
        SELECT 
            tv.id,
            nd.ho_ten AS ten_nguoi_dung,
            cm.ten_chuyen_mon,
            tv.cau_hoi,
            cg.ho_ten AS ten_chuyen_gia_tu_choi,
            tv.ly_do_tu_choi
        FROM tu_van tv
        LEFT JOIN tai_khoan nd ON nd.id = tv.nguoi_dung_id
        LEFT JOIN chuyen_mon cm ON cm.id = tv.chuyen_mon_id
        LEFT JOIN tai_khoan cg 
            ON cg.id = (
                SELECT t2.chuyen_gia_id 
                FROM tu_van t2 
                WHERE t2.id = tv.id AND t2.trang_thai = 'tu_choi'
                LIMIT 1
            )
        WHERE tv.trang_thai = 'cho_phan_cong'
        ORDER BY tv.ngay_gui DESC
    ";
    $stmt = $conn->query($sql);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $data]);
    break;

            /* ==============================================================
 🧩 4.1️⃣ DANH SÁCH CHUYÊN GIA PHÙ HỢP (để admin chọn thủ công)
 ============================================================== */

case 'list_chuyengia':
    $tuvan_id = $_GET['tuvan_id'] ?? null;
    if (!$tuvan_id) {
        echo json_encode(['success' => false, 'message' => 'Thiếu ID câu hỏi']);
        exit;
    }

    // 🔹 Lấy chuyên môn của câu hỏi
    $stmt = $conn->prepare("SELECT chuyen_mon_id FROM tu_van WHERE id = ?");
    $stmt->execute([$tuvan_id]);
    $cm_id = $stmt->fetchColumn();

    if (!$cm_id) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy chuyên môn câu hỏi']);
        exit;
    }

    /* 
       🔹 Lấy danh sách chuyên gia:
       - Có cùng chuyên môn
       - Vai trò = 2 (chuyên gia)
       - Trạng thái hoạt động
       - Không nằm trong danh sách chuyên gia đã từ chối câu hỏi này
       - Đếm số câu hỏi đang chờ trả lời để sắp xếp (ít trước, nhiều sau)
    */
    $sql = "
        SELECT 
            tk.id,
            tk.ho_ten,
            tk.email,
            COUNT(tv.id) AS so_cau_hoi
        FROM tai_khoan tk
        LEFT JOIN tu_van tv 
            ON tv.chuyen_gia_id = tk.id 
            AND tv.trang_thai = 'dang_cho_tra_loi'
        WHERE 
            tk.chuyen_mon_id = ? 
            AND tk.vai_tro_id = 2 
            AND tk.trang_thai = 'Hoạt động'
            AND tk.id NOT IN (
                SELECT chuyen_gia_id 
                FROM tu_van 
                WHERE id = ? 
                  AND chuyen_gia_id IS NOT NULL 
                  AND trang_thai = 'tu_choi'
            )
        GROUP BY tk.id
        ORDER BY so_cau_hoi ASC, tk.ho_ten ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$cm_id, $tuvan_id]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $data]);
    break;




        /* ==============================================================
         🟥 5️⃣ DANH SÁCH BỊ TỪ CHỐI (chỉ khi admin từ chối)
        ============================================================== */
        case 'list_bitu_choi':
            $sql = "
                SELECT tv.*, nd.ho_ten AS ten_nguoi_dung, cm.ten_chuyen_mon
                FROM tu_van tv
                LEFT JOIN tai_khoan nd ON nd.id = tv.nguoi_dung_id
                LEFT JOIN chuyen_mon cm ON cm.id = tv.chuyen_mon_id
                WHERE tv.trang_thai = 'bi_tu_choi'
                ORDER BY tv.ngay_gui DESC
            ";
            $stmt = $conn->query($sql);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $data]);
            break;


        default:
            echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
            break;
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
}
?>
