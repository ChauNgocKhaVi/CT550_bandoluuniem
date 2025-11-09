<?php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();

$client = new Google_Client();
$client->setClientId('');
$client->setClientSecret('');
$client->setRedirectUri('http://localhost/google_callback.php');
$client->addScope('email');
$client->addScope('profile');



$login_url = $client->createAuthUrl();
header('Location: ' . $login_url);
exit;