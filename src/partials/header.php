<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} // thêm để đảm bảo dùng session

?>

<!-- Header -->
<header class="bg-white shadow-sm py-3">


    <div class="container d-flex justify-content-between align-items-center">
        <div class="logo">
            <h3 class="text-pink fw-bold">🌸 Viet Memories</h3>


        </div>
        <!-- Search -->
        <form class="search-bar w-50 pe-5 position-relative" method="GET" action="tim_kiem.php">
            <input type="text" name="q" class="form-control rounded-pill pe-5" placeholder="Bạn cần tìm ..."
                id="searchInput">
            <!-- Icon micro -->
            <i class="bi bi-mic-fill text-pink position-absolute top-50 translate-middle-y"
                style="right: 55px; cursor: pointer;" id="voiceSearch"></i>
            <!-- Nút tìm kiếm -->
            <button type="submit" class="btn btn-pink rounded-pill position-absolute end-0 top-50 translate-middle-y">
                <i class="bi bi-search"></i>
            </button>
        </form>



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
                <li class="nav-item"><a href="../products.php" class="nav-link text-white">MỚI</a></li>
                <li class="nav-item"><a href="../products.php" class="nav-link text-white">SẢN PHẨM</a></li>
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
<!-- Form ghi âm giọng nói -->
<div class="modal fade" id="voiceModal" tabindex="-1" aria-labelledby="voiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4" style="border-radius: 20px;">
            <h5 class="text-pink fw-bold mb-3" id="voiceModalLabel">🎙️ Hệ thống đang nghe bạn nói...</h5>
            <div class="mic-animation mx-auto mb-3"></div>
            <p class="text-muted">Hãy nói nội dung bạn muốn tìm kiếm</p>
            <button type="button" class="btn btn-outline-pink mt-3" data-bs-dismiss="modal">Dừng lại</button>
        </div>
    </div>
</div>


<style>
#userMenu:hover i,
#userMenu:hover span {
    color: #fff !important;
}



.search-bar {
    position: relative;
}

.search-bar input {
    padding-right: 90px;
    /* để tránh chữ bị icon hoặc nút che */
}

.search-bar i.bi-mic-fill {
    font-size: 1.2rem;
    transition: 0.3s;
    z-index: 10;
}

.search-bar i.bi-mic-fill:hover {
    color: #e91e63;
    transform: scale(1.1);
}

.bg-pink {
    background-color: #e91e63 !important;
}

.btn-outline-pink {
    border: 1px solid #e91e63;
    color: #e91e63;
}

.btn-outline-pink:hover {
    background-color: #e91e63;
    color: white;
}
</style>


<style>
/* Hiệu ứng sóng micro */
.mic-animation {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: #e91e63;
    animation: pulse 1.2s infinite;
}


/* Hiệu ứng ẩn dần modal */
.fade-out {
    opacity: 0 !important;
    transform: translateY(-10px);
    transition: all 2s ease;
}


@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 1;
    }

    100% {
        transform: scale(1.5);
        opacity: 0;
    }
}
</style>

<script>
const voiceBtn = document.getElementById('voiceSearch');
const modalEl = document.getElementById('voiceModal');
const input = document.querySelector('.search-bar input');
const stopBtn = modalEl.querySelector('[data-bs-dismiss="modal"]');
const micAnim = document.querySelector('.mic-animation');
let recognition;

voiceBtn.addEventListener('click', () => {
    if ('webkitSpeechRecognition' in window) {
        recognition = new webkitSpeechRecognition();
        recognition.lang = 'vi-VN';

        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        // Bắt đầu hiệu ứng micro
        micAnim.classList.add('mic-animation');

        recognition.onresult = event => {
            input.value = event.results[0][0].transcript;
        };

        recognition.onend = () => {
            micAnim.classList.remove('mic-animation');
            // Cho form mờ dần khi Chrome tự dừng mic
            modalEl.classList.add('fade-out');
            setTimeout(() => {
                modalEl.classList.remove('fade-out');
                modal.hide();
            }, 800);
        };

        recognition.start();

        // Khi nhấn nút "Dừng lại"
        stopBtn.addEventListener('click', () => {
            if (recognition) {
                recognition.onend = null;
                recognition.stop();
            }

            // Tắt hiệu ứng micro
            micAnim.classList.remove('mic-animation');

            // Thêm hiệu ứng ẩn dần cho modal
            const modalContent = modalEl.querySelector('.modal-content');
            modalContent.classList.add('fade-out');

            // Sau khi hiệu ứng kết thúc, ẩn modal thật sự
            setTimeout(() => {
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                modalInstance.hide();
                modalContent.classList.remove('fade-out'); // reset để lần sau vẫn mượt
            }, 400);
        }, {
            once: true
        });


    } else {
        alert('Trình duyệt của bạn không hỗ trợ nhận diện giọng nói.');
    }
});
</script>