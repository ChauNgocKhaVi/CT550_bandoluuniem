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