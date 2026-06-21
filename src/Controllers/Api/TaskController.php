<?php

declare(strict_types=1);

namespace Taskmaster\Controllers\Api;

use InvalidArgumentException;
use Taskmaster\Core\Response;
use Taskmaster\Core\Request;
use Taskmaster\Services\TaskService;

final class TaskController {
   public function __construct(
      private readonly TaskService $taskService
   ) {
   }

   public function check(Request $request, array $_params): void {
      Response::success([
         'message' => 'Taskmaster PHP API is running',
      ]);
   }

   public function index(Request $request, array $params): void {
      $tasks = $this->taskService->getTasks();

      Response::success($tasks);
   }

   public function show(Request $request, array $params): void {
      $task = $this->taskService->getTask($params['id']);

      if ($task === null) {
         Response::error('Task not found.', 404);
      }

      Response::success($task);
   }

   public function store(Request $request, array $params): void {
      try {
         $task = $this->taskService->createTask($request->body);
         Response::success($task, 201);
      } catch (InvalidArgumentException $e) {
         Response::error($e->getMessage(), 422);
      }
   }

   // Full update (PUT)
   public function update(Request $request, array $params): void {
      try {
         $task = $this->taskService->updateTask(
            id: $params['id'],
            data: $request->body
         );
         if ($task === null) {
            Response::error('Task not found.', 404);
         }
         Response::success($task);
      } catch (InvalidArgumentException $e) {
         Response::error($e->getMessage(), 422);
      }
   }

   // Partial update (PATCH)
   public function patch(Request $request, array $params): void {
      try {
         $task = $this->taskService->patchTask(
            id: $params['id'],
            data: $request->body
         );
         if ($task === null) {
            Response::error('Task not found.', 404);
         }
         Response::success($task);
      } catch (InvalidArgumentException $e) {
         Response::error($e->getMessage(), 422);
      }
   }

   public function destroy(Request $request, array $params): void {
      $deleted = $this->taskService->deleteTask(
         $params['id']
      );
      if (! $deleted) {
         Response::error('Task not found.', 404);
         return;
      }
      Response::success([
         'message' => 'Task deleted successfully.',
      ]);
   }
}
