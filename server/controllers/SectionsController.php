<?php
  include "./models/SectionModel.php";
  include "./models/CategoryModel.php";

  class SectionsController
  {
    public static function getAllSections(array $req, array $res): array {
      $res["sections"] = SectionModel::findAll();
      return $res;
    }

    public static function getSection(array $req, array $res): array {
      $id = (int) $req["params"]["id"];

      if ($id === null) {
        return $res;
      }

      $res["section"] = SectionModel::findOne(["id" => $id]);
      return $res;
    }

    public static function getSectionCategories(array $req, array $res): array {
      $section_id = (int) $req["params"]["section_id"];

      if (!isset($section_id)) {
        return $res;
      }

      $res["categories"] = CategoryModel::find(["section_id" => $section_id]);
      return $res;
    }

    public static function createSection(array $req, array $res): array {
      $name = $req["params"]["name"];

      if (!isset($name)) {
        return $res;
      }

      $section = SectionModel::create($name);
      $res["section"] = $section->getSectionObject();

      return $res;
    }
  }