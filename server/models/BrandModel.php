<?php
  class BrandModel extends BaseModel implements IModel
  {
    public static string $tableName = "Brands";
    private int $id;
    private string $name;

    public static function find(array $conditions, ?array $joinConditions = null): array|null {
      return parent::_find(self::$tableName, $conditions, $joinConditions);
    }

    public static function findOne(array $conditions, ?array $joinConditions = null): array|null {
      return parent::_findOne(self::$tableName, $conditions, $joinConditions);
    }

    public static function findAll(): array {
      return parent::_findAll(self::$tableName);
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