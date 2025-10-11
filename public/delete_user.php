<?php
require_once __DIR__ . '/../src/bootstrap.php';

session_start();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Ép kiểu an toàn

    try {
        // Xóa người dùng
        $stmt = $PDO->prepare("DELETE FROM Users WHERE user_id = :id");
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = "<div class='alert alert-success'>Xóa người dùng thành công!</div>";
        } else {
            $_SESSION['message'] = "<div class='alert alert-warning'>Không tìm thấy người dùng để xóa.</div>";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "<div class='alert alert-danger'>Lỗi khi xóa: " . $e->getMessage() . "</div>";
    }
}

// Quay lại trang danh sách
header("Location: users_admin.php");
exit;