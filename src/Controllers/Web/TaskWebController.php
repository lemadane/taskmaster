<?php

declare(strict_types=1);

namespace Taskmaster\Controllers\Web;

use InvalidArgumentException;
use Taskmaster\Core\Request;
use Taskmaster\Core\Response;
use Taskmaster\Services\TaskService;
use Taskmaster\Views\TaskWebView;
use Taskmaster\Models\Task;

final class TaskWebController {
   public function __construct(
      private readonly TaskService $taskService
   ) {
   }

   public function index(Request $request, array $params): void {
      $tasksData = $this->taskService->getTasks();
      $tasks = array_map(
         fn(array $data) => Task::fromArray($data),
         $tasksData
      );

      header('Content-Type: text/html; charset=utf-8');
      TaskWebView::index($tasks);
   }

   public function create(Request $request, array $params): void {
      header('Content-Type: text/html; charset=utf-8');
      TaskWebView::create();
   }

   public function store(Request $request, array $params): void {
      try {
         $this->taskService->createTask($request->body);
         header('Location: /tasks');
         exit;
      } catch (InvalidArgumentException $e) {
         http_response_code(422);
         header('Content-Type: text/html; charset=utf-8');
         echo "Validation Error: " . htmlspecialchars($e->getMessage()) . '<br><a href="/tasks/create">Back</a>';
         exit;
      }
   }

   public function edit(Request $request, array $params): void {
      $taskData = $this->taskService->getTask($params['id']);
      if ($taskData === null) {
         http_response_code(404);
         header('Content-Type: text/html; charset=utf-8');
         echo "Task not found";
         exit;
      }
      
      $task = Task::fromArray($taskData);
      header('Content-Type: text/html; charset=utf-8');
      TaskWebView::edit($task);
   }

   public function update(Request $request, array $params): void {
      try {
         $task = $this->taskService->updateTask(
            id: $params['id'],
            data: $request->body
         );
         if ($task === null) {
            http_response_code(404);
            header('Content-Type: text/html; charset=utf-8');
            echo "Task not found";
            exit;
         }
         header('Location: /tasks');
         exit;
      } catch (InvalidArgumentException $e) {
         http_response_code(422);
         header('Content-Type: text/html; charset=utf-8');
         echo "Validation Error: " . htmlspecialchars($e->getMessage()) . '<br><a href="/tasks/' . htmlspecialchars($params['id']) . '/edit">Back</a>';
         exit;
      }
   }

   public function destroy(Request $request, array $params): void {
      $deleted = $this->taskService->deleteTask($params['id']);
      if (!$deleted) {
         http_response_code(404);
         header('Content-Type: text/html; charset=utf-8');
         echo "Task not found";
         exit;
      }
      header('Location: /tasks');
      exit;
   }
}
