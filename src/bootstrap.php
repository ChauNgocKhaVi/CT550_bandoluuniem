<?php




require_once 'functions.php';
require_once __DIR__ . '/../libraries/Psr4AutoloaderClass.php';

$loader = new Psr4AutoloaderClass;
$loader->register();

$loader->addNamespace('CT550\Labs', __DIR__ . '/classes');

try {
    $PDO = (new \CT550\Labs\PDOFactory())->create([
        'dbhost' => 'localhost',
        'dbname' => 'doluuniem_db',
        'dbuser' => 'root',
        'dbpass' => ''
    ]);
} catch (Exception $ex) {
    echo 'Không thể kết nối đến MySQL,
        kiểm tra lại username/password đến MySQL.<br>';
    exit("<pre>{$ex}</pre>");
}