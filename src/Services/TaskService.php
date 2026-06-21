<?php

declare(strict_types=1);

namespace Taskmaster\Services;

use InvalidArgumentException;
use Taskmaster\Enums\TaskStatus;
use Taskmaster\Models\Task;
use Taskmaster\Repositories\TaskRepository;

final class TaskService {

   public function __construct(
      private readonly TaskRepository $taskRepository
   ) {
   }

   public function getTasks(): array {
      return array_map(
         fn(Task $task) => $task->toArray(),
         $this->taskRepository->findAll()
      );
   }

   public function getTask(string $id): ?array {
      $task = $this->taskRepository
         ->findById($id);
      return $task?->toArray();
   }

   public function createTask(array $data): array {
      $this->validateCreate($data);
      return $this->taskRepository
         ->create($data)
         ->toArray();
   }

   public function updateTask(string $id, array $data): ?array {
      $this->validateUpdate($data);

      $task = $this->taskRepository
         ->update($id, $data);

      return $task?->toArray();
   }

   public function patchTask(string $id, array $data): ?array {
      $this->validatePatch($data);
      $task = $this->taskRepository
         ->patch($id, $data);
      return $task?->toArray();
   }

   public function deleteTask(string $id): bool {
      return $this->taskRepository->delete($id);
   }

   private function validateCreate(array $data): void {
      if (empty($data['title'])) {
         throw new InvalidArgumentException(
            'Title is required.'
         );
      }

      if (isset($data['status'])) {
         $this->validateStatus($data['status']);
      }

      if (isset($data['due_date'])) {
         $this->validateDueDate($data['due_date']);
      }
   }

   private function validateUpdate(array $data): void {
      if (empty($data['title'])) {
         throw new InvalidArgumentException(
            'Title is required.'
         );
      }
      if (empty($data['status'])) {
         throw new InvalidArgumentException(
            'Status is required.'
         );
      }
      $this->validateStatus($data['status']);

      if (isset($data['due_date'])) {
         $this->validateDueDate($data['due_date']);
      }
   }

   private function validatePatch(array $data): void {
      if (
         isset($data['title'])
         && trim((string) $data['title']) === ''
      ) {
         throw new InvalidArgumentException(
            'Title cannot be empty.'
         );
      }
      if (isset($data['status'])) {
         $this->validateStatus($data['status']);
      }

      if (isset($data['due_date'])) {
         $this->validateDueDate($data['due_date']);
      }
   }

   private function validateDueDate(mixed $dueDate): void {
      if (empty($dueDate)) {
         return;
      }
      if (!is_string($dueDate)) {
         throw new InvalidArgumentException(
            'Due date must be a valid date string.'
         );
      }
      try {
         new \DateTimeImmutable($dueDate);
      } catch (\Throwable $e) {
         throw new InvalidArgumentException(
            'Invalid due date format.'
         );
      }
   }

   private function validateStatus(string $status): void {
      if (!in_array( // in_array checks if the status is one of the valid enum values
         $status,
         array_column(TaskStatus::cases(), 'value'), // array_column extracts the 'value' from each enum case to get a list of valid statuses
         true
      )) {    // strict check
         throw new InvalidArgumentException(
            'Invalid task status.'
         );
      }
   }
}
