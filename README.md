# Taskmaster

Taskmaster is a simple PHP task management project built for learning modern PHP project structure, routing, SQLite, repositories, services, controllers, and basic MVC/API design.

## Features

* Create tasks
* List tasks
* View a single task
* Update tasks
* Patch tasks partially
* Soft delete tasks using a `purged` column
* SQLite database
* Composer autoloading
* Basic layered structure:

  * Controller
  * Service
  * Repository
  * Model
  * View/Response

## Tech Stack

* PHP 8.1+
* Composer
* SQLite
* PDO

## Project Structure

```text
TASKMASTER
├── database/
│   └── taskmaster.sqlite
├── public/
├── src/
│   ├── Controllers/
│   │   └── TaskController.php
│   ├── Core/
│   │   ├── Database.php
│   │   ├── Request.php
│   │   ├── Response.php
│   │   └── Router.php
│   ├── Enums/
│   │   └── TaskStatus.php
│   ├── Models/
│   │   └── Task.php
│   ├── Repositories/
│   │   └── TaskRepository.php
│   ├── Services/
│   │   └── TaskService.php
│   ├── Utilities/
│   │   └── functions.php
│   └── Views/
│       └── TaskView.php
├── vendor/
├── .gitignore
├── composer.json
├── composer.lock
└── README.md
```

## Requirements

Make sure PHP, Composer, and SQLite support are installed.

```bash
php -v
composer -V
php -m | grep -i sqlite
```

On Ubuntu, you may need:

```bash
sudo apt update
sudo apt install php php-sqlite3 sqlite3 composer
```

## Installation

Clone the project:

```bash
git clone <your-repository-url>
cd taskmaster
```

Install dependencies:

```bash
composer install
```

Generate Composer autoload files:

```bash
composer dump-autoload
```

## Running the Project

Start the PHP built-in server:

```bash
php -S localhost:8000 -t public
```

Open in your browser:

```text
http://localhost:8000
```

## Database

This project uses SQLite.

Database file:

```text
database/taskmaster.sqlite
```

The `tasks` table should include fields similar to:

```sql
CREATE TABLE tasks (
    id TEXT PRIMARY KEY,
    title TEXT NOT NULL,
    description TEXT NULL,
    status TEXT NOT NULL DEFAULT 'pending',
    due_date TEXT NULL,
    purged INTEGER NOT NULL DEFAULT 0,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
);
```

SQLite does not have a native boolean type, so this project stores booleans as integers:

```text
false = 0
true  = 1
```

Soft-deleted tasks are marked as:

```sql
purged = 1
```

Normal task queries should only show:

```sql
purged = 0
```

## Example API Endpoints

Depending on your current router setup, the project may support routes similar to:

```text
GET     /tasks
GET     /tasks/{id}
POST    /tasks
PUT     /tasks/{id}
PATCH   /tasks/{id}
DELETE  /tasks/{id}
```

For MVC-style pages, routes may look like:

```text
GET   /tasks
GET   /tasks/create
POST  /tasks
GET   /tasks/{id}/edit
POST  /tasks/{id}/update
POST  /tasks/{id}/delete
```

## Example Task JSON

```json
{
  "title": "Learn PHP MVC",
  "description": "Practice building a small PHP project without a framework.",
  "status": "pending",
  "due_date": "2026-06-30"
}
```

## Task Status

Task statuses are defined using a PHP enum:

```php
enum TaskStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
}
```

## Soft Delete Behavior

Tasks are not physically deleted from the database.

Instead of:

```sql
DELETE FROM tasks WHERE id = :id
```

the project uses:

```sql
UPDATE tasks
SET purged = 1
WHERE id = :id
```

This keeps the task record in the database while hiding it from normal GET requests.

## Security Notes

The repository uses prepared statements through PDO:

```php
$statement = $this->pdo->prepare("
    SELECT * FROM tasks
    WHERE id = :id
");

$statement->execute([
    'id' => $id,
]);
```

This helps prevent SQL injection because user input is passed separately from the SQL string.

Avoid writing SQL like this:

```php
$this->pdo->query("SELECT * FROM tasks WHERE id = '$id'");
```

## Development Notes

Useful commands:

```bash
composer dump-autoload
php -S localhost:8000 -t public
```

Check PHP syntax:

```bash
find src public -name "*.php" -print0 | xargs -0 -n1 php -l
```

Check Git status:

```bash
git status
```

## Git Ignore

Recommended `.gitignore` entries:

```gitignore
/vendor/
.env
.env.*
!.env.example

/database/*.sqlite
/database/*.sqlite3
/database/*.db

.vscode/
.idea/

*.log
.DS_Store
Thumbs.db
```

## License

This project is for learning purposes.
