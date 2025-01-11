<?php
// Configurações de ambiente
define('ENVIRONMENT', 'development'); // 'development' ou 'production'

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'biblia');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configurações de segurança
define('HASH_SALT', 'sua_salt_secreta_aqui'); // Usado para hashing
define('SESSION_NAME', 'BIBLIA_SESSION');
define('CSRF_TOKEN_NAME', 'biblia_csrf_token');

// Configurações de cache
define('CACHE_ENABLED', false);
define('CACHE_DURATION', 3600); // 1 hora em segundos

// Configurações de API
define('API_RATE_LIMIT', 100); // requisições por hora
define('API_VERSION', 'v1');

// Configurações de log
define('LOG_ERRORS', true);
define('LOG_PATH', __DIR__ . '/logs');

// Timezone
date_default_timezone_set('America/Sao_Paulo');
