<?php
header('Cache-Control: no cache'); //no cache
session_cache_limiter('private_no_expire'); // works
session_cache_limiter('public'); // works too
session_start();

$temp_file = '/path/to/temp/file.txt';
if (file_exists($temp_file)) {
  unlink($temp_file);
}

require_once 'config.php';
require_once 'core/Core.php';
require_once 'core/vendor/autoload.php';
require_once 'controllers/Routes.php';

$core = new Core($routes);
$core->start();
