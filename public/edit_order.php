<?php
session_start();
require_once __DIR__ . '/../src/bootstrap.php';

// 🔹 Kiểm tra quyền admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dang_nhap.php");
    exit;
}

$pdo = $PDO;

// 🔹 Lấy ID đơn hàng từ URL
$order_id = $_GET['id'] ?? null;
if (!$order_id) {
    header("Location: orders_admin.php");
    exit;
}

// 🔹 Lấy thông tin đơn hàng hiện tại
$stmt = $pdo->prepare("
    SELECT 
        o.*, 
        u.full_name AS customer_name, 
        u.email AS customer_email 
    FROM Orders o
    LEFT JOIN Users u ON o.user_id = u.user_id
    WHERE o.order_id = ?
");
$stmt->execute([$order_id]);
$currentOrder = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$currentOrder) {
    $message = "<div class='alert alert-danger'>Không tìm thấy đơn hàng.</div>";
} else {
    // 🔹 Khi người dùng gửi form
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $status = $_POST['status'] ?? 'pending';
        $payment_status = $_POST['payment_status'] ?? 'unpaid';
        $payment_method = $_POST['payment_method'] ?? 'cash';
        $shipping_address = trim($_POST['shipping_address']);
        $shipping_fee = $_POST['shipping_fee'] ?? 0;
        try {
            $total_amount = !empty($_POST['total_amount']) ? $_POST['total_amount'] : $currentOrder['total_amount'];


            $updateStmt = $pdo->prepare("
    UPDATE Orders 
    SET status = ?, payment_status = ?, payment_method = ?, shipping_address = ?, shipping_fee = ?, total_amount = ?
    WHERE order_id = ?
");
            $updateStmt->execute([
                $status,
                $payment_status,
                $payment_method,
                $shipping_address,
                $shipping_fee,
                $total_amount,
                $order_id
            ]);


            $message = "<div class='alert alert-success text-center'>✅ Cập nhật đơn hàng thành công!</div>";

            // Reload lại dữ liệu mới
            $stmt->execute([$order_id]);
            $currentOrder = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Lỗi khi cập nhật: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}

include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header_admin.php';
?>

<div class="container mt-4">
    <h3 class="mb-4 text-pink text-center fw-bold">✏️ Chỉnh sửa đơn hàng</h3>

    <div class="mx-auto" style="max-width: 650px;">
        <form method="POST" class="p-4 shadow-sm bg-light rounded">
            <input type="hidden" name="shipping_fee" id="shipping_fee_input">

            <!-- Thông báo -->
            <?php if (!empty($message)): ?>
            <div class="alert custom-alert text-center mb-3">
                <?= $message ?>
            </div>
            <?php endif; ?>

            <!-- Thông tin khách hàng -->
            <div class="mb-3">
                <label class="form-label fw-semibold">👤 Khách hàng</label>
                <input type="text" class="form-control"
                    value="<?= htmlspecialchars($currentOrder['customer_name'] ?? 'Khách ẩn danh') ?>" disabled>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label fw-semibold">📧 Email</label>
                <input type="text" class="form-control"
                    value="<?= htmlspecialchars($currentOrder['customer_email'] ?? 'Không có') ?>" disabled>
            </div>

            <!-- Trạng thái đơn hàng -->
            <div class="mb-3">
                <label class="form-label fw-semibold">🚚 Trạng thái đơn hàng</label>
                <select name="status" class="form-select" required>
                    <?php
                    $statuses = ['pending' => 'Chờ xử lý', 'confirmed' => 'Đã xác nhận', 'shipping' => 'Đang giao', 'delivered' => 'Đã giao', 'canceled' => 'Đã hủy'];
                    foreach ($statuses as $value => $label):
                        $selected = ($currentOrder['status'] === $value) ? 'selected' : '';
                        echo "<option value='$value' $selected>$label</option>";
                    endforeach;
                    ?>
                </select>
            </div>

            <!-- Trạng thái thanh toán -->
            <div class="mb-3">
                <label class="form-label fw-semibold">💳 Trạng thái thanh toán</label>
                <select name="payment_status" class="form-select" required>
                    <option value="unpaid" <?= $currentOrder['payment_status'] === 'unpaid' ? 'selected' : '' ?>>Chưa
                        thanh toán</option>
                    <option value="paid" <?= $currentOrder['payment_status'] === 'paid' ? 'selected' : '' ?>>Đã thanh
                        toán</option>
                </select>
            </div>

            <!-- Phương thức thanh toán -->
            <div class="mb-3">
                <label class="form-label fw-semibold">💰 Phương thức thanh toán</label>
                <select name="payment_method" class="form-select" required>
                    <option value="cash" <?= $currentOrder['payment_method'] === 'cash' ? 'selected' : '' ?>>Tiền mặt
                    </option>
                    <option value="credit_card"
                        <?= $currentOrder['payment_method'] === 'credit_card' ? 'selected' : '' ?>>Thẻ tín dụng</option>
                </select>
            </div>

            <!-- Địa chỉ giao hàng -->

            <div class="mb-3">
                <label class="form-label fw-semibold">📦 Địa chỉ giao hàng</label>

                <!-- Dropdown chọn địa chỉ -->
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <select id="province" class="form-select">
                            <option value="">-- Chọn Tỉnh/Thành phố --</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="district" class="form-select">
                            <option value="">-- Chọn Quận/Huyện --</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="ward" class="form-select">
                            <option value="">-- Chọn Phường/Xã --</option>
                        </select>
                    </div>
                </div>

                <!-- Ô nhập địa chỉ chi tiết -->
                <textarea id="shipping_address" name="shipping_address" class="form-control" rows="2"
                    placeholder="Số nhà, đường..."><?= htmlspecialchars($currentOrder['shipping_address'] ?? '') ?></textarea>

                <!-- Hiển thị phí vận chuyển -->
                <div class="mt-3">
                    <label class="fw-semibold">🚚 Phí vận chuyển:</label>
                    <span id="shipping_fee" class="fw-bold text-danger">0 ₫</span>
                </div>
            </div>


            <!-- Tổng tiền -->
            <div class="mb-3">
                <label class="form-label fw-semibold">💸 Tổng tiền (₫)</label>
                <input type="text" id="total_amount_input" class="form-control"
                    value="<?= number_format($currentOrder['total_amount'], 0, ',', '.') ?>" disabled>
            </div>

            <input type="hidden" name="total_amount" id="total_amount_hidden">

            <!-- Nút -->
            <div class="d-flex justify-content-between mt-4">
                <a href="orders_admin.php" class="btn btn-secondary rounded-pill px-4">⬅ Quay lại</a>
                <button type="submit" class="btn btn-pink rounded-pill px-4">💾 Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<!-- 🔹 CSS -->
<style>
:root {
    --pink-main: #e91e63;
    --pink-light: #fce4ec;
    --pink-dark: #c2185b;
}

body {
    background-color: #f8f9fa;
    font-family: 'Segoe UI', sans-serif;
}

.text-pink {
    color: var(--pink-main);
}

.btn-pink {
    background-color: var(--pink-main);
    color: white;
    font-weight: 600;
    border: none;
    transition: all 0.3s ease;
}

.btn-pink:hover {
    background-color: var(--pink-dark);
}

form {
    background-color: var(--pink-light);
    border: 1px solid var(--pink-main);
    border-radius: 12px;
}

.form-label {
    font-weight: 600;
    color: var(--pink-dark);
}

input:focus,
select:focus,
textarea:focus {
    border-color: var(--pink-main);
    box-shadow: 0 0 0 0.2rem rgba(233, 30, 99, 0.25);
}

textarea.form-control {
    resize: none;
}

/* 🔹 Giúp menu dropdown luôn hiển thị phía dưới */
select {
    position: relative;
    z-index: 10;
}

/* 🔹 Fix trường hợp dropdown bị che hoặc mở ngược */
select::-ms-expand {
    display: none;
}

/* 🔹 Tăng chiều cao khung chọn khi mở */
select:focus {
    overflow-y: auto;
    max-height: 200px;
}

/* Bảng nabar */

body {
    font-family: 'Segoe UI', sans-serif;
    background-color: #f8f9fa;
}

#sidebar .nav-link {
    font-weight: 500;
    color: #333;
    padding: 10px 15px;
    transition: all 0.3s ease;
}

#sidebar .nav-link:hover {
    background-color: #e3f2fd;
    color: #e91e63;
    border-radius: 8px;
}

#sidebar .nav-link.active {
    background-color: #e91e63;
    color: white;
    border-radius: 8px;
}

.table {
    width: 100%;
    background-color: white;
    border-radius: 10px;
    overflow: hidden;
}

.table th,
.table td {
    vertical-align: middle;
    text-align: center;
}

.table thead th {
    background-color: #e91e63;
    color: white;
}

#productTable {
    width: 100%;
    max-width: 900px;
    margin: 0 auto;
    table-layout: fixed;
    word-wrap: break-word;
}

.table-title {
    text-align: center;
    max-width: 900px;
    margin: 20px auto 10px auto;
}

/* Nút màu hồng */
.btn-pink {
    background-color: #e91e63;
    color: white;
    font-weight: 600;
}

.btn-pink:hover {
    background-color: #c2185b;
    color: white;
}
</style>

<!-- ✅ Script load tỉnh/huyện/xã và tính phí -->
<script>
const provinceSelect = document.getElementById("province");
const districtSelect = document.getElementById("district");
const wardSelect = document.getElementById("ward");
const addressInput = document.getElementById("shipping_address");
const shippingFeeEl = document.getElementById("shipping_fee");
const totalInput = document.getElementById("total_amount_input");
const productTotal = <?= $currentOrder['total_amount'] - ($currentOrder['shipping_fee'] ?? 0) ?>;
let provincesData = [];
let shippingFee = 0;

// 🟢 1. Tải dữ liệu tỉnh/huyện/xã từ file nội bộ
fetch("data/vietnam.json")
    .then(res => res.json())
    .then(data => {
        provincesData = data;

        // ✅ Gọi tính phí ship lần đầu theo tỉnh hiện tại (nếu có)
        const currentProvince = provinceSelect.value || "";
        calculateShippingFee(currentProvince);

        // ✅ Thêm dòng này ngay sau khi gọi hàm trên
        document.getElementById("total_amount_hidden").value = productTotal + shippingFee;

        // ✅ Khi đổi tỉnh thì tính lại phí ship
        provinceSelect.addEventListener("change", function() {
            calculateShippingFee(this.value);
        });

        // Thêm danh sách tỉnh
        data.forEach(p => {
            const option = new Option(p.Name, p.Name);
            option.dataset.code = p.Id;
            provinceSelect.add(option);
        });

        // 🟢 Điền lại địa chỉ cũ nếu có
        const oldAddress = "<?= htmlspecialchars($currentOrder['shipping_address'] ?? '') ?>";
        if (oldAddress) {
            const parts = oldAddress.split(',').map(p => p.trim());
            const provinceName = parts.pop() || "";
            const districtName = parts.pop() || "";
            const wardName = parts.pop() || "";

            // Chọn tỉnh
            const province = data.find(p => p.Name === provinceName);
            if (province) {
                provinceSelect.value = province.Name;

                // Load huyện
                province.Districts.forEach(d => {
                    const opt = new Option(d.Name, d.Name);
                    opt.dataset.code = d.Id;
                    districtSelect.add(opt);
                });

                // Chọn huyện
                const district = province.Districts.find(d => d.Name === districtName);
                if (district) {
                    districtSelect.value = district.Name;

                    // Load xã
                    district.Wards.forEach(w => {
                        const opt = new Option(w.Name, w.Name);
                        opt.dataset.code = w.Id;
                        wardSelect.add(opt);
                    });

                    // Chọn xã
                    const ward = district.Wards.find(w => w.Name === wardName);
                    if (ward) wardSelect.value = ward.Name;
                }
            }

            // Tính lại phí ship theo tỉnh cũ
            calculateShippingFee(provinceName);
        }
    });

// 🟢 2. Khi chọn tỉnh → load huyện
provinceSelect.addEventListener("change", () => {
    const provinceCode = provinceSelect.options[provinceSelect.selectedIndex].dataset.code;
    const province = provincesData.find(p => p.Id == provinceCode);

    districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

    province.Districts.forEach(d => {
        const option = new Option(d.Name, d.Name);
        option.dataset.code = d.Id;
        districtSelect.add(option);
    });

    updateAddress();
    calculateShippingFee(provinceSelect.value);
});

// 🟢 3. Khi chọn huyện → load xã
districtSelect.addEventListener("change", () => {
    const provinceCode = provinceSelect.options[provinceSelect.selectedIndex].dataset.code;
    const districtCode = districtSelect.options[districtSelect.selectedIndex].dataset.code;

    const province = provincesData.find(p => p.Id == provinceCode);
    const district = province.Districts.find(d => d.Id == districtCode);

    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
    district.Wards.forEach(w => {
        const option = new Option(w.Name, w.Name);
        option.dataset.code = w.Id;
        wardSelect.add(option);
    });

    updateAddress();
});

// 🟢 4. Khi chọn xã → cập nhật địa chỉ
wardSelect.addEventListener("change", updateAddress);

// 🟢 Cập nhật địa chỉ đầy đủ
function updateAddress() {
    const province = provinceSelect.value;
    const district = districtSelect.value;
    const ward = wardSelect.value;
    const base = addressInput.value.split(',')[0].trim();
    addressInput.value = [base, ward, district, province].filter(Boolean).join(', ');
}

// 🟢 5. Tính phí vận chuyển + cập nhật tổng tiền
function calculateShippingFee(provinceName) {
    shippingFee = 0;

    if (productTotal >= 300000) {
        shippingFee = 0;
    } else if (provinceName.includes("Hồ Chí Minh")) {
        shippingFee = 20000;
    } else if (provinceName.includes("Hà Nội")) {
        shippingFee = 25000;
    } else {
        shippingFee = 35000;
    }

    shippingFeeEl.textContent =
        shippingFee === 0 ? "Miễn phí vận chuyển" : shippingFee.toLocaleString('vi-VN') + " ₫";

    const newTotal = productTotal + shippingFee;
    totalInput.value = newTotal.toLocaleString('vi-VN') + " ₫";

    // ✅ Cập nhật input ẩn chính xác
    document.getElementById("shipping_fee_input").value = shippingFee;
    document.getElementById("total_amount_hidden").value = newTotal;
}
</script>