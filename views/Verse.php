<?php
$book = $data['book'];
$verses = $data['verses'];
$firstVerse = reset($verses);
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
            Versículo<?php echo count($verses) > 1 ? 's' : ''; ?> <?php echo $data['startVerse'] . ($data['startVerse'] != $data['endVerse'] ? '-' . $data['endVerse'] : ''); ?>
        </div>

        <div class="verse-actions" style="margin: 20px 0; display: flex; gap: 10px; flex-wrap: wrap;">
            <!-- Seletor de intervalo de versículos -->
            <div class="verse-range-selector" style="display: flex; align-items: center; gap: 10px;">
                <label style="color: var(--text-light);">Ver versículos:</label>
                <input type="number" id="startVerse" value="<?php echo $data['startVerse']; ?>" min="1" max="<?php echo end($book['versiculos']); ?>" style="width: 60px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                <span style="color: var(--text-light);">até</span>
                <input type="number" id="endVerse" value="<?php echo $data['endVerse']; ?>" min="1" max="<?php echo end($book['versiculos']); ?>" style="width: 60px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                <button onclick="viewVerseRange()" class="nav-link" style="background: var(--primary-color); color: white; border: none;">
                    <i class="fas fa-list"></i> Ver versículos
                </button>
            </div>

            <!-- Dropdown de comparação -->
            <div class="dropdown">
                <button class="nav-link dropdown-toggle" style="background: var(--primary-color); color: white; border: none;">
                    <i class="fas fa-exchange-alt"></i> Comparar versões
                </button>
                <div class="dropdown-menu">
                    <?php foreach ($data['versions'] as $version): ?>
                        <?php if ($version['sigla'] !== $data['selectedVersion']): ?>
                            <a href="<?php echo BASE_URL . $data['selectedVersion'] . '+' . $version['sigla'] . '/' . $book['sigla'] . '/' . $data['chapter'] . '/' . $data['startVerse'] . ($data['startVerse'] != $data['endVerse'] ? '-' . $data['endVerse'] : ''); ?>" 
                               class="dropdown-item">
                                <?php echo $version['nome']; ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="verse-content">
        <?php foreach ($verses as $verse): ?>
            <?php if ($verse && isset($verse['versiculo'], $verse['texto'])): ?>
                <div class="current-verse">
                    <div class="verse-content-wrapper">
                        <span class="verse-number"><?php echo $verse['versiculo']; ?></span>
                        <div class="verse-text">
                            <?php echo $verse['texto']; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <div class="verse-navigation">
        <?php if ((int)$data['startVerse'] > 1): ?>
            <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $book['sigla'] . '/' . $data['chapter'] . '/' . ($data['startVerse'] - 1); ?>" class="nav-link">
                <i class="fas fa-chevron-left"></i> Versículo anterior
            </a>
        <?php endif; ?>

        <?php if ((int)$data['endVerse'] < (int)$data['totalVerses']): ?>
            <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $book['sigla'] . '/' . $data['chapter'] . '/' . ($data['endVerse'] + 1); ?>" class="nav-link">
                Próximo versículo <i class="fas fa-chevron-right"></i>
            </a>
        <?php endif; ?>
    </div>

    <!-- Debug info (temporário) -->
    <div style="margin-top: 20px; font-size: 12px; color: #666;">
        Debug: 
        Start: <?php echo $data['startVerse']; ?>, 
        End: <?php echo $data['endVerse']; ?>, 
        Total: <?php echo $data['totalVerses']; ?>
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
        cursor: pointer;
    }

    .nav-link:hover {
        background: #f5f7fa;
        border-color: #2e4a7b;
    }

    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        min-width: 200px;
        padding: 5px 0;
        margin: 2px 0 0;
        background-color: #fff;
        border: 1px solid rgba(0,0,0,.15);
        border-radius: 4px;
        box-shadow: 0 6px 12px rgba(0,0,0,.175);
    }

    .dropdown:hover .dropdown-menu {
        display: block;
    }

    .dropdown-item {
        display: block;
        padding: 8px 20px;
        clear: both;
        font-weight: 400;
        color: #333;
        text-align: inherit;
        white-space: nowrap;
        background-color: transparent;
        border: 0;
        text-decoration: none;
    }

    .dropdown-item:hover {
        color: #2e4a7b;
        background-color: #f5f7fa;
    }

    .verse-range-selector input[type="number"] {
        -moz-appearance: textfield;
    }

    .verse-range-selector input[type="number"]::-webkit-outer-spin-button,
    .verse-range-selector input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
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

        .verse-navigation {
            flex-direction: column;
            gap: 10px;
        }

        .verse-actions {
            flex-direction: column;
            width: 100%;
        }

        .dropdown {
            width: 100%;
        }

        .dropdown-menu {
            width: 100%;
        }

        .nav-link {
            width: 100%;
            justify-content: center;
        }

        .verse-range-selector {
            flex-direction: column;
            align-items: stretch;
            width: 100%;
        }

        .verse-range-selector input[type="number"] {
            width: 100% !important;
        }
    }

    .breadcrumb {
        margin: 10px 0;
        padding: 10px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .breadcrumb a {
        color: var(--primary-color);
        text-decoration: none;
        transition: color 0.2s;
    }

    .breadcrumb a:hover {
        color: var(--primary-hover);
    }
</style>

<script>
function viewVerseRange() {
    const startVerse = document.getElementById('startVerse').value;
    const endVerse = document.getElementById('endVerse').value;
    
    if (startVerse && endVerse) {
        const start = parseInt(startVerse);
        const end = parseInt(endVerse);
        
        if (start > end) {
            alert('O versículo inicial deve ser menor que o final');
            return;
        }
        
        const url = `<?php echo BASE_URL . $data['selectedVersion'] . '/' . $book['sigla'] . '/' . $data['chapter']; ?>/${start}${start !== end ? '-' + end : ''}`;
        window.location.href = url;
    }
}

// Validação dos inputs
document.getElementById('startVerse').addEventListener('change', function() {
    const start = parseInt(this.value);
    const end = parseInt(document.getElementById('endVerse').value);
    
    if (start > end) {
        document.getElementById('endVerse').value = start;
    }
});

document.getElementById('endVerse').addEventListener('change', function() {
    const start = parseInt(document.getElementById('startVerse').value);
    const end = parseInt(this.value);
    
    if (end < start) {
        document.getElementById('startVerse').value = end;
    }
});
</script>
