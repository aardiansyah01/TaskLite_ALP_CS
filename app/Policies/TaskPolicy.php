<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array(
            $user->role,
            ['admin', 'manager', 'member'],
            true
        );
    }

    public function view(User $user, Task $task): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ((int) $user->clearance_level
            < (int) $task->classification) {
            return false;
        }

        if ($user->role === 'manager') {
            return $user->department_id !== null
                && (int) $user->department_id
                    === (int) $task->department_id;
        }

        if ($user->role === 'member') {
            return (int) $task->assigned_to === (int) $user->id
                && $user->department_id !== null
                && (int) $user->department_id
                    === (int) $task->department_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'manager'
            && $user->department_id !== null;
    }

    public function update(User $user, Task $task): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'manager'
            && $user->department_id !== null
            && (int) $user->department_id
                === (int) $task->department_id
            && (int) $user->clearance_level
                >= (int) $task->classification;
    }

    public function updateStatus(User $user, Task $task): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'manager') {
            return $user->department_id !== null
                && (int) $user->department_id
                    === (int) $task->department_id
                && (int) $user->clearance_level
                    >= (int) $task->classification;
        }

        if ($user->role === 'member') {
            return (int) $task->assigned_to === (int) $user->id
                && $user->department_id !== null
                && (int) $user->department_id
                    === (int) $task->department_id
                && (int) $user->clearance_level
                    >= (int) $task->classification
                && $task->status !== 'completed';
        }

        return false;
    }

    public function delete(User $user, Task $task): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'manager'
            && $user->department_id !== null
            && (int) $user->department_id
                === (int) $task->department_id
            && (int) $user->clearance_level
                >= (int) $task->classification
            && $task->status !== 'completed';
    }
}