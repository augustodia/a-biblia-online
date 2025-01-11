<?php
$SELECTED_VERSION_INDEX = array_search($data['selectedVersion'], array_column($data['versions'], 'sigla'));

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta http-equiv="pragma" content="no-cache" />
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($data['pageTitle']) ? $data['pageTitle'] . ' - A Bíblia Online' : 'A Bíblia Online - Leia e Compare Versões da Bíblia'; ?></title>
  <meta name="description" content="<?php echo isset($data['pageDescription']) ? $data['pageDescription'] : 'Leia a Bíblia online gratuitamente. Compare diferentes versões, pesquise versículos e acesse todos os livros da Bíblia em português.'; ?>">
  <meta name="keywords" content="bíblia online, bíblia, versículos, <?php echo isset($data['pageKeywords']) ? $data['pageKeywords'] : 'biblia sagrada, novo testamento, velho testamento'; ?>">
  
  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?php echo isset($_SERVER['REQUEST_URI']) ? BASE_URL . ltrim($_SERVER['REQUEST_URI'], '/') : BASE_URL; ?>">
  <meta property="og:title" content="<?php echo isset($data['pageTitle']) ? $data['pageTitle'] . ' - A Bíblia Online' : 'A Bíblia Online'; ?>">
  <meta property="og:description" content="<?php echo isset($data['pageDescription']) ? $data['pageDescription'] : 'Leia a Bíblia online gratuitamente. Compare diferentes versões, pesquise versículos e acesse todos os livros da Bíblia em português.'; ?>">
  <meta property="og:image" content="<?php echo BASE_URL; ?>public/assets/img/og-image.jpg">

  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="<?php echo isset($_SERVER['REQUEST_URI']) ? BASE_URL . ltrim($_SERVER['REQUEST_URI'], '/') : BASE_URL; ?>">
  <meta property="twitter:title" content="<?php echo isset($data['pageTitle']) ? $data['pageTitle'] . ' - A Bíblia Online' : 'A Bíblia Online'; ?>">
  <meta property="twitter:description" content="<?php echo isset($data['pageDescription']) ? $data['pageDescription'] : 'Leia a Bíblia online gratuitamente. Compare diferentes versões, pesquise versículos e acesse todos os livros da Bíblia em português.'; ?>">
  <meta property="twitter:image" content="<?php echo BASE_URL; ?>public/assets/img/og-image.jpg">

  <!-- Favicon -->
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo BASE_URL; ?>public/assets/img/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo BASE_URL; ?>public/assets/img/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="<?php echo BASE_URL; ?>public/assets/img/favicon-16x16.png">
  <link rel="manifest" href="<?php echo BASE_URL; ?>public/assets/site.webmanifest">
  <link rel="mask-icon" href="<?php echo BASE_URL; ?>public/assets/img/safari-pinned-tab.svg" color="#2e4a7b">
  <meta name="msapplication-TileColor" content="#2e4a7b">
  <meta name="theme-color" content="#2e4a7b">

  <!-- Canonical URL -->
  <link rel="canonical" href="<?php echo isset($_SERVER['REQUEST_URI']) ? BASE_URL . ltrim($_SERVER['REQUEST_URI'], '/') : BASE_URL; ?>">

  <!-- Fonts e CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    :root {
      --primary-color: #2e4a7b;
      --primary-light: #3c77c1;
      --primary-lighter: #4e8ed1;
      --primary-hover: #6b8bc3;
      --text-color: #333;
      --text-light: #666;
      --border-color: #eee;
      --bg-light: #f8f9fa;
      --shadow-sm: 0 2px 4px rgba(0,0,0,0.1);
      --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
      --radius-sm: 4px;
      --radius-md: 8px;
      --header-height-mobile: 60px;
      --sidebar-width: 280px;
    }

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
      background-color: var(--primary-hover);
      border-radius: var(--radius-sm);
    }

    html, body {
      height: 100%;
      overflow: hidden;
    }

    body {
      display: flex;
      background-color: var(--bg-light);
      color: var(--text-color);
    }

    aside {
      min-width: 280px;
      height: 100%;
      background-color: var(--primary-color);
      color: #FFF;
      display: flex;
      flex-direction: column;
      padding: 0 20px;
      box-shadow: var(--shadow-md);
      overflow-y: auto;
      position: relative;
    }

    .sidebar-header {
      position: fixed;
      width: 240px;
      padding: 20px 0;
      background-color: var(--primary-color);
      z-index: 1000;
    }

    .sidebar-header h1 {
      font-size: 1.5em;
      font-weight: 700;
      margin-bottom: 20px;
    }

    .separator {
      width: 100%;
      height: 1px;
      background-color: rgba(255,255,255,0.1);
      margin: 15px 0;
    }

    .version-title {
      font-size: 0.9em;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 10px;
      opacity: 0.8;
    }

    .selected-version {
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 12px 15px;
      background-color: rgba(255,255,255,0.1);
      border-radius: var(--radius-sm);
      transition: all 0.3s ease;
      color: #fff;
      text-decoration: none;
    }

    .selected-version:hover {
      background-color: rgba(255,255,255,0.15);
    }

    .selected-version i {
      transition: transform 0.3s ease;
      font-size: 0.8em;
      color: #fff;
    }

    .selected-version.collapsed i {
      transform: rotate(-180deg);
    }

    .collapse {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease-out;
      background-color: rgba(255,255,255,0.05);
      border-radius: 0 0 var(--radius-sm) var(--radius-sm);
    }

    .collapse.show {
      max-height: 300px;
      overflow-y: auto;
    }

    .collapse ul {
      list-style-type: none;
      padding: 5px 0;
    }

    .collapse li a {
      display: block;
      padding: 10px 15px;
      color: #FFF;
      text-decoration: none;
      transition: background-color 0.2s;
    }

    .collapse li a:hover {
      background-color: var(--primary-hover);
    }

    .search-bar {
      margin: 15px 0;
    }

    .search-bar form {
      display: flex;
      gap: 8px;
    }

    .search-bar input[type="text"] {
      flex: 1;
      padding: 10px 12px;
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: var(--radius-sm);
      background-color: rgba(255,255,255,0.1);
      color: #FFF;
      font-size: 0.9em;
      transition: all 0.3s ease;
    }

    .search-bar input[type="text"]::placeholder {
      color: rgba(255,255,255,0.6);
    }

    .search-bar input[type="text"]:focus {
      outline: none;
      background-color: rgba(255,255,255,0.15);
      border-color: rgba(255,255,255,0.3);
    }

    .search-bar button {
      padding: 10px;
      background-color: var(--primary-light);
      border: none;
      border-radius: var(--radius-sm);
      color: #FFF;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .search-bar button:hover {
      background-color: var(--primary-lighter);
    }

    .book-list {
      padding-top: 280px;
      padding-bottom: 20px;
    }

    .book-list h3 {
      font-size: 0.9em;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 15px;
      opacity: 0.8;
    }

    .book-list ul {
      list-style-type: none;
      border-radius: var(--radius-sm);
      overflow: hidden;
    }

    .book-list li a {
      display: block;
      padding: 10px 15px;
      color: #FFF;
      text-decoration: none;
      transition: all 0.2s ease;
      font-size: 0.95em;
    }

    .book-list li a:hover {
      background-color: var(--primary-hover);
      padding-left: 20px;
    }

    main {
      flex-grow: 1;
      overflow-y: auto;
      background-color: var(--bg-light);
    }

    .mobile-header {
      display: flex;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: var(--header-height-mobile);
      background: var(--primary-color);
      align-items: center;
      justify-content: center;
      padding: 0;
      z-index: 1030;
    }

    .mobile-header .home-link {
      font-size: 1.2em;
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
      padding: 15px;
      white-space: nowrap;
    }

    @media (max-width: 768px) {
      body {
        flex-direction: column;
        overflow-x: hidden;
      }

      .mobile-header {
        display: flex;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: var(--header-height-mobile);
        background: var(--primary-color);
        align-items: center;
        padding: 0;
        z-index: 1030;
      }

      .mobile-header h1 {
        color: white;
        font-size: 1.2em;
        margin-left: 60px;
        white-space: nowrap;
      }

      aside {
        position: fixed;
        left: -100%;
        top: 0;
        width: 85%;
        height: 100%;
        transition: left 0.3s ease;
        z-index: 1050;
        padding: 0 15px;
      }

      aside.show {
        left: 0;
      }

      .sidebar-header {
        position: relative;
        width: 100%;
        padding-top: 15px;
        padding-bottom: 15px;
      }

      .book-list {
        padding-top: 20px;
        padding-bottom: 100px;
      }

      main {
        margin-left: 0;
        margin-top: var(--header-height-mobile);
        width: 100%;
        height: calc(100vh - var(--header-height-mobile));
        overflow-y: auto;
      }

      .overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1040;
      }

      .overlay.show {
        display: block;
      }

      .search-bar {
        position: relative;
        margin: 10px 0;
      }

      .search-bar input[type="text"] {
        width: 100%;
      }
    }

    .menu-toggle {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: var(--header-height-mobile);
      height: var(--header-height-mobile);
      background: var(--primary-color);
      border: none;
      color: white;
      font-size: 1.5em;
      cursor: pointer;
      z-index: 1100;
      padding: 0;
      align-items: center;
      justify-content: center;
    }

    @media (max-width: 768px) {
      .menu-toggle {
        display: flex;
      }

      .mobile-header {
        padding: 0;
      }
    }

    .close-menu {
      display: none;
      position: absolute;
      top: 15px;
      right: 0px;
      padding: 10px;
      color: white;
      background: rgba(255,255,255,0.1);
      border: none;
      border-radius: var(--radius-sm);
      cursor: pointer;
      font-size: 1.2em;
    }

    @media (max-width: 768px) {
      aside.show {
        left: 0;
      }

      aside.show .close-menu {
        display: block;
      }
    }

    .collapse li a.active {
      background-color: var(--primary-hover);
    }

    .search-bar {
      margin: 15px 0;
    }

    .home-link {
      text-decoration: none;
      color: #FFF !important;
      font-weight: bold;
      transition: color 0.3s ease;
      display: inline-block;
    }

    .home-link:hover {
      color: rgba(255, 255, 255, 0.9) !important;
      text-decoration: none;
    }

    .home-link:active {
      transform: scale(0.98);
    }

    /* Ajustes para mobile */
    @media (max-width: 768px) {
      .mobile-header .home-link {
        font-size: 1.2em;
        margin: 0 auto;
        padding: 15px;
        display: block;
        text-align: center;
      }
      
      .sidebar-header .home-link {
        font-size: 1.2em;
        color: #FFF;
      }

      .sidebar-header h1 {
        margin: 0;
        padding: 0;
      }
    }
  </style>
</head>

<body>
  <button class="menu-toggle" aria-label="Menu">
    <i class="fas fa-bars"></i>
  </button>

  <div class="mobile-header">
    <a href="<?php echo BASE_URL; ?>" class="home-link">A Bíblia Online</a>
  </div>

  <div class="overlay"></div>

  <aside>
    <header class="sidebar-header">
      <button class="close-menu">
        <i class="fas fa-times"></i>
      </button>
      <h1><a href="<?php echo BASE_URL; ?>" class="home-link">A Bíblia Online</a></h1>
      <div class="separator"></div>
      <h3 class="version-title">Versão</h3>
      <a href="#" class="selected-version">
        <?php 
        $selectedVersionName = '';
        foreach ($data['versions'] as $version) {
          if ($version['sigla'] === $data['selectedVersion']) {
            $selectedVersionName = $version['nome'];
            break;
          }
        }
        echo $selectedVersionName;
        ?>
        <i class="fas fa-chevron-down" style="margin-left: 2px" ;></i>
      </a>
      <div class="collapse">
        <ul>
          <?php foreach ($data['versions'] as $version) : ?>
            <li>
              <a href="<?php echo BASE_URL . $version['sigla']; ?>" <?php echo ($version['sigla'] === $data['selectedVersion']) ? 'class="active"' : ''; ?>>
                <?php echo $version['nome']; ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="separator"></div>
      <div class="search-bar">
        <form method="GET" action="<?php echo BASE_URL; ?>search/<?php echo $data['selectedVersion']; ?>">
          <input
            type="text"
            name="q"
            value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"
            placeholder="Buscar"
            required
          >
          <button type="submit">
            <i class="fas fa-search"></i>
          </button>
        </form>
      </div>
    </header>
    <div class="book-list">
      <h3>Livros da Bíblia</h3>
      <ul>
        <?php foreach ($data['books'] as $book) : ?>
          <li>
            <a href="<?php echo BASE_URL . $data['selectedVersion'] . '/' . $book['sigla']; ?>">
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
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('aside');
    const overlay = document.querySelector('.overlay');
    const body = document.body;
    const closeMenu = document.querySelector('.close-menu');

    menuToggle.addEventListener('click', toggleMenu);
    overlay.addEventListener('click', toggleMenu);
    closeMenu.addEventListener('click', toggleMenu);

    function toggleMenu() {
      sidebar.classList.toggle('show');
      overlay.classList.toggle('show');
      body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
      menuToggle.style.display = sidebar.classList.contains('show') ? 'none' : 'flex';
    }

    // Fechar menu ao clicar em um link dos livros
    const bookLinks = document.querySelectorAll('.book-list a');
    bookLinks.forEach(link => {
      link.addEventListener('click', () => {
        if (window.innerWidth <= 768) {
          toggleMenu();
        }
      });
    });

    // Fechar menu ao redimensionar para desktop
    window.addEventListener('resize', () => {
      if (window.innerWidth > 768) {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        body.style.overflow = '';
        menuToggle.style.display = 'none';
      } else {
        menuToggle.style.display = sidebar.classList.contains('show') ? 'none' : 'flex';
      }
    });

    // Ajuste para o dropdown de versões
    const selectedVersion = document.querySelector('.selected-version');
    const collapse = document.querySelector('.collapse');
    
    selectedVersion.addEventListener('click', (event) => {
      event.preventDefault();
      collapse.classList.toggle('show');
      selectedVersion.classList.toggle('collapsed');
    });
  </script>
</body>

</html>
