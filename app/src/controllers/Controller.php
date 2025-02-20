<?php

namespace App\Controllers;

/**
 * Class Controller
 * 
 * This class is the parent class for all controllers.
*/
class Controller {
  protected array $params;
  protected string $reqMethod;
  protected array $body;
  protected string $className;

  /**
    * Controller constructor.
    * 
    * @param array $params Parameters passed to the controller.
  */
  public function __construct($params) {
    $this->className = $this->getCallerClassName();
    $this->params = $params;
    $this->reqMethod = strtolower($_SERVER['REQUEST_METHOD']);
    $this->body = (array) json_decode(file_get_contents('php://input'));

    $this->header();
  }

  /**
    * Get the name of the calling class.
    * 
    * @return string The name of the calling class.
  */
  protected function getCallerClassName() {
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);

    if (isset($backtrace[1]['object'])) {
      $fullClassName = get_class($backtrace[1]['object']);
      $className = basename(str_replace('\\', '/', $fullClassName));

      return $className;
    }

    return 'Unknown';
  }

  /**
    * Set the HTTP headers for the response.
  */
  protected function header() {
    header('Access-Control-Allow-Origin: *');
    header('Content-type: application/json; charset=utf-8');
  }
}