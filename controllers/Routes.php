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
      if ($route['method'] === $method &&  preg_match($route['route'], $url, $params)) {
        array_shift($params);
        return call_user_func_array($route['callback'], $params);
      }
    }

    echo "404";
  }
}

$routes = new Routes();

// Rota de pesquisa. Ex: /search
$routes->add('/^\/search$/', function () {
  $searchController = new SearchController();
  $searchController->search();
});

// Rota Home
$routes->add('/^\/$/', function () {
  $homeController = new HomeController();
  $homeController->index('ARA');
});

// Rota tradução: Ex: ara
$routes->add('/^([a-z]+)$/', function (String $versionAcronym) {
  $homeController = new HomeController();
  $homeController->index($versionAcronym);
});

// Rota de acesso ao livro. Ex: ara/gn, arc/2sm
$routes->add('/^([a-z]+)\/([a-z]+)$/', function ($version, $bookAcronym) {
  $chaptersController = new ChaptersController();
  $chaptersController->index($version, $bookAcronym);
});

// Rota de acesso ao capítulo. Ex: ara/gn/1
$routes->add('/^([a-z]+)\/([\da-z]+)\/([0-9]+)$/', function ($versionAcronym, $bookAcronym, $chapterNumber) {
  $chapterNumber = (int) $chapterNumber;
  $versesController = new VersesController();
  $versesController->index($versionAcronym, $bookAcronym, $chapterNumber);
});

// Rota de acesso a biblia. Ex: ara/gn/1/1
$routes->add('/^([a-z]+)\/([a-z]+)\/([0-9]+)\/([0-9]+)$/', function ($version, $book, $chapter, $verse) {
  // echo "Versão: " . $version . "<br>";
  // echo "Livro: " . $book . "<br>";
  // echo "Capítulo: " . $chapter . "<br>";
  // echo "Versículo: " . $verse . "<br>";

  // $homeController = new HomeController();
  // $homeController->index();
});

// Rota de acesso a mais de uma versão. Ex: ara+ara/gn/1/1
$routes->add('/^([a-z]+)\+([a-z]+)\/([a-z]+)\/([0-9]+)\/([0-9]+)$/', function ($version1, $version2, $book, $chapter, $verse) {
  // echo "Versão 1: " . $version1 . "<br>";
  // echo "Versão 2: " . $version2 . "<br>";
  // echo "Livro: " . $book . "<br>";
  // echo "Capítulo: " . $chapter . "<br>";
  // echo "Versículo: " . $verse . "<br>";

  // $homeController = new HomeController();
  // $homeController->index();
});

// Rota de acesso a mais de um versículo. Ex: ara/gn/1/1-3
$routes->add('/^([a-z]+)\/([a-z]+)\/([0-9]+)\/([0-9]+)-([0-9]+)$/', function ($version, $book, $chapter, $verse1, $verse2) {
  // echo "Versão: " . $version . "<br>";
  // echo "Livro: " . $book . "<br>";
  // echo "Capítulo: " . $chapter . "<br>";
  // echo "Versículo 1: " . $verse1 . "<br>";
  // echo "Versículo 2: " . $verse2 . "<br>";

  // $homeController = new HomeController();
  // $homeController->index();
});

global $routes;
