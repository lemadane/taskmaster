<?php

declare(strict_types=1);

namespace Taskmaster\Repositories;

use PDO;
use Taskmaster\Models\Task;
use Taskmaster\Enums\TaskStatus;
use function Taskmaster\Utilities\generateUUID;

final class TaskRepository {
   public function __construct(
      private readonly PDO $pdo
   ) {
   }

   public function findAll(): array {
      $statement = $this->pdo->query("
            SELECT * FROM tasks
            WHERE purged = 0
            ORDER BY created_at DESC
        ");

      $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
      return array_map(
         fn(array $row) => Task::fromArray($row),
         $rows
      );
   }

   public function findById(string $id): ?Task {
      $statement = $this->pdo->prepare("
            SELECT * FROM tasks
            WHERE id = :id
               AND purged = 0
            LIMIT 1
        ");
      $statement->execute([
         'id' => $id,
      ]);
      $row = $statement->fetch(PDO::FETCH_ASSOC);
      if (!$row) {
         return null;
      }
      return Task::fromArray($row);
   }

   public function create(array $data): Task {
      $now = date('c');
      $task = new Task(
         id: generateUUID(),
         title: $data['title'],
         description: $data['description'] ?? null,
         status: TaskStatus::from($data['status'] ?? 'pending'),
         dueDate: !empty($data['due_date'])
            ? new \DateTimeImmutable($data['due_date'])
            : null,
         purged: false,
         createdAt: $now,
         updatedAt: $now
      );
      $statement = $this->pdo->prepare("
            INSERT INTO tasks (
                id,
                title,
                description,
                status,
                due_date,
                purged,
                created_at,
                updated_at
            ) VALUES (
                :id,
                :title,
                :description,
                :status,
                :due_date,
                :purged,
                :created_at,
                :updated_at
            )
        ");
      $statement->execute([
         'id' => $task->id,
         'title' => $task->title,
         'description' => $task->description,
         'status' => $task->status->value,
         'due_date' => $task->dueDate?->format('c'),
         'purged' => 0,
         'created_at' => $task->createdAt,
         'updated_at' => $task->updatedAt,
      ]);

      return $task;
   }

   // 
   public function update(string $id, array $data): ?Task {
      $existing = $this->findById($id);

      // If the task doesn't exist, return null
      if ($existing === null) {
         return null;
      }
      $now = date('c'); // ISO 8601 format

      // Update the task in the database
      $statement = $this->pdo->prepare("
            UPDATE tasks
            SET
                title = :title,
                description = :description,
                status = :status,
                due_date = :due_date,
                updated_at = :updated_at
            WHERE id = :id
        ");

      // Use the new values if provided, otherwise keep existing values
      $statement->execute([
         'id' => $id,
         'title' => $data['title'],
         'description' => $data['description'] ?? null,
         'status' => TaskStatus::from($data['status'])->value,
         'due_date' => !empty($data['due_date'])
            ? (new \DateTimeImmutable($data['due_date']))->format('c')
            : null,
         'updated_at' => $now,
      ]);
      return $this->findById($id);
   }

   // The patch method allows for partial updates, so we only update the fields that are provided in the $data array.
   public function patch(
      string $id,
      array $data
   ): ?Task {
      $existing = $this->findById($id);
      if ($existing === null) {
         return null;
      }
      $now = date('c'); // ISO 8601 format
      $statement = $this->pdo->prepare("
            UPDATE tasks
            SET
                title = :title,
                description = :description,
                status = :status,
                due_date = :due_date,
                updated_at = :updated_at
            WHERE id = :id
        ");

      // For each field, we check if it's provided in the $data array. If it is, we use the new value; if not, we keep the existing value.
      $statement->execute([
         'id' => $id,
         'title' => $data['title'] ?? $existing->title,
         'description' => array_key_exists('description', $data)
            ? $data['description']
            : $existing->description,
         'status' => isset($data['status'])
            ? TaskStatus::from($data['status'])->value
            : $existing->status->value,
         'due_date' => array_key_exists('due_date', $data)
            ? (!empty($data['due_date']) ? (new \DateTimeImmutable($data['due_date']))->format('c') : null)
            : $existing->dueDate?->format('c'),
         'updated_at' => $now,
      ]);
      return $this->findById($id);
   }

   // The delete method removes a task from the database based on its ID. It returns true if the deletion was successful (i.e., if a row was affected), or false if no task with the given ID was found.
   public function delete(string $id): bool {
      $now = date('c');
      $statement = $this->pdo->prepare("
        UPDATE tasks
        SET 
            purged = 1,
            updated_at = :updated_at
        WHERE id = :id
          AND purged = 0
    ");
      $statement->execute([
         'id' => $id,
         'updated_at' => $now,
      ]);
      return $statement->rowCount() > 0;
   }
}
