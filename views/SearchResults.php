<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        .search-query {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .verse-item {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            transition: background-color 0.3s, transform 0.2s;
        }

        .verse-item:hover {
            background-color: #f1f1f1;
            transform: translateX(5px);
        }

        .verse-item a {
            text-decoration: none;
            color: #333;
            font-size: 16px;
            display: flex;
            align-items: center;
        }

        .verse-item a:hover {
            color: #007bff;
        }

        .verse-number {
            font-weight: bold;
            color: #555;
            margin-right: 8px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            text-decoration: none;
            color: #007bff;
            padding: 8px 12px;
            border: 1px solid #ddd;
            margin: 0 5px;
            border-radius: 4px;
            transition: background-color 0.3s, color 0.3s;
        }

        .pagination a:hover {
            background-color: #007bff;
            color: #fff;
        }

        .pagination .active {
            background-color: #007bff;
            color: #fff;
            border-color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="search-query">
            Search results for: <strong><?php echo htmlspecialchars($data['query']); ?></strong>
        </div>
        <ul class="verses-list">
            <?php foreach ($data['results'] as $result) : ?>
                <li class="verse-item">
                    <a href="<?php echo BASE_URL . $result['versao_id'] . '/' . $result['livro_id'] . '/' . $result['capitulo'] . '/' . $result['versiculo']; ?>">
                        <span class="verse-number"><?php echo $result['versiculo']; ?></span> - <?php echo $result['texto']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="pagination">
            <?php if ($data['currentPage'] > 1) : ?>
                <a href="<?php echo BASE_URL . 'search?query=' . urlencode($data['query']) . '&page=' . ($data['currentPage'] - 1); ?>">Previous</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= ceil(count($data['results']) / $data['resultsPerPage']); $i++) : ?>
                <a href="<?php echo BASE_URL . 'search?query=' . urlencode($data['query']) . '&page=' . $i; ?>" class="<?php echo ($i == $data['currentPage']) ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($data['currentPage'] < ceil(count($data['results']) / $data['resultsPerPage'])) : ?>
                <a href="<?php echo BASE_URL . 'search?query=' . urlencode($data['query']) . '&page=' . ($data['currentPage'] + 1); ?>">Next</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
