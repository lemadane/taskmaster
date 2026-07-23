package com.taskmaster.controller;

import com.taskmaster.model.Task;
import com.taskmaster.model.TaskStatus;
import com.taskmaster.service.TaskService;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Optional;

@Controller
@RequestMapping("/tasks")
public class TaskWebController {

    private final TaskService taskService;

    public TaskWebController(TaskService taskService) {
        this.taskService = taskService;
    }

    @GetMapping
    public String index(Model model) {
        List<Task> tasks = taskService.getTasks();
        model.addAttribute("tasks", tasks);
        return "tasks/list";
    }

    @GetMapping("/create")
    public String create(Model model) {
        model.addAttribute("statuses", TaskStatus.values());
        return "tasks/create";
    }

    @PostMapping
    public String store(
            @RequestParam String title,
            @RequestParam(required = false) String description,
            @RequestParam(required = false, defaultValue = "pending") String status,
            @RequestParam(required = false, name = "due_date") String dueDate,
            Model model) {
        try {
            taskService.createTask(title, description, status, dueDate);
            return "redirect:/tasks";
        } catch (IllegalArgumentException e) {
            model.addAttribute("errorMessage", e.getMessage());
            model.addAttribute("backUrl", "/tasks/create");
            model.addAttribute("statuses", TaskStatus.values());
            return "tasks/validation_error";
        }
    }

    @GetMapping("/{id}/edit")
    public String edit(@PathVariable String id, Model model) {
        Optional<Task> task = taskService.getTask(id);
        if (task.isEmpty()) {
            return "error/404";
        }
        model.addAttribute("task", task.get());
        model.addAttribute("statuses", TaskStatus.values());
        return "tasks/edit";
    }

    @PostMapping("/{id}/update")
    public String update(
            @PathVariable String id,
            @RequestParam String title,
            @RequestParam(required = false) String description,
            @RequestParam String status,
            @RequestParam(required = false, name = "due_date") String dueDate,
            Model model) {
        try {
            Optional<Task> updated = taskService.updateTask(id, title, description, status, dueDate);
            if (updated.isEmpty()) {
                return "error/404";
            }
            return "redirect:/tasks";
        } catch (IllegalArgumentException e) {
            model.addAttribute("errorMessage", e.getMessage());
            model.addAttribute("backUrl", "/tasks/" + id + "/edit");
            model.addAttribute("statuses", TaskStatus.values());
            return "tasks/validation_error";
        }
    }

    @PostMapping("/{id}/delete")
    public ResponseEntity<?> destroy(
            @PathVariable String id,
            @RequestHeader(value = "HX-Request", required = false) String hxRequest) {
        boolean deleted = taskService.deleteTask(id);
        if (!deleted) {
            return ResponseEntity.status(HttpStatus.NOT_FOUND).body("Task not found");
        }
        if (hxRequest != null) {
            return ResponseEntity.ok("");
        }
        return ResponseEntity.status(HttpStatus.FOUND)
                .header("Location", "/tasks")
                .build();
    }
}
