<?php
  class CategoryModel extends BaseModel implements IModel
  {
    public static string $tableName = "Categories";
    private int $id;
    private int $section_id;
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