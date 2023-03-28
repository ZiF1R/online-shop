<?php
  class OrdersController
  {
    public static function createOrder(array $req, array $res): array {
      $products = json_decode($req["params"]["products"]);
      $user_id = $req["params"]["user_id"];

      $maxId = QueryBuilder::table("Orders")->max("id")->get()[0];
      $maxId = $maxId["max"];

      $orderId = 1;
      if (isset($maxId)) {
        $orderId = (int)$maxId + 1;
      }

      $order = [];
      $created = date('Y-m-d h:i:s', time());
      foreach ($products as $product) {
        $orderProduct = [
          "id" => $orderId,
          "user_id" => $user_id,
          "product_code" => $product->code,
          "created" => $created,
          "closed" => "1",
          "count" => $product->order_count,
        ];
        $order[] = $orderProduct;
      }
      QueryBuilder::table("Orders")
        ->insert($order);

      $res["order"] = QueryBuilder::table("Orders")
        ->where("id", "=", $orderId)
        ->get();

      return $res;
    }
  }