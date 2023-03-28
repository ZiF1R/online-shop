<?php
  class BrandModel implements IModel
  {
    public static string $tableName = "Brands";
    private int $id;
    private string $name;

    public static function find(array $conditions, ?array $joinConditions = null): array|null {
      return DatabaseController::find(self::$tableName, $conditions, $joinConditions);
    }

    public static function findOne(array $conditions, ?array $joinConditions = null): array|null {
      return DatabaseController::findOne(self::$tableName, $conditions, $joinConditions);
    }

    public static function findAll(): array {
      return DatabaseController::findAll(self::$tableName);
    }

    public function __construct(int $id, string $name)
    {
      $this->id = $id;
      $this->name = $name;
    }

    public function getId(): int
    {
      return $this->id;
    }

    public function getName(): string
    {
      return $this->name;
    }
  }