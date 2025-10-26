<?php
require_once __DIR__ . '/../src/bootstrap.php';

// Lấy danh sách thương hiệu
$stmtBrand = $PDO->prepare("SELECT * FROM Brands ORDER BY brand_name ASC");
$stmtBrand->execute();
$brands = $stmtBrand->fetchAll(PDO::FETCH_ASSOC);



$where = [];
$params = [];

// ✅ Lọc nhiều thương hiệu (IN ...)
if (!empty($_GET['brand_id']) && is_array($_GET['brand_id'])) {
    $brandPlaceholders = [];
    foreach ($_GET['brand_id'] as $i => $brand) {
        $ph = ":brand_$i";
        $brandPlaceholders[] = $ph;
        $params["brand_$i"] = $brand;
    }
    $where[] = "p.brand_id IN (" . implode(",", $brandPlaceholders) . ")";
}

// ✅ Lọc nhiều khoảng giá (checkbox)
if (!empty($_GET['price_range']) && is_array($_GET['price_range'])) {
    $priceConditions = [];

    foreach ($_GET['price_range'] as $range) {
        switch ($range) {
            case '1':
                $priceConditions[] = "p.price < 100000";
                break;
            case '2':
                $priceConditions[] = "p.price BETWEEN 100000 AND 200000";
                break;
            case '3':
                $priceConditions[] = "p.price BETWEEN 200000 AND 300000";
                break;
            case '4':
                $priceConditions[] = "p.price BETWEEN 300000 AND 500000";
                break;
            case '5':
                $priceConditions[] = "p.price BETWEEN 500000 AND 1000000";
                break;
            case '6':
                $priceConditions[] = "p.price > 1000000";
                break;
        }
    }

    if (!empty($priceConditions)) {
        $where[] = "(" . implode(" OR ", $priceConditions) . ")";
    }
}

// ✅ Ghép điều kiện
$sql = "
    SELECT p.*, c.category_name, b.brand_name
    FROM Products p
    LEFT JOIN Categories c ON p.category_id = c.category_id
    LEFT JOIN Brands b ON p.brand_id = b.brand_id
";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

// ✅ Xử lý sắp xếp
$sort = $_GET['sort'] ?? '';

switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY p.price DESC";
        break;
    case 'newest':
        $sql .= " ORDER BY p.created_at DESC";
        break;
    default:
        $sql .= " ORDER BY p.product_id DESC"; // mặc định ổn định
}


$stmt = $PDO->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);




// ✅ Đặt breadcrumb động
$pageTitle = "Sản phẩm";

include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header.php';

?>

<!-- Breadcrumbs -->
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb  ">
            <li class="breadcrumb-item">
                <a href="index.php" class="text-pink fw-semibold">
                    <i class="bi bi-house-door-fill me-1"></i> Trang chủ
                </a>
            </li>
            <li class="breadcrumb-item active text-pink fw-semibold" aria-current="page">
                <i class=" me-1"></i> <?= htmlspecialchars($pageTitle) ?>
            </li>
        </ol>
    </nav>
</div>

<div class="container-fluid my-4">
    <div class="row">

        <!-- ✅ Sidebar bộ lọc -->
        <div class="col-md-3">
            <form method="GET" class="p-3 bg-light rounded shadow-sm">
                <h5 class="text-pink fw-bold mb-3">🎯 Bộ lọc sản phẩm</h5>

                <!-- Giá sản phẩm -->
                <h6 class="fw-semibold">Giá sản phẩm</h6>
                <?php
                $price_options = [
                    "1" => "Dưới 100.000đ",
                    "2" => "100.000đ - 200.000đ",
                    "3" => "200.000đ - 300.000đ",
                    "4" => "300.000đ - 500.000đ",
                    "5" => "500.000đ - 1 triệu",
                    "6" => "Trên 1 triệu"
                ];

                foreach ($price_options as $key => $label):
                ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="price_range[]" value="<?= $key ?>"
                        onchange="this.form.submit()"
                        <?= (!empty($_GET['price_range']) && in_array($key, $_GET['price_range'])) ? 'checked' : '' ?>>

                    <label class="form-check-label"><?= $label ?></label>
                </div>
                <?php endforeach; ?>

                <hr>

                <!-- Thương hiệu -->
                <h6 class="fw-semibold">Thương hiệu</h6>
                <div class="brand-list">
                    <?php foreach ($brands as $b): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="brand_id[]" value="<?= $b['brand_id'] ?>"
                            onchange="this.form.submit()"
                            <?= (!empty($_GET['brand_id']) && in_array($b['brand_id'], $_GET['brand_id'])) ? 'checked' : '' ?>>

                        <label class="form-check-label"><?= htmlspecialchars($b['brand_name']) ?></label>
                    </div>
                    <?php endforeach; ?>
                </div>



            </form>
        </div>

        <!-- ✅ Danh sách sản phẩm -->
        <div class="col-md-9">

            <form method="GET" class="d-flex justify-content-between align-items-center mb-3">

                <?php if (!empty($_GET['sort'])): ?>
                <input type="hidden" name="sort" value="<?= $_GET['sort'] ?>">
                <?php endif; ?>


                <!-- ✅ Giữ lại các điều kiện lọc thương hiệu -->
                <?php if (!empty($_GET['brand_id'])): ?>
                <?php foreach ($_GET['brand_id'] as $brand): ?>
                <input type="hidden" name="brand_id[]" value="<?= $brand ?>">
                <?php endforeach; ?>
                <?php endif; ?>

                <!-- ✅ Giữ lại các điều kiện lọc giá -->
                <?php if (!empty($_GET['price_range'])): ?>
                <?php foreach ($_GET['price_range'] as $range): ?>
                <input type="hidden" name="price_range[]" value="<?= $range ?>">
                <?php endforeach; ?>
                <?php endif; ?>
                <span class="product-count"><?= count($products) ?> sản phẩm</span>

                <select name="sort" class="form-select w-auto" onchange="this.form.submit()">
                    <option value="">Sắp xếp: Mặc định</option>
                    <option value="price_asc" <?= ($_GET['sort'] ?? '') == 'price_asc' ? 'selected' : '' ?>>Giá tăng dần
                    </option>
                    <option value="price_desc" <?= ($_GET['sort'] ?? '') == 'price_desc' ? 'selected' : '' ?>>Giá giảm
                        dần</option>
                    <option value="newest" <?= ($_GET['sort'] ?? '') == 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                </select>

            </form>




            <?php if (empty($products)): ?>
            <p class="text-muted">Hiện chưa có sản phẩm nào.</p>

            <?php else: ?>
            <div class="row g-4">
                <?php foreach ($products as $p): ?>
                <div class="col-md-4 col-sm-6">
                    <div class="card product-card h-55">
                        <a href="product_detail.php?id=<?= $p['product_id'] ?>">
                            <img src="<?= htmlspecialchars($p['image'] ?? 'uploads/no-image.jpg') ?>"
                                class="product-img" alt="<?= htmlspecialchars($p['product_name']) ?>">
                        </a>

                        <div class="card-body text-center">
                            <p class="text-muted small mb-1"><?= htmlspecialchars($p['brand_name']) ?></p>
                            <a href="product_detail.php?id=<?= $p['product_id'] ?>" class="text-decoration-none">
                                <h6 class="fw-bold text-dark mb-2 product-name-hover">
                                    <?= htmlspecialchars($p['product_name']) ?>
                                </h6>
                            </a>

                            <div class="mb-3">
                                <?php if (!empty($p['original_price']) && $p['original_price'] > $p['price']): ?>
                                <span class="old-price">
                                    <?= number_format($p['original_price'], 0, ',', '.') ?>₫
                                </span>
                                <?php endif; ?>
                                <span class="price ms-2">
                                    <?= number_format($p['price'], 0, ',', '.') ?>₫
                                </span>
                            </div>

                            <button class="btn btn-cart add-to-cart" data-id="<?= $p['product_id'] ?>"
                                title="Thêm vào giỏ">
                                <i class="bi bi-cart-plus"></i>
                            </button>


                        </div>

                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </div>

    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {

    // ✅ Thêm vào giỏ hàng không rời trang
    document.querySelectorAll(".add-to-cart").forEach(btn => {
        btn.addEventListener("click", async () => {
            await addToCart(btn.dataset.id, false);
        });
    });


    async function addToCart(id) {
        const fd = new FormData();
        fd.append('product_id', id);

        try {
            const res = await fetch('cart_add.php', {
                method: 'POST',
                body: fd
            });

            const data = await res.json();

            if (data.status === "not_login") {
                showPopup("⚠️ Bạn cần đăng nhập để thêm sản phẩm vào giỏ!", () => {
                    window.location.href = "dang_nhap.php";
                });
                return;
            }

            if (data.status === "success") {
                showPopup("✅ Đã thêm vào giỏ hàng!");
            } else {
                showPopup("❌ Có lỗi xảy ra!");
            }
        } catch (e) {
            showPopup("⚠️ Lỗi kết nối!");
        }
    }



    // ✅ Popup giống thiết kế của bạn
    function showPopup(msg, callback = null) {
        const overlay = document.createElement("div");
        overlay.className = "toast-overlay";
        overlay.innerHTML = `
        <div class="toast-center">
            <p>${msg}</p>
            <button id="close-popup">OK</button>
        </div>
    `;

        document.body.appendChild(overlay);
        overlay.querySelector("#close-popup").onclick = () => {
            overlay.remove();
            if (callback) callback();
        };
    }


});
</script>

<style>
:root {
    --pink-main: #e91e63;
    --pink-light: #fce4ec;
}

body {
    font-family: 'Segoe UI', sans-serif;
    background-color: #fff;
}

.text-pink {
    color: var(--pink-main);
}

.product-card {
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
}

.card-img-top {
    height: 100px;
    object-fit: cover;
    border-bottom: 1px solid #eee;
}
</style>

<style>
/* ✅ Style lại nút */

.btn-cart,
.btn-buy {
    border-radius: 10px;
    font-size: 0.9rem;
    padding: 8px 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 36px;
    font-weight: 600;
    transition: 0.25s;
}

/* ✅ Icon giỏ hàng */
.btn-cart {
    border: 2px solid #e91e63;
    background: transparent;
    color: #e91e63;
}

.btn-cart i {
    font-size: 1.25rem;
}

.btn-cart:hover {
    background-color: #e91e63;
    color: #fff;
}

/* ✅ Mua ngay */
.btn-buy {
    background-color: #e91e63;
    color: white;
    border: none;
}

.btn-buy:hover {
    background-color: #c01752;
    color: #fff;
}

.btn-buy i {
    font-size: 1.2rem;
}


.product-card {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
    height: 100%;
}

.product-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
}

.product-img {
    width: 100%;
    height: 250px;
    object-fit: cover;
}

.price {
    font-weight: bold;
    font-size: 1.1rem;
}

.old-price {
    text-decoration: line-through;
    color: gray;
    font-size: 0.9rem;
}

/* Popup overlay */
.toast-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
    z-index: 9998;
    display: flex;
    justify-content: center;
    align-items: center;
}

.toast-center {
    background-color: white;
    padding: 25px 35px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
    z-index: 9999;
    animation: popupIn 0.3s ease;
    width: 350px;
}

@keyframes popupIn {
    from {
        transform: scale(0.8);
        opacity: 0;
    }

    to {
        transform: scale(1);
        opacity: 1;
    }
}

.toast-center button {
    background-color: #e91e63;
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 8px;
    cursor: pointer;
}

.toast-center button:hover {
    background-color: #9e1844ff;
}


/* ✅ Chỉ hiển thị 4 thương hiệu đầu tiên nhưng có thể cuộn để xem thêm */
.brand-list {
    height: 100px;
    /* Điều chỉnh tùy theo chiều cao bạn muốn */
    overflow-y: auto;
    padding-right: 5px;
}

/* ✅ Tùy chọn: thanh cuộn đẹp hơn */
.brand-list::-webkit-scrollbar {
    width: 6px;
}

.brand-list::-webkit-scrollbar-thumb {
    background: #e91e63;
    border-radius: 10px;
}

.product-count {
    font-size: 1.4rem;
    /* Chữ lớn hơn */
    font-weight: 700;
    /* Đậm hơn */
    color: var(--pink-main);
    /* Màu hồng chủ đạo */
}
</style>