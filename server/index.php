<?php
  include "./headers.php";
  include "./utilities/Route.php";
  include "./controllers/DatabaseController.php";
  include "./utilities/QueryBuilder.php";
  include "./interfaces/IModel.php";

  Route::get("/brands", "BrandsController::getAllBrands");

  //users routes
  Route::get("/users", "UsersController::login");
  Route::get("/users/{id}", "UsersController::getUser");
  Route::post("/users", "UsersController::createUser");
  Route::post("/users/{id}", "UsersController::changeUserData");

  // sections routes
  Route::get("/sections", "SectionsController::getAllSections");
  Route::post("/sections", "SectionsController::createSection");
  Route::delete("/sections", "SectionsController::deleteSection");
  Route::get("/sections/{id}", "SectionsController::getSection");
  Route::get("/sections/{section_id}/categories", "SectionsController::getSectionCategories");

  // categories routes
  Route::post("/categories", "CategoriesController::createCategory");
  Route::delete("/categories", "CategoriesController::deleteCategory");
  Route::get("/categories/{category_id}", "CategoriesController::getCategory");
  Route::get("/categories/{category_id}/products", "CategoriesController::getCategoryProducts");
  Route::get("/categories/{category_id}/filters", "CategoriesController::getCategoryFilters");

  // products routes
  Route::get("/products", "ProductsController::getProducts");
  Route::get("/products/{code}", "ProductsController::getProduct");
  Route::delete("/products/{code}", "ProductsController::deleteProduct");
  Route::get("/products/{code}/rating", "ProductsController::getProductTotalRating");
  Route::post("/products/{code}/feedback", "ProductsController::sendFeedback");
  Route::delete("/products/{code}/feedback", "ProductsController::removeFeedback");

  // orders routes
  Route::get("/orders", "OrdersController::createOrder");

  die();
