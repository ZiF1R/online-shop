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

    public static function changeUserData(array $req, array $res): array {
      $id = $req["params"]["id"];

      $name = $req["params"]["name"];
      $mail = self::correctEmail($req["params"]["mail"]);
      $birth = $req["params"]["birth"];
      $phone = $req["params"]["phone"];

      if (!self::isUniqueEmail($mail)) {
        $res["error"] = "Данная почта уже занята";
        return $res;
      }

      $userNewData = [
        "name" => $name,
        "mail" => $mail,
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