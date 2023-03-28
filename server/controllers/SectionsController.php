<?php
  include "./models/SectionModel.php";
  include "./models/CategoryModel.php";

  class SectionsController
  {
    public static function getAllSections(array $req, array $res): array {
      $res["sections"] = QueryBuilder::table("Sections")->get();
      return $res;
    }

    public static function getSection(array $req, array $res): array {
      $id = $req["params"]["id"];

      $res["section"] = QueryBuilder::table("Sections")
        ->where("id", "=", $id)
        ->get()[0];

      return $res;
    }

    public static function getSectionCategories(array $req, array $res): array {
      $section_id = (int) $req["params"]["section_id"];

      $res["categories"] = QueryBuilder::table("Categories")
        ->where("section_id", "=", $section_id)
        ->get();
      return $res;
    }

    public static function createSection(array $req, array $res): array {
      $name = $req["params"]["name"];

      if (!isset($name)) {
        return $res;
      }

      QueryBuilder::table("Sections")->insert([
        ["name" => $name]
      ]);
      $res["section"] = QueryBuilder::table("Sections")
        ->where("name", "=", $name)
        ->get();

      return $res;
    }

    public static function deleteSection(array $req, array $res): array {
      $section_id = $req["params"]["id"];

      self::deleteSectionProducts($section_id);
      QueryBuilder::table("Sections")
        ->where("id", "=", $section_id)
        ->delete();

      return $res;
    }

    private static function deleteSectionProducts(int $section_id) {
      $categories = QueryBuilder::table("Categories")
        ->where("section_id", "=", $section_id)
        ->get();

      foreach ($categories as $category) {
        QueryBuilder::table("Properties")
          ->where("category_id", "=", $category["id"])
          ->delete();

        $products = QueryBuilder::table("Products")
          ->where("category_id", "=", $category["id"])
          ->get();

        foreach ($products as $product) {
          QueryBuilder::table("Product_property_values")
            ->where("product_code", "=", $product["code"])
            ->delete();
          QueryBuilder::table("Visited_products")
            ->where("product_code", "=", $product["code"])
            ->delete();
        }

        QueryBuilder::table("Products")
          ->where("category_id", "=", $category["id"])
          ->delete();
      }

      QueryBuilder::table("Categories")
        ->where("section_id", "=", $section_id)
        ->delete();
    }
  }