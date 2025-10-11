<?php
require_once __DIR__ . '/../src/bootstrap.php';

session_start();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Ép kiểu an toàn

    try {
        // Xóa danh mục sản phẩm
        $stmt = $PDO->prepare("DELETE FROM Categories WHERE category_id = :id");
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
header("Location: categories_admin.php");
exit;