<?php

class Core {
  private $routes;

  public function __construct($routes) {
    $this->routes = $routes;
  }

  public function start() {
    // $url = '/';

    // if(isset($_GET['url'])) {
    //   $url .= $_GET['url'];
    // }

    $this->routes->submit();
  }
}
?>
