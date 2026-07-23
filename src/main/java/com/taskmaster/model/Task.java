package com.taskmaster.model;

import io.succinct.recordmaster.annotations.Id;
import io.succinct.recordmaster.annotations.Index;
import io.succinct.recordmaster.Record;
import java.time.Instant;

public record Task(
        @Id String id,
        String title,
        String description,
        TaskStatus status,
        String dueDate,
        @Index(ordered = true) boolean purged,
        Instant createdAt,
        Instant updatedAt
) implements Record {

    public String getFormattedDueDate() {
        if (dueDate == null || dueDate.trim().isEmpty()) {
            return null;
        }
        try {
            java.time.LocalDate date = java.time.LocalDate.parse(dueDate);
            return date.format(java.time.format.DateTimeFormatter.ofPattern("MMM dd, yyyy"));
        } catch (Exception e) {
            return dueDate;
        }
    }
}
