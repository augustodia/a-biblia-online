<?php
require_once 'env.php';

// Configuração de exibição de erros baseada no ambiente
if (ENVIRONMENT == 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// Configuração do banco de dados
$configDB = [
    'host' => DB_HOST,
    'dbname' => DB_NAME,
    'user' => DB_USER,
    'pass' => DB_PASS,
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];

try {
    $db = new PDO(
        "mysql:host={$configDB['host']};dbname={$configDB['dbname']};charset={$configDB['charset']}",
        $configDB['user'],
        $configDB['pass'],
        $configDB['options']
    );
    global $db;
} catch (PDOException $e) {
    if (ENVIRONMENT == 'development') {
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    } else {
        // Log do erro em produção
        error_log("Erro de conexão com o banco de dados: " . $e->getMessage());
        die('Erro interno do servidor. Por favor, tente novamente mais tarde.');
    }
}

// Obtém o protocolo (http ou https)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';

// Obtém o host atual (seja localhost ou o domínio do localtunnel)
$host = $_SERVER['HTTP_HOST'];

// Define a BASE_URL
define('BASE_URL', $protocol . $host . '/mvc/');
?>
