<?php

  $host = "localhost";
  $database = "php-project";
  $user = "root";
  $password = "";
  $link = mysqli_connect($host, $user, $password, $database);

  $data = json_decode(file_get_contents("brands.json"));
  $query = "INSERT INTO Brands (name) VALUES ";
  foreach ($data as $brands) {
    foreach ($brands as $brand) {
      $fullQuery = $query . "('$brand')";
      try {
        mysqli_query($link, $fullQuery);
      } catch (Exception $e) {}
    }
  }

  function formatConditions($conditions): string {
    $statements = [];
    foreach ($conditions as $column => $value) {
      array_push($statements, "`$column` LIKE " . "'%$value%'");
    }

    return implode(" AND ", $statements);
  }

  function getInsertQuery (string $table, array $colsAndVals): string {
    $columns = implode(", ", $colsAndVals[0]);

    for ($i = 0; $i < count($colsAndVals[1]); $i++) {
      $colsAndVals[1][$i] =  "'{$colsAndVals[1][$i]}'";
    }

    $values = implode(",", $colsAndVals[1]);
//    $values = preg_replace("/([^,]+)/", "'$1'", $values);
    return "INSERT INTO $table (" . $columns . ") VALUES (" . $values . ")";
  }

  function getItemPK(mysqli $link, string $table, string $pk, array $conditions): int|null {
    $query = "SELECT $pk FROM $table WHERE " . formatConditions($conditions);
    $result = mysqli_query($link, $query);
    $data = mysqli_fetch_assoc($result);

    $pk_value = null;
    if (isset($data) && count($data) > 0) {
      $pk_value = (int) $data[$pk];
    }

    return $pk_value;
  }

  function insertSection(mysqli $link, string $section): int|null {
    $query = getInsertQuery("Sections", [
      ["name"],
      [$section]
    ]);
    try {
      mysqli_query($link, $query);
    } catch (Exception $e) {}
    return getItemPK($link, "Sections", "id", ["name" => $section]);
  }

  function insertCategory(mysqli $link, string $category, int $section_id): int|null {
    $query = getInsertQuery("Categories", [
      ["name", "section_id"],
      [$category, $section_id]
    ]);
    try {
      mysqli_query($link, $query);
    } catch (Exception $e) {
      echo $e . "<br>";
    }
    return getItemPK($link, "Categories", "id", ["name" => $category]);
  }

  function insertProperty(mysqli $link, $data) {
    list($property, $category_id, $propertyData) = $data;
    $columns = ["name", "category_id", "type"];
    $values = [$property, $category_id];

    if (isset($propertyData->designation)) {
      array_push($columns, "designation");
      array_push($values, "Number");
      array_push($values, $propertyData->designation);
    } else {
      array_push($values, "String");
    }

    $query = getInsertQuery("Properties", [$columns, $values]);
    try {
      mysqli_query($link, $query);
    } catch (Exception $e) {}
  }

  function insertCategoryProductProperties(mysqli $link, int $category_id, $properties) {
    foreach ($properties as $property => $propertyData) {
      insertProperty($link, [$property, $category_id, $propertyData]);
    }
  }

  function insertProduct(mysqli $link, int $category_id, $productData): int|null {
    $columns = ["code", "name", "category_id", "brand_id", "price", "count"];
    $values = [
      $productData->code,
      $productData->name,
      $category_id,
      getItemPK($link, "Brands", "id", ["name"=>$productData->brand]),
      $productData->price,
      $productData->count,
    ];

    if (isset($productData->description)) {
      array_push($columns, "description");
      array_push($values, $productData->description);
    }

    if (isset($productData->photo_link)) {
      array_push($columns, "photo_link");
      array_push($values, $productData->photo_link);
    }

    if (isset($productData->discount)) {
      array_push($columns, "discount");
      array_push($values, $productData->discount);
    }

    $query = getInsertQuery("Products", [$columns, $values]);
    try {
      mysqli_query($link, $query);
    } catch (Exception $e) {}
    return getItemPK($link, "Products", "code", ["code" => $productData->code]);
  }

  function insertProductPropertyValues(mysqli $link, int $product_code, $properties) {
    $columns = ["product_code", "property_name", "value"];
    foreach ($properties as $property => $propertyData) {
      $values = [$product_code, $property, $propertyData->value];
      $query = getInsertQuery("Product_property_values", [$columns, $values]);
      try {
        mysqli_query($link, $query);
      } catch (Exception $e) {}
    }
  }

  function fillDatabase(mysqli $link, $data) {
    foreach ($data as $section => $categories) {
      $section_id = insertSection($link, $section);
      if (!isset($section_id)) {
        echo("Section ($section) insert error!<br>");
        continue;
      }
      foreach ($categories as $category => $products) {
        $category_id = insertCategory($link, $category, $section_id);
        if (!isset($category_id)) {
          echo("Category ($category, $section_id) insert error!<br>");
          continue;
        }
        foreach ($products as $product) {
          if (!isset($product)) {
            continue;
          }
          insertCategoryProductProperties($link, $category_id, $product->properties);
          $product_code = insertProduct($link, $category_id, $product);
          if (!isset($product_code)) {
            continue;
          }
          insertProductPropertyValues($link, $product_code, $product->properties);
        }
      }
    }
  }

  $data = json_decode(file_get_contents("data.json"));
  echo("Start: " . date('m/d/Y h:i:s a', time()) . "<br>");
  fillDatabase($link, $data);
  echo("End: " . date('m/d/Y h:i:s a', time()) . "<br>");
