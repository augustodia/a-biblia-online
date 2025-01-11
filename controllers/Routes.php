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

    // // Debug detalhado
    // echo "URL recebida: '" . $url . "'<br>";
    // echo "Método: " . $method . "<br>";
    // echo "GET params: ";
    // var_dump($_GET);
    // echo "<br>";
    
    foreach ($this->routes as $route) {
      // echo "Tentando rota: " . $route['route'] . "<br>";
      if ($route['method'] === $method && preg_match($route['route'], $url, $params)) {
        array_shift($params);
        return call_user_func_array($route['callback'], $params);
      }
    }

    // echo "404 - Rota não encontrada: " . $url;
  }
}

$routes = new Routes();
// Rota Home
$routes->add('/^\/$/', function () {
  $homeController = new HomeController();
  $homeController->index('ARA');
});

// Rota de busca - Aceita maiúsculas e minúsculas
$routes->add('/^search\/([a-zA-Z]+).*$/', function ($versionAcronym) {
  $searchController = new SearchController();
  $searchController->index($versionAcronym);
});

// Rota tradução: Ex: ara, ARA
$routes->add('/^([a-zA-Z]+)$/', function (String $versionAcronym) {
  $homeController = new HomeController();
  $homeController->index($versionAcronym);
});

// Rota de acesso ao livro. Ex: ara/gn, ARA/GN, ara/1tm
$routes->add('/^([a-zA-Z]+)\/([0-9]?[a-zA-Z]+)$/', function ($version, $bookAcronym) {
  $chaptersController = new ChaptersController();
  $chaptersController->index($version, $bookAcronym);
});

// Rota de acesso ao capítulo. Ex: ara/gn/1, ARA/GN/1, ara/1tm/1
$routes->add('/^([a-zA-Z]+)\/([0-9]?[a-zA-Z]+)\/([0-9]+)$/', function ($versionAcronym, $bookAcronym, $chapterNumber) {
  $chapterNumber = (int) $chapterNumber;
  $versesController = new VersesController();
  $versesController->index($versionAcronym, $bookAcronym, $chapterNumber);
});

// Rota de acesso ao versículo. Ex: ara/gn/1/1, ara/1tm/1/1
$routes->add('/^([a-zA-Z]+)\/([0-9]?[a-zA-Z]+)\/([0-9]+)\/([0-9]+)$/', function ($version, $book, $chapter, $verse) {
  $verseController = new VerseController();
  $verseController->show($version, $book, $chapter, $verse);
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

// Rota de acesso a mais de uma versão. Ex: ara+ara/gn/1/1, ara+ara/1tm/1/1
$routes->add('/^([a-zA-Z]+)\+([a-zA-Z]+)\/([0-9]?[a-zA-Z]+)\/([0-9]+)\/([0-9]+)$/', function ($version1, $version2, $book, $chapter, $verse) {
  // echo "Versão 1: " . $version1 . "<br>";
  // echo "Versão 2: " . $version2 . "<br>";
  // echo "Livro: " . $book . "<br>";
  // echo "Capítulo: " . $chapter . "<br>";
  // echo "Versículo: " . $verse . "<br>";

  // $homeController = new HomeController();
  // $homeController->index();
});

// Rota de acesso a mais de um versículo. Ex: ara/gn/1/1-3, ara/1tm/1/1-3
$routes->add('/^([a-zA-Z]+)\/([0-9]?[a-zA-Z]+)\/([0-9]+)\/([0-9]+)-([0-9]+)$/', function ($version, $book, $chapter, $verse1, $verse2) {
  // echo "Versão: " . $version . "<br>";
  // echo "Livro: " . $book . "<br>";
  // echo "Capítulo: " . $chapter . "<br>";
  // echo "Versículo 1: " . $verse1 . "<br>";
  // echo "Versículo 2: " . $verse2 . "<br>";

  // $homeController = new HomeController();
  // $homeController->index();
});

global $routes;
