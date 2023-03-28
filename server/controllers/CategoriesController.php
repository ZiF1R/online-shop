<?php
  include "./models/CategoryModel.php";
  include "./models/ProductModel.php";

  class CategoriesController
  {
    public static function createCategory(array $req, array $res): array {
      $name = $req["params"]["name"];
      $section_id = $req["params"]["section_id"];

      if (!isset($name)) {
        return $res;
      }

      QueryBuilder::table("Categories")->insert([
        [
          "name" => $name,
          "section_id" => $section_id,
        ]
      ]);
      $res["category"] = QueryBuilder::table("Categories")
        ->where("name", "=", $name)
        ->get();

      return $res;
    }

    public static function deleteCategory(array $req, array $res): array {
      $id = $req["params"]["id"];

      $category = QueryBuilder::table("Categories")
        ->where("id", "=", $id)
        ->get()[0];

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

      QueryBuilder::table("Categories")
        ->where("id", "=", $id)
        ->delete();

      return $res;
    }

    public static function getCategoryProducts(array $req, array $res): array {
      $category_id = (int) $req["params"]["category_id"];

      $query = QueryBuilder::table("Products")
        ->select(["Products.*"])
        ->where("Products.category_id", "=", $category_id)
        ->leftJoin("Product_feedback", "Products.code", "Product_feedback.product_code")
        ->avg("Product_feedback.rating", "rating", 0)
        ->count("Product_feedback.rating", "rates_count")
        ->groupBy("Products.code");

      $filters = "";
      $order = "";
      $orderBy = "";
      if (isset($req["params"]["order"]) && strlen($req["params"]["order"]) > 0) {
        $order = $req["params"]["order"];
      }

      if (isset($req["params"]["orderBy"]) && strlen($req["params"]["orderBy"]) > 0) {
        $orderBy = $req["params"]["orderBy"];
        $query->orderBy($orderBy, $order);
      }

      $products = $query->get();

      $filteredProducts = [];
      foreach ($products as $i => $product) {
        $product_code = $product["code"];
        $q = QueryBuilder::table("Properties")
          ->select(["name", "designation", "value"])
          ->join("Product_property_values", "name", "property_name and product_code = $product_code")
          ->where("category_id", "=", $category_id);

        $products[$i]["properties"] = $q->get();

        if (isset($req["params"]["filters"]) && strlen($req["params"]["filters"]) > 0) {
          $filters = json_decode($req["params"]["filters"]);
          $p = $product;
          $p["properties"] = $q->get();
          if (self::isMatchFilters($p["properties"], $filters)) {
            $filteredProducts[] = $products[$i];
          }
        }
      }

      if (isset($req["params"]["filters"]) && strlen($req["params"]["filters"]) > 0) {
        $res["products"] = $filteredProducts;
      } else {
        $res["products"] = $products;
      }

      return $res;
    }

    public static function getCategory(array $req, array $res): array {
      $category_id = $req["params"]["category_id"];
      $category = QueryBuilder::table("Categories")
        ->where("id", "=", $category_id)
        ->get();

      $res["category"] = $category;
      if (count($category) === 1) {
        $res["category"] = $category[0];
      }

      return $res;
    }

    public static function getCategoryFilters(array $req, array $res): array {
      $category_id = $req["params"]["category_id"];
      $properties = self::getCategoryProperties($category_id);

      $res["filters"] = [];
      foreach ($properties as $property) {
        $values = self::getPropertyValues($category_id, $property);

        $res["filters"][] = [
          "property" => $property,
          "values" => $values,
        ];
      }

      return $res;
    }

    private static function getFilterByName($filters, $name) {
      foreach ($filters as $filter => $value) {
        if ($filter === $name) {
          return $value;
        }
      }
    }

    private static function isMatchFilters($product_properties, $filters): bool {
      foreach ($product_properties as $property) {
        $name = $property["name"];
        $prop_value = $property["value"];

        $prop_filters = self::getFilterByName($filters, $name);

        $range_values = [];
        if (!is_array($prop_filters)) {
          foreach ($prop_filters as $key => $item) {
            if (gettype($item) !== "string") {
              $range_values[$key] = $item;
            }
          }
        }

        if (is_array($prop_filters) && count($prop_filters) === 0) {
          continue;
        } else if (!is_array($prop_filters) && count($range_values) === 0) {
          continue;
        }

        $isMatch = false;
        $from = -999999;
        $to = 999999;
        if (!is_array($prop_filters)) {
          if (isset($range_values['from']) && gettype($range_values['from']) !== "string") {
            $from = $range_values['from'];
            if (isset($range_values['to']) && gettype($range_values['to']) !== "string") {
              $to = $range_values['to'];
            }
          } else {
            $to = $range_values['to'];
          }
        }


        if (is_array($prop_filters)) {
          $isMatch = in_array($prop_value, $prop_filters);
        } else {
          $isMatch = (int)$prop_value >= $from && (int)$prop_value <= $to;
        }

        if (!$isMatch) {
          return false;
        }
      }

      return true;
    }

    private static function getCategoryProperties(int $category_id): array {
      return QueryBuilder::table("Properties")
        ->where("category_id", "=", $category_id)
        ->get();
    }

    private static function getPropertyValues(int $category_id, array $property): array {
      $query = QueryBuilder::table("Properties")
        ->select(["Properties.name"])
        ->where("Properties.category_id", "=", $category_id)
        ->where("Products.category_id", "=", $category_id)
        ->where("Properties.name", "=", $property["name"])
        ->join("Product_property_values", "Properties.name", "Product_property_values.property_name")
        ->join("Products", "Product_property_values.product_code", "Products.code");

      if ($property["type"] == "Number") {
        $result = $query
          ->min("Product_property_values.value")
          ->max("Product_property_values.value")
          ->groupBy("Properties.name")
          ->get();
        return count($result) > 0 ? $result[0] : [];
      } else {
        return $query->select(["value"])->get();
      }
    }
  }