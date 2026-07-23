package com.taskmaster.service;

import com.taskmaster.model.Task;
import com.taskmaster.model.TaskStatus;
import com.taskmaster.repository.TaskRepository;
import org.springframework.stereotype.Service;

import java.time.Instant;
import java.time.LocalDate;
import java.time.format.DateTimeParseException;
import java.util.List;
import java.util.Optional;
import java.util.UUID;

@Service
public class TaskService {

    private final TaskRepository taskRepository;

    public TaskService(TaskRepository taskRepository) {
        this.taskRepository = taskRepository;
    }

    public List<Task> getTasks() {
        return taskRepository.findAll();
    }

    public Optional<Task> getTask(String id) {
        return taskRepository.findById(id);
    }

    public Task createTask(String title, String description, String statusVal, String dueDateVal) {
        if (title == null || title.trim().isEmpty()) {
            throw new IllegalArgumentException("Title is required.");
        }

        TaskStatus status = TaskStatus.PENDING;
        if (statusVal != null && !statusVal.trim().isEmpty()) {
            status = parseStatus(statusVal);
        }

        String dueDate = null;
        if (dueDateVal != null && !dueDateVal.trim().isEmpty()) {
            parseDueDate(dueDateVal);
            dueDate = dueDateVal.trim();
        }

        Instant now = Instant.now();
        Task task = new Task(
                UUID.randomUUID().toString(),
                title.trim(),
                description,
                status,
                dueDate,
                false,
                now,
                now
        );

        return taskRepository.create(task);
    }

    public Optional<Task> updateTask(String id, String title, String description, String statusVal, String dueDateVal) {
        Optional<Task> existingOpt = taskRepository.findById(id);
        if (existingOpt.isEmpty()) {
            return Optional.empty();
        }

        if (title == null || title.trim().isEmpty()) {
            throw new IllegalArgumentException("Title is required.");
        }
        if (statusVal == null || statusVal.trim().isEmpty()) {
            throw new IllegalArgumentException("Status is required.");
        }

        TaskStatus status = parseStatus(statusVal);
        String dueDate = null;
        if (dueDateVal != null && !dueDateVal.trim().isEmpty()) {
            parseDueDate(dueDateVal);
            dueDate = dueDateVal.trim();
        }

        Task existing = existingOpt.get();
        Task updated = new Task(
                existing.id(),
                title.trim(),
                description,
                status,
                dueDate,
                existing.purged(),
                existing.createdAt(),
                Instant.now()
        );

        return Optional.of(taskRepository.update(updated));
    }

    public Optional<Task> patchTask(String id, String title, String description, String statusVal, Boolean hasDescription, String dueDateVal, Boolean hasDueDate) {
        Optional<Task> existingOpt = taskRepository.findById(id);
        if (existingOpt.isEmpty()) {
            return Optional.empty();
        }

        Task existing = existingOpt.get();

        String newTitle = existing.title();
        if (title != null) {
            if (title.trim().isEmpty()) {
                throw new IllegalArgumentException("Title cannot be empty.");
            }
            newTitle = title.trim();
        }

        String newDesc = existing.description();
        if (hasDescription != null && hasDescription) {
            newDesc = description;
        }

        TaskStatus newStatus = existing.status();
        if (statusVal != null) {
            newStatus = parseStatus(statusVal);
        }

        String newDueDate = existing.dueDate();
        if (hasDueDate != null && hasDueDate) {
            if (dueDateVal == null || dueDateVal.trim().isEmpty()) {
                newDueDate = null;
            } else {
                parseDueDate(dueDateVal);
                newDueDate = dueDateVal.trim();
            }
        }

        Task updated = new Task(
                existing.id(),
                newTitle,
                newDesc,
                newStatus,
                newDueDate,
                existing.purged(),
                existing.createdAt(),
                Instant.now()
        );

        return Optional.of(taskRepository.update(updated));
    }

    public boolean deleteTask(String id) {
        return taskRepository.delete(id);
    }

    private TaskStatus parseStatus(String statusVal) {
        try {
            return TaskStatus.fromValue(statusVal);
        } catch (IllegalArgumentException e) {
            throw new IllegalArgumentException("Invalid task status.");
        }
    }

    private LocalDate parseDueDate(String dueDateVal) {
        try {
            return LocalDate.parse(dueDateVal);
        } catch (DateTimeParseException e) {
            throw new IllegalArgumentException("Invalid due date format.");
        }
    }
}
