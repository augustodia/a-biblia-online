
<div class="verse-container">
    <div class="verse-header">
        <h2><?php echo $data['verse']['book_name']; ?> <?php echo $data['verse']['capitulo']; ?>:<?php echo $data['verse']['versiculo']; ?></h2>
        <p class="verse-info">
            <?php echo $data['versions'][array_search($data['selectedVersion'], array_column($data['versions'], 'sigla'))]['nome']; ?> - <?php echo $data['verse']['book_name']; ?>
        </p>
    </div>

    <div class="verse-content">
        <!-- Versículos anteriores -->
        <?php if (!empty($data['previousVerses'])): ?>
            <div class="context-verses previous-verses">
                <?php foreach ($data['previousVerses'] as $verse): ?>
                    <div class="context-verse">
                        <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $verse['book'] . '/' . $verse['capitulo'] . '/' . $verse['versiculo']; ?>">
                            <span class="verse-number"><?php echo $verse['versiculo']; ?></span>
                            <span class="verse-text"><?php echo $verse['texto']; ?></span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Versículo atual -->
        <div class="current-verse">
            <div class="verse-content-wrapper">
                <span class="verse-number"><?php echo $data['verse']['versiculo']; ?></span>
                <div class="verse-text">
                    <?php echo $data['verse']['texto']; ?>
                </div>
            </div>
        </div>

        <!-- Versículos seguintes -->
        <?php if (!empty($data['nextVerses'])): ?>
            <div class="context-verses next-verses">
                <?php foreach ($data['nextVerses'] as $verse): ?>
                    <div class="context-verse">
                        <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $verse['book'] . '/' . $verse['capitulo'] . '/' . $verse['versiculo']; ?>">
                            <span class="verse-number"><?php echo $verse['versiculo']; ?></span>
                            <span class="verse-text"><?php echo $verse['texto']; ?></span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="verse-navigation">
        <?php if (!empty($data['previousVerses'])): ?>
            <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $data['verse']['book'] . '/' . $data['verse']['capitulo'] . '/' . ($data['verse']['versiculo'] - 1); ?>" class="nav-link">
                <i class="fas fa-chevron-left"></i> Versículo anterior
            </a>
        <?php endif; ?>

        <?php if (!empty($data['nextVerses'])): ?>
            <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $data['verse']['book'] . '/' . $data['verse']['capitulo'] . '/' . ($data['verse']['versiculo'] + 1); ?>" class="nav-link">
                Próximo versículo <i class="fas fa-chevron-right"></i>
            </a>
        <?php endif; ?>
    </div>
</div>

<style>
    .verse-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }

    .verse-header {
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }

    .verse-header h2 {
        color: #2e4a7b;
        margin-bottom: 10px;
    }

    .verse-info {
        color: #666;
        font-size: 0.9em;
    }

    .verse-content {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .context-verses {
        padding: 15px;
        background: #f8f9fa;
    }

    .context-verse {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    .context-verse:last-child {
        border-bottom: none;
    }

    .context-verse a {
        display: flex;
        align-items: flex-start;
        color: #666;
        text-decoration: none;
        transition: color 0.2s;
        gap: 15px;
    }

    .context-verse a:hover {
        color: #2e4a7b;
    }

    .verse-number {
        color: #2e4a7b;
        font-weight: bold;
        min-width: 25px;
    }

    .verse-text {
        line-height: 1.6;
        flex: 1;
    }

    .current-verse {
        padding: 30px;
        background: #fff;
        border-left: 4px solid #2e4a7b;
    }

    .verse-content-wrapper {
        display: flex;
        align-items: flex-start;
        gap: 15px;
    }

    .current-verse .verse-number {
        font-size: 1.1em;
    }

    .current-verse .verse-text {
        font-size: 1.2em;
        color: #333;
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
        border-color: #2e4a7b;
    }

    @media (max-width: 768px) {
        .verse-container {
            padding: 10px;
        }

        .current-verse {
            padding: 20px;
        }

        .verse-content-wrapper {
            gap: 12px;
        }

        .current-verse .verse-text {
            font-size: 1.1em;
        }

        .context-verse a {
            gap: 12px;
        }

        .verse-navigation {
            gap: 10px;
        }

        .nav-link {
            width: 100%;
            justify-content: center;
        }
    }
</style>
