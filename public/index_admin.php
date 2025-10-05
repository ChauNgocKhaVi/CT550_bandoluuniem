<?php
require_once __DIR__ . '/../src/bootstrap.php';

session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: dang_nhap.php");
    exit;
}
include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header_admin.php';
?>

<body>


    <!-- Main Content -->
    <div class="p-4 flex-grow-1">
        <h2>Bảng điều khiển</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card text-bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Tổng sản phẩm</h5>
                        <p class="card-text">120 sản phẩm đang hoạt động</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Đơn hàng hôm nay</h5>
                        <p class="card-text">35 đơn hàng mới</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Người dùng mới</h5>
                        <p class="card-text">8 tài khoản đăng ký</p>
                    </div>
                </div>
            </div>
        </div>
    </div>



</body>

<style>
body {
    font-family: 'Segoe UI', sans-serif;
}

#sidebar .nav-link {
    font-weight: 500;
    color: #333;
}

#sidebar .nav-link:hover {
    background-color: #f8d7da;
    border-radius: 5px;
}

#sidebar .nav-link.active {
    background-color: #e91e63;
    color: white;
    border-radius: 5px;
}
</style>