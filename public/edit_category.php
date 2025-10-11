<?php
session_start();
require_once __DIR__ . '/../src/bootstrap.php';

// Kiểm tra quyền admin (nếu có phân quyền)
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dang_nhap.php");
    exit;
}

// Kết nối CSDL
$pdo = $PDO;

// Lấy ID danh mục cần chỉnh sửa từ URL (VD: edit_category.php?id=3)
$category_id = $_GET['id'] ?? null;

// Nếu không có ID, quay lại trang danh sách
if (!$category_id) {
    header("Location: categories_admin.php");
    exit;
}

// Lấy thông tin danh mục hiện tại
$stmt = $pdo->prepare("SELECT * FROM Categories WHERE category_id = ?");
$stmt->execute([$category_id]);
$currentCategory = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$currentCategory) {
    $message = "<div class='alert alert-danger'>Không tìm thấy danh mục.</div>";
} else {
    // Nếu người dùng gửi form
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $category_name = trim($_POST['category_name']);

        if (empty($category_name)) {
            $message = "<div class='alert alert-warning'>Vui lòng nhập tên danh mục.</div>";
        } else {
            try {
                $updateStmt = $pdo->prepare("UPDATE Categories SET category_name = ? WHERE category_id = ?");
                $updateStmt->execute([$category_name, $category_id]);
                $message = "<div class='alert alert-success text-center'>Cập nhật danh mục thành công!</div>";

                // Reload lại dữ liệu
                $stmt = $pdo->prepare("SELECT * FROM Categories WHERE category_id = ?");
                $stmt->execute([$category_id]);
                $currentCategory = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $message = "<div class='alert alert-danger'>Lỗi khi cập nhật: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }
}

include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header_admin.php';
?>

<div class="container mt-4">
    <h3 class="mb-4 text-pink text-center fw-bold">✏️ Chỉnh sửa danh mục</h3>

    <div class="mx-auto" style="max-width: 500px;">
        <form method="POST" class="p-4 shadow-sm bg-light rounded">

            <!-- Hiển thị thông báo -->
            <?php if (!empty($message)): ?>
            <div class="alert custom-alert text-center mb-3">
                <?= $message ?>
            </div>
            <?php endif; ?>

            <!-- Category name -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 col-form-label fw-semibold">Tên danh mục</label>
                <div class="col-sm-8">
                    <input type="text" name="category_name" class="form-control"
                        value="<?= htmlspecialchars($currentCategory['category_name']) ?>" required>
                </div>
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-between mt-4">
                <a href="categories_admin.php" class="btn btn-secondary rounded-pill px-4">Quay lại</a>
                <button type="submit" class="btn btn-pink rounded-pill px-4">Cập nhật</button>
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
</style>