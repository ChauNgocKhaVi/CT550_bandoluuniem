<?php
session_start();
require_once __DIR__ . '/../src/bootstrap.php';


$message = "";

// Xử lý khi submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username   = trim($_POST['username']);
    $full_name  = trim($_POST['full_name']);
    $email      = trim($_POST['email']);
    $phone      = trim($_POST['phone_number']);
    $password   = $_POST['password'];
    $role       = $_POST['role'];

    // Kiểm tra dữ liệu hợp lệ
    if (empty($username) || empty($full_name) || empty($email) || empty($password)) {
        $message = "<div class='alert alert-danger'>Vui lòng điền đầy đủ thông tin.</div>";
    } elseif (!preg_match('/^[0-9]{10,11}$/', $phone)) {
        $message = "<div class='alert alert-danger'>Số điện thoại chỉ gồm 10-11 chữ số.</div>";
    } else {
        try {
            // Mã hóa mật khẩu
            $hashed_pw = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("INSERT INTO Users (username, full_name, email, password, phone_number, role) 
                                   VALUES (:username, :full_name, :email, :password, :phone_number, :role)");
            $stmt->execute([
                ':username'     => $username,
                ':full_name'    => $full_name,
                ':email'        => $email,
                ':password'     => $hashed_pw,
                ':phone_number' => $phone,
                ':role'         => $role
            ]);

            $message = "<div class='alert alert-success'>Thêm người dùng thành công!</div>";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "<div class='alert alert-danger'>Tên đăng nhập hoặc email đã tồn tại.</div>";
            } else {
                $message = "<div class='alert alert-danger'>Lỗi: " . $e->getMessage() . "</div>";
            }
        }
    }
}

include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header_admin.php';
?>



<div class="container mt-4">
    <h3 class="mb-4 text-pink text-center">➕ Thêm người dùng mới</h3>
    <?= $message ?>

    <div class="mx-auto" style="max-width: 600px;">
        <form method="POST" class="p-4 shadow-sm bg-light rounded">
            <!-- Username -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 col-form-label fw-semibold">👤 Tên đăng nhập</label>
                <div class="col-sm-8">
                    <input type="text" name="username" class="form-control" required>
                </div>
            </div>

            <!-- Full name -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 col-form-label fw-semibold">📝 Họ và tên</label>
                <div class="col-sm-8">
                    <input type="text" name="full_name" class="form-control" required>
                </div>
            </div>

            <!-- Email -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 col-form-label fw-semibold">📧 Email</label>
                <div class="col-sm-8">
                    <input type="email" name="email" class="form-control" required>
                </div>
            </div>

            <!-- Phone -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 col-form-label fw-semibold">📱 Số điện thoại</label>
                <div class="col-sm-8">
                    <input type="text" name="phone_number" class="form-control" pattern="[0-9]{10,11}"
                        title="Chỉ nhập số, từ 10–11 ký tự">
                </div>
            </div>

            <!-- Password -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 col-form-label fw-semibold">🔒 Mật khẩu</label>
                <div class="col-sm-8">
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>

            <!-- Role -->
            <div class="row mb-3 align-items-center">
                <label class="col-sm-4 col-form-label fw-semibold">🎯 Vai trò</label>
                <div class="col-sm-8">
                    <select name="role" class="form-select">
                        <option value="customer">Khách hàng</option>
                        <option value="admin">Quản trị viên</option>
                    </select>
                </div>
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-between mt-4">
                <a href="users_list.php" class="btn btn-secondary rounded-pill px-4">Quay lại</a>
                <button type="submit" class="btn btn-pink rounded-pill px-4">Thêm người dùng</button>
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

/* .form-control,
.form-select {
    border-radius: 50px;
    padding-left: 15px;
} */

input:focus,
select:focus {
    border-color: var(--pink-main);
    box-shadow: 0 0 0 0.2rem rgba(233, 30, 99, 0.25);
}
</style>

<style>
.col-form-label {
    white-space: nowrap;
    /* tránh label bị xuống dòng */
}

form .form-control,
form .form-select {
    height: 38px;
    padding: 5px 10px;
}
</style>