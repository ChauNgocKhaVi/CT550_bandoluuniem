<?php
require_once __DIR__ . '/../src/bootstrap.php';

// 🔹 Kiểm tra quyền admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dang_nhap.php");
    exit;
}

$pdo = $PDO;

// 🔹 Lấy import_id từ URL
$import_id = $_GET['id'] ?? null;
if (!$import_id) {
    header('Location: import_list.php');
    exit;
}

// 🔹 Lấy thông tin phiếu nhập
$stmt = $pdo->prepare("
    SELECT * FROM ImportReceipts WHERE import_id = ?
");
$stmt->execute([$import_id]);
$import = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$import) {
    $_SESSION['message'] = "Phiếu nhập không tồn tại.";
    header('Location: import_list.php');
    exit;
}

// 🔹 Lấy danh sách chi tiết sản phẩm
$stmt = $pdo->prepare("
    SELECT id.*, p.product_name
    FROM ImportDetails id
    LEFT JOIN Products p ON id.product_id = p.product_id
    WHERE id.import_id = ?
");
$stmt->execute([$import_id]);
$details = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 🔹 Lấy danh sách nhà cung cấp và sản phẩm để chọn
$suppliers = $pdo->query("SELECT * FROM Suppliers")->fetchAll(PDO::FETCH_ASSOC);
$products = $pdo->query("SELECT * FROM Products")->fetchAll(PDO::FETCH_ASSOC);

// 🔹 Xử lý form khi submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_POST['supplier_id'];
    $note = $_POST['note'];

    // ✅ Cập nhật thông tin phiếu nhập
    $stmt = $pdo->prepare("UPDATE ImportReceipts SET supplier_id = ?, note = ? WHERE import_id = ?");
    $stmt->execute([$supplier_id, $note, $import_id]);

    // 🔹 Trừ lại số lượng cũ trong kho trước khi xóa chi tiết
    foreach ($details as $detail) {
        $stmt = $pdo->prepare("UPDATE Products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
        $stmt->execute([$detail['quantity'], $detail['product_id']]);
    }

    // 🔹 Xóa chi tiết cũ
    $pdo->prepare("DELETE FROM ImportDetails WHERE import_id = ?")->execute([$import_id]);

    // 🔹 Thêm chi tiết mới và cộng số lượng vào kho
    foreach ($_POST['product_id'] as $index => $product_id) {
        $quantity = $_POST['quantity'][$index];
        $import_price = $_POST['import_price'][$index];
        if ($product_id && $quantity > 0 && $import_price > 0) {
            $stmt = $pdo->prepare("
                INSERT INTO ImportDetails (import_id, product_id, quantity, import_price)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$import_id, $product_id, $quantity, $import_price]);

            // Cộng số lượng mới vào kho
            $stmt2 = $pdo->prepare("UPDATE Products SET stock_quantity = stock_quantity + ? WHERE product_id = ?");
            $stmt2->execute([$quantity, $product_id]);
        }
    }

    $_SESSION['message'] = "Cập nhật phiếu nhập thành công!";
    header("Location: edit_import.php?id=$import_id");
    exit;
}


include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header_admin.php';
?>

<div class="container mt-4">
    <h3 class="mb-4 text-pink text-center fw-bold">✏️ Sửa phiếu nhập #<?= $import_id ?></h3>

    <div class="mx-auto" style="max-width: 800px;">
        <form method="POST" class="p-4 shadow-sm bg-light rounded">

            <!-- Thông báo -->
            <?php if (isset($_SESSION['message'])): ?>
            <div class="alert custom-alert text-center mb-3">
                <?= $_SESSION['message'];
                    unset($_SESSION['message']); ?>
            </div>
            <?php endif; ?>

            <!-- Nhà cung cấp -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-3 col-form-label fw-semibold">Nhà cung cấp</label>
                <div class="col-sm-9">
                    <select name="supplier_id" class="form-select" required>
                        <?php foreach ($suppliers as $supplier): ?>
                        <option value="<?= $supplier['supplier_id'] ?>"
                            <?= $supplier['supplier_id'] == $import['supplier_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($supplier['supplier_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Ghi chú -->
            <div class="row mb-3 align-items-start">
                <label class="col-sm-3 col-form-label fw-semibold">Ghi chú</label>
                <div class="col-sm-9">
                    <textarea name="note" class="form-control"><?= htmlspecialchars($import['note']) ?></textarea>
                </div>
            </div>

            <!-- Sản phẩm -->
            <h5 class="text-pink fw-semibold mb-3">Sản phẩm</h5>
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
                    <?php foreach ($details as $detail): ?>
                    <tr>
                        <td>
                            <select name="product_id[]" class="form-select" required>
                                <?php foreach ($products as $p): ?>
                                <option value="<?= $p['product_id'] ?>"
                                    <?= $p['product_id'] == $detail['product_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['product_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><input type="number" name="quantity[]" value="<?= $detail['quantity'] ?>"
                                class="form-control" min="1" required></td>
                        <td><input type="number" name="import_price[]"
                                value="<?= intval($detail['import_price']) == $detail['import_price'] ? intval($detail['import_price']) : $detail['import_price'] ?>"
                                class="form-control" min="0" step="0.01" required></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger remove-row">Xóa</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <button type="button" class="btn btn-sm btn-outline-primary mb-3" id="addRow">+ Thêm sản phẩm</button>

            <!-- Nút -->
            <div class="d-flex justify-content-between mt-4">
                <a href="import_admin.php" class="btn btn-secondary rounded-pill px-4">Quay lại</a>
                <button type="submit" class="btn btn-pink rounded-pill px-4">Cập nhật phiếu nhập</button>
            </div>
        </form>
    </div>
</div>

<!-- JS thêm/xóa dòng sản phẩm -->
<script>
document.getElementById('addRow').addEventListener('click', function() {
    const table = document.getElementById('productsTable').querySelector('tbody');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <select name="product_id[]" class="form-select" required>
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