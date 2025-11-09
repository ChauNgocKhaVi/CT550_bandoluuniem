<?php
require_once __DIR__ . '/../src/bootstrap.php';

// ✅ Kiểm tra quyền admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dang_nhap.php");
    exit;
}

$pdo = $PDO;

// ✅ Kiểm tra ID hợp lệ
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = '<div class="alert alert-danger text-center">ID nhà cung cấp không hợp lệ.</div>';
    header('Location: suppliers.php');
    exit;
}

$supplier_id = (int) $_GET['id'];

// ✅ Kiểm tra xem nhà cung cấp có tồn tại không
$stmt = $pdo->prepare("SELECT * FROM Suppliers WHERE supplier_id = ?");
$stmt->execute([$supplier_id]);
$supplier = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$supplier) {
    $_SESSION['message'] = '<div class="alert alert-danger text-center">Không tìm thấy nhà cung cấp!</div>';
    header('Location: suppliers.php');
    exit;
}

// ✅ Thực hiện xóa
try {
    $stmt = $pdo->prepare("DELETE FROM Suppliers WHERE supplier_id = ?");
    $stmt->execute([$supplier_id]);

    $_SESSION['message'] = '<div class="alert alert-success text-center">Xóa nhà cung cấp thành công!</div>';
} catch (PDOException $e) {
    // Nếu nhà cung cấp đang được tham chiếu trong bảng khác (ví dụ Thuốc)
    $_SESSION['message'] = '<div class="alert alert-warning text-center">
        Không thể xóa nhà cung cấp này vì đang được sử dụng trong dữ liệu khác.
    </div>';
}

header('Location: suppliers_admin.php');
exit;