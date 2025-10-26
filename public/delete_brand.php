<?php
require_once __DIR__ . '/../src/bootstrap.php';

session_start();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Ép kiểu an toàn

    try {
        // Xóa danh mục sản phẩm
        $stmt = $PDO->prepare("DELETE FROM Brands WHERE brand_id = :id");
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = "<div class='alert alert-success'>Xóa thương hiệu thành công!</div>";
        } else {
            $_SESSION['message'] = "<div class='alert alert-warning'>Không tìm thấy thương hiệu để xóa.</div>";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "<div class='alert alert-danger'>Lỗi khi xóa: " . $e->getMessage() . "</div>";
    }
}

// Quay lại trang danh sách
header("Location: brands_admin.php");
exit;