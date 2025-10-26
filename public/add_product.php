<?php
session_start();
require_once __DIR__ . '/../src/bootstrap.php';

use CT550\Labs\Product;

// Tạo đối tượng Product
$product = new Product($PDO);

$message = $product->handleAddProductForm($_POST, $_FILES);

// Lấy danh sách thể loại và thương hiệu để hiển thị trong <select>
$categories = $product->getAllCategories();
$brands = $product->getAllBrands();

include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header_admin.php';
?>

<div class="container mt-4">
    <h3 class="mb-4 text-pink text-center fw-bold">➕ Thêm sản phẩm mới</h3>

    <div class="mx-auto" style="max-width: 700px;">
        <form method="POST" enctype="multipart/form-data" class="p-4 shadow-sm bg-light rounded">

            <!-- Thông báo -->
            <?php if (!empty($message)): ?>
            <div class="text-center mb-3"><?= $message ?></div>
            <?php endif; ?>

            <!-- Tên sản phẩm -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 col-form-label fw-semibold">Tên sản phẩm</label>
                <div class="col-sm-8">
                    <input type="text" name="product_name" class="form-control" required>
                </div>
            </div>

            <!-- Thể loại -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 col-form-label fw-semibold">Thể loại</label>
                <div class="col-sm-8">
                    <select name="category_id" class="form-select" required>
                        <option value="">-- Chọn thể loại --</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- 🔹 Thương hiệu -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 col-form-label fw-semibold">Thương hiệu</label>
                <div class="col-sm-8">
                    <select name="brand_id" class="form-select" required>
                        <option value="">-- Chọn thương hiệu --</option>
                        <?php foreach ($brands as $brand): ?>
                        <option value="<?= $brand['brand_id'] ?>">
                            <?= htmlspecialchars($brand['brand_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Mô tả -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 col-form-label fw-semibold">Mô tả</label>
                <div class="col-sm-8">
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
            </div>

            <!-- Giá gốc -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 col-form-label fw-semibold">Giá gốc (VNĐ)</label>
                <div class="col-sm-8">
                    <input type="number" name="original_price" class="form-control" step="0.01" min="0">
                </div>
            </div>

            <!-- Giá bán -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 col-form-label fw-semibold">Giá bán (VNĐ)</label>
                <div class="col-sm-8">
                    <input type="number" name="price" class="form-control" step="0.01" min="0" required>
                </div>
            </div>

            <!-- Số lượng tồn -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 col-form-label fw-semibold">Số lượng tồn</label>
                <div class="col-sm-8">
                    <input type="number" name="stock_quantity" class="form-control" min="0" value="0">
                </div>
            </div>

            <!-- Ảnh sản phẩm -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 col-form-label fw-semibold">Ảnh sản phẩm</label>
                <div class="col-sm-8">
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
            </div>

            <!-- Nút -->
            <div class="d-flex justify-content-between mt-4">
                <a href="products_admin.php" class="btn btn-secondary rounded-pill px-4">Quay lại</a>
                <button type="submit" class="btn btn-pink rounded-pill px-4">Thêm sản phẩm</button>
            </div>
        </form>
    </div>
</div>

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
textarea:focus,
select:focus {
    border-color: var(--pink-main);
    box-shadow: 0 0 0 0.2rem rgba(233, 30, 99, 0.25);
}

.col-form-label {
    white-space: nowrap;
}

form .form-control,
form .form-select {
    height: 38px;
    padding: 5px 10px;
}
</style>