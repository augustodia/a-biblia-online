<?php

abstract class BaseController
{
  protected $data = [];
  protected $view;
  protected $model;

  public function __construct()
  {
    $this->initializeSession();
    $this->checkCSRF();
  }

  protected function loadModel($model)
  {
    $modelName = ucfirst($model) . 'Model';
    require_once 'models/' . $modelName . '.php';
    $this->model = new $modelName();
  }

  protected function loadView($view, $data = [])
  {
    $this->data = array_merge($this->data, $data);
    require_once 'views/' . $view . '.php';
  }

  protected function loadTemplate($view, $data = [])
  {
    $this->view = $view;
    $this->data = array_merge($this->data, $data);
    
    // Adiciona token CSRF aos dados
    $this->data['csrf_token'] = $this->generateCSRFToken();
    
    require_once 'templates/template.php';
  }

  protected function redirect($url)
  {
    header('Location: ' . BASE_URL . $url);
    exit;
  }

  protected function json($data, $statusCode = 200)
  {
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
  }

  private function initializeSession()
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_name(SESSION_NAME);
      session_start();
    }
  }

  protected function generateCSRFToken()
  {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
      $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
  }

  protected function checkCSRF()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
        (!isset($_POST[CSRF_TOKEN_NAME]) ||
         !isset($_SESSION[CSRF_TOKEN_NAME]) ||
         !hash_equals($_SESSION[CSRF_TOKEN_NAME], $_POST[CSRF_TOKEN_NAME]))) {
      $this->json(['error' => 'Token CSRF inválido'], 403);
    }
  }

  protected function validateInput($data, $rules)
  {
    $errors = [];
    foreach ($rules as $field => $rule) {
      if (!isset($data[$field]) || !preg_match($rule, $data[$field])) {
        $errors[$field] = 'Campo inválido';
      }
    }
    return $errors;
  }

  protected function logError($message, $context = [])
  {
    if (LOG_ERRORS) {
      $logMessage = date('Y-m-d H:i:s') . ' - ' . $message . ' - ' . json_encode($context) . PHP_EOL;
      error_log($logMessage, 3, LOG_PATH . '/error.log');
    }
  }
}
