<?php
  include "./interfaces/IRoute.php";

  class Route implements IRoute
  {
    private static function isMatchRoute(string $route): bool {
      if (!isset($_GET["request_url"])) {
        exit();
      }

      if (self::getRouteParams($route) === null) {
        return false;
      }

      return true;
    }

    private static function isValidCallback($callback): bool {
//      return is_callable($callback);
      return true;
    }

    private static function getRouteParams(string $route): array | null {
      if (!isset($_GET["request_url"])) {
        exit();
      }
      $url = $_GET["request_url"];
      $splittedRoute = array_slice(explode("/", $route), 1);
      $splittedUrl = array_slice(explode("/", $url), 1);

      if (str_starts_with($_GET["request_url"], "/products/") && str_contains($_GET["request_url"], "?product=") && $route === "/products/{code}") {
        $len = count($splittedUrl) - 1;
        $splittedUrl[$len - 1] .= $splittedUrl[$len];
        $splittedUrl = array_slice($splittedUrl, $len - 1);
      }
      if (count($splittedUrl) !== count($splittedRoute)) {
        return null;
      }

      $queryParams = self::getUrlSearchParams($url);
      $_GET = array_merge($_GET, $queryParams);

      if (str_starts_with($_GET["request_url"], "/products/") && str_contains($_GET["request_url"], "?product=") && $route === "/products/{code}") {
        return [];
      }

      $params = [];
      foreach ($splittedRoute as $i => $value) {
        $urlPart = $splittedUrl[$i];
        $urlPart = preg_replace("/\?.*$/", "", $urlPart);
        if (str_contains($value, "{")) {
          $param = preg_replace("/[{}]/", "", $value);
          $params[$param] = $urlPart;
        } else if ($value !== $urlPart) {
          return null;
        }
      }

      return $params;
    }

    private static function getUrlSearchParams(string $url): array {
      $params = parse_url($url, PHP_URL_QUERY);
      if (!isset($params)) {
        return [];
      }

      $params = explode("&", $params);

      $result = [];
      foreach ($params as $param) {
        list($key, $value) = explode("=", $param);
        $result[$key] = $value;
      }

      return $result;
    }

    private static function executeCallback(string $route, string $callback, $_PUT = [], $_DELETE = []): void {
      $res = [];
      $req = [
        "params" => array_merge(self::getRouteParams($route), $_GET, $_POST, $_DELETE, $_PUT)
      ];

      list($class, $method) = explode("::", $callback);
      include "./controllers/{$class}.php";
      echo json_encode(call_user_func_array([$class, $method], [$req, $res]), JSON_UNESCAPED_UNICODE);
      die();
    }

    public static function get(string $route, string $callback): void {
      if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !self::isMatchRoute($route) || count($_POST) > 0) {
        return;
      }

      self::executeCallback($route, $callback);
    }

    public static function post(string $route, string $callback): void {
      if (count($_POST) === 0 || !self::isMatchRoute($route)) {
        return;
      }

      $_POST = self::parsePostedData($_POST);
      self::executeCallback($route, $callback);
    }

    private static function parsePostedData($data) {
      foreach ($data as $obj => $v) {
        foreach (json_decode($obj) as $key => $value) {
          $data[$key] = $value;
        }
        unset($data[$obj]);
      }

      return $data;
    }

    public static function put(string $route, string $callback): void {
      parse_str(file_get_contents('php://input'), $_PUT);
      if ($_SERVER['REQUEST_METHOD'] !== 'PUT' || !self::isMatchRoute($route) || count($_PUT) === 0) {
        return;
      }

      self::executeCallback($route, $callback, $_PUT=$_PUT);
    }

    public static function delete(string $route, string $callback): void {
      parse_str(file_get_contents('php://input'), $_DELETE);
      if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' || !self::isMatchRoute($route)) {
        return;
      }

      $_DELETE = self::parsePostedData($_DELETE);
      self::executeCallback($route, $callback, $_DELETE=$_DELETE);
    }
  }