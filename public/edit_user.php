<?php
session_start();
require_once __DIR__ . '/../src/bootstrap.php';

use CT550\Labs\User;

$user = new User($PDO);

// Lấy ID người dùng cần chỉnh sửa từ URL (VD: edit_user.php?id=5)
$user_id = $_GET['id'] ?? null;

// Nếu không có ID, quay lại trang danh sách
if (!$user_id) {
    header("Location: users_admin.php");
    exit;
}

// Lấy thông tin người dùng hiện tại
$currentUser = $user->getUserById($user_id);

if (!$currentUser) {
    $message = "<div class='alert alert-danger'>Không tìm thấy người dùng.</div>";
} else {
    // Nếu người dùng submit form
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $message = $user->handleEditUserForm($user_id, $_POST);
        // Sau khi cập nhật thành công, reload lại dữ liệu
        $currentUser = $user->getUserById($user_id);
    }
}

include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header_admin.php';
?>

<div class="container mt-4">
    <h3 class="mb-4 text-pink text-center fw-bold">✏️ Chỉnh sửa thông tin người dùng</h3>

    <div class="mx-auto" style="max-width: 600px;">
        <form method="POST" class="p-4 shadow-sm bg-light rounded">

            <!-- Hiển thị thông báo -->
            <?php if (!empty($message)): ?>
            <div class="alert custom-alert text-center mb-3">
                <?= $message ?>
            </div>
            <?php endif; ?>

            <!-- Username -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 col-form-label fw-semibold">Tên đăng nhập</label>
                <div class="col-sm-8">
                    <input type="text" name="username" class="form-control"
                        value="<?= htmlspecialchars($currentUser['username']) ?>" required>
                </div>
            </div>

            <!-- Full name -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 col-form-label fw-semibold">Họ và tên</label>
                <div class="col-sm-8">
                    <input type="text" name="full_name" class="form-control"
                        value="<?= htmlspecialchars($currentUser['full_name']) ?>" required>
                </div>
            </div>

            <!-- Email -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 col-form-label fw-semibold">Email</label>
                <div class="col-sm-8">
                    <input type="email" name="email" class="form-control"
                        value="<?= htmlspecialchars($currentUser['email']) ?>" required>
                </div>
            </div>

            <!-- Phone -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 col-form-label fw-semibold">Số điện thoại</label>
                <div class="col-sm-8">
                    <input type="text" name="phone_number" class="form-control" pattern="[0-9]{10,11}"
                        title="Chỉ nhập số, từ 10–11 ký tự"
                        value="<?= htmlspecialchars($currentUser['phone_number']) ?>">
                </div>
            </div>

            <!-- Password -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 col-form-label fw-semibold">Mật khẩu mới</label>
                <div class="col-sm-8">
                    <input type="password" name="password" class="form-control" placeholder="Để trống nếu không đổi">
                </div>
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-between mt-4">
                <a href="users_admin.php" class="btn btn-secondary rounded-pill px-4">Quay lại</a>
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

form .form-control,
form .form-select {
    height: 38px;
    padding: 5px 10px;
}
</style>