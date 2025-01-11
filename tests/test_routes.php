<?php

// URL de teste
$testUrl = 'ARC+ARCC/gn/1/3';

// Regex para comparação
$pattern = '/^([a-zA-Z]+\+[a-zA-Z]+)\/([0-9]?[a-zA-Z]+)\/([0-9]+)\/([0-9]+)(?:-([0-9]+))?$/';

// Teste da regex
if (preg_match($pattern, $testUrl, $matches)) {
    echo "URL válida!\n";
    echo "Matches encontrados:\n";
    var_dump($matches);
} else {
    echo "URL inválida!\n";
}

// Teste com outras URLs
$testUrls = [
    'ARC+ARCC/gn/1/3',
    'ARC+ARCC/gn/1/3-5',
    'ARA+NVI/ex/2/10',
    'ARA+NVI/ex/2/10-15',
    'ARC/gn/1/3', // Não deve dar match
];

echo "\nTestando múltiplas URLs:\n";
foreach ($testUrls as $url) {
    echo "\nTestando URL: " . $url . "\n";
    if (preg_match($pattern, $url, $matches)) {
        echo "✓ Match encontrado:\n";
        var_dump($matches);
    } else {
        echo "✗ Sem match\n";
    }
} 