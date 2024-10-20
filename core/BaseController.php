<?php

abstract class BaseController
{
  protected $view;
  protected $model;

  public function __construct()
  {
    $this->view = new stdClass();
  }

  protected function loadModel($model)
  {
    $modelName = ucfirst($model) . 'Model';
    require_once 'models/' . $modelName . '.php';
    $this->model = new $modelName();
  }

  protected function loadView($view, $data = [])
  {
    extract($data);
    require_once 'views/' . $view . '.php';
  }

  protected function loadTemplate($view, $data = [])
  {
    require_once 'templates/template.php';
  }

  // protected function loadViewInTemplate($view, $data = []) {
  //   extract($data);
  //   require_once 'views/'.$view .'.php';
  // }
}
