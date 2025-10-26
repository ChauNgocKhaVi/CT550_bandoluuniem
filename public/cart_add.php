<?php
require_once __DIR__ . '/../src/bootstrap.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'not_login']);
    exit;
}

// Nếu giỏ hàng chưa tồn tại thì khởi tạo
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Kiểm tra nếu có dữ liệu gửi từ form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int) $_POST['product_id'];

    // Lấy thông tin sản phẩm từ CSDL
    $stmt = $PDO->prepare("SELECT product_id, product_name, price, image FROM Products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Nếu sản phẩm đã có trong giỏ -> tăng số lượng
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += 1;
        } else {
            // Nếu chưa có -> thêm mới
            $_SESSION['cart'][$product_id] = [
                'id' => $product['product_id'],
                'name' => $product['product_name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => 1
            ];
        }

        // Sau khi thêm xong, chuyển về trang giỏ hàng hoặc quay lại trang cũ
        header("Location: cart.php?added=1");
        exit;
    } else {
        echo "Không tìm thấy sản phẩm.";
    }
} else {
    header("Location: index.php");
    exit;
}