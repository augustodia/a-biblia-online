<?php
$book = $data['book'];
$verses = $data['verses'];
?>

<div class="container" style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    <div class="header" style="margin-bottom: 30px;">
        <h1 style="color: var(--primary-color); font-size: 1.8em; margin-bottom: 10px;">
            <?php echo $book['nome'] . ' ' . $data['chapter'] . ':' . $data['startVerse'] . '-' . $data['endVerse']; ?>
        </h1>
        <div class="breadcrumb" style="color: var(--text-light); font-size: 0.9em;">
            <a href="<?php echo BASE_URL; ?>" style="color: var(--primary-color); text-decoration: none;">Início</a> &gt;
            <a href="<?php echo BASE_URL . $data['selectedVersion']; ?>" style="color: var(--primary-color); text-decoration: none;"><?php echo $data['selectedVersion']; ?></a> &gt;
            <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $book['sigla']; ?>" style="color: var(--primary-color); text-decoration: none;"><?php echo $book['nome']; ?></a> &gt;
            <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $book['sigla'] . '/' . $data['chapter']; ?>" style="color: var(--primary-color); text-decoration: none;">Capítulo <?php echo $data['chapter']; ?></a> &gt;
            Versículos <?php echo $data['startVerse'] . '-' . $data['endVerse']; ?>
        </div>
    </div>

    <div class="verses-container" style="background: white; padding: 30px; border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
        <?php foreach ($verses as $index => $verse): ?>
            <div class="verse-item" style="margin-bottom: 20px;">
                <div class="verse-number" style="color: var(--primary-color); font-weight: 500; margin-bottom: 5px;">
                    Versículo <?php echo $data['startVerse'] + $index; ?>
                </div>
                <p style="line-height: 1.6; font-size: 1.1em;">
                    <?php echo $verse['texto']; ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="navigation" style="margin-top: 30px; display: flex; justify-content: space-between;">
        <?php if ($data['startVerse'] > 1) : ?>
            <?php 
            $prevStart = max(1, $data['startVerse'] - ($data['endVerse'] - $data['startVerse'] + 1));
            $prevEnd = $data['startVerse'] - 1;
            ?>
            <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $book['sigla'] . '/' . $data['chapter'] . '/' . $prevStart . '-' . $prevEnd; ?>" 
               style="color: var(--primary-color); text-decoration: none; display: flex; align-items: center; gap: 5px;">
                <i class="fas fa-chevron-left"></i> Versículos anteriores
            </a>
        <?php else : ?>
            <div></div>
        <?php endif; ?>

        <?php if ($data['endVerse'] < $book['versiculos'][$data['chapter'] - 1]) : ?>
            <?php 
            $nextStart = $data['endVerse'] + 1;
            $nextEnd = min($book['versiculos'][$data['chapter'] - 1], $nextStart + ($data['endVerse'] - $data['startVerse']));
            ?>
            <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $book['sigla'] . '/' . $data['chapter'] . '/' . $nextStart . '-' . $nextEnd; ?>" 
               style="color: var(--primary-color); text-decoration: none; display: flex; align-items: center; gap: 5px;">
                Próximos versículos <i class="fas fa-chevron-right"></i>
            </a>
        <?php endif; ?>
    </div>
</div> 