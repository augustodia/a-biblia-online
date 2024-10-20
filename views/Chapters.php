<div class="header-main">
  <h2>Capítulo:</h2>
  <select id="chapter-select" onchange="window.location.href=this.value;">
    <?php foreach ($data['chapters'] as $chapter) : ?>
      <option value="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $data['book'] . '/' . $chapter['capitulo']; ?>">
        <?php echo $chapter['capitulo']; ?>
      </option>
    <?php endforeach; ?>
  </select>
</div>

<style>
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
</style>