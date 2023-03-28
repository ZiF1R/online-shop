<?php
  class QueryBuilder extends DatabaseController
  {
    private string $table = "";
    private string $top = "";
    private string $columns = "*";
    private string $join = "";
    private string $where = "";
    private string $orderBy = "";
    private string $groupBy = "";
    private string $limit = "";
    private string $query = "";

    public static function table(string $table): QueryBuilder {
      return new QueryBuilder($table);
    }

    private static function setColumnDefaultValue(string $column, $defaultValue) {
      return "COALESCE($column, $defaultValue)";
    }

    private function __construct($table) {
      $this->table = $table;
    }

    public function getSelectQuery(): string {
      $params = [
        "SELECT", $this->top, $this->columns, "FROM", $this->table,
        $this->join, $this->where, $this->groupBy, $this->orderBy, $this->limit
      ];
      $this->query = implode(" ", $params);
      return $this->query;
    }

    public function getDeleteQuery(): string {
      $params = [
        "DELETE", "FROM", $this->table,
        $this->where
      ];
      $this->query = implode(" ", $params);
      return $this->query;
    }

    public function getInsertQuery(string $columns, string $values): string {
      $this->query = implode(" ", [
        "INSERT INTO", $this->table, "(", $columns, ") VALUES", $values
      ]);
      return $this->query;
    }

    public function getUpdateQuery($columns): string {
      $params = [
        "UPDATE", $this->table, "SET",
        $columns, $this->where
      ];
      $this->query = implode(" ", $params);
      return $this->query;
    }

    private function setColumns(array $columns) {
      $this->columns = implode(", ", $columns);
    }

    private function setWhere(string $column, string $operator, string|null $value) {
      $val = isset($value) ? "'$value'" : 'NULL';
      $condition = implode(" ", [$column, $operator, $val]);
      if (strlen($this->where) === 0) {
        $this->where = "WHERE " . $condition;
      } else {
        $this->where .= " AND " . $condition;
      }
    }

    private function setJoin(string $table, string $targetTableColumn, string $joinTableColumn, string $joinType) {
      $params = [
        " $joinType", $table, "on", $targetTableColumn,
        "=", $joinTableColumn
      ];
      $this->join .= implode(" ", $params);
    }

    private function setOrderBy(string $column, string $order) {
      if (strlen($this->orderBy) === 0) {
        $this->orderBy = "ORDER BY " . $column . " " . $order;
      } else {
        $this->orderBy .= ", " . $column . " " . $order;
      }
    }

    private function setGroupBy(string $column) {
      if (strlen($this->groupBy) === 0) {
        $this->groupBy = "GROUP BY " . $column;
      } else {
        $this->groupBy .= ", " . $column;
      }
    }

    private function splitColumnsAndValues(array $data) {
      $columns = [];
      $values = [];

      $i = 0;
      $filled_columns = false;
      foreach ($data as $row) {
        $values[$i] = [];
        foreach ($row as $column => $value) {
          if (!$filled_columns) {
            $columns[] = $column;
          }
          $values[$i][] = "'$value'";
        }
        $filled_columns = true;
        $values[$i] = implode(", ", $values[$i]);
        $i++;
      }

      $values = "(" . implode("), (", $values) . ")";
      $columns = implode(", ", $columns);

      return [
        "columns" => $columns,
        "values" => $values,
      ];
    }

    public function select(array $columns): QueryBuilder {
      $this->setColumns($columns);
      return $this;
    }

    public function insert(array $rows) {
      $result = self::splitColumnsAndValues($rows);
      $columns = $result["columns"];
      $values = $result["values"];
      parent::query($this->getInsertQuery($columns, $values));
    }

    public function get(): array {
      $result = parent::query($this->getSelectQuery());
      return parent::fetchResults($result);
    }

    public function delete() {
      parent::query($this->getDeleteQuery());
    }

    public function update(array $columns) {
      $changes = [];
      foreach ($columns as $column => $value) {
        $changes[] = "$column = '$value'";
      }
      $changes = implode(", ", $changes);
      parent::query($this->getUpdateQuery($changes));
    }

    public function where(string $column, string $operator, string|null $value): QueryBuilder {
      $this->setWhere($column, $operator, $value);
      return $this;
    }

    public function rawWhere(string $where): QueryBuilder {
      if (strlen($this->where) === 0) {
        $this->where = "WHERE " . $where;
      } else {
        $this->where .= " AND " . $where;
      }
      return $this;
    }

    public function join(string $table, string $targetTableColumn, string $joinTableColumn, string $joinType = "JOIN"): QueryBuilder {
      $this->setJoin($table, $targetTableColumn, $joinTableColumn, $joinType);
      return $this;
    }

    public function leftJoin(string $table, string $targetTableColumn, string $joinTableColumn): QueryBuilder {
      return $this->join($table, $targetTableColumn, $joinTableColumn, "LEFT JOIN");
    }

    public function rightJoin(string $table, string $targetTableColumn, string $joinTableColumn): QueryBuilder {
      return $this->join($table, $targetTableColumn, $joinTableColumn, "RIGHT JOIN");
    }

    public function orderBy(string $column, string $order): QueryBuilder {
      $this->setOrderBy($column, $order);
      return $this;
    }

    public function groupBy(string $column): QueryBuilder {
      $this->setGroupBy($column);
      return $this;
    }

    public function avg(string $column, string $alias = "avg", $defaultValue = null): QueryBuilder {
      if (isset($defaultValue)) {
        $default = self::setColumnDefaultValue("AVG($column)", $defaultValue);
        $this->columns .= ", $default as $alias";
      } else if ($this->columns === "*") {
        $this->columns = "AVG($column) as $alias";
      } else {
        $this->columns .= ", AVG($column) as $alias";
      }
      return $this;
    }

    public function limit(int $number): QueryBuilder {
      $this->limit = "LIMIT $number";
      return $this;
    }

    public function count(string $column, string $alias = "count"): QueryBuilder {
      $this->columns .= ", count($column) as $alias";
      return $this;
    }

    public function top(int $number): QueryBuilder {
      $this->top = "TOP($number)";
      return $this;
    }

    public function min(string $column, string $alias = "min"): QueryBuilder {
      if ($this->columns === "*") {
        $this->columns = "min(CONVERT($column, SIGNED)) as $alias";
      } else {
        $this->columns .= ", min(CONVERT($column, SIGNED)) as $alias";
      }
      return $this;
    }

    public function max(string $column, string $alias = "max"): QueryBuilder {
      if ($this->columns === "*") {
        $this->columns = "max(CONVERT($column, SIGNED)) as $alias";
      } else {
        $this->columns .= ", max(CONVERT($column, SIGNED)) as $alias";
      }
      return $this;
    }

    public function sum(string $condition, string $alias = "sum"): QueryBuilder {
      if ($this->columns === "*") {
        $this->columns = "sum($condition) as $alias";
      } else {
        $this->columns .= ", sum($condition) as $alias";
      }
      return $this;
    }
  }