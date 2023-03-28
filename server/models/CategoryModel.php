<?php
  class CategoryModel implements IModel
  {
    public static string $tableName = "Categories";
    private int $id;
    private int $section_id;
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

    public function __construct(int $section_id, string $name)
    {
      $this->name = $name;
      $this->section_id = $section_id;
    }

    public function getId(): int
    {
      return $this->id;
    }

    public function getSectionId(): int
    {
      return $this->section_id;
    }

    public function getName(): string
    {
      return $this->name;
    }
  }