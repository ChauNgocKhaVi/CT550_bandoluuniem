<?php
require_once __DIR__ . '/../src/bootstrap.php'; // file chứa kết nối PDO


$q = $_GET['q'] ?? ''; // lấy từ khóa tìm kiếm
$products = [];

if (!empty($q)) {
    $stmt = $PDO->prepare("
        SELECT p.*, 
               c.category_name, 
               b.brand_name
        FROM Products p
        LEFT JOIN Categories c ON p.category_id = c.category_id
        LEFT JOIN Brands b ON p.brand_id = b.brand_id
        WHERE p.product_name LIKE :keyword
           OR p.description LIKE :keyword
           OR b.brand_name LIKE :keyword
           OR c.category_name LIKE :keyword
    ");
    $stmt->execute(['keyword' => "%$q%"]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ✅ Đặt breadcrumb động
$pageTitle = "Tìm kiếm";

include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header.php';
?>

<style>
.text-pink {
    color: #e91e63;
}

.btn-pink {
    background-color: #e91e63;
    color: white;
    border: none;
}

.btn-pink:hover {
    background-color: #9a0b42ff;
    color: white;
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
    height: 220px;
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
</style>

<body>

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

    <div class="container my-5">
        <?php if (isset($_SESSION['added_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['added_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['added_message']); // xóa thông báo sau khi hiển thị 
            ?>
        <?php endif; ?>






        <h4 class="mb-4">🔍 Kết quả tìm kiếm cho:
            <span class="text-pink">"<?= htmlspecialchars($q) ?>"</span>
        </h4>

        <?php if (empty($q)): ?>
        <p class="text-muted">Vui lòng nhập từ khóa để tìm kiếm sản phẩm.</p>

        <?php elseif (empty($products)): ?>
        <p class="text-muted">Không tìm thấy sản phẩm nào phù hợp.</p>

        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($products as $p): ?>
            <div class="col-md-3 col-sm-6">
                <div class="card product-card h-100">
                    <img src="<?= htmlspecialchars($p['image'] ?? 'uploads/no-image.jpg') ?>"
                        alt="<?= htmlspecialchars($p['product_name']) ?>" class="product-img">

                    <div class="card-body text-center">
                        <?php if (!empty($p['brand_name'])): ?>
                        <p class="text-muted small mb-1"><?= htmlspecialchars($p['brand_name']) ?></p>
                        <?php endif; ?>

                        <h6 class="fw-bold text-dark mb-2">
                            <?= htmlspecialchars($p['product_name']) ?>
                        </h6>

                        <div class="mb-3">
                            <span class="price">
                                <?= number_format($p['price'], 0, ',', '.') ?>₫
                            </span>
                        </div>


                        <form action="cart_add.php" method="POST" onsubmit="return showAddedMessage()">
                            <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                            <button type="button" class="btn btn-sm btn-pink add-to-cart"
                                data-id="<?= $p['product_id'] ?>">
                                🛒 Mua ngay
                            </button>

                        </form>


                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

</body>
<style>
/* Nền mờ toàn màn hình */
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

/* Hộp thông báo giữa màn hình */
.toast-center {
    background-color: white;
    padding: 25px 35px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
    z-index: 9999;
    animation: popupIn 0.3s ease;
    max-width: 90%;
    width: 350px;
}

.toast-center p {
    font-size: 1.1rem;
    color: #333;
    margin-bottom: 15px;
}

.toast-center button {
    background-color: #e91e63;
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s;
}

.toast-center button:hover {
    background-color: #9e1844ff;
}

/* Hiệu ứng xuất hiện */
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
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Gắn sự kiện cho tất cả nút "Mua ngay"
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', async () => {
            const productId = btn.dataset.id;

            const formData = new FormData();
            formData.append('product_id', productId);

            try {
                const res = await fetch('cart_add.php', {
                    method: 'POST',
                    body: formData
                });

                if (res.ok) {
                    showPopup("✅ Sản phẩm đã được thêm vào giỏ hàng!");
                } else {
                    showPopup("❌ Có lỗi xảy ra, vui lòng thử lại.");
                }
            } catch {
                showPopup("⚠️ Không thể kết nối đến máy chủ.");
            }
        });
    });

    // Hàm hiển thị popup giữa màn hình
    function showPopup(message) {
        // Tạo overlay
        const overlay = document.createElement('div');
        overlay.className = 'toast-overlay';

        // Tạo hộp thông báo
        const toast = document.createElement('div');
        toast.className = 'toast-center';
        toast.innerHTML = `
            <p>${message}</p>
            <button id="close-toast">OK</button>
        `;

        overlay.appendChild(toast);
        document.body.appendChild(overlay);

        // Khi nhấn nút OK thì ẩn popup
        toast.querySelector('#close-toast').addEventListener('click', () => {
            overlay.remove();
        });
    }
});
</script>