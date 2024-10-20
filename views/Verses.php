<!-- Header com a seleção de capítulo -->
<div class="header-main">
  <h2>Capítulo:</h2>
  <select id="chapter-select" onchange="window.location.href=this.value;">
    <?php foreach ($data['chapters'] as $chapter) : ?>
      <option value="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $data['book'] . '/' . $chapter['capitulo']; ?>"
        <?php echo ($chapter['capitulo'] == $data['selectedChapter']) ? 'selected' : ''; ?>>
        <?php echo $chapter['capitulo']; ?>
      </option>
    <?php endforeach; ?>
  </select>
</div>

<!-- Lista de versículos -->
<ul class="verses-list">
  <?php foreach ($data['verses'] as $verse) : ?>
    <li class="verse-item">
      <a
        href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $data['book'] . '/' . $data['selectedChapter'] . '/' . $verse['versiculo']; ?>">
        <span class="verse-number"><?php echo $verse['versiculo']; ?></span> - <?php echo $verse['texto']; ?>
      </a>
    </li>
  <?php endforeach; ?>
</ul>

<style>
  /* Estilo para o header fixo */
  .header-main {
    display: flex;
    align-items: center;
    gap: 10px;
    position: sticky;
    top: 0;
    background-color: #f8f9fa;
    /* Fundo levemente claro */
    padding: 10px 20px;
    box-shadow: 0 4px 2px -2px rgba(0, 0, 0, 0.1);
    /* Sombra suave para destacar */
    z-index: 1000;
    /* Para garantir que o header fique acima do conteúdo principal */
  }

  #chapter-select {
    padding: 8px;
    font-size: 16px;
    border-radius: 4px;
    border: 1px solid #ccc;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    /* Leve sombra para dar destaque */
    background-color: #ffffff;
    /* Cor de fundo branco */
  }

  #chapter-select:focus {
    outline: none;
    border-color: #007bff;
    /* Azul ao focar */
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
  }

  h2 {
    margin: 0;
    font-size: 18px;
    font-weight: bold;
  }

  /* Estilo da lista de versículos */
  .verses-list {
    list-style-type: none;
    padding: 0;
    margin: 20px;
    /* Margem para afastar da borda */
  }

  .verse-item {
    padding: 15px;
    border-bottom: 1px solid #e0e0e0;
    transition: background-color 0.3s, transform 0.2s;
    /* Suave ao passar o mouse */
  }

  .verse-item:hover {
    background-color: #f1f1f1;
    transform: translateX(5px);
    /* Leve movimento ao passar o mouse */
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
    /* Azul ao passar o mouse */
  }

  .verse-number {
    font-weight: bold;
    color: #555;
    margin-right: 8px;
  }
</style>