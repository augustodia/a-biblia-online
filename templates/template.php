<?php
$SELECTED_VERSION_INDEX = array_search($data['selectedVersion'], array_column($data['versions'], 'sigla'));

// echo '<pre>';
// print_r($data['versions']);
// echo '</pre>';
// echo $data['selectedVersion'];
// echo $SELECTED_VERSION_INDEX;
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta http-equiv="pragma" content="no-cache" />
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>A Bíblia Online</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    /* Reset e configuração básica */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Roboto', sans-serif;
    }

    *::-webkit-scrollbar {
      width: 6px;
    }

    *::-webkit-scrollbar-thumb {
      background-color: #8d93b1;
      border-radius: 10px;
    }

    html,
    body {
      height: 100%;
      overflow: hidden;
    }

    a {
      text-decoration: none;
      color: inherit;
    }

    body {
      display: flex;
      background-color: #f9f9f9;
    }

    aside {
      min-width: 260px;
      height: 100%;
      background-color: #2e4a7b;
      color: #FFF;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 0 20px;
      box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
      overflow-y: auto;
    }

    .sidebar-header {
      position: fixed;
      padding: 10px;
      background-color: #2e4a7b;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .separator {
      width: 100%;
      height: 1px;
      background-color: #8d93b1;
      margin: 10px 0;
    }

    .version-title {
      align-self: flex-start;
      font-weight: bold;
      margin-bottom: 10px;
    }

    .selected-version {
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 12px;
      width: 100%;
      background-color: #3c77c1;
      border-radius: 5px;
      transition: background-color 0.3s;
    }

    .selected-version:hover {
      background-color: #4e8ed1;
    }

    .selected-version i {
      transition: transform 0.3s ease;
    }

    .selected-version.collapsed i {
      transform: rotate(-180deg);
    }

    .collapse {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease-out;
      width: 100%;
      background-color: #4e8ed1;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      border-bottom-left-radius: 5px;
      border-bottom-right-radius: 5px;
    }

    .collapse.show {
      max-height: none;
      overflow: visible;
      transition: max-height 0.3s ease-in;
    }

    .collapse ul {
      list-style-type: none;
      padding: 0;
      margin-top: -6px;
    }

    .collapse li {
      padding: 8px;
    }

    .collapse li:not(:last-child) {
      border-bottom: 1px solid #ccc;
    }

    .collapse li:hover {
      background-color: #6b8bc3;
    }

    .collapse a {
      color: #FFF;
    }



    .book-list {
      margin-top: 200px;
      width: 100%;
      margin-bottom: 40px;
    }

    .book-list ul {
      list-style-type: none;
      padding: 0;
      width: 100%;
    }

    .book-list li {
      cursor: pointer;
    }

    .book-list li a {
      display: block;
      padding: 8px 5px;
    }

    .book-list li:hover {
      background-color: #6b8bc3;
      color: #FFF;
    }

    main {
      flex-grow: 1;
      overflow-y: auto;
    }

    @media (max-width: 768px) {
      aside {
        width: 100%;
        height: auto;
        padding: 10px;
      }

      main {
        padding: 10px;
      }
    }
  </style>
</head>

<body>
  <aside>
    <header class="sidebar-header">
      <h1>A Bíblia Online</h1>
      <div class="separator"></div>
      <h3 class="version-title">Versão</h3>
      <a href="#" class="selected-version">
        <?php echo $data['versions'][$SELECTED_VERSION_INDEX]['nome']; ?>
        <i class="fas fa-chevron-down" style="margin-left: 2px" ;></i>
      </a>
      <div class=" collapse">
        <ul>
          <?php foreach ($data['versions'] as $version) : ?>
            <li><a href="<?php echo BASE_URL . strtolower($version['sigla']); ?>"><?php echo $version['nome']; ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </header>
    <div class="book-list">
      <h3>Livros da Bíblia</h3>
      <ul>
        <?php foreach ($data['books'] as $book) : ?>
          <li>
            <a href="<?php echo BASE_URL . $data['versions'][$SELECTED_VERSION_INDEX]['sigla'] . '/' . $book['sigla'] . '/' . '1' ?>">
              <?php echo $book['nome']; ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </aside>

  <main>
    <?php $this->loadView($view, $data) ?>
  </main>

  <script>
    const selectedVersion = document.querySelector('.selected-version');
    const collapse = document.querySelector('.collapse');
    const chevronIcon = selectedVersion.querySelector('i');
    const bookList = document.querySelector('.book-list');

    selectedVersion.addEventListener('click', (event) => {
      event.preventDefault();
      collapse.classList.toggle('show');
      selectedVersion.classList.toggle('collapsed');
    });
  </script>
</body>

</html>