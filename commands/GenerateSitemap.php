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
    private $totalUrlsByVersion = [];
    private $currentFileIndex = 1;
    private const CHUNK_SIZE = 1000;
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

            // Força limpar a linha atual antes de escrever
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

        // Limpa diretório de sitemaps antigos
        if (file_exists($this->outputDir)) {
            array_map('unlink', glob($this->outputDir . '/*'));
        }

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
            
            // Conta versículos e calcula combinações
            $versesCount = $this->db->prepare('
                SELECT l.sigla, v.capitulo, COUNT(*) as total_verses
                FROM versiculos v
                INNER JOIN versoes ver ON v.versao_id = ver.id
                INNER JOIN livros l ON v.livro_id = l.id
                WHERE ver.sigla = ?
                GROUP BY l.sigla, v.capitulo
            ');
            $versesCount->execute([$version['sigla']]);
            $totalVerses = 0;
            $totalCombinations = 0;

            while ($chapter = $versesCount->fetch()) {
                $totalVerses += $chapter['total_verses'];
                // Para cada versículo inicial, podemos combinar com todos os versículos seguintes
                // Fórmula: soma de (n-1) + (n-2) + ... + 1, onde n é o número de versículos
                $n = $chapter['total_verses'];
                $totalCombinations += ($n * ($n - 1)) / 2;
            }
            
            // Armazena o total para esta versão
            $this->totalUrlsByVersion[$version['sigla']] = 1 + $totalBooks + $totalChapters + $totalVerses + $totalCombinations;
            
            $this->log(sprintf("Total para %s: %d URLs (Livros: %d, Capítulos: %d, Versículos: %d, Combinações: %d)",
                $version['sigla'],
                $this->totalUrlsByVersion[$version['sigla']],
                $totalBooks,
                $totalChapters,
                $totalVerses,
                $totalCombinations
            ));
        }

        $this->log("\nIniciando processamento...\n");

        foreach ($versions as $version) {
            $this->urls = [];
            $this->processedUrls = 0;
            $this->currentFileIndex = 1;
            $versionSitemapFiles = []; // Array para armazenar os sitemaps desta versão
            
            $this->log("\nProcessando versão " . $version['sigla'] . "...");
            
            // Adiciona URL da versão
            $this->addUrl($this->baseUrl . '/' . $version['sigla'], '0.9', 'weekly');
            $this->processedUrls++;
            $this->showProgress($this->processedUrls, $this->totalUrlsByVersion[$version['sigla']], "URLs " . $version['sigla']);
            
            // Adiciona livros
            $this->addBooksForVersion($version, $this->totalUrlsByVersion[$version['sigla']]);
            
            // Adiciona capítulos
            $this->addChaptersForVersion($version, $this->totalUrlsByVersion[$version['sigla']]);
            
            // Adiciona versículos
            $this->addVersesForVersion($version, $this->totalUrlsByVersion[$version['sigla']], $versionSitemapFiles);

            // Se ainda houver URLs não salvas, salva no último arquivo
            if (count($this->urls) > 0) {
                $sitemapFile = $this->writeSitemapFile($version['sigla'] . '-' . str_pad($this->currentFileIndex++, 3, '0', STR_PAD_LEFT));
                $versionSitemapFiles[] = [
                    'loc' => str_replace(__DIR__ . '/../public', $this->baseUrl, $sitemapFile),
                    'lastmod' => date('Y-m-d')
                ];
            }

            // Cria o índice para esta versão
            if (!empty($versionSitemapFiles)) {
                $this->createVersionSitemapIndex($version['sigla'], $versionSitemapFiles);
                // Adiciona o índice da versão ao índice principal
                $sitemapFiles[] = [
                    'loc' => $this->baseUrl . '/sitemaps/sitemap-' . strtolower($version['sigla']) . '.xml',
                    'lastmod' => date('Y-m-d')
                ];
            }
            
            echo "\n"; // Nova linha após completar a versão
        }

        $this->createSitemapIndex($sitemapFiles);
    }

    private function addBooksForVersion($version, $totalUrls) {
        $booksModel = new BooksModel();
        $books = $booksModel->all();
        
        foreach ($books as $book) {
            $this->addUrl(
                $this->baseUrl . '/' . $version['sigla'] . '/' . rawurlencode($book['sigla']),
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
                    $this->baseUrl . '/' . $version['sigla'] . '/' . rawurlencode($book['sigla']) . '/' . $chapter,
                    '0.7',
                    'weekly'
                );
                $this->processedUrls++;
                $this->showProgress($this->processedUrls, $totalUrls, "URLs " . $version['sigla']);
            }
        }
    }

    private function addVersesForVersion($version, $totalUrls, &$versionSitemapFiles): void {
        $offset = 0;
        $currentFileUrls = 0;

        // Query otimizada: Pega os versículos e o total do capítulo em uma única consulta
        $versesStmt = $this->db->prepare('
            WITH VerseInfo AS (
                SELECT 
                    l.sigla as livro_sigla,
                    v.capitulo,
                    v.versiculo,
                    COUNT(*) OVER (PARTITION BY l.sigla, v.capitulo) as total_versiculos_capitulo,
                    ROW_NUMBER() OVER (PARTITION BY l.sigla, v.capitulo ORDER BY v.versiculo) as verse_position
                FROM versiculos v
                INNER JOIN versoes ver ON v.versao_id = ver.id
                INNER JOIN livros l ON v.livro_id = l.id
                WHERE ver.sigla = ?
            )
            SELECT 
                livro_sigla,
                capitulo,
                versiculo,
                total_versiculos_capitulo
            FROM VerseInfo
            ORDER BY livro_sigla, capitulo, versiculo
            LIMIT ? OFFSET ?
        ');
        
        do {
            $versesStmt->execute([$version['sigla'], self::CHUNK_SIZE, $offset]);
            $verses = $versesStmt->fetchAll(PDO::FETCH_ASSOC);
            $count = count($verses);
            
            foreach ($verses as $verse) {
                // URL do versículo individual
                $this->addUrl(
                    $this->baseUrl . '/' . $version['sigla'] . '/' . rawurlencode($verse['livro_sigla']) . '/' .
                    $verse['capitulo'] . '/' . $verse['versiculo'],
                    '0.6',
                    'monthly'
                );
                $this->processedUrls++;
                $currentFileUrls++;

                // Gera combinações de versículos apenas até o total real do capítulo
                $combinationsGenerated = $this->addVerseRangeCombinations(
                    $version,
                    $verse['livro_sigla'],
                    $verse['capitulo'],
                    (int)$verse['versiculo'],
                    (int)$verse['total_versiculos_capitulo']
                );
                
                $this->processedUrls += $combinationsGenerated;
                $currentFileUrls += $combinationsGenerated;

                // Atualiza o progresso após cada versículo e suas combinações
                $this->showProgress($this->processedUrls, $totalUrls, "URLs " . $version['sigla']);

                // Se atingiu o limite, salva o arquivo atual e limpa o buffer
                if ($currentFileUrls >= 45000) {
                    $sitemapFile = $this->writeSitemapFile($version['sigla'] . '-' . str_pad($this->currentFileIndex++, 3, '0', STR_PAD_LEFT));
                    $versionSitemapFiles[] = [
                        'loc' => str_replace(__DIR__ . '/../public', $this->baseUrl, $sitemapFile),
                        'lastmod' => date('Y-m-d')
                    ];
                    $currentFileUrls = 0;
                }
            }
            
            $offset += $count;
        } while ($count === self::CHUNK_SIZE);

        // Salva as URLs restantes se houver
        if ($currentFileUrls > 0) {
            $sitemapFile = $this->writeSitemapFile($version['sigla'] . '-' . str_pad($this->currentFileIndex++, 3, '0', STR_PAD_LEFT));
            $versionSitemapFiles[] = [
                'loc' => str_replace(__DIR__ . '/../public', $this->baseUrl, $sitemapFile),
                'lastmod' => date('Y-m-d')
            ];
        }
    }

    private function addVerseRangeCombinations($version, $book, $chapter, $startVerse, $totalVerses): int {
        $combinationsCount = 0;
        // Gera apenas combinações até o último versículo do capítulo
        for ($endVerse = $startVerse + 1; $endVerse <= $totalVerses; $endVerse++) {
            $this->addUrl(
                $this->baseUrl . '/' . $version['sigla'] . '/' . rawurlencode($book) . '/' .
                $chapter . '/' . $startVerse . '-' . $endVerse,
                '0.5',
                'monthly'
            );
            $combinationsCount++;
        }
        return $combinationsCount;
    }

    private function addUrl($loc, $priority, $changefreq): void {
        $this->xmlWriter->startElement('url');
        $this->xmlWriter->writeElement('loc', $loc);
        $this->xmlWriter->writeElement('lastmod', date('Y-m-d'));
        $this->xmlWriter->writeElement('changefreq', $changefreq);
        $this->xmlWriter->writeElement('priority', $priority);
        $this->xmlWriter->endElement();
        $this->urls[] = true;
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

        // Move o cursor uma linha acima e limpa
        echo "\033[1A\033[2K";
        
        // Salva a mensagem de log
        echo sprintf(
            "Sitemap para %s gerado com sucesso (%d URLs, %s)\n",
            $suffix,
            count($this->urls),
            $this->formatBytes(filesize($outputFile))
        );

        // Limpa apenas as URLs e o XMLWriter, mantém o contador
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

    private function createVersionSitemapIndex($version, $sitemapFiles): void {
        $indexFile = $this->outputDir . '/sitemap-' . strtolower($version) . '.xml';
        
        $handle = fopen($indexFile, 'wb');
        if ($handle === false) {
            throw new SitemapFileWriteException("Não foi possível abrir o arquivo index da versão para escrita");
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
            "\nSitemap index para %s gerado com sucesso (%d sitemaps, %s)",
            $version,
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
