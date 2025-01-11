<?php
$book = $data['book'];
$versesData = $data['versesData'];
$selectedVersions = $data['selectedVersions'];
?>

<div class="verse-container">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="<?php echo BASE_URL; ?>">Início</a> &gt;
        <a href="<?php echo BASE_URL . $data['selectedVersion']; ?>"><?php echo $data['selectedVersion']; ?></a> &gt;
        <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $book['sigla']; ?>"><?php echo $book['nome']; ?></a> &gt;
        <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $book['sigla'] . '/' . $data['chapter']; ?>">Capítulo <?php echo $data['chapter']; ?></a> &gt;
        <span>Versículo <?php echo $data['startVerse'] . ($data['startVerse'] != $data['endVerse'] ? '-' . $data['endVerse'] : ''); ?></span>
    </div>

    <div class="verse-header">
        <h1><?php echo $book['nome'] . ' ' . $data['chapter'] . ':' . $data['startVerse'] . ($data['startVerse'] != $data['endVerse'] ? '-' . $data['endVerse'] : ''); ?></h1>
    </div>

    <!-- Controle de versões -->
    <div class="version-control">
        <div class="version-manager">
            <div class="selected-versions">
                <?php foreach ($selectedVersions as $version): ?>
                    <div class="version-tag">
                        <?php echo $version; ?>
                        <button onclick="removeVersion('<?php echo $version; ?>')" class="remove-version">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="version-selector">
                <select id="versionToAdd" onchange="addVersion(this.value); this.value='';">
                    <option value="">Adicionar versão...</option>
                    <?php foreach ($data['versions'] as $version): ?>
                        <?php if (!in_array($version['sigla'], $selectedVersions)): ?>
                            <option value="<?php echo $version['sigla']; ?>">
                                <?php echo $version['nome']; ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Conteúdo dos versículos -->
    <div class="comparison-container">
        <?php foreach ($selectedVersions as $version): ?>
            <div class="version-column">
                <div class="version-title"><?php echo $version; ?></div>
                <?php foreach ($versesData[$version] as $verse): ?>
                    <div class="verse-content">
                        <span class="verse-number"><?php echo $verse['versiculo']; ?></span>
                        <?php echo $verse['texto']; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Navegação -->
    <div class="verse-navigation">
        <?php if ($data['hasPreviousVerse']): ?>
            <a href="<?php echo BASE_URL . implode('+', $selectedVersions) . '/' . $book['sigla'] . '/' . $data['chapter'] . '/' . ($data['startVerse'] - 1) . ($data['startVerse'] != $data['endVerse'] ? '-' . ($data['endVerse'] - 1) : ''); ?>" class="nav-link">
                <i class="fas fa-chevron-left"></i> Anterior
            </a>
        <?php else: ?>
            <div></div> <!-- Espaçador para manter o layout -->
        <?php endif; ?>

        <?php if ($data['hasNextVerse']): ?>
            <a href="<?php echo BASE_URL . implode('+', $selectedVersions) . '/' . $book['sigla'] . '/' . $data['chapter'] . '/' . ($data['startVerse'] + 1) . ($data['startVerse'] != $data['endVerse'] ? '-' . ($data['endVerse'] + 1) : ''); ?>" class="nav-link">
                Próximo <i class="fas fa-chevron-right"></i>
            </a>
        <?php else: ?>
            <div></div> <!-- Espaçador para manter o layout -->
        <?php endif; ?>
    </div>

    <!-- Debug info (temporário) -->
    <div style="margin-top: 20px; font-size: 12px; color: #666;">
        Debug: 
        Start: <?php echo $data['startVerse']; ?>, 
        End: <?php echo $data['endVerse']; ?>, 
        Total: <?php echo $data['totalVerses']; ?>, 
        Has Next: <?php echo $data['hasNextVerse'] ? 'Sim' : 'Não'; ?>, 
        Has Previous: <?php echo $data['hasPreviousVerse'] ? 'Sim' : 'Não'; ?>
    </div>
</div>

<script>
function getCurrentUrl() {
    const book = '<?php echo $book['sigla']; ?>';
    const chapter = '<?php echo $data['chapter']; ?>';
    const verse = '<?php echo $data['startVerse']; ?>';
    const endVerse = '<?php echo $data['endVerse']; ?>';
    return { book, chapter, verse, endVerse };
}

function addVersion(version) {
    if (!version) return;
    
    const currentVersions = <?php echo json_encode($selectedVersions); ?>;
    if (currentVersions.includes(version)) return;
    
    const { book, chapter, verse, endVerse } = getCurrentUrl();
    const newVersions = [...currentVersions, version];
    
    const verseRange = verse !== endVerse ? `${verse}-${endVerse}` : verse;
    window.location.href = `<?php echo BASE_URL ?>${newVersions.join('+')}/${book}/${chapter}/${verseRange}`;
}

function removeVersion(version) {
    const currentVersions = <?php echo json_encode($selectedVersions); ?>;
    if (currentVersions.length <= 1) return; // Não permitir remover a última versão
    
    const { book, chapter, verse, endVerse } = getCurrentUrl();
    const newVersions = currentVersions.filter(v => v !== version);
    
    const verseRange = verse !== endVerse ? `${verse}-${endVerse}` : verse;
    window.location.href = `<?php echo BASE_URL ?>${newVersions.join('+')}/${book}/${chapter}/${verseRange}`;
}
</script>

<style>
.verse-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.breadcrumb {
    margin-bottom: 20px;
    font-size: 14px;
}

.breadcrumb a {
    color: var(--primary-color);
    text-decoration: none;
}

.verse-header {
    margin-bottom: 20px;
}

.version-control {
    margin-bottom: 20px;
}

.version-manager {
    background: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.selected-versions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 15px;
}

.version-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    background-color: var(--primary-color);
    color: white;
    border-radius: 4px;
    font-size: 0.9em;
}

.remove-version {
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    padding: 0;
    font-size: 0.9em;
    opacity: 0.8;
    transition: opacity 0.2s;
}

.remove-version:hover {
    opacity: 1;
}

.version-selector select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: var(--bg-light);
    font-size: 1em;
    color: var(--text-color);
    cursor: pointer;
}

.version-selector select:hover {
    border-color: var(--primary-color);
}

.comparison-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.version-column {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.version-title {
    background: var(--primary-color);
    color: white;
    padding: 10px;
    text-align: center;
    font-weight: bold;
}

.verse-content {
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.verse-number {
    font-weight: bold;
    margin-right: 8px;
    color: var(--primary-color);
}

.verse-navigation {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

.nav-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 15px;
    background-color: white;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm);
    color: var(--text-color);
    text-decoration: none;
    transition: all 0.2s;
    cursor: pointer;
}

.nav-link:hover {
    background-color: var(--bg-light);
    border-color: var(--primary-color);
    color: var(--primary-color);
}

@media (max-width: 768px) {
    .verse-navigation {
        flex-direction: column;
        align-items: stretch;
    }

    .nav-link {
        justify-content: center;
        margin-bottom: 10px;
    }
}
</style> 