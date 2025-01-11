<?php
$book = $data['book'];
$verses1 = $data['verses1'];
$verses2 = $data['verses2'];
?>

<div class="verse-container">
  <div class="verse-header">
    <div class="breadcrumb">
      <a href="<?php echo BASE_URL; ?>">Início</a> &gt;
      <a href="<?php echo BASE_URL . $data['selectedVersion']; ?>"><?php echo $data['selectedVersion']; ?></a> &gt;
      <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $book['sigla']; ?>"><?php echo $book['nome']; ?></a> &gt;
      <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $book['sigla'] . '/' . $data['chapter']; ?>">Capítulo <?php echo $data['chapter']; ?></a> &gt;
      <span>Versículo <?php echo $data['startVerse'] . ($data['startVerse'] != $data['endVerse'] ? '-' . $data['endVerse'] : ''); ?></span>
    </div>
    <h1><?php echo $book['nome'] . ' ' . $data['chapter'] . ':' . $data['startVerse'] . ($data['startVerse'] != $data['endVerse'] ? '-' . $data['endVerse'] : ''); ?></h1>
  </div>

  <div class="version-selectors">
    <div class="version-selector">
      <label for="version1">Primeira Versão:</label>
      <select id="version1" onchange="changeVersion(1, this.value)">
        <?php foreach ($data['versions'] as $version): ?>
          <option value="<?php echo $version['sigla']; ?>" <?php echo $version['sigla'] === $data['selectedVersion'] ? 'selected' : ''; ?>>
            <?php echo $version['nome']; ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="version-selector">
      <label for="version2">Segunda Versão:</label>
      <select id="version2" onchange="changeVersion(2, this.value)">
        <?php foreach ($data['versions'] as $version): ?>
          <option value="<?php echo $version['sigla']; ?>" <?php echo $version['sigla'] === $data['compareVersion'] ? 'selected' : ''; ?>>
            <?php echo $version['nome']; ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="verse-content-wrapper">
    <div class="verse-content">
      <div class="verse-column">
        <div class="version-title"><?php echo $data['selectedVersion']; ?></div>
        <?php foreach ($verses1 as $verse): ?>
          <div class="verse-item">
            <span class="verse-number"><?php echo $verse['versiculo']; ?></span>
            <span class="verse-text"><?php echo $verse['texto']; ?></span>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="verse-column">
        <div class="version-title"><?php echo $data['compareVersion']; ?></div>
        <?php foreach ($verses2 as $verse): ?>
          <div class="verse-item">
            <span class="verse-number"><?php echo $verse['versiculo']; ?></span>
            <span class="verse-text"><?php echo $verse['texto']; ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="verse-navigation">
    <?php if ($data['startVerse'] > 1): ?>
      <a href="<?php echo BASE_URL . $data['selectedVersion'] . '+' . $data['compareVersion'] . '/' . $book['sigla'] . '/' . $data['chapter'] . '/' . ($data['startVerse'] - 1) . ($data['startVerse'] != $data['endVerse'] ? '-' . ($data['endVerse'] - 1) : ''); ?>" class="nav-link">
        <i class="fas fa-chevron-left"></i> Versículo anterior
      </a>
    <?php endif; ?>

    <?php if ($data['endVerse'] < $data['totalVerses']): ?>
      <a href="<?php echo BASE_URL . $data['selectedVersion'] . '+' . $data['compareVersion'] . '/' . $book['sigla'] . '/' . $data['chapter'] . '/' . ($data['startVerse'] + 1) . ($data['startVerse'] != $data['endVerse'] ? '-' . ($data['endVerse'] + 1) : ''); ?>" class="nav-link">
        Próximo versículo <i class="fas fa-chevron-right"></i>
      </a>
    <?php endif; ?>
  </div>
</div>

<style>
  .verse-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
  }

  .verse-header {
    margin-bottom: 20px;
  }

  .breadcrumb {
    font-size: 0.9em;
    color: var(--text-light);
    margin-bottom: 10px;
  }

  .breadcrumb a {
    color: var(--text-light);
    text-decoration: none;
  }

  .breadcrumb a:hover {
    color: var(--primary-color);
  }

  .version-selectors {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
  }

  .version-selector {
    display: flex;
    flex-direction: column;
    gap: 5px;
  }

  .version-selector label {
    font-size: 0.9em;
    font-weight: 500;
    color: var(--text-color);
  }

  .version-selector select {
    padding: 10px 15px;
    border: 2px solid var(--border-color);
    border-radius: var(--radius-sm);
    background-color: white;
    font-size: 1em;
    color: var(--text-color);
    cursor: pointer;
    min-width: 200px;
    box-shadow: var(--shadow-sm);
    transition: all 0.2s ease;
  }

  .version-selector select:hover {
    border-color: var(--primary-hover);
    box-shadow: var(--shadow-md);
  }

  .version-selector select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(46, 74, 123, 0.1);
  }

  .verse-content-wrapper {
    background: white;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    margin-bottom: 20px;
  }

  .verse-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    padding: 20px;
  }

  .verse-column {
    display: flex;
    flex-direction: column;
    gap: 15px;
  }

  .version-title {
    font-weight: 500;
    padding: 8px 12px;
    background-color: var(--primary-color);
    color: white;
    border-radius: var(--radius-sm);
    text-align: center;
  }

  .verse-item {
    display: flex;
    gap: 10px;
    line-height: 1.6;
  }

  .verse-number {
    color: var(--primary-color);
    font-weight: 500;
    min-width: 25px;
  }

  .verse-navigation {
    display: flex;
    justify-content: space-between;
    gap: 20px;
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
    .verse-content {
      grid-template-columns: 1fr;
    }

    .version-selectors {
      flex-direction: column;
      gap: 10px;
    }

    .version-selector select {
      width: 100%;
    }

    .verse-navigation {
      flex-direction: column;
      align-items: stretch;
    }

    .nav-link {
      justify-content: center;
    }
  }
</style>

<script>
  function changeVersion(position, newVersion) {
    const version1 = position === 1 ? newVersion : document.getElementById('version1').value;
    const version2 = position === 2 ? newVersion : document.getElementById('version2').value;
    
    window.location.href = `<?php echo BASE_URL ?>${version1}+${version2}/<?php echo $book['sigla'] ?>/<?php echo $data['chapter'] ?>/<?php echo $data['startVerse'] . ($data['startVerse'] != $data['endVerse'] ? '-' . $data['endVerse'] : ''); ?>`;
  }
</script> 