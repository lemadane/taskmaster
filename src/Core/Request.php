<?php

declare(strict_types=1);

namespace Taskmaster\Core;

final class Request {
   public function __construct(
      public readonly string $method,
      public readonly string $path,
      public readonly array $body,
      public readonly array $query
   ) {
   }

   public static function capture(): self {
      $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
      $uri = $_SERVER['REQUEST_URI'] ?? '/';
      $path = parse_url($uri, PHP_URL_PATH) ?: '/';
      $rawBody = file_get_contents('php://input');
      $body = [];
      if ($rawBody !== false && trim($rawBody) !== '') {
         $decoded = json_decode($rawBody, true);
         if (is_array($decoded)) {
            $body = $decoded;
         }
      }
      if (empty($body) && !empty($_POST)) {
         $body = $_POST;
      }
      return new self(
         method: strtoupper($method),
         path: $path,
         body: $body,
         query: $_GET
      );
   }
}
