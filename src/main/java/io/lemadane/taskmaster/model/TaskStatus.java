package io.lemadane.taskmaster.model;

public enum TaskStatus {
    PENDING("pending", "Pending"),
    IN_PROGRESS("in_progress", "In Progress"),
    COMPLETED("completed", "Completed"),
    CANCELLED("cancelled", "Cancelled"),
    ON_HOLD("on_hold", "On Hold"),
    ON_QUEUE("on_queue", "On Queue");

    private final String value;
    private final String displayName;

    TaskStatus(String value, String displayName) {
        this.value = value;
        this.displayName = displayName;
    }

    public String getValue() {
        return value;
    }

    public String getDisplayName() {
        return displayName;
    }

    public static TaskStatus fromValue(String value) {
        for (TaskStatus status : values()) {
            if (status.value.equalsIgnoreCase(value)) {
                return status;
            }
        }
        throw new IllegalArgumentException("Unknown status: " + value);
    }
}
