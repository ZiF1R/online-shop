<?php
  include "./models/ProductModel.php";
  include "./models/CategoryModel.php";
  include "./models/BrandModel.php";

  class ProductsController
  {
    public static function getProducts(array $req, array $res): array {
      if (!isset($req["params"]["search"])) {
        return QueryBuilder::table("Products")->get();
      } else if ($req["params"]["search"] === "") {
        return [];
      }
      $search = (string) $req["params"]["search"];

      $res["products"] = QueryBuilder::table("Products")
        ->select(["Products.*", "Brands.name as brand_name", "Categories.name as category_name"])
        ->where("Products.name", "LIKE", "%$search%")
        ->join("Brands", "Products.brand_id", "Brands.id")
        ->join("Categories", "Products.category_id", "Categories.id")
        ->leftJoin("Product_feedback", "Products.code", "Product_feedback.product_code")
        ->avg("Product_feedback.rating", "rating", 0)
        ->groupBy("Products.code")
        ->get();

      return $res;
    }

    public static function getProduct(array $req, array $res): array {
      if (isset($req["params"]["product"])) {
        return self::createProduct($req, $res);
      }
      $code = $req["params"]["code"];

      $query = QueryBuilder::table("Products")
        ->select(["Products.*"])
        ->where("Products.code", "=", $code)
        ->leftJoin("Product_feedback", "Products.code", "Product_feedback.product_code")
        ->avg("Product_feedback.rating", "rating", 0)
        ->count("Product_feedback.id", "rates_count")
        ->groupBy("Products.code");

      $product = $query->get()[0];
      $product["properties"] = self::getProductProperties($code, $product["category_id"]);
      $product["feedback"] = self::getProductFeedback($code);

      for ($i = 0; $i < count($product["feedback"]); $i++) {
        $product["feedback"][$i]["replies"] = self::getFeedbackReplies($code, $product["feedback"][$i]["id"]);
      }

      $res["product"] = $product;
      return $res;
    }

    public static function deleteProduct(array $req, array $res): array {
      $code = $req["params"]["code"];

      QueryBuilder::table("Product_property_values")
        ->where("product_code", "=", $code)
        ->delete();

      QueryBuilder::table("Products")
        ->where("code", "=", $code)
        ->delete();

      return $res;
    }

    public static function createProduct(array $req, array $res): array {
      $product = json_decode($req["params"]["product"], JSON_UNESCAPED_UNICODE);

      $prod = [
        "code" => $product["code"],
        "name" => $product["name"],
        "category_id" => $product["category_id"],
        "brand_id" => $product["brand_id"],
        "price" => $product["price"],
        "count" => $product["count"],
        "description" => $product["description"],
        "photo_link" => $product["photo_link"],
      ];

      QueryBuilder::table("Products")
        ->insert([$prod]);

      foreach ($product["properties"] as $property) {
        QueryBuilder::table("Product_property_values")
          ->insert([[
            "product_code" => $product["code"],
            "property_name" => $property["name"],
            "value" => $property["value"],
          ]]);
      }

      $res["product"] = $product;
      return $res;
    }

    public static function sendFeedback(array $req, array $res): array {
      $user_id = $req["params"]["user_id"];
      $code = $req["params"]["code"];
      $rating = $req["params"]["rating"];
      $comment = $req["params"]["comment"];
      $reply_comment_id = $req["params"]["reply_comment_id"];

      $feedback = [
        "user_id" => $user_id,
        "product_code" => $code,
        "created" => date('Y-m-d', time()),
      ];
      if (isset($rating)) {
        $feedback["rating"] = $rating;
      }
      if (strlen($comment) > 0) {
        $feedback["comment"] = $comment;
      }
      if (isset($reply_comment_id)) {
        $feedback["reply_comment_id"] = $reply_comment_id;
      }
      QueryBuilder::table("Product_feedback")->insert([$feedback]);
      return $res;
    }

    public static function removeFeedback(array $req, array $res) {
      $code = $req["params"]["code"];
      $id = $req["params"]["id"];

      QueryBuilder::table("Product_feedback")
        ->where("reply_comment_id", "=", $id)
        ->where("product_code", "=", $code)
        ->delete();

      QueryBuilder::table("Product_feedback")
        ->where("id", "=", $id)
        ->where("product_code", "=", $code)
        ->delete();
    }

    public static function getProductTotalRating(array $req, array $res): array {
      $code = $req["params"]["code"];

      $res["rating"] = QueryBuilder::table("Product_feedback")
        ->where("Product_feedback.product_code", "=", $code)
        ->join("Products", "Product_feedback.product_code", "Products.code")
        ->avg("Product_feedback.rating", "average")
        ->count("Product_feedback.rating")
        ->sum("case when rating = 1 then 1 else 0 end", "rate_1")
        ->sum("case when rating = 2 then 1 else 0 end", "rate_2")
        ->sum("case when rating = 3 then 1 else 0 end", "rate_3")
        ->sum("case when rating = 4 then 1 else 0 end", "rate_4")
        ->sum("case when rating = 5 then 1 else 0 end", "rate_5")
        ->groupBy("Products.code")
        ->get()[0];

      return $res;
    }

    private static function getFeedbackReplies($code, $comment_id): array {
      return QueryBuilder::table("Product_feedback")
        ->select(["Product_feedback.*", "name as user_name"])
        ->where("product_code", "=", $code)
        ->where("reply_comment_id", "=", $comment_id)
        ->join("Users", "user_id", "Users.id")
        ->get();
    }

    private static function getProductFeedback($code): array {
      return QueryBuilder::table("Product_feedback")
        ->select(["Product_feedback.*", "name as user_name"])
        ->where("product_code", "=", $code)
        ->where("reply_comment_id", "IS", NULL)
        ->join("Users", "user_id", "Users.id")
        ->get();
    }

    private static function getProductProperties(string $code, string $category_id): array {
      return QueryBuilder::table("Properties")
        ->select(["name", "designation", "value"])
        ->join("Product_property_values", "name", "property_name and product_code = $code")
        ->where("category_id", "=", $category_id)
        ->get();
    }
  }