package io.lemadane.taskmaster.repository;

import io.lemadane.taskmaster.model.Task;
import io.succinct.recordmaster.RecordDatabase;
import io.succinct.recordmaster.RecordTable;
import org.springframework.stereotype.Repository;

import java.time.Instant;
import java.util.List;
import java.util.Optional;

@Repository
public class TaskRepository {

    private final RecordDatabase db;

    public TaskRepository(RecordDatabase db) {
        this.db = db;
    }

    public List<Task> findAll() {
        return db.transaction(tx -> {
            RecordTable<String, Task> table = tx.table(Task.class);
            return table.query().list().stream()
                    .filter(task -> !task.purged())
                    .sorted((a, b) -> b.createdAt().compareTo(a.createdAt()))
                    .toList();
        });
    }

    public Optional<Task> findById(String id) {
        return db.transaction(tx -> {
            RecordTable<String, Task> table = tx.table(Task.class);
            return table.findById(id).filter(task -> !task.purged());
        });
    }

    public Task create(Task task) {
        return db.transaction(tx -> {
            RecordTable<String, Task> table = tx.table(Task.class);
            table.insert(task);
            return task;
        });
    }

    public Task update(Task task) {
        return db.transaction(tx -> {
            RecordTable<String, Task> table = tx.table(Task.class);
            table.update(task);
            return task;
        });
    }

    public boolean delete(String id) {
        return db.transaction(tx -> {
            RecordTable<String, Task> table = tx.table(Task.class);
            Optional<Task> existing = table.findById(id);
            if (existing.isPresent() && !existing.get().purged()) {
                Task task = existing.get();
                Task updated = new Task(
                        task.id(),
                        task.title(),
                        task.description(),
                        task.status(),
                        task.dueDate(),
                        true, // soft delete / purged = true
                        task.createdAt(),
                        Instant.now()
                );
                table.update(updated);
                return true;
            }
            return false;
        });
    }
}
