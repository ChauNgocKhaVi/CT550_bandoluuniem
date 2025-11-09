<?php
require_once __DIR__ . '/../src/bootstrap.php';

// 🔹 Kiểm tra quyền admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dang_nhap.php");
    exit;
}

$pdo = $PDO;

// 🔹 Lấy danh sách phiếu nhập hàng
// 🔹 Lấy danh sách phiếu nhập hàng kèm sản phẩm
$query = "
    SELECT 
        ir.import_id,
        s.supplier_name,
        ir.import_date,
        ir.total_amount,
        ir.note,
        p.product_name,
        id.quantity,
        id.import_price
    FROM ImportReceipts ir
    LEFT JOIN Suppliers s ON ir.supplier_id = s.supplier_id
    LEFT JOIN ImportDetails id ON ir.import_id = id.import_id
    LEFT JOIN Products p ON id.product_id = p.product_id
    ORDER BY ir.import_id DESC, id.import_detail_id ASC
";
$stmt = $pdo->query($query);
$imports = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Gom dữ liệu theo import_id
$groupedImports = [];
foreach ($imports as $row) {
    $groupedImports[$row['import_id']]['info'] = [
        'supplier_name' => $row['supplier_name'],
        'import_date' => $row['import_date'],
        'total_amount' => $row['total_amount'],
        'note' => $row['note']
    ];
    $groupedImports[$row['import_id']]['products'][] = [
        'product_name' => $row['product_name'],
        'quantity' => $row['quantity'],
        'import_price' => $row['import_price']
    ];
}

include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header_admin.php';
?>

<!-- Main content -->
<div class="p-4 flex-grow-1">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="text-pink mb-0">📦 Quản lý nhập kho</h3>

        <!-- Search -->
        <div class="search-bar w-30 pe-5">
            <div class="input-group">
                <input type="text" id="searchInput" class="form-control rounded-pill" placeholder="Tìm phiếu nhập...">
                <button class="btn btn-pink rounded-pill ms-2">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>

        <a href="add_import.php" class="btn btn-pink">
            <i class="bi bi-plus-circle"></i> Thêm phiếu nhập
        </a>
    </div>

    <!-- Hiển thị thông báo -->
    <?php if (isset($_SESSION['message'])): ?>
    <div class="mt-3">
        <?= $_SESSION['message']; ?>
    </div>
    <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <table class="table table-bordered table-hover align-middle shadow-sm rounded">
        <thead class="table-primary text-center">
            <tr>
                <th>Mã phiếu</th>
                <th>Nhà cung cấp</th>
                <th>Ngày nhập</th>
                <th>Tên sản phẩm</th>
                <th>Số lượng</th>
                <th>Giá nhập (₫)</th>
                <th>Tổng tiền (₫)</th>
                <th>Ghi chú</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($groupedImports as $import_id => $import): ?>
            <?php
                $products = $import['products'];
                $rowspan = count($products); // số dòng sản phẩm
                foreach ($products as $index => $product):
                ?>
            <tr>
                <?php if ($index === 0): ?>
                <td rowspan="<?= $rowspan ?>"><?= $import_id ?></td>
                <td rowspan="<?= $rowspan ?>"><?= htmlspecialchars($import['info']['supplier_name']) ?></td>
                <td rowspan="<?= $rowspan ?>"><?= date('d/m/Y H:i', strtotime($import['info']['import_date'])) ?></td>
                <?php endif; ?>

                <td><?= htmlspecialchars($product['product_name']) ?></td>
                <td><?= $product['quantity'] ?></td>
                <td class="text-end"><?= number_format($product['import_price'], 0, ',', '.') ?></td>

                <?php if ($index === 0): ?>
                <td rowspan="<?= $rowspan ?>" class="text-end">
                    <?= number_format($import['info']['total_amount'], 0, ',', '.') ?></td>
                <td rowspan="<?= $rowspan ?>"><?= htmlspecialchars($import['info']['note']) ?></td>
                <td rowspan="<?= $rowspan ?>">
                    <a href="edit_import.php?id=<?= $import_id ?>" class="btn btn-sm btn-outline-primary me-1 mb-1">
                        <i class="bi bi-pencil"></i> Sửa
                    </a>
                    <a href="delete_import.php?id=<?= $import_id ?>"
                        onclick="return confirm('Bạn có chắc muốn xóa phiếu nhập này không?');"
                        class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash"></i> Xóa
                    </a>
                </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>

    </table>

</div>

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
</style>

<!-- JS: tìm kiếm phiếu nhập -->
<script>
document.getElementById("searchInput").addEventListener("keyup", function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll("#importTable tbody tr");
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});
</script>