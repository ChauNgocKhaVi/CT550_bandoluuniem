<?php
require_once __DIR__ . '/../src/bootstrap.php';

// Xuất kho từ đơn hàng
if (isset($_GET['export_order_id'])) {
    $order_id = $_GET['export_order_id'];

    // Lấy đơn hàng
    $stmt = $PDO->prepare("SELECT * FROM Orders WHERE order_id = ? AND status = 'confirmed'");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        // Tạo phiếu xuất
        $stmt = $PDO->prepare("INSERT INTO ExportReceipts (reason, total_amount) VALUES (?, ?)");
        $stmt->execute(["Bán hàng (Đơn #$order_id)", $order['total_amount']]);
        $export_id = $PDO->lastInsertId();

        // Lấy chi tiết đơn hàng
        $stmt = $PDO->prepare("SELECT * FROM OrderDetails WHERE order_id = ?");
        $stmt->execute([$order_id]);
        $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($details as $d) {
            // Lấy giá bán hiện tại sản phẩm
            $stmt2 = $PDO->prepare("SELECT price FROM Products WHERE product_id = ?");
            $stmt2->execute([$d['product_id']]);
            $price = $stmt2->fetchColumn();

            // Thêm chi tiết xuất
            $stmt3 = $PDO->prepare("INSERT INTO ExportDetails (export_id, product_id, quantity, export_price) VALUES (?, ?, ?, ?)");
            $stmt3->execute([$export_id, $d['product_id'], $d['quantity'], $price]);

            // Trừ kho
            $stmt4 = $PDO->prepare("UPDATE Products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
            $stmt4->execute([$d['quantity'], $d['product_id']]);
        }

        // Cập nhật trạng thái đơn hàng
        $stmt = $PDO->prepare("UPDATE Orders SET status = 'shipping' WHERE order_id = ?");
        $stmt->execute([$order_id]);

        $_SESSION['message'] = "✅ Xuất kho thành công cho đơn hàng #$order_id";
        header("Location: orders_admin.php");
        exit;
    }
}



// ✅ Kiểm tra quyền admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dang_nhap.php");
    exit;
}

$pdo = $PDO;

// ✅ Lấy danh sách đơn hàng
$query = "
    SELECT 
        o.order_id,
        u.full_name AS customer_name,
        o.order_date,
        o.total_amount,
        o.status,
        o.payment_status,
        o.payment_method,
        o.shipping_address
    FROM Orders o
    LEFT JOIN Users u ON o.user_id = u.user_id
    ORDER BY o.order_date DESC
";
$stmt = $pdo->query($query);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header_admin.php';
?>

<!-- 🔹 Main content -->
<div class="p-4 flex-grow-1">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="text-pink mb-0">Quản lý đơn hàng</h3>

        <!-- Search -->
        <div class="search-bar w-30 pe-5">
            <div class="input-group">
                <input type="text" id="searchInput" class="form-control rounded-pill" placeholder="Tìm đơn hàng...">
                <button class="btn btn-pink rounded-pill ms-2">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Hiển thị thông báo -->
    <?php if (isset($_SESSION['message'])): ?>
    <div class="mt-3"><?= $_SESSION['message']; ?></div>
    <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <!-- 🔹 Bảng đơn hàng -->
    <table class="table table-bordered table-hover align-middle shadow-sm rounded" id="orderTable">
        <thead class="table-primary text-center">
            <tr>
                <th>ID</th>
                <th>Khách hàng</th>
                <th>Ngày đặt</th>
                <th>Tổng tiền (₫)</th>
                <th>Trạng thái</th>
                <th>Thanh toán</th>
                <th>Phương thức</th>
                <th>Địa chỉ giao hàng</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= $order['order_id'] ?></td>
                <td><?= htmlspecialchars($order['customer_name'] ?? 'Khách lạ') ?></td>
                <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                <td class="text-danger fw-bold"><?= number_format($order['total_amount'], 0, ',', '.') ?></td>

                <!-- Trạng thái đơn hàng -->
                <td>
                    <?php
                            $statusColors = [
                                'pending' => 'secondary',
                                'confirmed' => 'info',
                                'shipping' => 'warning',
                                'delivered' => 'success',
                                'canceled' => 'danger'
                            ];
                            $status = $order['status'];
                            ?>
                    <span class="badge bg-<?= $statusColors[$status] ?? 'secondary' ?>">
                        <?= ucfirst($status) ?>
                    </span>
                </td>

                <!-- Thanh toán -->
                <td>
                    <?php if ($order['payment_status'] === 'paid'): ?>
                    <span class="badge bg-success">Đã thanh toán</span>
                    <?php else: ?>
                    <span class="badge bg-danger">Chưa thanh toán</span>
                    <?php endif; ?>
                </td>

                <!-- Phương thức -->
                <td>
                    <?= $order['payment_method'] === 'cash' ? 'Tiền mặt' : 'Thẻ' ?>
                </td>

                <td class="text-start"><?= htmlspecialchars($order['shipping_address'] ?? '—') ?></td>

                <td class="text-center">
                    <a href="order_details.php?id=<?= $order['order_id'] ?>"
                        class="btn btn-sm btn-outline-primary me-1 mb-1">
                        <i class="bi bi-eye"></i> Xem
                    </a>
                    <a href="edit_order.php?id=<?= $order['order_id'] ?>"
                        class="btn btn-sm btn-outline-success me-1 mb-1">
                        <i class="bi bi-pencil-square"></i> Sửa
                    </a>
                    <a href="delete_order.php?id=<?= $order['order_id'] ?>"
                        onclick="return confirm('Bạn có chắc muốn xóa đơn hàng này không?');"
                        class="btn btn-sm btn-outline-danger mb-1">
                        <i class="bi bi-trash"></i> Xóa
                    </a>

                    <?php if ($order['status'] === 'confirmed'): ?>
                    <a href="orders_admin.php?export_order_id=<?= $order['order_id'] ?>"
                        onclick="return confirm('Xuất kho đơn hàng này?');" class="btn btn-sm btn-outline-warning mt-1">
                        <i class="bi bi-box-arrow-up"></i> Xuất kho
                    </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="9" class="text-center text-muted">Không có đơn hàng nào.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>



<!-- 🔹 JS: tìm kiếm đơn hàng -->
<script>
document.getElementById("searchInput").addEventListener("keyup", function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll("#orderTable tbody tr");
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});
</script>
<!-- CSS -->
<style>
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

/* Bảng hiển thị rõ ràng hơn */
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

#orderTable {
    table-layout: auto;
    width: 900px;
    /* Đặt chiều rộng cố định cho bảng */
    margin: 0 auto;
    /* Căn giữa bảng trong trang */
    word-wrap: break-word;
    /* Cho phép xuống dòng khi text quá dài */
}
</style>