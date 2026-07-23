package com.taskmaster.controller;

import com.taskmaster.model.Task;
import com.taskmaster.service.TaskService;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.Map;
import java.util.Optional;

@RestController
@RequestMapping("/api/tasks")
public class TaskApiController {

    private final TaskService taskService;

    public TaskApiController(TaskService taskService) {
        this.taskService = taskService;
    }

    @GetMapping("/check")
    public ResponseEntity<ApiResponse<Map<String, String>>> check() {
        return ResponseEntity.ok(ApiResponse.success(Map.of("message", "Taskmaster Java API is running")));
    }

    @GetMapping
    public ResponseEntity<ApiResponse<Iterable<Task>>> index() {
        return ResponseEntity.ok(ApiResponse.success(taskService.getTasks()));
    }

    @GetMapping("/{id}")
    public ResponseEntity<?> show(@PathVariable String id) {
        Optional<Task> task = taskService.getTask(id);
        if (task.isEmpty()) {
            return ResponseEntity.status(HttpStatus.NOT_FOUND).body(ApiResponse.error("Task not found."));
        }
        return ResponseEntity.ok(ApiResponse.success(task.get()));
    }

    @PostMapping
    public ResponseEntity<?> store(@RequestBody TaskRequest request) {
        try {
            Task task = taskService.createTask(
                    request.title(),
                    request.description(),
                    request.status(),
                    request.dueDate()
            );
            return ResponseEntity.status(HttpStatus.CREATED).body(ApiResponse.success(task));
        } catch (IllegalArgumentException e) {
            return ResponseEntity.status(HttpStatus.UNPROCESSABLE_ENTITY).body(ApiResponse.error(e.getMessage()));
        }
    }

    // Full update (PUT)
    @PutMapping("/{id}")
    public ResponseEntity<?> update(@PathVariable String id, @RequestBody TaskRequest request) {
        try {
            Optional<Task> task = taskService.updateTask(
                    id,
                    request.title(),
                    request.description(),
                    request.status(),
                    request.dueDate()
            );
            if (task.isEmpty()) {
                return ResponseEntity.status(HttpStatus.NOT_FOUND).body(ApiResponse.error("Task not found."));
            }
            return ResponseEntity.ok(ApiResponse.success(task.get()));
        } catch (IllegalArgumentException e) {
            return ResponseEntity.status(HttpStatus.UNPROCESSABLE_ENTITY).body(ApiResponse.error(e.getMessage()));
        }
    }

    // Partial update (PATCH)
    @PatchMapping("/{id}")
    public ResponseEntity<?> patch(@PathVariable String id, @RequestBody Map<String, Object> body) {
        try {
            String title = body.containsKey("title") ? (String) body.get("title") : null;
            String description = body.containsKey("description") ? (String) body.get("description") : null;
            String status = body.containsKey("status") ? (String) body.get("status") : null;
            String dueDate = body.containsKey("due_date") ? (String) body.get("due_date") : null;

            boolean hasDescription = body.containsKey("description");
            boolean hasDueDate = body.containsKey("due_date");

            Optional<Task> task = taskService.patchTask(id, title, description, status, hasDescription, dueDate, hasDueDate);
            if (task.isEmpty()) {
                return ResponseEntity.status(HttpStatus.NOT_FOUND).body(ApiResponse.error("Task not found."));
            }
            return ResponseEntity.ok(ApiResponse.success(task.get()));
        } catch (IllegalArgumentException e) {
            return ResponseEntity.status(HttpStatus.UNPROCESSABLE_ENTITY).body(ApiResponse.error(e.getMessage()));
        }
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<?> destroy(@PathVariable String id) {
        boolean deleted = taskService.deleteTask(id);
        if (!deleted) {
            return ResponseEntity.status(HttpStatus.NOT_FOUND).body(ApiResponse.error("Task not found."));
        }
        return ResponseEntity.ok(ApiResponse.success(Map.of("message", "Task deleted successfully.")));
    }
}
