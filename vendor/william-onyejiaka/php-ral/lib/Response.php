<?php
declare(strict_types=1);

namespace Lib;

ini_set('display_errors', 1);
class Response
{
  private $response_data;

  public function __construct()
  {

  }

  public function send_response1(int $response_code, array $response_data): void
  {
    $this->set_response_data($response_data);
    http_response_code($response_code);
    echo json_encode($this->create_response_array());
    exit();
  }

  public static function check_array(array $array)
  {
    return ((array() == $array) || array_keys($array) == range(0, count($array) - 1)) ? false : true;
  }

  public function json(array $response_data, int $response_code)
  {
    if (Response::check_array($response_data)) {
      http_response_code($response_code);
      echo json_encode($response_data);
      exit();
    }
    http_response_code(500);
    echo json_encode([
      'error' => true,
      'message' => "associative array needed"
    ]);
    exit();
  }

  private function set_response_data(array $response_data)
  {
    $this->response_data = $response_data;
  }

  private function create_response_array()
  {
    $response_array = [];
    foreach ($this->response_data as $value) {
      $response_array[$value[0]] = $value[1];
    }
    return $response_array;
  }

  public function send_error_response(int $error_code, $message)
  {
    $this->json([
      'error' => true,
      'message' => $message
    ], $error_code);
  }

  public function get_header($key)
  {
    return (getallheaders())[$key] ?? null;
  }

  public function render(string $htmlPath, array $data = [], string $basePath = "./../static/templates/")
  {
    header("Content-Type: text/html");

    ob_start();

    include_once $basePath . $htmlPath;

    $html_content = ob_get_clean();
    echo $html_content;
    exit();
  }

}