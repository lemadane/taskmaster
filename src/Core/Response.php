<?php

declare(strict_types=1);

namespace Taskmaster\Core;

final class Response {
   public static function success(
      mixed $data = null,
      int $statusCode = 200
   ): void {
      header('Content-Type: application/json; charset=utf-8');
      http_response_code($statusCode);
      echo json_encode([
         'success' => true,
         'data' => $data,
      ], JSON_PRETTY_PRINT);
      exit;
   }

   public static function error(
      string $message,
      int $statusCode = 400,
      mixed $errors = null
   ): void {
      header('Content-Type: application/json; charset=utf-8');
      http_response_code($statusCode);
      echo json_encode([
         'success' => false,
         'message' => $message,
         'errors' => $errors,
      ], JSON_PRETTY_PRINT);
      exit;
   }
}
