<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/bootstrap.php'; // file này bạn đã có rồi
session_start();

$client = new Google_Client();
$client->setClientId('');
$client->setClientSecret('');
$client->setRedirectUri('http://localhost/google_callback.php');
$client->addScope('email');
$client->addScope('profile');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    // Nếu có lỗi khi lấy token
    if (isset($token['error'])) {
        die('Lỗi đăng nhập Google: ' . htmlspecialchars($token['error_description']));
    }

    $client->setAccessToken($token['access_token']);
    $google_service = new Google_Service_Oauth2($client);
    $google_user = $google_service->userinfo->get();

    $google_id = $google_user->id;
    $full_name = $google_user->name;
    $email = $google_user->email;

    // Kiểm tra người dùng trong database
    $stmt = $PDO->prepare("SELECT * FROM Users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Nếu chưa có, thêm mới
    if (!$user) {
        $username = 'gg_' . substr(md5($google_id), 0, 8);
        $fake_password = password_hash(bin2hex(random_bytes(5)), PASSWORD_DEFAULT);

        $insert = $PDO->prepare("INSERT INTO Users (username, full_name, email, password, role) VALUES (?, ?, ?, ?, 'customer')");
        $insert->execute([$username, $full_name, $email, $fake_password]);

        // Lấy lại thông tin user vừa tạo
        $stmt = $PDO->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lưu user vào session
    $_SESSION['user'] = [
        'user_id' => $user['user_id'],
        'username' => $user['username'],
        'full_name' => $user['full_name'],
        'email' => $user['email'],
        'role' => $user['role']
    ];

    // Chuyển hướng sau khi đăng nhập
    if ($user['role'] === 'admin') {
        header('Location: index_admin.php');
    } else {
        header('Location: index.php');
    }
    exit;
} else {
    header('Location: dang_nhap.php');
    exit;
}