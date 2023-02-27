<?php
  include "./models/CategoryModel.php";
  include "./models/ProductModel.php";

  class CategoriesController
  {
    public static function getCategoryProducts(array $req, array $res) {
      $category_id = (int) $req["params"]["category_id"];

      if (!isset($category_id)) {
        return $res;
      }

      $joinConditions = [
        [
          "table" => CategoryModel::$tableName,
          "condition" => [
            "join_column" => "id",
            "target_column" => "category_id"
          ],
          "result" => [
            "needle_column" => "name",
            "as" => "category_name",
          ],
        ],
        [
          "table" => BrandModel::$tableName,
          "condition" => [
            "join_column" => "id",
            "target_column" => "brand_id"
          ],
          "result" => [
            "needle_column" => "name",
            "as" => "brand_name",
          ],
        ],
      ];
      $products = ProductModel::find(["category_id" => $category_id], $joinConditions);

      $res["products"] = $products;
      return $res;
    }
  }