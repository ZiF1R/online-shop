<?php
  class BrandsController
  {
    public static function getAllBrands(array $req, array $res): array {
      $res["brands"] = QueryBuilder::table("Brands")->get();
      return $res;
    }
  }