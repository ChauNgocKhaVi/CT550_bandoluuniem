<?php
session_start();
require_once __DIR__ . '/../src/bootstrap.php';

// ✅ Kiểm tra quyền admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dang_nhap.php");
    exit;
}

$pdo = $PDO;

// ✅ Lấy ID đơn hàng từ URL
$order_id = $_GET['id'] ?? null;
if (!$order_id) {
    die("❌ Không tìm thấy đơn hàng.");
}

// ✅ Lấy thông tin đơn hàng (KHÔNG LẤY DỮ LIỆU KHÁCH HÀNG)
$stmtOrder = $pdo->prepare("
    SELECT * 
    FROM Orders 
    WHERE order_id = ?
");
$stmtOrder->execute([$order_id]);
$order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("❌ Đơn hàng không tồn tại.");
}

// ✅ Lấy danh sách sản phẩm trong đơn hàng
$stmtDetails = $pdo->prepare("
    SELECT 
        od.quantity,
        p.product_name,
        p.price,
        (od.quantity * p.price) AS total_price
    FROM OrderDetails od
    JOIN Products p ON od.product_id = p.product_id
    WHERE od.order_id = ?
");
$stmtDetails->execute([$order_id]);
$orderDetails = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header_admin.php';
?>

<div class="container py-4">


    <h3 class="mb-3 text-pink">Chi tiết đơn hàng #<?= htmlspecialchars($order_id) ?></h3>

    <!-- 🔹 Bảng chi tiết sản phẩm -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title text-primary mb-3">Danh sách sản phẩm</h5>

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-secondary text-center">
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>Giá (₫)</th>
                        <th>Số lượng</th>
                        <th>Thành tiền (₫)</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($orderDetails)): ?>
                    <?php foreach ($orderDetails as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td class="text-end"><?= number_format($item['price'], 0, ',', '.') ?></td>
                        <td class="text-center"><?= $item['quantity'] ?></td>
                        <td class="text-end text-danger fw-bold"><?= number_format($item['total_price'], 0, ',', '.') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">Không có sản phẩm nào trong đơn hàng này.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Phí vận chuyển:</th>
                        <td class="text-end"><?= number_format($order['shipping_fee'], 0, ',', '.') ?> ₫</td>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-end">Tổng cộng:</th>
                        <td class="text-end text-danger fw-bold">
                            <?= number_format($order['total_amount'], 0, ',', '.') ?> ₫
                        </td>
                    </tr>
                </tfoot>
            </table>

            <p class="mt-3"><strong>Ngày đặt hàng:</strong> <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?>
            </p>
            <p><strong>Trạng thái:</strong>
                <span class="badge bg-<?= [
                                            'pending' => 'secondary',
                                            'confirmed' => 'info',
                                            'shipping' => 'warning',
                                            'delivered' => 'success',
                                            'canceled' => 'danger'
                                        ][$order['status']] ?>">
                    <?= ucfirst($order['status']) ?>
                </span>
            </p>

            <p><strong>Thanh toán:</strong>
                <?= $order['payment_status'] === 'paid'
                    ? '<span class="badge bg-success">Đã thanh toán</span>'
                    : '<span class="badge bg-danger">Chưa thanh toán</span>'; ?>
            </p>
        </div>
        <a href="orders_admin.php" class="btn btn-outline-secondary mb-3">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

</div>

<style>
.text-pink {
    color: #e91e63;
}
</style>