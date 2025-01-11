<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/VersionModel.php';
require_once __DIR__ . '/../models/BooksModel.php';
require_once __DIR__ . '/../models/VersesModel.php';

class SitemapDirectoryException extends Exception {}
class SitemapFileWriteException extends Exception {}

class SitemapGenerator {
    private $db;
    private $baseUrl;
    private $outputDir;
    private $urls = [];
    private $processedUrls = 0;
    private $totalUrls = 0;
    private const CHUNK_SIZE = 1000; // Processamento em chunks
    private $xmlWriter;

    public function __construct(String $baseUrl) {
        global $db;
        $this->db = $db;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->outputDir = __DIR__ . '/../public/sitemaps';
        $this->xmlWriter = new XMLWriter();
        $this->xmlWriter->openMemory();
        $this->xmlWriter->setIndent(true);
        
        if (!file_exists($this->outputDir)) {
            if (!mkdir($this->outputDir, 0777, true)) {
                throw new SitemapDirectoryException("Não foi possível criar o diretório: " . $this->outputDir);
            }
        }
    }

    private function log($message) {
        echo $message . "\n";
        flush();
    }

    private function showProgress($current, $total, $prefix) {
        static $lastPercent = -1;

        $current = min($current, $total);
        $percent = ($total > 0) ? round(($current / $total) * 100) : 0;

        if ($percent !== $lastPercent) {
            $barSize = 50;
            $filled = max(0, min($barSize, (int)($barSize * $percent / 100)));
            $empty = max(0, $barSize - $filled);
            $bar = str_repeat("█", $filled) . str_repeat("░", $empty);

            echo sprintf("\033[2K\r%s [%s] %d%% (%d/%d)",
                $prefix,
                $bar,
                $percent,
                $current,
                $total
            );
            flush();

            $lastPercent = $percent;
        }
    }

    public function generate() {
        if (ob_get_level()) {
            ob_end_clean();
        }

        $this->log("Iniciando geração dos sitemaps...\n");

        $versionsModel = new VersionModel();
        $versions = $versionsModel->all();
        $sitemapFiles = [];

        // Pré-calcula o total de URLs para cada versão
        foreach ($versions as $version) {
            $this->log("\nCalculando total de URLs para " . $version['sigla'] . "...");
            
            // Conta livros
            $booksModel = new BooksModel();
            $books = $booksModel->all();
            $totalBooks = count($books);
            
            // Conta capítulos
            $chaptersCount = $this->db->prepare('
                SELECT COUNT(DISTINCT v.capitulo) as total
                FROM versiculos v
                INNER JOIN versoes ver ON v.versao_id = ver.id
                WHERE ver.sigla = ?
            ');
            $chaptersCount->execute([$version['sigla']]);
            $totalChapters = $chaptersCount->fetch(PDO::FETCH_COLUMN);
            
            // Conta versículos
            $versesCount = $this->db->prepare('
                SELECT COUNT(DISTINCT CONCAT(l.sigla, v.capitulo, v.versiculo)) as total
                FROM versiculos v
                INNER JOIN versoes ver ON v.versao_id = ver.id
                INNER JOIN livros l ON v.livro_id = l.id
                WHERE ver.sigla = ?
            ');
            $versesCount->execute([$version['sigla']]);
            $totalVerses = $versesCount->fetch(PDO::FETCH_COLUMN);
            
            // 1 URL para a versão + URLs de livros + URLs de capítulos + URLs de versículos
            $totalUrlsForVersion = 1 + $totalBooks + $totalChapters + $totalVerses;
            $this->log(sprintf("Total para %s: %d URLs", $version['sigla'], $totalUrlsForVersion));
        }

        $this->log("\nIniciando processamento...\n");

        foreach ($versions as $version) {
            $this->urls = [];
            $this->log("\nProcessando versão " . $version['sigla'] . "...");
            
            // Adiciona URL da versão
            $this->addUrl($this->baseUrl . '/' . $version['sigla'], '0.9', 'weekly');
            $this->showProgress(1, $totalUrlsForVersion, "URLs " . $version['sigla']);
            
            // Adiciona livros
            $this->addBooksForVersion($version, $totalUrlsForVersion);
            
            // Adiciona capítulos
            $this->addChaptersForVersion($version, $totalUrlsForVersion);
            
            // Adiciona versículos
            $this->addVersesForVersion($version, $totalUrlsForVersion);

            // Gera arquivo
            $sitemapFile = $this->writeSitemapFile($version['sigla']);
            $sitemapFiles[] = [
                'loc' => str_replace(__DIR__ . '/../public', $this->baseUrl, $sitemapFile),
                'lastmod' => date('Y-m-d')
            ];
            
            echo "\n"; // Nova linha após completar a versão
        }

        $this->createSitemapIndex($sitemapFiles);
    }

    private function addBooksForVersion($version, $totalUrls) {
        $booksModel = new BooksModel();
        $books = $booksModel->all();
        
        foreach ($books as $book) {
            $this->addUrl(
                $this->baseUrl . '/' . $version['sigla'] . '/' . $book['sigla'],
                '0.8',
                'weekly'
            );
            $this->processedUrls++;
            $this->showProgress($this->processedUrls, $totalUrls, "URLs " . $version['sigla']);
        }
    }

    private function addChaptersForVersion($version, $totalUrls) {
        $booksModel = new BooksModel();
        $books = $booksModel->all();

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
                $this->processedUrls++;
                $this->showProgress($this->processedUrls, $totalUrls, "URLs " . $version['sigla']);
            }
        }
    }

    private function addVersesForVersion($version, $totalUrls): void {
        $offset = 0;
        $versesStmt = $this->db->prepare('
            SELECT DISTINCT l.sigla as livro_sigla, v.capitulo, v.versiculo
            FROM versiculos v
            INNER JOIN versoes ver ON v.versao_id = ver.id
            INNER JOIN livros l ON v.livro_id = l.id
            WHERE ver.sigla = ?
            ORDER BY l.sigla, v.capitulo, v.versiculo
            LIMIT ? OFFSET ?
        ');
        
        do {
            $versesStmt->execute([$version['sigla'], self::CHUNK_SIZE, $offset]);
            $verses = $versesStmt->fetchAll(PDO::FETCH_ASSOC);
            $count = count($verses);
            
            foreach ($verses as $verse) {
                $this->addUrl(
                    $this->baseUrl . '/' . $version['sigla'] . '/' . $verse['livro_sigla'] . '/' .
                    $verse['capitulo'] . '/' . $verse['versiculo'],
                    '0.6',
                    'monthly'
                );
                $this->processedUrls++;
                $this->showProgress($this->processedUrls, $totalUrls, "URLs " . $version['sigla']);
            }
            
            $offset += $count;
        } while ($count === self::CHUNK_SIZE);
    }

    private function addUrl($loc, $priority, $changefreq): void {
        $this->xmlWriter->startElement('url');
        $this->xmlWriter->writeElement('loc', $loc);
        $this->xmlWriter->writeElement('lastmod', date('Y-m-d'));
        $this->xmlWriter->writeElement('changefreq', $changefreq);
        $this->xmlWriter->writeElement('priority', $priority);
        $this->xmlWriter->endElement();
        $this->urls[] = true; // Apenas para contagem
    }

    private function writeSitemapFile($suffix): string {
        $outputFile = $this->outputDir . '/sitemap-' . strtolower($suffix) . '.xml';
        
        if (!file_exists(dirname($outputFile))) {
            if (!mkdir(dirname($outputFile), 0777, true)) {
                throw new SitemapDirectoryException("Não foi possível criar o diretório para o arquivo: " . $outputFile);
            }
        }

        $handle = fopen($outputFile, 'wb');
        if ($handle === false) {
            throw new SitemapFileWriteException("Não foi possível abrir o arquivo para escrita: " . $outputFile);
        }

        // Escreve o cabeçalho
        fwrite($handle, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
        fwrite($handle, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL);
        
        // Escreve o conteúdo
        fwrite($handle, $this->xmlWriter->outputMemory(true));
        
        // Escreve o fechamento
        fwrite($handle, '</urlset>');
        fclose($handle);
        
        if (!file_exists($outputFile)) {
            throw new SitemapFileWriteException("O arquivo não foi criado: " . $outputFile);
        }
        
        $this->log(sprintf(
            "\nSitemap para %s gerado com sucesso (%d URLs, %s)",
            $suffix,
            count($this->urls),
            $this->formatBytes(filesize($outputFile))
        ));

        $this->processedUrls = 0;
        $this->xmlWriter->flush();
        $this->urls = [];

        return $outputFile;
    }

    private function createSitemapIndex($sitemapFiles): void {
        $indexFile = __DIR__ . '/../public/sitemap.xml';
        
        if (!file_exists(dirname($indexFile))) {
            if (!mkdir(dirname($indexFile), 0777, true)) {
                throw new SitemapDirectoryException("Não foi possível criar o diretório para o sitemap index: " . $indexFile);
            }
        }

        $handle = fopen($indexFile, 'wb');
        if ($handle === false) {
            throw new SitemapFileWriteException("Não foi possível abrir o arquivo index para escrita");
        }

        fwrite($handle, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
        fwrite($handle, '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL);

        foreach ($sitemapFiles as $sitemap) {
            fwrite($handle, "  <sitemap>\n");
            fwrite($handle, "    <loc>{$sitemap['loc']}</loc>\n");
            fwrite($handle, "    <lastmod>{$sitemap['lastmod']}</lastmod>\n");
            fwrite($handle, "  </sitemap>\n");
        }

        fwrite($handle, '</sitemapindex>');
        fclose($handle);
        
        $this->log(sprintf(
            "\nSitemap index gerado com sucesso (%d sitemaps, %s)",
            count($sitemapFiles),
            $this->formatBytes(filesize($indexFile))
        ));
    }

    private function formatBytes($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $bytes;
        $unit = 0;
        
        while ($size > 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }
        
        return round($size, 2) . ' ' . $units[$unit];
    }
}
