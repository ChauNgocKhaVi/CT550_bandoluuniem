<?php
require_once __DIR__ . '/../src/bootstrap.php';
include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header.php';
?>

<div class="container my-5">
    <h3 class="mb-4">🛒 Giỏ hàng của bạn</h3>

    <?php if (isset($_GET['added'])): ?>
    <div class="alert alert-success">Đã thêm sản phẩm vào giỏ hàng!</div>
    <?php endif; ?>

    <?php if (empty($_SESSION['cart'])): ?>
    <p class="text-muted">Giỏ hàng trống.</p>
    <?php else: ?>
    <table class="table table-bordered text-center align-middle">
        <thead class="table-light">
            <tr>
                <th>Ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Tổng</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $total = 0;
                foreach ($_SESSION['cart'] as $item):
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
            <tr>
                <td><img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>"
                        width="60"></td>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= number_format($item['price'], 0, ',', '.') ?>₫</td>
                <td><?= $item['quantity'] ?></td>
                <td><?= number_format($subtotal, 0, ',', '.') ?>₫</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h5 class="text-end mt-3">Tổng cộng:
        <span class="text-pink fw-bold"><?= number_format($total, 0, ',', '.') ?>₫</span>
    </h5>
    <?php endif; ?>
</div>