<?php

  abstract class DatabaseController
  {
    private static string $host = "localhost";
    private static string $database = "php-project";
    private static string $user = "root";
    private static string $password = "";
    private static mysqli|null $link = null;

    private static function init() {
      self::$link = mysqli_connect(self::$host, self::$user, self::$password, self::$database);
    }

    protected static function query(string $query): mysqli_result|bool {
      if (!isset(self::$link)) {
        self::init();
      }
      return mysqli_query(self::$link, $query);
    }

    protected static function fetchResults(mysqli_result $result): array {
      $rowsCount = mysqli_num_rows($result);
      if ($rowsCount === 0) {
        return [];
      }

      $resultArr = [];
      while ($row = mysqli_fetch_assoc($result)) {
        array_push($resultArr, $row);
      }

      return $resultArr;
    }

    public static function find(string $table, array $conditions, ?array $joinConditions = null): array|null {
      $where = QueryBuilder::formatConditions($table, $conditions);
      $join_result = QueryBuilder::formatJoinConditions($table, $joinConditions);

      $join_needle_columns = [];
      $join_query = "";
      if (isset($join_result)) {
        $join_needle_columns = $join_result["needle_columns"];
        $join_query = $join_result["query"];
      }

      $result_columns = QueryBuilder::formatNeedleColumns($table, $join_needle_columns);
      $query = "SELECT " . $result_columns . " FROM $table " . $join_query . "WHERE " . $where;
      $result = self::query($query);

      return self::fetchResults($result);
    }

    public static function findOne(string $table, array $conditions, ?array $joinConditions = null): array|null {
      $result = self::find($table, $conditions);
      return count($result) > 0 ? $result[0] : $result;
    }

    public static function findAll(string $table): array {
      $query = "SELECT * FROM $table";
      $result = self::query($query);

      return self::fetchResults($result);
    }

    public static function create(string $table, array $params): void {
      $columns = implode(", ", $params[0]);
      $values = implode(", ", $params[1]);
      $values = preg_replace("/([^,\s]+)/", "'$1'", $values);
      $query = "INSERT INTO $table (" . $columns . ") VALUES (" . $values . ")";
      self::query($query);
    }
  }