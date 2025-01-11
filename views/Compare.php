<?php
$verse1 = $data['verse1'];
$verse2 = $data['verse2'];
$book = $data['book'];
?>

<div class="container" style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    <div class="header" style="margin-bottom: 30px;">
        <h1 style="color: var(--primary-color); font-size: 1.8em; margin-bottom: 10px;">
            <?php echo $book['nome'] . ' ' . $data['chapter'] . ':' . $data['verse']; ?>
        </h1>
        <div class="breadcrumb" style="color: var(--text-light); font-size: 0.9em;">
            <a href="<?php echo BASE_URL; ?>" style="color: var(--primary-color); text-decoration: none;">Início</a> &gt;
            <a href="<?php echo BASE_URL . $data['selectedVersion']; ?>" style="color: var(--primary-color); text-decoration: none;"><?php echo $data['selectedVersion']; ?></a> &gt;
            <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $book['sigla']; ?>" style="color: var(--primary-color); text-decoration: none;"><?php echo $book['nome']; ?></a> &gt;
            <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $book['sigla'] . '/' . $data['chapter']; ?>" style="color: var(--primary-color); text-decoration: none;">Capítulo <?php echo $data['chapter']; ?></a> &gt;
            Versículo <?php echo $data['verse']; ?>
        </div>
    </div>

    <div class="comparison-container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div class="verse-card" style="background: white; padding: 20px; border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
            <h2 style="color: var(--primary-color); font-size: 1.2em; margin-bottom: 15px;">
                <?php echo $data['selectedVersion']; ?>
            </h2>
            <p style="line-height: 1.6; font-size: 1.1em;">
                <?php echo $verse1['texto']; ?>
            </p>
        </div>

        <div class="verse-card" style="background: white; padding: 20px; border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
            <h2 style="color: var(--primary-color); font-size: 1.2em; margin-bottom: 15px;">
                <?php echo $data['compareVersion']; ?>
            </h2>
            <p style="line-height: 1.6; font-size: 1.1em;">
                <?php echo $verse2['texto']; ?>
            </p>
        </div>
    </div>

    <div class="navigation" style="margin-top: 30px; display: flex; justify-content: space-between;">
        <?php if ($data['verse'] > 1) : ?>
            <a href="<?php echo BASE_URL . $data['selectedVersion'] . '+' . $data['compareVersion'] . '/' . $book['sigla'] . '/' . $data['chapter'] . '/' . ($data['verse'] - 1); ?>" 
               style="color: var(--primary-color); text-decoration: none; display: flex; align-items: center; gap: 5px;">
                <i class="fas fa-chevron-left"></i> Versículo anterior
            </a>
        <?php else : ?>
            <div></div>
        <?php endif; ?>

        <?php if ($data['verse'] < $book['versiculos'][$data['chapter'] - 1]) : ?>
            <a href="<?php echo BASE_URL . $data['selectedVersion'] . '+' . $data['compareVersion'] . '/' . $book['sigla'] . '/' . $data['chapter'] . '/' . ($data['verse'] + 1); ?>" 
               style="color: var(--primary-color); text-decoration: none; display: flex; align-items: center; gap: 5px;">
                Próximo versículo <i class="fas fa-chevron-right"></i>
            </a>
        <?php endif; ?>
    </div>
</div> 