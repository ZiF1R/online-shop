<?php
  interface IModel
  {
    public static function find(array $conditions, ?array $joinConditions = null): array|null;
    public static function findOne(array $conditions, ?array $joinConditions = null): array|null;
    public static function findAll(): array;
  }