<?php
  abstract class BaseModel
  {
    protected static function formatConditions(string $table, array $conditions): string {
      $statements = [];
      foreach ($conditions as $column => $value) {
        array_push($statements, "$table.`$column` LIKE " . "'%$value%'");
      }

      return implode(" AND ", $statements);
    }

    protected static function formatJoinConditions(string $target_table, array $conditions): array|null {
      if (!isset($conditions)) {
        return null;
      }

      $result_query = "";
      $needle_columns = [];
      foreach ($conditions as $condition) {
        $table = $condition["table"];
        $on = $condition["condition"];
        $result = $condition["result"];

        $target = "$target_table.{$on['target_column']}";
        $join = "$table.{$on['join_column']}";
        $result_query .= "JOIN $table on $target = $join ";
        array_push(
          $needle_columns,
          [
            "$table.{$result['needle_column']}",
            $result['as']
          ]
        );
      }

      return [
        "query" => $result_query,
        "needle_columns" => $needle_columns,
      ];
    }

    protected static function formatNeedleColumns(string $table, array $join_needle_columns): string {
      $result = "$table.* ";
      foreach ($join_needle_columns as $column) {
        $col = $column[0];
        $alias = $column[1];
        $result .= ", $col as $alias";
      }

      return $result;
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

    protected static function _find(string $table, array $conditions, ?array $joinConditions = null): array|null {
      $where = self::formatConditions($table, $conditions);
      $join_result = self::formatJoinConditions($table, $joinConditions);

      $join_needle_columns = [];
      $join_query = "";
      if (isset($join_result)) {
        $join_needle_columns = $join_result["needle_columns"];
        $join_query = $join_result["query"];
      }

      $result_columns = self::formatNeedleColumns($table, $join_needle_columns);
      $query = "SELECT " . $result_columns . " FROM $table " . $join_query . "WHERE " . $where;
      $result = DatabaseController::query($query);

      return self::fetchResults($result);
    }

    protected static function _findOne(string $table, array $conditions, ?array $joinConditions = null): array|null {
      $result = self::_find($table, $conditions);
      return count($result) > 0 ? $result[0] : $result;
    }

    protected static function _findAll(string $table): array {
      $query = "SELECT * FROM $table";
      $result = DatabaseController::query($query);

      return self::fetchResults($result);
    }

    protected static function _create(string $table, array $params): void {
      $columns = implode(", ", $params[0]);
      $values = implode(", ", $params[1]);
      $values = preg_replace("/([^,\s]+)/", "'$1'", $values);
      $query = "INSERT INTO $table (" . $columns . ") VALUES (" . $values . ")";
      DatabaseController::query($query);
    }
  }