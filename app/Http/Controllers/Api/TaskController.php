<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Department;

class TaskController extends Controller
{
    private function relations(): array
    {
        return [
            'department:id,name',
            'creator:id,name,username',
            'assignee:id,name,username',
        ];
    }

    public function options(): JsonResponse
    {
        $user = auth('api')->user();

        if (!$user || !$user->can('create', Task::class)) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin membuat tugas.',
            ], 403);
        }

        if ($user->role === 'admin') {
            $departments = Department::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            $assignees = User::query()
                ->where('role', 'member')
                ->select('id', 'name', 'username', 'department_id')
                ->orderBy('name')
                ->get();
        } else {
            if (!$user->department_id) {
                return response()->json([
                    'message' => 'Manager belum memiliki departemen.',
                ], 403);
            }

            $departments = Department::query()
                ->where('id', $user->department_id)
                ->select('id', 'name')
                ->get();

            $assignees = User::query()
                ->where('role', 'member')
                ->where('department_id', $user->department_id)
                ->select('id', 'name', 'username', 'department_id')
                ->orderBy('name')
                ->get();
        }

        return response()->json([
            'departments' => $departments,
            'assignees' => $assignees,
        ]);
    }

    public function index(): JsonResponse
    {
        $user = auth('api')->user();

        if (!$user || !$user->can('viewAny', Task::class)) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin melihat tugas.',
            ], 403);
        }

        $query = Task::with($this->relations());

        if ($user->role === 'admin') {
        } elseif ($user->role === 'manager') {
            if (!$user->department_id) {
                return response()->json([
                    'message' => 'Manager belum memiliki departemen.',
                ], 403);
            }

            $query->where('department_id', $user->department_id)
                ->where(
                    'classification',
                    '<=',
                    $user->clearance_level
                );
        } elseif ($user->role === 'member') {
            if (!$user->department_id) {
                return response()->json([
                    'message' => 'Member belum memiliki departemen.',
                ], 403);
            }

            $query->where('assigned_to', $user->id)
                ->where('department_id', $user->department_id)
                ->where(
                    'classification',
                    '<=',
                    $user->clearance_level
                );
        } else {
            return response()->json([
                'message' => 'Role tidak dikenal.',
            ], 403);
        }

        return response()->json([
            'message' => 'Daftar tugas berhasil diambil.',
            'data' => $query->orderByDesc('id')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = auth('api')->user();

        if (!$user || !$user->can('create', Task::class)) {
            return response()->json([
                'message' => 'Role Anda tidak diizinkan membuat tugas.',
            ], 403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'department_id' => [
                'required', 'integer', 'exists:departments,id',
            ],
            'assigned_to' => [
                'required', 'integer', 'exists:users,id',
            ],
            'status' => [
                'sometimes', 'in:todo,in_progress,completed',
            ],
            'classification' => [
                'required', 'integer', 'between:1,3',
            ],
            'due_date' => ['nullable', 'date'],
        ]);

        $departmentId = (int) $validated['department_id'];
        $classification = (int) $validated['classification'];

        if (
            $user->role === 'manager'
            && $departmentId !== (int) $user->department_id
        ) {
            return response()->json([
                'message' => 'Manager hanya boleh membuat tugas di departemennya.',
            ], 403);
        }

        if (
            $user->role !== 'admin'
            && $classification > (int) $user->clearance_level
        ) {
            return response()->json([
                'message' => 'Clearance Anda tidak mencukupi.',
            ], 403);
        }

        $assignee = User::findOrFail($validated['assigned_to']);

        if ((int) $assignee->department_id !== $departmentId) {
            return response()->json([
                'message' => 'Penerima tugas harus berasal dari departemen yang dipilih.',
            ], 403);
        }

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'department_id' => $departmentId,
            'created_by' => $user->id,
            'assigned_to' => $assignee->id,
            'status' => $validated['status'] ?? 'todo',
            'classification' => $classification,
            'due_date' => $validated['due_date'] ?? null,
        ]);

        $task->load($this->relations());

        return response()->json([
            'message' => 'Tugas berhasil dibuat.',
            'data' => $task,
        ], 201);
    }

    public function show(Task $task): JsonResponse
    {
        $user = auth('api')->user();

        if (!$user || !$user->can('view', $task)) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin melihat tugas ini.',
            ], 403);
        }

        $task->load($this->relations());

        return response()->json([
            'data' => $task,
        ]);
    }

    public function update(
        Request $request,
        Task $task
    ): JsonResponse {
        $user = auth('api')->user();

        if (!$user || !$user->can('update', $task)) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin mengedit tugas ini.',
            ], 403);
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'department_id' => [
                'sometimes', 'required', 'integer',
                'exists:departments,id',
            ],
            'assigned_to' => [
                'sometimes', 'required', 'integer',
                'exists:users,id',
            ],
            'status' => [
                'sometimes', 'required',
                'in:todo,in_progress,completed',
            ],
            'classification' => [
                'sometimes', 'required', 'integer', 'between:1,3',
            ],
            'due_date' => ['sometimes', 'nullable', 'date'],
        ]);

        $departmentId = (int) (
            $validated['department_id'] ?? $task->department_id
        );

        $assigneeId = (int) (
            $validated['assigned_to'] ?? $task->assigned_to
        );

        $classification = (int) (
            $validated['classification'] ?? $task->classification
        );

        if (
            $user->role === 'manager'
            && $departmentId !== (int) $user->department_id
        ) {
            return response()->json([
                'message' => 'Manager tidak boleh memindahkan tugas ke departemen lain.',
            ], 403);
        }

        $assignee = User::findOrFail($assigneeId);

        if ((int) $assignee->department_id !== $departmentId) {
            return response()->json([
                'message' => 'Departemen penerima dan departemen tugas harus sama.',
            ], 403);
        }

        if (
            $user->role !== 'admin'
            && $classification > (int) $user->clearance_level
        ) {
            return response()->json([
                'message' => 'Clearance Anda tidak mencukupi.',
            ], 403);
        }

        $task->update($validated);
        $task->load($this->relations());

        return response()->json([
            'message' => 'Tugas berhasil diperbarui.',
            'data' => $task,
        ]);
    }

    public function updateStatus(
        Request $request,
        Task $task
    ): JsonResponse {
        $user = auth('api')->user();

        if (!$user || !$user->can('updateStatus', $task)) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin memperbarui status tugas ini.',
            ], 403);
        }

        $validated = $request->validate([
            'status' => [
                'required',
                'in:todo,in_progress,completed',
            ],
        ]);

        $task->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'message' => 'Status tugas berhasil diperbarui.',
            'data' => $task->fresh()->load($this->relations()),
        ]);
    }

    public function destroy(Task $task): JsonResponse
    {
        $user = auth('api')->user();

        if (!$user || !$user->can('delete', $task)) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin menghapus tugas ini.',
            ], 403);
        }

        $task->delete();

        return response()->json([
            'message' => 'Tugas berhasil dihapus.',
        ]);
    }
}