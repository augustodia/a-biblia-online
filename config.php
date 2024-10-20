<?php
require_once 'env.php';

if (ENVIROMENT == 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

$configDB = [
    'host' => DB_HOST,
    'dbname' => DB_NAME,
    'user' => DB_USER,
    'pass' => DB_PASS
];

global $db; // Se quiser deixar global
try {
  $db = new PDO("mysql:host={$configDB['host']};dbname={$configDB['dbname']}", $configDB['user'], $configDB['pass']);
} catch (PDOException $e) {
    echo $e->getMessage();
    exit;
}
?>
