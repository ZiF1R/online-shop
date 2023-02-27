<?php

  class DatabaseController
  {
      private static string $host = "localhost";
      private static string $database = "php-project";
      private static string $user = "root";
      private static string $password = "";
      private static mysqli|null $link = null;

      private static function init() {
        self::$link = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
      }

      public static function query(string $query): mysqli_result|bool {
        if (!isset(self::$link)) {
          self::init();
        }
        return mysqli_query(self::$link, $query);
      }
  }