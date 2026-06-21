<?php

declare(strict_types=1); // Declare strict types for better type safety

namespace Taskmaster\Core; // Define the namespace for the Router class

final class Router {
   private array $routes = [];

   public function get(
      string $path,
      callable $handler
   ): void {
      $this->add('GET', $path, $handler);
   }

   public function post(
      string $path,
      callable $handler
   ): void {
      $this->add('POST', $path, $handler);
   }

   public function put(
      string $path,
      callable $handler
   ): void {
      $this->add('PUT', $path, $handler);
   }

   public function patch(
      string $path,
      callable $handler
   ): void {
      $this->add('PATCH', $path, $handler);
   }

   public function delete(
      string $path,
      callable $handler
   ): void {
      $this->add('DELETE', $path, $handler);
   }

   private function add(
      string $method,
      string $path,
      callable $handler
   ): void {
      $this->routes[] = [
         'method' => $method,
         'path' => $path,
         'handler' => $handler,
      ];
   }

   public function dispatch(Request $request): void {
      foreach ($this->routes as $route) {
         if ($route['method'] !== $request->method) {
            continue;
         }
         $params = $this->match(
            $route['path'],
            $request->path
         );
         if ($params !== null) {
            call_user_func(
               $route['handler'],
               $request,
               $params
            );
            return;
         }
      }
      Response::error(
         'Route not found',
         404
      );
   }

   private function match(
      string $routePath,
      string $requestPath
   ): ?array {
      $routeParts = explode('/', trim($routePath, '/'));
      $requestParts = explode('/', trim($requestPath, '/'));
      if (count($routeParts) !== count($requestParts)) {
         return null;
      }
      $params = [];
      foreach ($routeParts as $index => $routePart) {
         $requestPart = $requestParts[$index];
         if (str_starts_with(
            $routePart,
            '{'
         ) && str_ends_with($routePart, '}')) {
            $paramName = trim($routePart, '{}');
            $params[$paramName] = $requestPart;
            continue;
         }
         if ($routePart !== $requestPart) {
            return null;
         }
      }
      return $params;
   }
}
