<?php

declare(strict_types=1);

use Taskmaster\Controllers\Api\TaskController;
use Taskmaster\Controllers\Web\TaskWebController;
use Taskmaster\Core\Database;
use Taskmaster\Core\Response;
use Taskmaster\Core\Request;
use Taskmaster\Core\Router;
use Taskmaster\Repositories\TaskRepository;
use Taskmaster\Services\TaskService;

// Autoload dependencies and classes
require_once __DIR__ . '/../vendor/autoload.php';

// Initialize database connection and repositories/services/controllers
$database = new Database();
$pdo = $database->connect();

// Initialize repositories, services, and controllers
$taskRepository = new TaskRepository($pdo);
$taskService = new TaskService($taskRepository);
$taskController = new TaskController($taskService);
$taskWebController = new TaskWebController($taskService);

// Capture the incoming request and dispatch it to the appropriate controller method
$request = Request::capture();

// Define routes and their corresponding controller methods
$router = new Router();

// API routes
$router->get('/api/tasks/check', [$taskController, 'check']);
$router->get('/api/tasks', [$taskController, 'index']);
$router->get('/api/tasks/{id}', [$taskController, 'show']);
$router->post('/api/tasks', [$taskController, 'store']);
$router->put('/api/tasks/{id}', [$taskController, 'update']);
$router->patch('/api/tasks/{id}', [$taskController, 'patch']);
$router->delete('/api/tasks/{id}', [$taskController, 'destroy']);

// Web MVC routes
$router->get('/tasks', [$taskWebController, 'index']);
$router->get('/tasks/create', [$taskWebController, 'create']);
$router->post('/tasks', [$taskWebController, 'store']);
$router->get('/tasks/{id}/edit', [$taskWebController, 'edit']);
$router->post('/tasks/{id}/update', [$taskWebController, 'update']);
$router->post('/tasks/{id}/delete', [$taskWebController, 'destroy']);


try {
    // Dispatch the request to the appropriate controller method
    $router->dispatch($request);
} catch (Throwable $e) {
    Response::error(
        message: $e->getMessage(),
        statusCode: 500
    );
}
