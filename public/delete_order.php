<?php
session_start();
require_once __DIR__ . '/../src/bootstrap.php';

// ✅ Kiểm tra quyền admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dang_nhap.php");
    exit;
}

$pdo = $PDO;

// ✅ Lấy order_id từ URL
$order_id = $_GET['id'] ?? null;
if (!$order_id) {
    $_SESSION['message'] = "<div class='alert alert-danger'>Không tìm thấy đơn hàng.</div>";
    header("Location: orders.php");
    exit;
}

try {
    // ✅ Bắt đầu transaction
    $pdo->beginTransaction();

    // Xóa chi tiết đơn hàng trước
    $stmtDetails = $pdo->prepare("DELETE FROM OrderDetails WHERE order_id = ?");
    $stmtDetails->execute([$order_id]);

    // Xóa đơn hàng
    $stmtOrder = $pdo->prepare("DELETE FROM Orders WHERE order_id = ?");
    $stmtOrder->execute([$order_id]);

    $pdo->commit();

    $_SESSION['message'] = "<div class='alert alert-success'>Đã xóa đơn hàng và chi tiết liên quan thành công.</div>";
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['message'] = "<div class='alert alert-danger'>Lỗi khi xóa đơn hàng: {$e->getMessage()}</div>";
}

// Quay lại trang danh sách đơn hàng
header("Location: orders_admin.php");
exit;