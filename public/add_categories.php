<?php
session_start();
require_once __DIR__ . '/../src/bootstrap.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dang_nhap.php");
    exit;
}

$message = "";

// Nếu form được gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = trim($_POST['category_name']);


    if (empty($category_name)) {
        $message = '<div class="alert alert-danger">⚠️ Vui lòng nhập tên thể loại.</div>';
    } else {
        $stmt = $PDO->prepare("INSERT INTO Categories (category_name) VALUES (:category_name )");
        $stmt->execute([

            ':category_name' => $category_name

        ]);
        $message = '<div class="alert alert-success">✅ Thêm thể loại thành công!</div>';
    }
}

// Lấy danh sách thể loại cha
$stmt = $PDO->query("SELECT category_id, category_name FROM Categories ORDER BY category_name ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header_admin.php';
?>

<div class="container mt-4">
    <h3 class="mb-4 text-pink text-center fw-bold">➕ Thêm thể loại sản phẩm</h3>

    <div class="mx-auto" style="max-width: 600px;">
        <form method="POST" class="p-4 shadow-sm bg-light rounded">

            <!-- Thông báo -->
            <?php if (!empty($message)): ?>
            <div class="text-center mb-3"><?= $message ?></div>
            <?php endif; ?>

            <!-- Tên thể loại -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 col-form-label fw-semibold">Tên thể loại</label>
                <div class="col-sm-8">
                    <input type="text" name="category_name" class="form-control" required>
                </div>
            </div>


            <!-- Nút -->
            <div class="d-flex justify-content-between mt-4">
                <a href="categories_admin.php" class="btn btn-secondary rounded-pill px-4">Quay lại</a>
                <button type="submit" class="btn btn-pink rounded-pill px-4">Thêm thể loại</button>
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