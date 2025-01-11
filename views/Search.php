<?php
define('SEARCH_PATH', 'search/');
define('PAGE_PARAM', '&page=');

// Log para debug
error_log("Dados recebidos na view Search: " . print_r($data, true));

// Organizar resultados por livro e capítulo
$organizedResults = [];
if (!empty($data['results'])) {
    foreach ($data['results'] as $verse) {
        if (isset($verse['book_name'])) {
            $bookKey = $verse['book_name'];
            $chapterKey = $verse['capitulo'];
            $organizedResults[$bookKey][$chapterKey][] = $verse;
        } else {
            error_log("Versículo sem book_name: " . print_r($verse, true));
        }
    }
}
?>

<div class="search-container">
    <div class="search-header">
        <h2>Resultados da Busca</h2>
        <p class="search-info">
            <?php if (!empty($data['results'])): ?>
                Encontrados <?php echo $data['pagination']['total']; ?> resultados para "<?php echo htmlspecialchars($data['searchTerm']); ?>"
                (Página <?php echo $data['pagination']['currentPage']; ?> de <?php echo $data['pagination']['totalPages']; ?>)
            <?php endif; ?>
        </p>
    </div>

    <?php if (!empty($organizedResults)): ?>
        <div class="search-results">
            <?php foreach ($organizedResults as $bookName => $chapters): ?>
                <div class="book-section">
                    <h3 class="book-title"><?php echo $bookName; ?></h3>
                    <?php foreach ($chapters as $chapter => $verses): ?>
                        <div class="chapter-section">
                            <h4 class="chapter-title">Capítulo <?php echo $chapter; ?></h4>
                            <ul class="verses-list">
                                <?php foreach ($verses as $verse): ?>
                                    <li class="verse-item">
                                        <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $verse['book'] . '/' . $verse['capitulo'] . '/' . $verse['versiculo']; ?>">
                                            <span class="verse-number"><?php echo $verse['versiculo']; ?></span>
                                            <span class="verse-text"><?php echo $verse['texto']; ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($data['pagination']['totalPages'] > 1): ?>
            <div class="pagination">
                <?php if ($data['pagination']['currentPage'] > 1): ?>
                    <a href="<?php echo BASE_URL . SEARCH_PATH . $data['selectedVersion'] . '?q=' . urlencode($data['searchTerm']) . PAGE_PARAM . ($data['pagination']['currentPage'] - 1); ?>" class="page-link">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </a>
                <?php endif; ?>

                <div class="page-numbers">
                    <?php
                    $start = max(1, $data['pagination']['currentPage'] - 2);
                    $end = min($data['pagination']['totalPages'], $data['pagination']['currentPage'] + 2);
                    
                    if ($start > 1): ?>
                        <a href="<?php echo BASE_URL . SEARCH_PATH . $data['selectedVersion'] . '?q=' . urlencode($data['searchTerm']) . PAGE_PARAM . '1'; ?>" class="page-link">1</a>
                        <?php if ($start > 2): ?>
                            <span class="page-ellipsis">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start; $i <= $end; $i++): ?>
                        <a href="<?php echo BASE_URL . SEARCH_PATH . $data['selectedVersion'] . '?q=' . urlencode($data['searchTerm']) . PAGE_PARAM . $i; ?>"
                           class="page-link <?php echo $i === $data['pagination']['currentPage'] ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($end < $data['pagination']['totalPages']): ?>
                        <?php if ($end < $data['pagination']['totalPages'] - 1): ?>
                            <span class="page-ellipsis">...</span>
                        <?php endif; ?>
                        <a href="<?php echo BASE_URL . SEARCH_PATH . $data['selectedVersion'] . '?q=' . urlencode($data['searchTerm']) . PAGE_PARAM . $data['pagination']['totalPages']; ?>" class="page-link">
                            <?php echo $data['pagination']['totalPages']; ?>
                        </a>
                    <?php endif; ?>
                </div>

                <?php if ($data['pagination']['currentPage'] < $data['pagination']['totalPages']): ?>
                    <a href="<?php echo BASE_URL . SEARCH_PATH . $data['selectedVersion'] . '?q=' . urlencode($data['searchTerm']) . PAGE_PARAM . ($data['pagination']['currentPage'] + 1); ?>" class="page-link">
                        Próxima <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="no-results">
            <p>Nenhum resultado encontrado para "<?php echo htmlspecialchars($data['searchTerm']); ?>"</p>
            <small>Tente usar palavras diferentes ou verifique a ortografia</small>
        </div>
    <?php endif; ?>
</div>

<style>
    .search-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }

    .search-header {
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }

    .search-header h2 {
        color: #2e4a7b;
        margin-bottom: 10px;
    }

    .search-info {
        color: #666;
        font-size: 0.9em;
    }

    .book-section {
        margin-bottom: 30px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .book-title {
        background: #2e4a7b;
        color: white;
        padding: 15px 20px;
        margin: 0;
        font-size: 1.2em;
    }

    .chapter-section {
        padding: 20px;
        border-bottom: 1px solid #eee;
    }

    .chapter-title {
        color: #2e4a7b;
        margin: 0 0 15px 0;
        font-size: 1.1em;
    }

    .verses-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .verse-item {
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .verse-item:last-child {
        border-bottom: none;
    }

    .verse-item a {
        display: flex;
        align-items: center;
        color: #333;
        text-decoration: none;
        transition: background-color 0.2s;
        padding: 10px;
        border-radius: 4px;
    }

    .verse-item a:hover {
        background-color: #f5f7fa;
    }

    .verse-number {
        color: #2e4a7b;
        font-weight: bold;
        margin-right: 10px;
        display: inline-block;
        min-width: 25px;
    }

    .verse-text {
        display: inline-block;
    }

    .no-results {
        text-align: center;
        padding: 40px 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .no-results p {
        color: #666;
        margin-bottom: 10px;
        font-size: 1.1em;
    }

    .no-results small {
        color: #999;
    }

    @media (max-width: 768px) {
        .search-container {
            padding: 10px;
        }

        .book-section {
            margin-bottom: 20px;
        }

        .chapter-section {
            padding: 15px;
        }
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 30px;
        gap: 10px;
    }

    .page-numbers {
        display: flex;
        gap: 5px;
    }

    .page-link {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        color: #2e4a7b;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .page-link:hover {
        background-color: #f5f7fa;
        border-color: #2e4a7b;
    }

    .page-link.active {
        background-color: #2e4a7b;
        color: white;
        border-color: #2e4a7b;
    }

    .page-ellipsis {
        padding: 8px 12px;
        color: #666;
    }
</style>
