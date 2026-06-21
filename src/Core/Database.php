<?php
// This file is responsible for managing the database connection and performing migrations.
declare(strict_types=1);

namespace Taskmaster\Core;

// Import the PDO class for database interactions
use PDO; // PDO is a built-in PHP class for database access


final class Database {

   public function connect(): PDO {
      $databasePath = __DIR__ . '/../../database/taskmaster.sqlite';
      $pdo = new PDO('sqlite:' . $databasePath);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->migrate($pdo);
      return $pdo;
   }

   private function migrate(PDO $pdo): void {
      $pdo->exec("
            CREATE TABLE IF NOT EXISTS tasks (
                id TEXT PRIMARY KEY,
                title TEXT NOT NULL,
                description TEXT NULL,
                status TEXT NOT NULL DEFAULT 'pending',
                due_date TEXT NULL,
                purged INTEGER NOT NULL DEFAULT 0,
                created_at TEXT NOT NULL,
                updated_at TEXT NOT NULL
            )
        ");
   }
}
