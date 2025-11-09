<?php
require_once __DIR__ . '/../src/bootstrap.php';

// 🔹 Kiểm tra quyền admin
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dang_nhap.php");
    exit;
}

$pdo = $PDO;

// 🔹 Lấy import_id từ URL
$import_id = $_GET['id'] ?? null;
if (!$import_id) {
    $_SESSION['message'] = "Không xác định phiếu nhập cần xóa.";
    header("Location: import_admin.php");
    exit;
}

// 🔹 Lấy chi tiết phiếu nhập
$stmt = $pdo->prepare("SELECT * FROM ImportDetails WHERE import_id = ?");
$stmt->execute([$import_id]);
$details = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 🔹 Trừ số lượng sản phẩm trong kho
foreach ($details as $detail) {
    $stmt = $pdo->prepare("
    UPDATE Products 
    SET stock_quantity = GREATEST(stock_quantity - ?, 0)
    WHERE product_id = ?
");
    $stmt->execute([$detail['quantity'], $detail['product_id']]);
}

// 🔹 Xóa chi tiết phiếu nhập
$pdo->prepare("DELETE FROM ImportDetails WHERE import_id = ?")->execute([$import_id]);

// 🔹 Xóa phiếu nhập
$pdo->prepare("DELETE FROM ImportReceipts WHERE import_id = ?")->execute([$import_id]);

$_SESSION['message'] = "✅ Xóa phiếu nhập thành công!";
header("Location: import_admin.php");
exit;