<?php
  class ProductModel implements IModel
  {
    public static string $tableName = "Products";
    private int $code;
    private int $category_id;
    private int $brand_id;
    private string $name;
    private float $price;
    private ?string $description;
    private int $count;
    private ?string $photo_link;
    private ?int $discount;

    public static function find(array $conditions, ?array $joinConditions = null): array|null {
      return DatabaseController::find(self::$tableName, $conditions, $joinConditions);
    }

    public static function findOne(array $conditions, ?array $joinConditions = null): array|null {
      return DatabaseController::findOne(self::$tableName, $conditions, $joinConditions);
    }

    public static function findAll(): array {
      return DatabaseController::findAll(self::$tableName);
    }

    private function __construct(int $code, int $category_id, int $brand_id, string $name, float $price, ?string $description, int $count, ?string $photo_link, ?int $discount) {
      $this->code = $code;
      $this->category_id = $category_id;
      $this->brand_id = $brand_id;
      $this->name = $name;
      $this->price = $price;
      $this->description = $description;
      $this->count = $count;
      $this->photo_link = $photo_link;
      $this->discount = $discount;
    }

    public function getCode(): int
    {
      return $this->code;
    }

    public function getCategoryId(): int
    {
      return $this->category_id;
    }

    public function getBrandId(): int
    {
      return $this->brand_id;
    }

    public function getName(): string
    {
      return $this->name;
    }

    public function getPrice(): float
    {
      return $this->price;
    }

    public function getDescription(): ?string
    {
      return $this->description;
    }

    public function getCount(): int
    {
      return $this->count;
    }

    public function getPhotoLink(): ?string
    {
      return $this->photo_link;
    }

    public function getDiscount(): ?int
    {
      return $this->discount;
    }

    private function getProductObject(): array {
      return [
        "code" => $this->getCode(),
        "category_id" => $this->getCategoryId(),
        "brand_id" => $this->getBrandId(),
        "name" => $this->getName(),
        "price" => $this->getPrice(),
        "description" => $this->getDescription(),
        "count" => $this->getCount(),
        "photo_link" => $this->getPhotoLink(),
        "discount" => $this->getDiscount(),
      ];
    }
  }