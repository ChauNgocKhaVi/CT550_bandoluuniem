<?php
require_once __DIR__ . '/../src/bootstrap.php';

if (!isset($_GET['order_id'])) {
    exit;
}

$order_id = intval($_GET['order_id']);

$stmt = $PDO->prepare("SELECT product_id FROM OrderDetails WHERE order_id = ? LIMIT 1");
$stmt->execute([$order_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

echo $product ? $product['product_id'] : '';