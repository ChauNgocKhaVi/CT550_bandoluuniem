<?php
require_once __DIR__ . '/../src/bootstrap.php';
session_start();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        // 🔹 Lấy ảnh cũ từ DB
        $stmt = $PDO->prepare("SELECT image FROM Products WHERE product_id = :id");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            // 🔹 Xóa file ảnh nếu tồn tại
            if (!empty($product['image'])) {
                $imagePath = __DIR__ . '/' . $product['image']; // ✅ Sửa lại đường dẫn đúng

            }

            // 🔹 Xóa sản phẩm trong DB
            $deleteStmt = $PDO->prepare("DELETE FROM Products WHERE product_id = :id");
            $deleteStmt->execute([':id' => $id]);

            if ($deleteStmt->rowCount() > 0) {
                $_SESSION['message'] .= "<div class='alert alert-success mt-2'>✅ Xóa sản phẩm thành công!</div>";
            } else {
                $_SESSION['message'] .= "<div class='alert alert-warning mt-2'>⚠️ Không tìm thấy sản phẩm để xóa.</div>";
            }
        } else {
            $_SESSION['message'] = "<div class='alert alert-warning'>⚠️ Sản phẩm không tồn tại.</div>";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "<div class='alert alert-danger'>❌ Lỗi khi xóa: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// 🔹 Quay lại trang danh sách sản phẩm
header("Location: products_admin.php");
exit;