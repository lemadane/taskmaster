<?php

declare(strict_types=1);

namespace Taskmaster\Views;

use Taskmaster\Models\Task;
use Taskmaster\Enums\TaskStatus;

final class TaskWebView
{
    /**
     * @param Task[] $tasks
     */
    public static function index(array $tasks): void
    {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Taskmaster</title>
            <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <style>
                body {
                    font-family: 'Roboto', sans-serif;
                    background-color: #f7f9ff;
                    color: #1c1b1f;
                    min-height: 100vh;
                    margin: 0;
                }
                
                /* Material Top App Bar */
                .app-bar {
                    background-color: #6750A4;
                    color: #ffffff;
                    padding: 0.75rem 1.5rem;
                    box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
                    display: flex;
                    align-items: center;
                    position: sticky;
                    top: 0;
                    z-index: 1000;
                }
                .app-bar-title {
                    font-size: 1.25rem;
                    font-weight: 500;
                    color: #ffffff;
                    text-decoration: none;
                    display: flex;
                    align-items: center;
                }

                /* Container */
                .main-container {
                    max-width: 1200px;
                    margin: 2rem auto;
                    padding: 0 1.5rem;
                }

                /* Header Section */
                .header-section {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 2rem;
                }
                .header-title {
                    font-size: 2rem;
                    font-weight: 400;
                    margin: 0;
                }
                .header-subtitle {
                    font-size: 0.875rem;
                    color: #49454f;
                    margin: 0.25rem 0 0 0;
                }

                /* Material Buttons */
                .btn-filled {
                    background-color: #6750A4;
                    color: #ffffff;
                    border: none;
                    border-radius: 100px;
                    padding: 0.625rem 1.5rem;
                    font-size: 0.875rem;
                    font-weight: 500;
                    cursor: pointer;
                    display: inline-flex;
                    align-items: center;
                    text-decoration: none;
                    box-shadow: 0px 1px 3px rgba(0,0,0,0.1);
                    transition: box-shadow 0.2s, background-color 0.2s;
                }
                .btn-filled:hover {
                    background-color: #533f8a;
                    box-shadow: 0px 2px 6px rgba(0,0,0,0.2);
                }

                .btn-text {
                    background: none;
                    border: none;
                    color: #6750A4;
                    padding: 0.5rem 1rem;
                    font-size: 0.875rem;
                    font-weight: 500;
                    cursor: pointer;
                    border-radius: 100px;
                    display: inline-flex;
                    align-items: center;
                    text-decoration: none;
                    transition: background-color 0.2s;
                }
                .btn-text:hover {
                    background-color: rgba(103, 80, 164, 0.08);
                }
                .btn-text.has-text-danger {
                    color: #b3261e;
                }
                .btn-text.has-text-danger:hover {
                    background-color: rgba(179, 38, 30, 0.08);
                }

                /* Responsive Grid */
                .task-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                    gap: 1.5rem;
                }
                
                /* Material Cards */
                .material-card {
                    background-color: #ffffff;
                    border-radius: 12px;
                    padding: 1.5rem;
                    border: 1px solid #cac4d0;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                    min-height: 200px;
                    transition: box-shadow 0.2s, border-color 0.2s;
                }
                .material-card:hover {
                    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.08);
                    border-color: #6750A4;
                }
                
                .card-top {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-start;
                    gap: 0.5rem;
                }
                .card-title {
                    font-size: 1.25rem;
                    font-weight: 500;
                    margin: 0;
                    color: #1c1b1f;
                }
                .card-desc {
                    color: #49454f;
                    font-size: 0.875rem;
                    margin-top: 0.75rem;
                    line-height: 1.4;
                    white-space: pre-wrap;
                }
                .card-meta {
                    margin-top: auto;
                    padding-top: 1rem;
                }
                .due-date {
                    font-size: 0.75rem;
                    color: #49454f;
                    display: flex;
                    align-items: center;
                }
                .card-actions {
                    display: flex;
                    justify-content: flex-end;
                    gap: 0.5rem;
                    margin-top: 1rem;
                    border-top: 1px solid #cac4d0;
                    padding-top: 0.75rem;
                }

                /* Status Badges */
                .m-tag {
                    display: inline-flex;
                    align-items: center;
                    padding: 0.25rem 0.75rem;
                    border-radius: 8px;
                    font-size: 0.75rem;
                    font-weight: 500;
                    white-space: nowrap;
                }
                .m-tag.is-completed {
                    background-color: #e8f5e9;
                    color: #2e7d32;
                }
                .m-tag.is-pending {
                    background-color: #fff8e1;
                    color: #f57f17;
                }
                .m-tag.is-in-progress {
                    background-color: #e3f2fd;
                    color: #0d47a1;
                }
                .m-tag.is-cancelled {
                    background-color: #ffebee;
                    color: #c62828;
                }
                .m-tag.is-on-hold {
                    background-color: #efebe9;
                    color: #4e342e;
                }
                .m-tag.is-on-queue {
                    background-color: #eceff1;
                    color: #37474f;
                }

                /* Empty state */
                .empty-state {
                    text-align: center;
                    padding: 4rem 2rem;
                    background-color: #ffffff;
                    border-radius: 12px;
                    border: 1px solid #cac4d0;
                }
                .empty-title {
                    font-size: 1.5rem;
                    font-weight: 400;
                    margin: 1rem 0 0.5rem;
                }
                .empty-desc {
                    color: #49454f;
                    margin-bottom: 1.5rem;
                }

                /* Responsive adjustments */
                @media (max-width: 768px) {
                    .header-section {
                        flex-direction: column;
                        align-items: flex-start;
                        gap: 1rem;
                    }
                }
            </style>
        </head>
        <body>
            <nav class="app-bar">
                <a class="app-bar-title" href="/tasks">
                    <span class="icon mr-2"><i class="fa-solid fa-list-check"></i></span> Taskmaster
                </a>
            </nav>

            <div class="main-container">
                <div class="header-section">
                    <div>
                        <h1 class="header-title">My Tasks</h1>
                        <p class="header-subtitle">Manage your daily tasks and schedule</p>
                    </div>
                    <div>
                        <a class="btn-filled" href="/tasks/create">
                            <span class="icon" style="margin-right: 0.5rem;"><i class="fa-solid fa-plus"></i></span>
                            <span>Create Task</span>
                        </a>
                    </div>
                </div>

                <?php if (empty($tasks)): ?>
                    <div class="empty-state">
                        <span class="icon" style="color: #cac4d0; font-size: 3rem;">
                            <i class="fa-solid fa-clipboard-list"></i>
                        </span>
                        <h2 class="empty-title">No tasks found</h2>
                        <p class="empty-desc">Get started by creating your very first task!</p>
                        <a class="btn-filled" href="/tasks/create">Create Task</a>
                    </div>
                <?php else: ?>
                    <div class="task-grid">
                        <?php foreach ($tasks as $task): ?>
                            <?php 
                                // Map task status to CSS class
                                $statusClass = 'is-pending';
                                $statusIcon = 'fa-clock';
                                
                                switch ($task->status->value) {
                                    case 'completed':
                                        $statusClass = 'is-completed';
                                        $statusIcon = 'fa-circle-check';
                                        break;
                                    case 'in_progress':
                                        $statusClass = 'is-in-progress';
                                        $statusIcon = 'fa-spinner fa-spin-pulse';
                                        break;
                                    case 'cancelled':
                                        $statusClass = 'is-cancelled';
                                        $statusIcon = 'fa-circle-xmark';
                                        break;
                                    case 'on_hold':
                                        $statusClass = 'is-on-hold';
                                        $statusIcon = 'fa-circle-pause';
                                        break;
                                    case 'on_queue':
                                        $statusClass = 'is-on-queue';
                                        $statusIcon = 'fa-list-ol';
                                        break;
                                    case 'pending':
                                    default:
                                        $statusClass = 'is-pending';
                                        $statusIcon = 'fa-clock';
                                        break;
                                }
                            ?>
                            <div class="material-card">
                                <div>
                                    <div class="card-top">
                                        <h3 class="card-title"><?= htmlspecialchars($task->title) ?></h3>
                                        <span class="m-tag <?= $statusClass ?>">
                                            <span class="icon" style="margin-right: 0.25rem;"><i class="fa-solid <?= $statusIcon ?>"></i></span>
                                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $task->status->value))) ?>
                                        </span>
                                    </div>
                                    <p class="card-desc"><?= htmlspecialchars($task->description ?? 'No description provided.') ?></p>
                                </div>
                                <div class="card-meta">
                                    <?php if ($task->dueDate): ?>
                                        <div class="due-date">
                                            <span class="icon" style="margin-right: 0.25rem;"><i class="fa-solid fa-calendar-days"></i></span>
                                            Due: <?= htmlspecialchars($task->dueDate->format('M d, Y')) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="card-actions">
                                        <a href="/tasks/<?= htmlspecialchars($task->id) ?>/edit" class="btn-text">
                                            <span class="icon" style="margin-right: 0.25rem;"><i class="fa-solid fa-pen-to-square"></i></span> Edit
                                        </a>
                                        <form
                                            action="/tasks/<?= htmlspecialchars($task->id) ?>/delete"
                                            method="POST"
                                            style="display: inline;"
                                            onsubmit="return confirm('Are you sure you want to delete this task?');"
                                        >
                                            <button type="submit" class="btn-text has-text-danger">
                                                <span class="icon" style="margin-right: 0.25rem;"><i class="fa-solid fa-trash-can"></i></span> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </body>
        </html>
        <?php
    }

    public static function create(): void
    {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Create Task</title>
            <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <style>
                body {
                    font-family: 'Roboto', sans-serif;
                    background-color: #f7f9ff;
                    color: #1c1b1f;
                    min-height: 100vh;
                    margin: 0;
                }
                
                /* Top App Bar */
                .app-bar {
                    background-color: #6750A4;
                    color: #ffffff;
                    padding: 0.75rem 1.5rem;
                    box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
                    display: flex;
                    align-items: center;
                    position: sticky;
                    top: 0;
                    z-index: 1000;
                }
                .app-bar-title {
                    font-size: 1.25rem;
                    font-weight: 500;
                    color: #ffffff;
                    text-decoration: none;
                    display: flex;
                    align-items: center;
                }

                /* Container */
                .main-container {
                    max-width: 600px;
                    margin: 3rem auto;
                    padding: 0 1.5rem;
                }

                /* Card Box */
                .m-box {
                    background-color: #ffffff;
                    border-radius: 12px;
                    padding: 2rem;
                    border: 1px solid #cac4d0;
                    box-shadow: 0px 1px 3px rgba(0,0,0,0.05);
                }

                .form-title {
                    font-size: 1.5rem;
                    font-weight: 400;
                    margin: 0 0 1.5rem 0;
                    display: flex;
                    align-items: center;
                }

                /* Input Styles */
                .form-group {
                    margin-bottom: 1.5rem;
                }
                .form-label {
                    display: block;
                    font-size: 0.875rem;
                    font-weight: 500;
                    margin-bottom: 0.5rem;
                    color: #1c1b1f;
                }
                .form-input, .form-textarea, .form-select {
                    width: 100%;
                    box-sizing: border-box;
                    padding: 0.75rem 1rem;
                    border: 1px solid #79747e;
                    border-radius: 4px;
                    font-family: inherit;
                    font-size: 1rem;
                    color: #1c1b1f;
                    background-color: #ffffff;
                    transition: border-color 0.2s;
                }
                .form-input:focus, .form-textarea:focus, .form-select:focus {
                    border-color: #6750A4;
                    outline: none;
                }
                .form-textarea {
                    resize: vertical;
                    min-height: 120px;
                }
                .form-row {
                    display: flex;
                    gap: 1rem;
                }
                .form-row > * {
                    flex: 1;
                }

                /* Material Buttons */
                .btn-filled {
                    background-color: #6750A4;
                    color: #ffffff;
                    border: none;
                    border-radius: 100px;
                    padding: 0.625rem 1.5rem;
                    font-size: 0.875rem;
                    font-weight: 500;
                    cursor: pointer;
                    display: inline-flex;
                    align-items: center;
                    text-decoration: none;
                    box-shadow: 0px 1px 3px rgba(0,0,0,0.1);
                    transition: box-shadow 0.2s, background-color 0.2s;
                }
                .btn-filled:hover {
                    background-color: #533f8a;
                    box-shadow: 0px 2px 6px rgba(0,0,0,0.2);
                }

                .btn-text {
                    background: none;
                    border: none;
                    color: #6750A4;
                    padding: 0.625rem 1.5rem;
                    font-size: 0.875rem;
                    font-weight: 500;
                    cursor: pointer;
                    border-radius: 100px;
                    display: inline-flex;
                    align-items: center;
                    text-decoration: none;
                    transition: background-color 0.2s;
                }
                .btn-text:hover {
                    background-color: rgba(103, 80, 164, 0.08);
                }

                .actions-group {
                    display: flex;
                    gap: 0.5rem;
                    margin-top: 2rem;
                }

                @media (max-width: 480px) {
                    .form-row {
                        flex-direction: column;
                        gap: 1.5rem;
                    }
                }
            </style>
        </head>
        <body>
            <nav class="app-bar">
                <a class="app-bar-title" href="/tasks">
                    <span class="icon mr-2"><i class="fa-solid fa-list-check"></i></span> Taskmaster
                </a>
            </nav>

            <div class="main-container">
                <div class="m-box">
                    <h1 class="form-title">
                        <span class="icon mr-2" style="color: #6750A4; margin-right: 0.5rem;"><i class="fa-solid fa-circle-plus"></i></span>Create Task
                    </h1>

                    <form action="/tasks" method="POST">
                        <div class="form-group">
                            <label class="form-label">Title</label>
                            <input class="form-input" type="text" name="title" placeholder="e.g. Plan layout structure" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea class="form-textarea" name="description" placeholder="Write description here..."></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <?php foreach (TaskStatus::cases() as $status): ?>
                                        <option value="<?= htmlspecialchars($status->value) ?>">
                                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $status->value))) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Due Date</label>
                                <input class="form-input" type="date" name="due_date">
                            </div>
                        </div>

                        <div class="actions-group">
                            <button type="submit" class="btn-filled">Save Task</button>
                            <a href="/tasks" class="btn-text">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </body>
        </html>
        <?php
    }

    public static function edit(Task $task): void
    {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Edit Task</title>
            <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <style>
                body {
                    font-family: 'Roboto', sans-serif;
                    background-color: #f7f9ff;
                    color: #1c1b1f;
                    min-height: 100vh;
                    margin: 0;
                }
                
                /* Top App Bar */
                .app-bar {
                    background-color: #6750A4;
                    color: #ffffff;
                    padding: 0.75rem 1.5rem;
                    box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
                    display: flex;
                    align-items: center;
                    position: sticky;
                    top: 0;
                    z-index: 1000;
                }
                .app-bar-title {
                    font-size: 1.25rem;
                    font-weight: 500;
                    color: #ffffff;
                    text-decoration: none;
                    display: flex;
                    align-items: center;
                }

                /* Container */
                .main-container {
                    max-width: 600px;
                    margin: 3rem auto;
                    padding: 0 1.5rem;
                }

                /* Card Box */
                .m-box {
                    background-color: #ffffff;
                    border-radius: 12px;
                    padding: 2rem;
                    border: 1px solid #cac4d0;
                    box-shadow: 0px 1px 3px rgba(0,0,0,0.05);
                }

                .form-title {
                    font-size: 1.5rem;
                    font-weight: 400;
                    margin: 0 0 1.5rem 0;
                    display: flex;
                    align-items: center;
                }

                /* Input Styles */
                .form-group {
                    margin-bottom: 1.5rem;
                }
                .form-label {
                    display: block;
                    font-size: 0.875rem;
                    font-weight: 500;
                    margin-bottom: 0.5rem;
                    color: #1c1b1f;
                }
                .form-input, .form-textarea, .form-select {
                    width: 100%;
                    box-sizing: border-box;
                    padding: 0.75rem 1rem;
                    border: 1px solid #79747e;
                    border-radius: 4px;
                    font-family: inherit;
                    font-size: 1rem;
                    color: #1c1b1f;
                    background-color: #ffffff;
                    transition: border-color 0.2s;
                }
                .form-input:focus, .form-textarea:focus, .form-select:focus {
                    border-color: #6750A4;
                    outline: none;
                }
                .form-textarea {
                    resize: vertical;
                    min-height: 120px;
                }
                .form-row {
                    display: flex;
                    gap: 1rem;
                }
                .form-row > * {
                    flex: 1;
                }

                /* Material Buttons */
                .btn-filled {
                    background-color: #6750A4;
                    color: #ffffff;
                    border: none;
                    border-radius: 100px;
                    padding: 0.625rem 1.5rem;
                    font-size: 0.875rem;
                    font-weight: 500;
                    cursor: pointer;
                    display: inline-flex;
                    align-items: center;
                    text-decoration: none;
                    box-shadow: 0px 1px 3px rgba(0,0,0,0.1);
                    transition: box-shadow 0.2s, background-color 0.2s;
                }
                .btn-filled:hover {
                    background-color: #533f8a;
                    box-shadow: 0px 2px 6px rgba(0,0,0,0.2);
                }

                .btn-text {
                    background: none;
                    border: none;
                    color: #6750A4;
                    padding: 0.625rem 1.5rem;
                    font-size: 0.875rem;
                    font-weight: 500;
                    cursor: pointer;
                    border-radius: 100px;
                    display: inline-flex;
                    align-items: center;
                    text-decoration: none;
                    transition: background-color 0.2s;
                }
                .btn-text:hover {
                    background-color: rgba(103, 80, 164, 0.08);
                }

                .actions-group {
                    display: flex;
                    gap: 0.5rem;
                    margin-top: 2rem;
                }

                @media (max-width: 480px) {
                    .form-row {
                        flex-direction: column;
                        gap: 1.5rem;
                    }
                }
            </style>
        </head>
        <body>
            <nav class="app-bar">
                <a class="app-bar-title" href="/tasks">
                    <span class="icon mr-2"><i class="fa-solid fa-list-check"></i></span> Taskmaster
                </a>
            </nav>

            <div class="main-container">
                <div class="m-box">
                    <h1 class="form-title">
                        <span class="icon mr-2" style="color: #6750A4; margin-right: 0.5rem;"><i class="fa-solid fa-pen-to-square"></i></span>Edit Task
                    </h1>

                    <form action="/tasks/<?= htmlspecialchars($task->id) ?>/update" method="POST">
                        <div class="form-group">
                            <label class="form-label">Title</label>
                            <input
                                class="form-input"
                                type="text"
                                name="title"
                                value="<?= htmlspecialchars($task->title) ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea class="form-textarea" name="description"><?= htmlspecialchars($task->description ?? '') ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <?php foreach (TaskStatus::cases() as $status): ?>
                                        <option value="<?= htmlspecialchars($status->value) ?>" <?= $task->status === $status ? 'selected' : '' ?>>
                                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $status->value))) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Due Date</label>
                                <input
                                    class="form-input"
                                    type="date"
                                    name="due_date"
                                    value="<?= $task->dueDate ? $task->dueDate->format('Y-m-d') : '' ?>"
                                >
                            </div>
                        </div>

                        <div class="actions-group">
                            <button type="submit" class="btn-filled">Update Task</button>
                            <a href="/tasks" class="btn-text">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
}
