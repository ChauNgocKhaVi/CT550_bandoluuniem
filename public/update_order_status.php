<?php
require_once __DIR__ . '/../src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? null;
    $status = $_POST['status'] ?? null;
    $user_id = $_SESSION['user']['user_id'] ?? null;

    if ($order_id && $status && $user_id) {
        $stmt = $PDO->prepare("UPDATE Orders SET status = ? WHERE order_id = ? AND user_id = ?");
        $stmt->execute([$status, $order_id, $user_id]);
    }

    header('Location: my_orders.php');
    exit;
}