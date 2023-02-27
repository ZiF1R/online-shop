<?php
  interface IRoute {
    public static function get(string $route, string $callback);
    public static function post(string $route, string $callback);
    public static function put(string $route, string $callback);
    public static function delete(string $route, string $callback);
  }