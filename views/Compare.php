<?php
$verses1 = $data['verses1'];
$verses2 = $data['verses2'];
$book = $data['book'];
?>

<div class="verse-container">
    <div class="verse-header">
        <h2><?php echo $book['nome'] . ' ' . $data['chapter'] . ':' . $data['startVerse'] . ($data['startVerse'] != $data['endVerse'] ? '-' . $data['endVerse'] : ''); ?></h2>
        
        <!-- Breadcrumb -->
        <div class="breadcrumb" style="color: var(--text-light); font-size: 0.9em; margin: 10px 0;">
            <a href="<?php echo BASE_URL; ?>" style="color: var(--primary-color); text-decoration: none;">Início</a> &gt;
            <a href="<?php echo BASE_URL . $data['selectedVersion']; ?>" style="color: var(--primary-color); text-decoration: none;"><?php echo $data['selectedVersion']; ?></a> &gt;
            <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $book['sigla']; ?>" style="color: var(--primary-color); text-decoration: none;"><?php echo $book['nome']; ?></a> &gt;
            <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $book['sigla'] . '/' . $data['chapter']; ?>" style="color: var(--primary-color); text-decoration: none;">Capítulo <?php echo $data['chapter']; ?></a> &gt;
            Versículo<?php echo count($verses1) > 1 ? 's' : ''; ?> <?php echo $data['startVerse'] . ($data['startVerse'] != $data['endVerse'] ? '-' . $data['endVerse'] : ''); ?>
        </div>
    </div>

    <div class="comparison-container">
        <div class="verse-content">
            <div class="verse-card">
                <h3 class="version-title"><?php echo $data['selectedVersion']; ?></h3>
                <?php foreach ($verses1 as $verse): ?>
                    <div class="current-verse">
                        <div class="verse-content-wrapper">
                            <span class="verse-number"><?php echo $verse['versiculo']; ?></span>
                            <div class="verse-text"><?php echo $verse['texto']; ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="verse-card">
                <h3 class="version-title"><?php echo $data['compareVersion']; ?></h3>
                <?php foreach ($verses2 as $verse): ?>
                    <div class="current-verse">
                        <div class="verse-content-wrapper">
                            <span class="verse-number"><?php echo $verse['versiculo']; ?></span>
                            <div class="verse-text"><?php echo $verse['texto']; ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="verse-navigation">
        <?php if ($data['startVerse'] > 1) : ?>
            <a href="<?php echo BASE_URL . $data['selectedVersion'] . '+' . $data['compareVersion'] . '/' . $book['sigla'] . '/' . $data['chapter'] . '/' . ($data['startVerse'] - 1) . ($data['startVerse'] != $data['endVerse'] ? '-' . ($data['endVerse'] - 1) : ''); ?>" class="nav-link">
                <i class="fas fa-chevron-left"></i> Versículo anterior
            </a>
        <?php endif; ?>

        <?php if ($data['endVerse'] < $book['versiculos'][$data['chapter'] - 1]) : ?>
            <a href="<?php echo BASE_URL . $data['selectedVersion'] . '+' . $data['compareVersion'] . '/' . $book['sigla'] . '/' . $data['chapter'] . '/' . ($data['startVerse'] + 1) . ($data['startVerse'] != $data['endVerse'] ? '-' . ($data['endVerse'] + 1) : ''); ?>" class="nav-link">
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

    .comparison-container {
        margin-bottom: 20px;
    }

    .verse-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .verse-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .version-title {
        color: white;
        font-size: 1.2em;
        margin: 0;
        padding: 15px 20px;
        background: var(--primary-color);
        border-bottom: 1px solid #eee;
    }

    .current-verse {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
    }

    .current-verse:last-child {
        border-bottom: none;
    }

    .verse-content-wrapper {
        display: flex;
        align-items: flex-start;
        gap: 15px;
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
        cursor: pointer;
    }

    .nav-link:hover {
        background: #f5f7fa;
        border-color: #2e4a7b;
    }

    @media (max-width: 768px) {
        .verse-container {
            padding: 10px;
        }

        .verse-content {
            grid-template-columns: 1fr;
        }

        .verse-card {
            margin-bottom: 20px;
        }

        .current-verse {
            padding: 12px 15px;
        }

        .verse-content-wrapper {
            gap: 12px;
        }

        .verse-navigation {
            flex-direction: column;
            gap: 10px;
        }

        .nav-link {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .verse-container {
            padding: 10px;
        }

        .verse-header h2 {
            font-size: 1.3em;
        }

        .current-verse {
            padding: 10px 12px;
        }

        .verse-content-wrapper {
            gap: 10px;
        }

        .verse-text {
            font-size: 0.95em;
        }
    }
</style> 