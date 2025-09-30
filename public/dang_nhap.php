<?php
require_once __DIR__ . '/../src/bootstrap.php';

use CT550\Labs\User;

$message = "";

// Xử lý khi submit form đăng ký
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username   = trim($_POST['username']);
    $full_name  = trim($_POST['full_name']);
    $email      = trim($_POST['email']);
    $phone      = trim($_POST['phone_number']);
    $password   = $_POST['password'];
    $confirm_pw = $_POST['confirm_password'];

    if ($password !== $confirm_pw) {
        $message = "❌ Mật khẩu xác nhận không khớp.";
    } else {
        try {
            $user = new User($PDO); // $pdo có trong bootstrap.php
            $ok = $user->register($username, $full_name, $email, $password, $phone);

            if ($ok) {
                $message = "✅ Đăng ký thành công! Bạn có thể đăng nhập.";
            }
        } catch (Exception $e) {
            $message = "❌ Lỗi: " . $e->getMessage();
        }
    }
}

// Xử lý đăng nhập

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $usernameOrEmail = $_POST['username_or_email'] ?? '';  // đúng name trong form
    $password = $_POST['password'] ?? '';
    $user = new User($PDO);
    $result = $user->login($usernameOrEmail, $password);

    if ($result) {
        session_start();
        $_SESSION['user'] = $result;
        
        // ✅ Chuyển hướng về trang chủ
        header("Location: index.php");
        exit;
    } else {
        $message = "Sai tên đăng nhập/email hoặc mật khẩu!";
    }
}

include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header.php';
?>



<body class="bg-light">
    <?php if (!empty($message)): ?>
    <div class="alert alert-info text-center">
        <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>
    <div class="container py-5">
        <div class="card mx-auto shadow-lg rounded-4" style="max-width: 500px;">
            <div class="card-header bg-white border-0 text-center">
                <div class="btn-group w-100" role="group">
                    <button type="button" class="btn btn-tab active" onclick="showTab('login')">ĐĂNG NHẬP</button>
                    <button type="button" class="btn btn-tab" onclick="showTab('register')">ĐĂNG KÝ</button>
                </div>
            </div>
            <div class="card-body ">

                <!-- Đăng nhập -->
                <div id="login-tab">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Email hoặc tên đăng nhập</label>
                            <input type="text" name="username_or_email" class="form-control rounded-pill"
                                placeholder="Nhập email hoặc tên đăng nhập" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu</label>
                            <input type="password" name="password" class="form-control rounded-pill"
                                placeholder="Nhập mật khẩu" required>
                        </div>
                        <div class="d-grid mb-2">
                            <button type="submit" name="login" class="btn btn-pink rounded-pill">ĐĂNG NHẬP</button>
                        </div>
                        <div class="text-end mb-3">
                            <a href="#" class="text-decoration-none text-pink">Quên mật khẩu?</a>
                        </div>
                        <div class="text-center text-muted mb-2">Hoặc đăng nhập với</div>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-facebook rounded-pill">Đăng nhập bằng Facebook</button>
                            <button type="button" class="btn btn-google rounded-pill">Đăng nhập bằng Google</button>
                        </div>
                    </form>
                </div>

                <!-- Đăng ký -->
                <div id="register-tab" style="display: none;">
                    <form method="POST" action="">
                        <div class="mb-2">
                            <label class="form-label">Tên đăng nhập *</label>
                            <input type="text" name="username" class="form-control rounded-pill"
                                placeholder="Nhập tên đăng nhập" required>
                        </div>
                        <!-- Họ tên -->
                        <div class="mb-2">
                            <label class="form-label">Họ tên *</label>
                            <input type="text" name="full_name" class="form-control rounded-pill"
                                placeholder="Nhập họ tên" required>
                        </div>
                        <!-- Điện thoại -->
                        <div class="mb-2">
                            <label class="form-label">Điện thoại *</label>
                            <input type="tel" name="phone_number" class="form-control rounded-pill"
                                placeholder="Nhập số điện thoại">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control rounded-pill" placeholder="Nhập email"
                                required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Mật khẩu *</label>
                            <input type="password" name="password" class="form-control rounded-pill"
                                placeholder="Tạo mật khẩu" required>
                        </div>
                        <!-- Nhập lại mật khẩu -->
                        <div class="mb-4">
                            <label class="form-label">Nhập lại mật khẩu *</label>
                            <input type="password" name="confirm_password" class="form-control rounded-pill"
                                placeholder="Xác nhận mật khẩu" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="register" class="btn btn-pink rounded-pill">ĐĂNG KÝ</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>

</html>
<style>
body {
    font-family: 'Segoe UI', sans-serif;
}

.btn-tab {
    background-color: #f8f9fa;
    border: none;
    font-weight: 600;
    color: #e91e63;
    padding: 10px 0;
}

.btn-tab.active {
    border-bottom: 3px solid #e91e63;
    color: #d81b60;
}

.btn-pink {
    background-color: #e91e63;
    color: white;
    font-weight: 600;
    border: none;
}

.btn-pink:hover {
    background-color: #d81b60;
}

.text-pink {
    color: #e91e63;
}

.btn-facebook {
    background-color: #3b5998;
    color: white;
    font-weight: 500;
}

.btn-google {
    background-color: #db4437;
    color: white;
    font-weight: 500;
}
</style>
<script>
function showTab(tab) {
    document.getElementById('login-tab').style.display = tab === 'login' ? 'block' : 'none';
    document.getElementById('register-tab').style.display = tab === 'register' ? 'block' : 'none';

    const buttons = document.querySelectorAll('.btn-tab');
    buttons.forEach(btn => btn.classList.remove('active'));
    document.querySelector(`.btn-tab[onclick="showTab('${tab}')"]`).classList.add('active');
}
</script>