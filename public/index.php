<?php
require_once __DIR__ . '/../src/bootstrap.php';
include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header.php';

?>


<body>

    <!-- Slide thông báo -->
    <div id="storeCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <!-- Slide 1 -->
            <div class="carousel-item active">
                <img src="anh/tb1.jpg" class="d-block w-100" alt="Thông báo 1">
            </div>
            <!-- Slide 2 -->
            <div class="carousel-item">
                <img src="anh/tb2.jpg" class="d-block w-100" alt="Thông báo 2">
            </div>
            <!-- Slide 3 -->
            <div class="carousel-item">
                <img src="anh/tb3.jpg" class="d-block w-100" alt="Thông báo 3">
            </div>
        </div>

        <!-- Nút điều hướng -->
        <button class="carousel-control-prev" type="button" data-bs-target="#storeCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#storeCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <!-- Tiêu đề -->
    <div class="container my-4 pt-5">
        <h4 class="fw-bold text-pink">Sản phẩm mới</h4>
    </div>

    <div class="container">
        <div class="row g-4">

            <!-- Sản phẩm 1 -->
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>🐱 Móc khóa Hello Kitty</h6>
                        <p class="text-danger fw-bold">70,000₫</p>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm 2 -->
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>🐱 Gấu bông Hello Kitty 40cm</h6>
                        <p class="text-danger fw-bold">160,000₫</p>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm 3 -->
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>👜 Bộ túi mỹ phẩm Hello Kitty</h6>
                        <p class="text-danger fw-bold">140,000₫</p>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm 4 -->
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>🎀 Bộ phụ kiện trang trí Hello Kitty</h6>
                        <p class="text-danger fw-bold">65,000₫</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Nút xem thêm -->
        <div class="text-center mt-4">
            <button class="btn btn-pink" onclick="seeMore()">Xem thêm</button>
        </div>
    </div>


    <!-- Tiêu đề -->
    <div class="container my-4 pt-5">
        <h4 class="fw-bold text-pink">🎁 Quà lưu niệm (491 sản phẩm)</h4>
    </div>

    <!-- Danh sách sản phẩm -->
    <div class="container">
        <div class="row g-4">

            <!-- Sản phẩm 1 -->
            <div class="col-md-3 d-flex">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <img src="anh/nenthom.jpg" class="d-block w-100" alt="">
                        <h6 class="text-muted">CHIP CHIP</h6>
                        <p class="fw-semibold">Nến Thơm Bath & Body Works Mùi Orange + Ginger</p>
                        <p class="text-danger fw-bold fs-5">510,000₫</p>
                        <button class="btn btn-pink w-100" onclick="addToCart('Orange + Ginger')">THÊM VÀO GIỎ</button>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm 2 -->
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <img src="anh/nenthom2.jpg" class="d-block w-100" alt="">
                        <h6 class="text-muted">CHIP CHIP</h6>
                        <p class="fw-semibold">Nến Thơm Bath & Body Works Mùi Pineapple + Mango</p>
                        <p class="text-danger fw-bold fs-5">420,000₫</p>
                        <button class="btn btn-pink w-100" onclick="addToCart('Pineapple + Mango')">THÊM VÀO
                            GIỎ</button>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <img src="anh/nenthom3.jpg" class="d-block w-100" alt="">
                        <h6 class="text-muted">CHIP CHIP</h6>
                        <p class="fw-semibold">Nến Thơm Bath & Body Works Mùi Pineapple + Mango</p>
                        <p class="text-danger fw-bold fs-5">420,000₫</p>
                        <button class="btn btn-pink w-100" onclick="addToCart('Pineapple + Mango')">THÊM VÀO
                            GIỎ</button>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <img src="anh/nenthom4.jpg" class="d-block w-100" alt="">
                        <h6 class="text-muted">CHIP CHIP</h6>
                        <p class="fw-semibold">Nến Thơm Bath & Body Works Mùi Pineapple + Mango</p>
                        <p class="text-danger fw-bold fs-5">420,000₫</p>
                        <button class="btn btn-pink w-100" onclick="addToCart('Pineapple + Mango')">THÊM VÀO
                            GIỎ</button>
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-4 pt-3">

            <!-- Sản phẩm 1 -->
            <div class="col-md-3 d-flex">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <img src="anh/nenthom.jpg" class="d-block w-100" alt="">
                        <h6 class="text-muted">CHIP CHIP</h6>
                        <p class="fw-semibold">Nến Thơm Bath & Body Works Mùi Orange + Ginger</p>
                        <p class="text-danger fw-bold fs-5">510,000₫</p>
                        <button class="btn btn-pink w-100" onclick="addToCart('Orange + Ginger')">THÊM VÀO GIỎ</button>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm 2 -->
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <img src="anh/nenthom2.jpg" class="d-block w-100" alt="">
                        <h6 class="text-muted">CHIP CHIP</h6>
                        <p class="fw-semibold">Nến Thơm Bath & Body Works Mùi Pineapple + Mango</p>
                        <p class="text-danger fw-bold fs-5">420,000₫</p>
                        <button class="btn btn-pink w-100" onclick="addToCart('Pineapple + Mango')">THÊM VÀO
                            GIỎ</button>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <img src="anh/nenthom3.jpg" class="d-block w-100" alt="">
                        <h6 class="text-muted">CHIP CHIP</h6>
                        <p class="fw-semibold">Nến Thơm Bath & Body Works Mùi Pineapple + Mango</p>
                        <p class="text-danger fw-bold fs-5">420,000₫</p>
                        <button class="btn btn-pink w-100" onclick="addToCart('Pineapple + Mango')">THÊM VÀO
                            GIỎ</button>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <img src="anh/nenthom4.jpg" class="d-block w-100" alt="">
                        <h6 class="text-muted">CHIP CHIP</h6>
                        <p class="fw-semibold">Nến Thơm Bath & Body Works Mùi Pineapple + Mango</p>
                        <p class="text-danger fw-bold fs-5">420,000₫</p>
                        <button class="btn btn-pink w-100" onclick="addToCart('Pineapple + Mango')">THÊM VÀO
                            GIỎ</button>
                    </div>
                </div>
            </div>

        </div>

        <!-- Nút xem thêm -->
        <div class="text-center mt-4">
            <button class="btn btn-pink" onclick="seeMore()">Xem thêm</button>
        </div>
    </div>

    <div class="container my-4 pt-3">
        <h4 class="fw-bold text-pink">Chủ đề mới</h4>
    </div>

    <!-- Danh mục chủ đề -->
    <div class="container my-4">
        <div class="row text-center g-4">

            <!-- Hello Kitty -->
            <div class="col-6 col-md-3">
                <div class="card border-0">
                    <img src="anh/CD_hellokitty.png" class="card-img-top" alt="Hello Kitty">
                </div>
            </div>

            <!-- Bát đĩa -->
            <div class="col-6 col-md-3">
                <div class="card border-0">
                    <img src="anh/CD_bat_dia.png" class="card-img-top" alt="Hello Kitty">
                </div>
            </div>

            <!-- Quạt -->
            <div class="col-6 col-md-3">
                <div class="card border-0">
                    <img src="anh/CD_quat.png" class="card-img-top" alt="Quạt Kitty">
                </div>
            </div>

            <!-- Phụ kiện làm đẹp -->
            <div class="col-6 col-md-3">
                <div class="card border-0">
                    <img src="anh/CD_phu_kien_lam_dep.png" class="card-img-top" alt="Phụ kiện làm đẹp">
                </div>
            </div>



        </div>

    </div>

    <!-- Danh sách sản phẩm Hello Kitty -->
    <div class="container">
        <div class="row g-4">

            <!-- Sản phẩm 1 -->
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>🐱 Móc khóa Hello Kitty</h6>
                        <p class="text-danger fw-bold">70,000₫</p>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm 2 -->
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>🐱 Gấu bông Hello Kitty 40cm</h6>
                        <p class="text-danger fw-bold">160,000₫</p>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm 3 -->
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>👜 Bộ túi mỹ phẩm Hello Kitty</h6>
                        <p class="text-danger fw-bold">140,000₫</p>
                    </div>
                </div>
            </div>

            <!-- Sản phẩm 4 -->
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>🎀 Bộ phụ kiện trang trí Hello Kitty</h6>
                        <p class="text-danger fw-bold">65,000₫</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Nút xem thêm -->
        <div class="text-center mt-4">
            <button class="btn btn-pink" onclick="seeMore()">Xem thêm</button>
        </div>
    </div>


</body>

</html>


<script>
// Ví dụ: thông báo khi người dùng nhấn vào giỏ hàng
document.querySelector('.bi-cart')?.addEventListener('click', () => {
    alert('Giỏ hàng của bạn đang trống!');
});

function addToCart(productName) {
    alert(`Đã thêm "${productName}" vào giỏ hàng!`);
}
</script>