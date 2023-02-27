<?php
  include "./headers.php";
  include "./utilities/Route.php";
  include "./controllers/DatabaseController.php";
  include "./models/BaseModel.php";
  include "./interfaces/IModel.php";

  // sections routes
  Route::get("/sections", "SectionsController::getAllSections");
  Route::post("/sections", "SectionsController::createSection");
  Route::get("/sections/{id}", "SectionsController::getSection");
  Route::get("/sections/{section_id}/categories", "SectionsController::getSectionCategories");

  // categories routes
  Route::get("/categories/{category_id}/products", "CategoriesController::getCategoryProducts");

  // products routes
  Route::get("/products", "ProductsController::getProducts");

  die();
