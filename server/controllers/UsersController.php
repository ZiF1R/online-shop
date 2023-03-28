<?php
  class UsersController
  {
    public static function createUser(array $req, array $res): array {
      $name = $req["params"]["name"];
      $email = $req["params"]["email"];
      $password = $req["params"]["password"];

      $email = self::correctEmail($email);

      // check with password_verify()
      $password_hashed = password_hash($password,PASSWORD_DEFAULT);

      if (!self::isUniqueEmail($email)) {
        $res["error"] = "Пользователь с такой почтой уже существует";
        return $res;
      }

      $user = [
        "mail" => $email,
        "password" => $password_hashed,
        "is_admin" => 0,
        "name" => $name
      ];
      QueryBuilder::table("Users")->insert([$user]);

      $res["user"] = QueryBuilder::table("Users")
        ->where("mail", "=", $email)
        ->get()[0];

      return $res;
    }

    public static function login(array $req, array $res): array {
      $email = $req["params"]["email"];
      $password = $req["params"]["password"];
      $email = self::correctEmail($email);

      $user = QueryBuilder::table("Users")
        ->where("mail", "=", $email)
        ->get();

      if (count($user) === 0 || !password_verify($password, $user[0]["password"])) {
        $res["error"] = "Неправильный логин или пароль";
        return $res;
      }

      $res["user"] = $user[0];
      return $res;
    }

    public static function getUser(array $req, array $res): array {
      $id = $req["params"]["id"];

      $res["user"] = QueryBuilder::table("Users")
        ->where("id", "=", $id)
        ->get()[0];

      return $res;
    }

    public static function setWatchedProduct(array $req, array $res): array {
      $id = (int) $req["params"]["id"];
      $product_code = (int) $req["params"]["product_code"];

      if (self::isAlreadyWatched($id, $product_code)) {
        return $res;
      }

      $watch_info = [
        "user_id" => $id,
        "product_code" => $product_code,
        "visit_date" => date('Y-m-d h:i:s', time())
      ];

      QueryBuilder::table("Visited_products")
        ->insert([$watch_info]);

      return $res;
    }

    public static function getWatchedProducts(array $req, array $res): array {
      $id = (int) $req["params"]["id"];

      $products = QueryBuilder::table("Visited_products")
        ->select(["Products.*", "Visited_products.visit_date"])
        ->where("Visited_products.user_id", "=", $id)
        ->join("Products", "Visited_products.product_code", "Products.code")
        ->leftJoin("Product_feedback", "Products.code", "Product_feedback.product_code")
        ->avg("Product_feedback.rating", "rating", 0)
        ->count("Product_feedback.id", "rates_count")
        ->groupBy("Products.code")
        ->groupBy("Visited_products.id")
        ->orderBy("visit_date", "DESC")
        ->get();

      foreach ($products as $i => $product) {
        $products[$i]["properties"] = self::getProductProperties($product["code"], $product["category_id"]);
      }

      $res["products"] = $products;
      return $res;
    }

    public static function removeWatchedProduct(array $req, array $res): array {
      $id = (int) $req["params"]["id"];
      $code = (int) $req["params"]["product_code"];

      QueryBuilder::table("Visited_products")
        ->where("user_id", "=", $id)
        ->where("product_code", "=", $code)
        ->delete();

      return $res;
    }

    private static function getProductProperties(string $code, string $category_id): array {
      return QueryBuilder::table("Properties")
        ->select(["name", "designation", "value"])
        ->join("Product_property_values", "name", "property_name and product_code = $code")
        ->where("category_id", "=", $category_id)
        ->get();
    }

    private static function isAlreadyWatched(int $user_id, int $code): bool {
      return count(QueryBuilder::table("Visited_products")
        ->where("user_id", "=", $user_id)
        ->where("product_code", "=", $code)
        -> get()) > 0;
    }

    public static function changeUserData(array $req, array $res): array {
      $id = $req["params"]["id"];

      $name = $req["params"]["name"];
      $mail = self::correctEmail($req["params"]["mail"]);
      $newMail = self::correctEmail($req["params"]["newMail"]);
      $birth = $req["params"]["birth"];
      $phone = $req["params"]["phone"];

      if ($mail !== $newMail && !self::isUniqueEmail($newMail)) {
        $res["error"] = "Данная почта уже занята";
        return $res;
      }

      $userNewData = [
        "name" => $name,
        "mail" => $newMail,
        "birth" => null,
        "phone" => null,
      ];

      if (isset($birth) && strlen($birth) > 0) {
        $userNewData["birth"] = $birth;
      }

      if (isset($phone) && strlen($phone) > 0) {
        $userNewData["phone"] = $phone;
      }

      QueryBuilder::table("Users")
        ->where("id", "=", $id)
        ->update($userNewData);

      $res["user"] = QueryBuilder::table("Users")
        ->where("id", "=", $id)
        ->get()[0];

      return $res;
    }

    private static function correctEmail(string $email): string {
      return preg_replace("/(@[^_]*)_+/", "$1.", $email);
    }

    private static function isUniqueEmail(string $email): bool {
      $users = QueryBuilder::table("Users")
        ->where("mail", "=", $email)
        ->get();
      return count($users) === 0;
    }
  }