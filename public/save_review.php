<?php
require_once __DIR__ . '/../src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();

    if (!isset($_SESSION['user'])) {
        http_response_code(403);
        exit("Chưa đăng nhập");
    }

    $user_id = $_SESSION['user']['user_id'];
    $product_id = $_POST['product_id'] ?? null;
    $rating = $_POST['rating'] ?? 0;
    $comment = trim($_POST['comment'] ?? '');

    if (!$product_id || $rating < 1 || $rating > 5) {
        http_response_code(400);
        exit("Dữ liệu không hợp lệ");
    }

    $stmt = $PDO->prepare("
        INSERT INTO Reviews (product_id, user_id, rating, comment)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$product_id, $user_id, $rating, $comment]);

    echo "success";
}