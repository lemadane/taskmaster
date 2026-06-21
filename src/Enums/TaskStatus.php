<?php

declare(strict_types=1);

namespace Taskmaster\Enums;

enum TaskStatus: string {
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case OnHold = 'on_hold';
    case OnQueue = 'on_queue';
}