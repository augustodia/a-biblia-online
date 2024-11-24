<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <style>
        .search-results {
            margin: 20px;
        }
        .book-chapter-header {
            font-weight: bold;
            margin-top: 20px;
        }
        .verse-item {
            margin: 10px 0;
        }
        .highlight {
            background-color: yellow;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .pagination button {
            margin: 0 5px;
            padding: 10px 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="search-results">
        <?php
        foreach ($searchResults as $book => $chapters) {
            echo "<div class='book-chapter-header'>{$book}</div>";
            foreach ($chapters as $chapter => $verses) {
                echo "<div class='book-chapter-header'>Chapter {$chapter}</div>";
                foreach ($verses as $verse) {
                    $highlightedText = str_replace($query, "<span class='highlight'>{$query}</span>", $verse['texto']);
                    echo "<div class='verse-item'><a href='" . BASE_URL . "{$version}/{$verse['livro_sigla']}/{$chapter}/{$verse['versiculo']}'>{$verse['versiculo']} - {$highlightedText}</a></div>";
                }
            }
        }
        ?>
    </div>
    <div class="pagination">
        <?php if ($currentPage > 1): ?>
            <button onclick="window.location.href='<?php echo BASE_URL . "search?query={$query}&version={$version}&page=" . ($currentPage - 1); ?>'">Previous</button>
        <?php endif; ?>
        <?php if ($currentPage < $totalPages): ?>
            <button onclick="window.location.href='<?php echo BASE_URL . "search?query={$query}&version={$version}&page=" . ($currentPage + 1); ?>'">Next</button>
        <?php endif; ?>
    </div>
</body>
</html>
