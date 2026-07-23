package com.taskmaster.controller;

import com.fasterxml.jackson.annotation.JsonProperty;

public record TaskRequest(
        String title,
        String description,
        String status,
        @JsonProperty("due_date") String dueDate
) {}
