<?php
  class SectionModel implements IModel
  {
    public static string $tableName = "Sections";
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

    public static function create(string $name): SectionModel {
      $values = [["name"], [$name]];
      $section = self::findOne(["name" => $name]);

      if (count($section) > 0) {
        return new SectionModel($section["id"], $section["name"]);
      }

      DatabaseController::create(self::$tableName, $values);
      $new_section = self::findOne(["name" => $name]);
      return new SectionModel($new_section["id"], $new_section["name"]);
    }

    private function __construct(int $id, string $name)
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

    public function getSectionObject(): array {
      return [
        "id" => $this->getId(),
        "name" => $this->getName(),
      ];
    }
  }