<div class="verses-container">
    <div class="verses-header">
        <h2><?php echo $data['books'][array_search($data['book'], array_column($data['books'], 'sigla'))]['nome']; ?> <?php echo $data['chapter']; ?></h2>
        <p class="verses-info">
            <?php echo $data['versions'][array_search($data['selectedVersion'], array_column($data['versions'], 'sigla'))]['nome']; ?>
        </p>
    </div>

    <div class="verses-content">
        <?php foreach ($data['verses'] as $verse) : ?>
            <div class="verse-item">
                <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $data['book'] . '/' . $data['chapter'] . '/' . $verse['versiculo']; ?>">
                    <span class="verse-number"><?php echo $verse['versiculo']; ?></span>
                    <span class="verse-text"><?php echo $verse['texto']; ?></span>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="verse-navigation">
        <?php if ($data['chapter'] > 1) : ?>
            <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $data['book'] . '/' . ($data['chapter'] - 1); ?>" class="nav-link">
                <i class="fas fa-chevron-left"></i> Capítulo anterior
            </a>
        <?php endif; ?>

        <?php if ($data['chapter'] < count($data['chapters'])) : ?>
            <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $data['book'] . '/' . ($data['chapter'] + 1); ?>" class="nav-link">
                Próximo capítulo <i class="fas fa-chevron-right"></i>
            </a>
        <?php endif; ?>
    </div>
</div>

<style>
    .verses-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }

    .verses-header {
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--border-color);
    }

    .verses-header h2 {
        color: var(--primary-color);
        margin-bottom: 10px;
        font-size: 1.5em;
    }

    .verses-info {
        color: var(--text-light);
        font-size: 0.9em;
    }

    .verses-content {
        background: #fff;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .verse-navigation {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    .nav-link {
        padding: 10px 20px;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        color: var(--primary-color);
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .nav-link:hover {
        background: #f5f7fa;
        border-color: var(--primary-color);
    }

    .verse-item {
        border-bottom: 1px solid var(--border-color);
    }

    .verse-item:last-child {
        border-bottom: none;
    }

    .verse-item a {
        display: flex;
        padding: 15px 20px;
        color: var(--text-color);
        text-decoration: none;
        transition: background-color 0.2s;
        gap: 15px;
        align-items: flex-start;
    }

    .verse-item a:hover {
        background-color: var(--bg-light);
    }

    .verse-number {
        color: var(--primary-color);
        font-weight: bold;
        min-width: 25px;
        font-size: 0.9em;
        padding-top: 3px;
    }

    .verse-text {
        line-height: 1.6;
        flex: 1;
    }

    @media (max-width: 768px) {
        .verses-container {
            padding: 15px;
        }

        .verses-header h2 {
            font-size: 1.3em;
        }

        .verse-item a {
            padding: 12px 15px;
            gap: 12px;
        }

        .verse-text {
            font-size: 0.95em;
        }

        .verse-navigation {
            gap: 10px;
        }

        .nav-link {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .verses-container {
            padding: 10px;
        }

        .verses-header {
            margin-bottom: 20px;
        }

        .verses-header h2 {
            font-size: 1.2em;
        }

        .verse-item a {
            padding: 10px 12px;
            gap: 10px;
        }

        .verse-number {
            min-width: 20px;
        }

        .verse-text {
            font-size: 0.9em;
        }
    }
</style>