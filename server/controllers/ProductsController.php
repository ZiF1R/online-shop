<?php
  include "./models/ProductModel.php";
  include "./models/CategoryModel.php";
  include "./models/BrandModel.php";

  class ProductsController
  {
    // get products by searching  -> name=pattern
    // get products of category (full info)  -> category_id=0 then join to product_feedback and product_properties tables
    // get products applying filter conditions  -> column=pattern (include filtering like in getting products of category)
    // get products of category with order (maybe including filter)  -> (include filtering like in getting products of category) apply sorting to query
    // get full info of specific product  -> join to product_feedback and product_properties tables
    // get user visited products  -> join to visited_products table / find product in visited_products table by condition
    // get user favourite products  -> join to favourite_products table / find product in favourite_products table by condition

    // TODO: unified query builder class

    public static function getProducts(array $req, array $res): array {
      if (!isset($req["params"]["search"])) {
        return ProductModel::findAll();
      } else if ($req["params"]["search"] === "") {
        return [];
      }
      $search = (string) $req["params"]["search"];

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
      $products = ProductModel::find(["name" => $search], $joinConditions) ?? [];

      $res["products"] = $products;
      return $res;
    }
  }