<?php
$current_page = basename($_SERVER['PHP_SELF']); // Lấy tên file hiện tại, ví dụ: "users_admin.php"
?>


<!-- Header Admin -->
<header class="bg-white shadow-sm py-3">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="logo">
            <h3 class="text-pink fw-bold">🌸 Viet Memories</h3>
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
                            <button type="submit" class="dropdown-item text-danger">Đăng xuất</button>
                        </form>
                    </li>
                </ul>
            </div>
            <?php else: ?>
            <!-- Nếu chưa đăng nhập -->
            <button class="btn btn-outline-pink" onclick="window.location.href='dang_nhap.php'">
                <i class="bi bi-person">Tài khoản</i>
            </button>
            <?php endif; ?>

        </div>
    </div>

</header>
<!-- Navbar cho mobile -->
<nav class="navbar bg-white shadow-sm d-lg-none px-3">
    <div class="container-fluid">
        <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasSidebar">
            <i class="bi bi-list fs-4"></i>
        </button>

    </div>
</nav>

<div class="d-flex">
    <!-- Sidebar cho desktop -->
    <div class="bg-white border-end shadow-sm d-none d-lg-block" id="sidebar" style="width: 250px; min-height: 100vh;">

        <ul class="nav flex-column p-3">
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'index_admin.php' ? 'active' : '' ?>" href="index_admin.php">
                    <i class="bi bi-speedometer2 me-2"></i> Tổng quan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'products_admin.php' ? 'active' : '' ?>"
                    href="products_admin.php">
                    <i class="bi bi-box-seam me-2"></i> Sản phẩm
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'orders_admin.php' ? 'active' : '' ?>" href="orders_admin.php">
                    <i class="bi bi-cart-check me-2"></i> Đơn hàng
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'users_admin.php' ? 'active' : '' ?>" href="users_admin.php">
                    <i class="bi bi-people me-2"></i> Người dùng
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'reports_admin.php' ? 'active' : '' ?>"
                    href="reports_admin.php">
                    <i class="bi bi-bar-chart-line me-2"></i> Báo cáo
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="../logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                </a>
            </li>
        </ul>

    </div>

    <!-- Sidebar Offcanvas cho mobile -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title text-primary fw-bold" id="offcanvasSidebarLabel">🌸 Admin Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link active" href="index_admin.php"><i
                            class="bi bi-speedometer2 me-2"></i>
                        Tổng quan</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-box-seam me-2"></i> Sản phẩm</a>
                </li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-cart-check me-2"></i> Đơn hàng</a>
                </li>
                <li class="nav-item"><a class="nav-link" href="users_admin.php"><i class="bi bi-people me-2"></i>
                        Người dùng</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-bar-chart-line me-2"></i> Báo
                        cáo</a></li>
                <li class="nav-item">
                    <form method="POST" action="../logout.php" class="m-0">
                        <button type="submit" class="nav-link text-danger border-0 bg-transparent">
                            <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
    <style>
    #userMenu:hover i,
    #userMenu:hover span {
        color: #fff !important;
    }
    </style>