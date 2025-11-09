<?php
require_once __DIR__ . '/../src/bootstrap.php';

// 🔹 Kiểm tra đăng nhập
if (!isset($_SESSION['user'])) {
    header('Location: dang_nhap.php');
    exit;
}

$user_id = $_SESSION['user']['user_id']; // cột user_id trong bảng Users

// 🔹 Lấy danh sách đơn hàng theo user
$stmt = $PDO->prepare("
    SELECT order_id, order_date, total_amount, status 
    FROM Orders 
    WHERE user_id = ? 
    ORDER BY order_date DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header.php';
?>

<style>
.order-card {
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #eee;
    transition: 0.3s;
}

.order-card:hover {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.order-header {
    background: #f9f9f9;
    font-weight: 500;
    font-size: 15px;
    padding: 10px 15px;
}

.product-img {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 10px;
    margin-right: 15px;
    border: 1px solid #ddd;
}

.product-name {
    font-weight: 500;
    color: #333;
}

.order-status span {
    font-weight: 500;
}

.text-pink {
    color: #e83e8c;
}

.order-total {
    font-size: 1.05rem;
    font-weight: bold;
    color: #dc3545;
}

.toast-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.toast-center {
    background: white;
    padding: 25px 35px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
    animation: popupIn 0.3s ease;
    width: 350px;
}

.toast-center p {
    font-size: 1rem;
    margin-bottom: 15px;
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
    background-color: #c2185b;
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
</style>

<div class="container mt-5 mb-5">
    <h3 class="fw-bold mb-4 text-pink">🛍️ Đơn hàng của tôi</h3>

    <?php if (empty($orders)): ?>
    <div class="text-center mt-5">
        <img src="/CT550/public/uploads/no-order.png" alt="No orders" style="width:150px; opacity:0.8;">
        <p class="mt-3 text-muted">Bạn chưa có đơn hàng nào.</p>
    </div>
    <?php else: ?>
    <?php foreach ($orders as $order): ?>
    <div class="order-card mb-4">
        <div class="order-header d-flex justify-content-between align-items-center">
            <div>
                🧾 <strong>Mã đơn:</strong> <?= htmlspecialchars($order['order_id']) ?>
            </div>
            <div>
                <i class="bi bi-calendar3"></i> <?= htmlspecialchars($order['order_date']) ?>
            </div>
        </div>

        <div class="p-3">
            <?php
                    $stmtItems = $PDO->prepare("
    SELECT p.product_name, p.image, p.price, od.quantity, od.product_id 
    FROM OrderDetails od
    JOIN Products p ON od.product_id = p.product_id
    WHERE od.order_id = ?
");

                    $stmtItems->execute([$order['order_id']]);
                    $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
                    ?>

            <?php if (!empty($items)): ?>
            <?php foreach ($items as $item): ?>
            <div class="d-flex align-items-center border-bottom py-2">
                <img src="<?= htmlspecialchars($item['image'] ?? 'uploads/no-image.jpg') ?>"
                    alt="<?= htmlspecialchars($item['product_name']) ?>" class="product-img">
                <div class="flex-grow-1">
                    <div class="product-name"><?= htmlspecialchars($item['product_name']) ?></div>
                    <div class="text-muted small">Giá: <?= number_format($item['price'], 0, ',', '.') ?>₫</div>
                </div>
                <div class="text-center" style="width:80px;">
                    x<?= htmlspecialchars($item['quantity']) ?>
                </div>
                <div class="fw-bold text-end" style="width:120px;">
                    <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>₫
                </div>

            </div>

            <?php if ($order['status'] === 'delivered'): ?>
            <div class="text-end mt-2">
                <button type="button" class="btn btn-outline-pink btn-sm rounded-pill"
                    onclick="showReviewPopup(<?= $order['order_id'] ?>, <?= $item['product_id'] ?>)">
                    💬 Đánh giá
                </button>
            </div>
            <?php endif; ?>


            <?php endforeach; ?>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center pt-3">
                <div class="order-status">
                    <strong>Trạng thái:</strong>
                    <?php
                            switch ($order['status']) {
                                case 'pending':
                                    echo '<span class="text-secondary">⏳ Đang chờ xác nhận</span>';
                                    break;
                                case 'confirmed':
                                    echo '<span class="text-primary">✅ Đã xác nhận</span>';
                                    break;
                                case 'shipping':
                                    echo '<span class="text-warning">🚚 Đang giao hàng</span>';
                                    break;
                                case 'delivered':
                                    echo '<span class="text-success">🎉 Đã giao</span>';
                                    break;
                                case 'canceled':
                                    echo '<span class="text-danger">❌ Đã hủy</span>';
                                    break;
                            }
                            ?>
                </div>

                <div class="text-end">
                    <div class="order-total mb-2">
                        Tổng: <?= number_format($order['total_amount'], 0, ',', '.') ?>₫
                    </div>

                    <?php if ($order['status'] === 'shipping'): ?>
                    <button type="button" class="btn btn-success btn-sm rounded-pill px-3"
                        onclick="confirmReceived(<?= $order['order_id'] ?>)">
                        ✅ Đã nhận hàng
                    </button>
                    <?php endif; ?>


                </div>
            </div>

        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

    <!-- 🟩 Popup đánh giá sản phẩm -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content shadow-lg border-0 rounded-4 position-relative">
                <button type="button" class="btn-close close-top-right" data-bs-dismiss="modal"
                    aria-label="Đóng"></button>

                <div class="modal-header border-0 bg-light rounded-top-4">
                    <h5 class="modal-title fw-bold text-pink" id="reviewModalLabel">💬 Đánh giá đơn hàng</h5>
                </div>
                <div class="modal-body text-center px-4 pb-4">
                    <form id="reviewForm">
                        <p class="text-muted mb-3">Hãy cho chúng tôi biết trải nghiệm của bạn 💖</p>

                        <div class="star-rating mb-3">
                            <i class="bi bi-star" data-value="1"></i>
                            <i class="bi bi-star" data-value="2"></i>
                            <i class="bi bi-star" data-value="3"></i>
                            <i class="bi bi-star" data-value="4"></i>
                            <i class="bi bi-star" data-value="5"></i>
                        </div>

                        <input type="hidden" name="rating" value="0">

                        <textarea name="comment" class="form-control mb-3 rounded-3" rows="3"
                            placeholder="Viết nhận xét của bạn..." required></textarea>

                        <button type="submit" class="btn btn-pink w-100 py-2 fw-semibold rounded-pill">Gửi đánh
                            giá</button>

                    </form>
                </div>
            </div>
        </div>
    </div>



</div>

<script>
const reviewModal = new bootstrap.Modal(document.getElementById('reviewModal'));

function confirmReceived(orderId) {
    // Hiện popup xác nhận
    showPopup("📦 Xác nhận bạn đã nhận được hàng?", () => {
        fetch("update_order_status.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `order_id=${orderId}&status=delivered`
            })
            .then(res => res.text())
            .then(() => {
                // Gọi PHP để lấy product_id đầu tiên trong đơn
                fetch(`get_first_product.php?order_id=${orderId}`)
                    .then(res => res.text())
                    .then(productId => {
                        showReviewPopup(orderId, productId);
                    })
                    .catch(() => showPopup("⚠️ Không lấy được sản phẩm để đánh giá!"));
            })

            .catch(() => showPopup("⚠️ Lỗi khi cập nhật đơn hàng!"));
    });
}

// ✅ Popup xác nhận
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

// ✅ Hiện modal đánh giá sản phẩm (với sao)
function showReviewPopup(orderId, productId) {
    const form = document.getElementById("reviewForm");
    form.dataset.orderId = orderId;
    form.dataset.productId = productId;
    reviewModal.show();
    initStarRating();
}


// ⭐ Xử lý chọn sao
function initStarRating() {
    const stars = document.querySelectorAll('.star-rating i');
    const input = document.querySelector('input[name="rating"]');
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const value = parseInt(this.dataset.value);
            input.value = value;
            stars.forEach(s => {
                if (parseInt(s.dataset.value) <= value) {
                    s.classList.add('text-warning', 'bi-star-fill');
                    s.classList.remove('bi-star');
                } else {
                    s.classList.remove('text-warning', 'bi-star-fill');
                    s.classList.add('bi-star');
                }
            });
        });
    });
}


// 🟢 Gửi đánh giá
document.getElementById("reviewForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const orderId = this.dataset.orderId;
    const productId = this.dataset.productId;
    const formData = new FormData(this);
    formData.append("order_id", orderId);
    formData.append("product_id", productId);

    fetch("save_review.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(() => {
            showPopup("💖 Cảm ơn bạn đã đánh giá sản phẩm!", () => {
                reviewModal.hide();
                location.reload();
            });
        })
        .catch(() => showPopup("⚠️ Lỗi khi gửi đánh giá!"));
});
</script>

<style>
.star-rating {
    font-size: 1.6rem;
    color: #ccc;
    cursor: pointer;
}

.star-rating i {
    transition: color 0.2s, transform 0.2s;
}

.star-rating i:hover {
    transform: scale(1.2);
    color: gold;
}


#reviewModal .modal-content {
    border-radius: 12px;
    padding: 10px 20px;
}

#reviewModal .modal-body {
    text-align: center;
}

#reviewModal .card {
    margin: 0 auto;
    max-width: 400px;
    border-radius: 10px;
}

#reviewModal img {
    display: block;
    margin: 0 auto;
}

#reviewModal textarea {
    resize: none;
    text-align: center;
}

#reviewModal .star-rating {
    justify-content: center;
    display: flex;
    margin-bottom: 10px;
}



/* 🌸 Nút màu hồng chủ đạo */
.btn-pink {
    background-color: #e83e8c;
    color: #fff;
    border: none;
    transition: all 0.3s;
}

.btn-pink:hover {
    background-color: #d63384;
    transform: translateY(-1px);
}

/* 🌟 Hiệu ứng sao */
.star-rating {
    font-size: 1.8rem;
    color: #ccc;
    cursor: pointer;
    display: flex;
    justify-content: center;
    gap: 6px;
}

.star-rating i {
    transition: color 0.25s, transform 0.25s;
}

.star-rating i:hover {
    color: gold;
    transform: scale(1.2);
}

/* 🌸 Form nhỏ gọn và dễ nhìn */
#reviewModal .modal-content {
    border-radius: 15px;
}

#reviewModal textarea {
    resize: none;
    text-align: center;
}

/* 🌸 Tiêu đề modal */
#reviewModal .modal-title {
    color: #e83e8c;
}

/* ✅ Căn dấu X ở góc phải trên cùng */
#reviewModal .close-top-right {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 5;
    background-color: #f8f9fa;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    justify-content: center;
    align-items: center;
    opacity: 0.8;
    transition: all 0.2s ease;
}

#reviewModal .close-top-right:hover {
    opacity: 1;
    background-color: #e9ecef;
}
</style>