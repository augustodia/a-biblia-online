<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/VersionModel.php';
require_once __DIR__ . '/../models/BooksModel.php';
require_once __DIR__ . '/../models/VersesModel.php';

class SitemapGenerator {
    private $db;
    private $baseUrl;
    private $outputFile;
    private $urls = [];

    public function __construct(String $baseUrl) {
        global $db;
        $this->db = $db;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->outputFile = __DIR__ . '/../public/sitemap.xml';
    }

    private function log($message) {
        echo $message . "\n";
        flush();
    }

    private function showProgress($current, $total, $prefix) {
        static $lastPercent = -1;
        static $lastPrefix = '';
        
        // Garante que current não seja maior que total
        $current = min($current, $total);
        $percent = ($total > 0) ? round(($current / $total) * 100) : 0;
        
        // Reseta o lastPercent se mudou o prefixo
        if ($prefix !== $lastPrefix) {
            $lastPercent = -1;
            $lastPrefix = $prefix;
        }
        
        // Só atualiza se a porcentagem mudou
        if ($percent !== $lastPercent) {
            $barSize = 50;
            $filled = max(0, min($barSize, (int)($barSize * $percent / 100)));
            $empty = max(0, $barSize - $filled);
            
            $bar = str_repeat("█", $filled) . str_repeat("░", $empty);
            
            // Move o cursor para o início da linha e limpa
            echo sprintf("\033[2K\r%s [%s] %d%% (%d/%d)",
                $prefix,
                $bar,
                $percent,
                $current,
                $total
            );
            
            if ($current >= $total) {
                echo "\n";
            }
            
            $lastPercent = $percent;
            flush();
        }
    }

    public function generate() {
        // Desativa o buffer de saída do PHP para garantir saída em tempo real
        if (ob_get_level()) {
          ob_end_clean();
        }
        
        $this->log("Iniciando geração do sitemap...\n");
        
        $this->addHomePage();
        $this->log("✓ Página inicial adicionada");
        
        $this->addVersionPages();
        $this->log("✓ Páginas de versões adicionadas");
        
        $this->addBookPages();
        $this->log("✓ Páginas de livros adicionadas");
        
        $this->addChapterPages();
        $this->log("✓ Páginas de capítulos adicionadas");
        
        $this->addVersePages();
        $this->log("✓ Páginas de versículos adicionadas");
        
        $this->writeSitemapFile();
    }

    private function addHomePage() {
        $this->addUrl($this->baseUrl, '1.0', 'daily');
    }

    private function addVersionPages() {
        $versionsModel = new VersionModel();
        $versions = $versionsModel->all();
        $total = count($versions);
        $count = 0;
        
        $this->log("\nProcessando versões...");
        foreach ($versions as $version) {
            $this->addUrl(
                $this->baseUrl . '/' . $version['sigla'],
                '0.9',
                'weekly'
            );
            $count++;
            $this->showProgress($count, $total, "Versões");
        }
        $this->log("└─ Adicionadas $count versões");
    }

    private function addBookPages() {
        $versionsModel = new VersionModel();
        $versions = $versionsModel->all();
        $booksModel = new BooksModel();
        $books = $booksModel->all();
        $total = count($books); // Corrigido: cada versão tem o mesmo número de livros
        $count = 0;

        $this->log("\nProcessando livros...");
        foreach ($versions as $version) {
            $this->log("  ├─ Versão: " . $version['sigla']);
            foreach ($books as $book) {
                $this->addUrl(
                    $this->baseUrl . '/' . $version['sigla'] . '/' . $book['sigla'],
                    '0.8',
                    'weekly'
                );
                $count++;
                $this->showProgress($count % $total ?: $total, $total, "Livros");
            }
        }
        $totalUrls = count($versions) * count($books);
        $this->log("└─ Adicionados $totalUrls livros no total");
    }

    private function addChapterPages() {
        $versionsModel = new VersionModel();
        $versions = $versionsModel->all();
        $booksModel = new BooksModel();
        $books = $booksModel->all();
        
        $this->log("\nContando capítulos...");
        $totalChaptersByVersion = [];
        $grandTotal = 0;
        
        // Primeiro, vamos contar os capítulos reais
        foreach ($versions as $version) {
            $this->log("  ├─ Contando capítulos da versão: " . $version['sigla']);
            
            // Conta os capítulos reais
            $chapters = $this->db->prepare('
                SELECT COUNT(*) as total
                FROM (
                    SELECT DISTINCT v.capitulo
                    FROM versiculos v
                    INNER JOIN versoes ver ON v.versao_id = ver.id
                    INNER JOIN livros l ON v.livro_id = l.id
                    WHERE ver.sigla = ?
                ) as temp
            ');
            $chapters->execute([$version['sigla']]);
            $totalChaptersByVersion[$version['sigla']] = $chapters->fetch(PDO::FETCH_COLUMN);
            $grandTotal += $totalChaptersByVersion[$version['sigla']];
        }

        // Agora adicionar as URLs
        $totalProcessed = 0;
        $this->log("\nProcessando capítulos...");
        foreach ($versions as $version) {
            $count = 0;
            $total = $totalChaptersByVersion[$version['sigla']];
            $this->log("  ├─ Processando capítulos da versão: " . $version['sigla']);
            
            foreach ($books as $book) {
                $chapters = $this->db->prepare('
                    SELECT DISTINCT v.capitulo
                    FROM versiculos v
                    INNER JOIN versoes ver ON v.versao_id = ver.id
                    INNER JOIN livros l ON v.livro_id = l.id
                    WHERE ver.sigla = ? AND l.sigla = ?
                    ORDER BY v.capitulo
                ');
                $chapters->execute([$version['sigla'], $book['sigla']]);
                
                while ($chapter = $chapters->fetch(PDO::FETCH_COLUMN)) {
                    $this->addUrl(
                        $this->baseUrl . '/' . $version['sigla'] . '/' . $book['sigla'] . '/' . $chapter,
                        '0.7',
                        'weekly'
                    );
                    $count++;
                    $totalProcessed++;
                    $this->showProgress($count, $total, "Capítulos (" . $version['sigla'] . ")");
                }
            }
            if ($count > 0) {
                echo "\n";
            }
        }
        $this->log("└─ Adicionados $grandTotal capítulos no total");
    }

    private function addVersePages() {
        $versionsModel = new VersionModel();
        $versions = $versionsModel->all();
        
        $this->log("\nContando versículos...");
        $totalVersesByVersion = [];
        $grandTotal = 0;
        
        foreach ($versions as $version) {
            $this->log("  ├─ Contando versículos da versão: " . $version['sigla']);
            
            // Conta os versículos considerando a combinação única de livro+capítulo+versículo
            $countQuery = $this->db->prepare('
                SELECT COUNT(*) as total
                FROM (
                    SELECT DISTINCT l.sigla, v.capitulo, v.versiculo
                    FROM versiculos v
                    INNER JOIN versoes ver ON v.versao_id = ver.id
                    INNER JOIN livros l ON v.livro_id = l.id
                    WHERE ver.sigla = ?
                ) as temp
            ');
            $countQuery->execute([$version['sigla']]);
            $totalVersesByVersion[$version['sigla']] = $countQuery->fetch(PDO::FETCH_COLUMN);
            $grandTotal += $totalVersesByVersion[$version['sigla']];
        }

        // Agora adicionar as URLs
        $this->log("\nProcessando versículos...");
        foreach ($versions as $version) {
            $count = 0;
            $total = $totalVersesByVersion[$version['sigla']];
            $this->log("  ├─ Processando versículos da versão: " . $version['sigla']);
            
            // Processa os versículos em lotes para melhor performance
            $verses = $this->db->prepare('
                SELECT DISTINCT l.sigla as livro_sigla, v.capitulo, v.versiculo
                FROM versiculos v
                INNER JOIN versoes ver ON v.versao_id = ver.id
                INNER JOIN livros l ON v.livro_id = l.id
                WHERE ver.sigla = ?
                ORDER BY l.sigla, v.capitulo, v.versiculo
            ');
            $verses->execute([$version['sigla']]);
            
            while ($verse = $verses->fetch(PDO::FETCH_ASSOC)) {
                $this->addUrl(
                    $this->baseUrl . '/' . $version['sigla'] . '/' . $verse['livro_sigla'] . '/' .
                    $verse['capitulo'] . '/' . $verse['versiculo'],
                    '0.6',
                    'monthly'
                );
                $count++;
                $this->showProgress($count, $total, "Versículos (" . $version['sigla'] . ")");
            }
            
            if ($count > 0) {
                echo "\n";
                if ($count !== $total) {
                    $this->log("  ├─ AVISO: Contagem não bateu para " . $version['sigla'] . 
                              " (Processado: $count, Esperado: $total)");
                }
            }
        }
        $this->log("└─ Adicionados $grandTotal versículos no total");
    }

    private function addUrl($loc, $priority, $changefreq) {
        $this->urls[] = [
            'loc' => $loc,
            'priority' => $priority,
            'changefreq' => $changefreq,
            'lastmod' => date('Y-m-d')
        ];
    }

    private function writeSitemapFile() {
        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->startDocument('1.0', 'UTF-8');

        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        $totalUrls = count($this->urls);
        $processedUrls = 0;

        $this->log("\nEscrevendo arquivo do sitemap...");
        foreach ($this->urls as $url) {
            $xml->startElement('url');
            $xml->writeElement('loc', $url['loc']);
            $xml->writeElement('lastmod', $url['lastmod']);
            $xml->writeElement('changefreq', $url['changefreq']);
            $xml->writeElement('priority', $url['priority']);
            $xml->endElement();

            $processedUrls++;
            if ($processedUrls % 1000 === 0) {
                $this->showProgress($processedUrls, $totalUrls, "Escrevendo sitemap");
            }
        }

        $xml->endElement();

        $directory = dirname($this->outputFile);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($this->outputFile, $xml->outputMemory());
        $this->log("\nSitemap gerado com sucesso em: " . $this->outputFile);
        $this->log("Total de URLs: " . number_format(count($this->urls), 0, ',', '.'));
        
        $fileSize = filesize($this->outputFile);
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $fileSize;
        $unit = 0;
        while ($size > 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }
        $this->log("Tamanho do arquivo: " . round($size, 2) . " " . $units[$unit]);
    }
}
