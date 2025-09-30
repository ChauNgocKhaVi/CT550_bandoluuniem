<?php
session_start();

// Xóa session user
if (isset($_SESSION['user'])) {
    unset($_SESSION['user']);
}

// Nếu muốn xóa toàn bộ session
// session_destroy();

// Chuyển hướng về trang chủ
header("Location: index.php");
exit;