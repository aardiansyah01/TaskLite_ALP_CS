<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminUserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::query()
            ->select([
                'id',
                'name',
                'username',
                'email',
                'role',
                'department_id',
                'clearance_level',
            ])
            ->with('department:id,name')
            ->orderBy('id')
            ->get();

        return response()->json([
            'message' => 'Daftar pengguna berhasil diambil.',
            'data' => $users,
        ]);
    }
}