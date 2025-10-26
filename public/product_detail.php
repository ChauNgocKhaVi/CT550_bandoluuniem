<?php
require_once __DIR__ . '/../src/bootstrap.php';

$id = $_GET['id'] ?? 0;

$stmt = $PDO->prepare("
    SELECT p.*, b.brand_name, c.category_name
    FROM Products p
    LEFT JOIN Brands b ON p.brand_id = b.brand_id
    LEFT JOIN Categories c ON p.category_id = c.category_id
    WHERE p.product_id = :id
");
$stmt->execute(['id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("❌ Sản phẩm không tồn tại!");
}

$pageTitle = $product['product_name'];
include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header.php';
?>

<div class="container mt-4">
    <div class="row">

        <!-- Ảnh sản phẩm -->
        <div class="col-md-5">
            <img src="<?= htmlspecialchars($product['image']) ?>" class="img-fluid rounded shadow-sm"
                alt="<?= htmlspecialchars($product['product_name']) ?>">
        </div>

        <!-- Thông tin -->
        <div class="col-md-7">
            <h3 class="fw-bold text-dark mb-2">
                <?= htmlspecialchars($product['product_name']) ?>
            </h3>

            <p class="text-muted">
                Thương hiệu: <strong class="text-pink"><?= htmlspecialchars($product['brand_name']) ?></strong>
            </p>

            <div class="price-block mb-3">
                <?php if (!empty($product['original_price']) && $product['original_price'] > $product['price']): ?>
                <span class="old-price me-2">
                    <?= number_format($product['original_price'], 0, ',', '.') ?>₫
                </span>
                <?php endif; ?>

                <span class="price text-pink fw-bold fs-4">
                    <?= number_format($product['price'], 0, ',', '.') ?>₫
                </span>
            </div>

            <p class="mt-3 mb-4"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

            <p class="text-muted">
                Còn lại: <strong><?= $product['stock_quantity'] ?></strong> sản phẩm
            </p>

            <button class="btn btn-add-cart" data-id="<?= $product['product_id'] ?>">
                <i class="bi bi-cart-plus"></i> Thêm vào giỏ
            </button>
        </div>
    </div>

    <hr class="my-5">

    <!-- Gợi ý sản phẩm liên quan -->
    <h4 class="fw-bold text-pink mb-3">Sản phẩm cùng thương hiệu</h4>
    <div id="related-products" class="row"></div>
</div>

<script>
document.querySelector(".btn-add-cart")?.addEventListener("click", async function() {

    const fd = new FormData();
    fd.append("product_id", this.dataset.id);

    const res = await fetch("cart_add.php", {
        method: "POST",
        body: fd
    });
    const data = await res.json();

    if (data.status === "success") {
        showPopup("✅ Đã thêm vào giỏ hàng!");
    } else if (data.status === "not_login") {
        showPopup("⚠️ Vui lòng đăng nhập!", () => window.location = "dang_nhap.php");
    } else {
        showPopup("❌ Có lỗi xảy ra!");
    }
});

// Hàm show popup giữ nguyên giống file products
</script>

<style>
.old-price {
    text-decoration: line-through;
    color: gray;
    font-size: 1rem;
}

.price {
    font-size: 1.7rem;
    font-weight: bold;
}

.btn-add-cart {
    background: #e91e63;
    color: #fff;
    padding: 10px 25px;
    border: none;
    border-radius: 10px;
    font-weight: bold;
}

.btn-add-cart:hover {
    background: #c01752;
}
</style>

<?php include __DIR__ . '/../src/partials/footer.php'; ?>