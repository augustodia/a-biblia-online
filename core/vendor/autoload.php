<?php
class Autoload {
  public function __construct() {
    spl_autoload_register(array($this, 'loader'));
  }

  private function loader($className) {
    if(file_exists('controllers/'.$className.'.php')) {
      require_once 'controllers/'.$className.'.php';
    } else if(file_exists('models/'.$className.'.php')) {
      require_once 'models/'.$className.'.php';
    } else if(file_exists('core/'.$className.'.php')) {
      require_once 'core/'.$className.'.php';
    }
  }
}

new Autoload();
?>