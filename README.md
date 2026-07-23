# Taskmaster

Taskmaster is a modern, high-performance task management web application built as a native **Java 21** application utilizing **Spring Boot MVC**, **Virtual Threads**, **HTMX**, **AlpineJS**, **Piped Template Engine**, and **BulmaCSS** with custom Material Design aesthetics.

It utilizes the custom **RecordMaster** database as its transactional storage engine.

---

## Features

* **Virtual Threads Enabled**: Native configuration for low-overhead concurrency on standard Web request handling.
* **Modern Web UI**: Built with BulmaCSS + custom Material Theme tokens for a stunning visual layout.
* **HTMX Interactivity**: Dynamic actions (like soft-deletions) without full page reloads.
* **Piped Template Engine**: Custom templating layout (`|layout|`, `|section|`, `|each|`, `|if|`) for view presentation.
* **RecordMaster Database**: Leverages transactional, document/record-based storage (`db.transaction(tx -> ...)`).
* **Robust CRUD**:
  - Create tasks with due dates, description, and custom statuses.
  - List and sort tasks dynamically (filtering out soft-deleted tasks).
  - Modify tasks via edit forms.
  - Soft delete tasks dynamically (sets `purged` status to `true` in DB).

---

## Tech Stack

* **Language**: Java 21
* **Framework**: Spring Boot 3.3.1 (Web MVC)
* **Template Engine**: Piped Template Engine (Spring Boot Starter)
* **Database**: RecordMaster
* **Frontend**: HTMX, AlpineJS, BulmaCSS (v1.0.0 CDN), FontAwesome (v6.4.0)

---

## Project Structure

```text
TASKMASTER
├── db/
│   └── taskmaster.db                  # RecordMaster database folder
├── src/
│   ├── main/
│   │   ├── java/io/lemadane/taskmaster/
│   │   │   ├── config/
│   │   │   │   └── RecordMasterConfig.java   # RecordMaster database setup
│   │   │   ├── controller/
│   │   │   │   ├── ApiResponse.java          # Wrapper JSON response DTO
│   │   │   │   ├── TaskApiController.java     # JSON endpoints
│   │   │   │   ├── TaskRequest.java          # JSON payload request DTO
│   │   │   │   └── TaskWebController.java     # HTML Web MVC controller
│   │   │   ├── model/
│   │   │   │   ├── Task.java                 # Task record model
│   │   │   │   └── TaskStatus.java           # Task status enum
│   │   │   ├── repository/
│   │   │   │   └── TaskRepository.java       # RecordMaster repository
│   │   │   ├── service/
│   │   │   │   └── TaskService.java          # Core validations and transactions
│   │   │   └── TaskmasterApplication.java    # Spring Boot starter class
│   │   └── resources/
│   │       ├── pte-templates/
│   │       │   ├── layouts/
│   │       │   │   └── main.pte              # Main layout template (CSS, scripts)
│   │       │   ├── error/
│   │       │   │   └── 404.pte               # Page/task not found template
│   │       │   └── tasks/
│   │       │       ├── list.pte              # Task list view with HTMX
│   │       │       ├── create.pte            # Create form view
│   │       │       ├── edit.pte              # Edit form view
│   │       │       └── validation_error.pte  # Input validation error view
│   │       └── application.properties        # App properties (Virtual threads config)
│   └── test/                                 # Unit/Integration tests
├── build.gradle                              # Gradle build configuration
├── settings.gradle                           # Gradle settings configuration
└── README.md
```

---

## Requirements

Ensure you have **Java 21 JDK** installed:

```bash
java -version
```

---

## Installation & Build

Build the project locally using Gradle:

```bash
./gradlew clean compileJava
```

---

## Running the Application

To run the embedded Tomcat server on port `8000`:

```bash
./gradlew bootRun
```

Once running, access the web interface in your browser:
```text
http://localhost:8000/tasks
```

---

## Example API Endpoints

The API is fully compliant with the previous API endpoints:

```text
GET     /api/tasks/check       # Heartbeat/status checks
GET     /api/tasks             # Fetch all non-purged tasks
GET     /api/tasks/{id}        # Fetch a single task
POST    /api/tasks             # Create a new task (JSON)
PUT     /api/tasks/{id}        # Update a task completely (JSON)
PATCH   /api/tasks/{id}        # Partially update task parameters (JSON)
DELETE  /api/tasks/{id}        # Soft delete a task
```

---

## Development Notes

### 1. Spring Boot & Gradle Compatibility
This project uses **Gradle 9.6.1**. To avoid plugin compatibility errors with Gradle's new `CopyProcessingSpec` API during jar building, run the application directly using the `./gradlew bootRun` task.

### 2. RecordMaster Metamodel
The project compiles with `recordmaster-processor` which generates record metadata. Fields are filtered and queried through transactional blocks:
```java
db.transaction(tx -> {
    RecordTable<String, Task> table = tx.table(Task.class);
    // CRUD operations...
});
```

### 3. HTMX Actions
HTML interactions are enriched via HTMX attributes. For instance, soft-deletions on task cards use asynchronous POST requests with automatic element swap:
```html
<button 
    hx-post="/tasks/|task.id|/delete"
    hx-target="#task-|task.id|"
    hx-swap="outerHTML"
    hx-confirm="Are you sure you want to delete this task?"
>
    Delete
</button>
```
The controller returns an empty `200 OK` response for HTMX requests, which removes the card element seamlessly without reload.
