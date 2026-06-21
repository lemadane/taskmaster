<?php

declare(strict_types=1);

namespace Taskmaster\Models;

use Taskmaster\Enums\TaskStatus;
use DateTimeImmutable;

final class Task {
   public function __construct(
      public readonly string $id,
      public readonly string $title,
      public readonly ?string $description,
      public readonly TaskStatus $status,
      public readonly ?\DateTimeImmutable $dueDate,
      public readonly bool $purged,
      public readonly string $createdAt,
      public readonly string $updatedAt
   ) {
   }

   public static function fromArray(array $data): self
{
    return new self(
        id: (string) $data['id'],
        title: (string) $data['title'],
        description: $data['description'] ?? null,
        status: $data['status'] instanceof TaskStatus
            ? $data['status']
            : TaskStatus::from($data['status']),
        dueDate: empty($data['due_date'])
            ? null
            : new DateTimeImmutable($data['due_date']),
        purged: (bool) ($data['purged'] ?? false),
        createdAt: (string) $data['created_at'],
        updatedAt: (string) $data['updated_at'],
    );
}

   public function toArray(): array {
      return [
         'id' => $this->id,
         'title' => $this->title,
         'description' => $this->description,
         'status' => $this->status,
         'due_date' => $this->dueDate?->format('c'),
         'purged' => $this->purged,
         'created_at' => $this->createdAt,
         'updated_at' => $this->updatedAt,
      ];
   }
}
