<?php
session_start();
require_once __DIR__ . '/../src/bootstrap.php';

// ✅ Kiểm tra quyền admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dang_nhap.php");
    exit;
}

$pdo = $PDO;

// ✅ Lấy danh sách nhà cung cấp
$stmt = $pdo->query("SELECT supplier_id, supplier_name FROM Suppliers ORDER BY supplier_name");
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Lấy danh sách sản phẩm
$stmt = $pdo->query("SELECT product_id, product_name FROM Products ORDER BY product_name");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Xử lý khi gửi form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_POST['supplier_id'] ?? null;
    $note = trim($_POST['note'] ?? '');
    $products_selected = $_POST['product_id'] ?? [];
    $quantities = $_POST['quantity'] ?? [];
    $prices = $_POST['import_price'] ?? [];

    if (empty($supplier_id) || empty($products_selected)) {
        $message = "<div class='alert alert-warning text-center'>⚠️ Vui lòng chọn nhà cung cấp và ít nhất một sản phẩm!</div>";
    } else {
        try {
            $pdo->beginTransaction();

            // 🔹 Tạo phiếu nhập
            $stmt = $pdo->prepare("INSERT INTO ImportReceipts (supplier_id, note) VALUES (?, ?)");
            $stmt->execute([$supplier_id, $note]);
            $import_id = $pdo->lastInsertId();

            $total = 0;
            $stmtDetail = $pdo->prepare("
                INSERT INTO ImportDetails (import_id, product_id, quantity, import_price)
                VALUES (?, ?, ?, ?)
            ");

            foreach ($products_selected as $index => $product_id) {
                $qty = (int)$quantities[$index];
                $price = (float)$prices[$index];
                $stmtDetail->execute([$import_id, $product_id, $qty, $price]);
                $total += $qty * $price;

                // ✅ Cập nhật tồn kho
                $pdo->prepare("UPDATE Products SET stock_quantity = stock_quantity + ? WHERE product_id = ?")
                    ->execute([$qty, $product_id]);
            }

            $pdo->prepare("UPDATE ImportReceipts SET total_amount = ? WHERE import_id = ?")
                ->execute([$total, $import_id]);

            $pdo->commit();

            $message = "<div class='alert alert-success text-center'>✅ Thêm phiếu nhập thành công!</div>";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $message = "<div class='alert alert-danger text-center'>❌ Lỗi: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}

include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header_admin.php';
?>

<div class="container mt-4">
    <h3 class="mb-4 text-pink text-center fw-bold">➕ Thêm phiếu nhập kho</h3>

    <div class="mx-auto" style="max-width: 800px;">
        <?php if (!empty($message)) echo $message; ?>

        <form method="POST" class="p-4 shadow-sm bg-light rounded">

            <!-- Nhà cung cấp -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-3 col-form-label fw-semibold">Nhà cung cấp</label>
                <div class="col-sm-9">
                    <select name="supplier_id" class="form-select" required>
                        <option value="">-- Chọn nhà cung cấp --</option>
                        <?php foreach ($suppliers as $sup): ?>
                        <option value="<?= $sup['supplier_id'] ?>"><?= htmlspecialchars($sup['supplier_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Ghi chú -->
            <div class="row mb-3 align-items-start">
                <label class="col-sm-3 col-form-label fw-semibold">Ghi chú</label>
                <div class="col-sm-9">
                    <textarea name="note" class="form-control" rows="2"></textarea>
                </div>
            </div>

            <!-- Sản phẩm -->
            <h5 class="text-pink fw-semibold mb-3">Sản phẩm nhập</h5>
            <table class="table table-bordered" id="productsTable">
                <thead class="table-primary text-center">
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Giá nhập (₫)</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <select name="product_id[]" class="form-select" required>
                                <option value="">-- Chọn sản phẩm --</option>
                                <?php foreach ($products as $p): ?>
                                <option value="<?= $p['product_id'] ?>"><?= htmlspecialchars($p['product_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><input type="number" name="quantity[]" class="form-control" min="1" required></td>
                        <td><input type="number" name="import_price[]" class="form-control" min="0" step="0.01"
                                required></td>
                        <td class="text-center"><button type="button"
                                class="btn btn-sm btn-danger remove-row">Xóa</button></td>
                    </tr>
                </tbody>
            </table>

            <button type="button" class="btn btn-sm btn-outline-primary mb-3" id="addRow">+ Thêm sản phẩm</button>

            <div class="d-flex justify-content-between mt-4">
                <a href="import_admin.php" class="btn btn-secondary rounded-pill px-4">Quay lại</a>
                <button type="submit" class="btn btn-pink rounded-pill px-4">Lưu phiếu nhập</button>
            </div>
        </form>
    </div>
</div>

<!-- JS thêm/xóa dòng -->
<script>
document.getElementById('addRow').addEventListener('click', function() {
    const table = document.getElementById('productsTable').querySelector('tbody');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <select name="product_id[]" class="form-select" required>
                <option value="">-- Chọn sản phẩm --</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= $p['product_id'] ?>"><?= htmlspecialchars($p['product_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td><input type="number" name="quantity[]" class="form-control" min="1" required></td>
        <td><input type="number" name="import_price[]" class="form-control" min="0" step="0.01" required></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row">Xóa</button></td>
    `;
    table.appendChild(row);
});

document.addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('remove-row')) {
        e.target.closest('tr').remove();
    }
});
</script>

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
select:focus {
    border-color: var(--pink-main);
    box-shadow: 0 0 0 0.2rem rgba(233, 30, 99, 0.25);
}

.col-form-label {
    white-space: nowrap;
}

form .form-control {
    height: 38px;
    padding: 5px 10px;
}

.table th,
.table td {
    vertical-align: middle;
    text-align: center;
}


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