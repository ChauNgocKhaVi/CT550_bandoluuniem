<?php
require_once __DIR__ . '/../src/bootstrap.php';

include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header.php';

?>

<nav aria-label="breadcrumb" class="mt-3">
    <ol class="breadcrumb" id="breadcrumb">
        <!-- Tự động sinh nội dung -->
    </ol>
</nav>

<body class="bg-light">
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
                    <div class="mb-3">
                        <label class="form-label">Email hoặc tên đăng nhập</label>
                        <input type="text" class="form-control rounded-pill"
                            placeholder="Nhập email hoặc tên đăng nhập">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control rounded-pill" placeholder="Nhập mật khẩu">
                    </div>
                    <div class="d-grid mb-2">
                        <button class="btn btn-pink rounded-pill">ĐĂNG NHẬP</button>
                    </div>
                    <div class="text-end mb-3">
                        <a href="#" class="text-decoration-none text-pink">Quên mật khẩu?</a>
                    </div>
                    <div class="text-center text-muted mb-2">Hoặc đăng nhập với</div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-facebook rounded-pill">Đăng nhập bằng Facebook</button>
                        <button class="btn btn-google rounded-pill">Đăng nhập bằng Google</button>
                    </div>
                </div>

                <!-- Đăng ký -->
                <div id="register-tab" style="display: none;">
                    <div class="mb-2">
                        <label class="form-label">Tên đăng nhập *</label>
                        <input type="text" class="form-control rounded-pill" placeholder="Nhập tên đăng nhập">
                    </div>
                    <!-- Họ tên -->
                    <div class="mb-2">
                        <label class="form-label">Họ tên *</label>
                        <input type="text" class="form-control rounded-pill" placeholder="Nhập họ tên">
                    </div>
                    <!-- Điện thoại -->
                    <div class="mb-2">
                        <label class="form-label">Điện thoại *</label>
                        <input type="tel" class="form-control rounded-pill" placeholder="Nhập số điện thoại">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control rounded-pill" placeholder="Nhập email">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Mật khẩu *</label>
                        <input type="password" class="form-control rounded-pill" placeholder="Tạo mật khẩu">
                    </div>
                    <!-- Nhập lại mật khẩu -->
                    <div class="mb-4">
                        <label class="form-label">Nhập lại mật khẩu *</label>
                        <input type="password" class="form-control rounded-pill" placeholder="Xác nhận mật khẩu">
                    </div>

                    <div class="d-grid">
                        <button class="btn btn-pink rounded-pill">ĐĂNG KÝ</button>
                    </div>
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