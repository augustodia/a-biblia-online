<?php

class Routes
{
  private $routes = array();

  public function add($route, $callback, $method = 'GET')
  {
    $this->routes[] = array(
      'route' => $route,
      'callback' => $callback,
      'method' => $method
    );
  }

  public function submit()
  {
    $url = isset($_GET['url']) ? rawurldecode($_GET['url']) : '/';
    $method = $_SERVER['REQUEST_METHOD'];
    
    foreach ($this->routes as $route) {
      if ($route['method'] === $method && preg_match($route['route'], $url, $params)) {
        array_shift($params);
        return call_user_func_array($route['callback'], $params);
      }
    }

    header("HTTP/1.0 404 Not Found");
    echo "404 - Página não encontrada";
  }
}

$routes = new Routes();

// Rotas da aplicação
$routes->add('/^\/$/', function () {
  $homeController = new HomeController();
  $homeController->index('ARA');
});

$routes->add('/^search\/([a-zA-Z]+).*$/', function ($versionAcronym) {
  $searchController = new SearchController();
  $searchController->index($versionAcronym);
});

$routes->add('/^([a-zA-Z]+)$/', function (String $versionAcronym) {
  $homeController = new HomeController();
  $homeController->index($versionAcronym);
});

$routes->add('/^([a-zA-Z]+)\/([0-9]?[a-zA-Z]+)$/', function ($version, $bookAcronym) {
  $chaptersController = new ChaptersController();
  $chaptersController->index($version, $bookAcronym);
});

$routes->add('/^([a-zA-Z]+)\/([0-9]?[a-zA-Z]+)\/([0-9]+)$/', function ($versionAcronym, $bookAcronym, $chapterNumber) {
  $chapterNumber = (int) $chapterNumber;
  $versesController = new VersesController();
  $versesController->index($versionAcronym, $bookAcronym, $chapterNumber);
});

$routes->add('/^([a-zA-Z]+)\/([0-9]?[a-zA-Z]+)\/([0-9]+)\/([0-9]+)$/', function ($version, $book, $chapter, $verse) {
  $verseController = new VerseController();
  $verseController->show($version, $book, $chapter, $verse);
});

// TODO: Implementar comparação de versões
$routes->add('/^([a-zA-Z]+)\+([a-zA-Z]+)\/([0-9]?[a-zA-Z]+)\/([0-9]+)\/([0-9]+)$/', function ($version1, $version2, $book, $chapter, $verse) {
  // Implementação futura
});

// TODO: Implementar visualização de múltiplos versículos
$routes->add('/^([a-zA-Z]+)\/([0-9]?[a-zA-Z]+)\/([0-9]+)\/([0-9]+)-([0-9]+)$/', function ($version, $book, $chapter, $verse1, $verse2) {
  // Implementação futura
});

global $routes;
