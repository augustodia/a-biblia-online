<?php
require_once __DIR__ . '/commands/GenerateSitemap.php';

echo "Iniciando geração do sitemap...\n\n";
$startTime = microtime(true);

$generator = new SitemapGenerator('https://abibliaonline.com/');
$generator->generate();

$endTime = microtime(true);
$executionTime = round($endTime - $startTime, 2);
echo "\nTempo de execução: {$executionTime} segundos\n";
