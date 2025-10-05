<?php
session_start(); // thêm để đảm bảo dùng session
?>

<!-- Header -->
<header class="bg-white shadow-sm py-3">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="logo">
            <h3 class="text-pink fw-bold">🌸 Viet Memories</h3>
        </div>
        <!-- Search -->
        <div class="search-bar w-50 pe-5">
            <div class="input-group">
                <input type="text" class="form-control rounded-pill" placeholder="Bạn cần tìm ...">
                <button class="btn btn-pink rounded-pill ms-2">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
        <div class="icons d-flex gap-3">
            <?php if (isset($_SESSION['user'])): ?>
            <!-- Nếu đã đăng nhập -->
            <div class="dropdown">
                <button class="btn btn-outline-pink dropdown-toggle d-flex align-items-center gap-2" type="button"
                    id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle text-pink"></i>
                    <span class="fw-bold text-pink">
                        <?= htmlspecialchars($_SESSION['user']['username']) ?>
                    </span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="userMenu">
                    <li>
                        <form method="POST" action="logout.php" class="m-0">
                            <button type="submit" class="dropdown-item text-danger">Thông tin tài khoản</button>

                        </form>
                    </li>
                    <li>
                        <form method="POST" action="logout.php" class="m-0">

                            <button type="submit" class="dropdown-item text-danger">Đăng xuất</button>
                        </form>
                    </li>
                </ul>
            </div>
            <?php else: ?>
            <!-- Nếu chưa đăng nhập -->
            <button class="btn btn-outline-pink" onclick="window.location.href='dang_nhap.php'">
                <i class="bi bi-person"></i>
            </button>
            <?php endif; ?>

            <button class="btn btn-outline-pink"><i class="bi bi-cart"></i></button>
        </div>
    </div>
</header>
<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg bg-pink navbar-dark">
    <div class="container">
        <!-- Nút toggle khi màn hình nhỏ -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu -->
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a href="#" class="nav-link text-white">VỀ VIET MEMORIES</a></li>
                <li class="nav-item"><a href="#" class="nav-link text-white">SẢN PHẨM MỚI</a></li>
                <li class="nav-item"><a href="#" class="nav-link text-white">DANH MỤC SẢN PHẨM</a></li>
                <li class="nav-item"><a href="#" class="nav-link text-white">💝 DEAL HOT DƯỚI 100K 💝</a></li>
                <li class="nav-item">
                    <NG href="#" class="nav-link text-white">THƯƠNG HIỆU</NG>
                </li>
                <li class="nav-item"><a href="#" class="nav-link text-white">TIN TỨC</a></li>
            </ul>
        </div>
    </div>
</nav>

<style>
#userMenu:hover i,
#userMenu:hover span {
    color: #fff !important;
}
</style>