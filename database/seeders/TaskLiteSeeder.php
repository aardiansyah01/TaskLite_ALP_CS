<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TaskLiteSeeder extends Seeder
{
    public function run(): void
    {
        $it = Department::firstOrCreate([
            'name' => 'Informatika',
        ]);

        $hr = Department::firstOrCreate([
            'name' => 'HR',
        ]);

        $admin = User::updateOrCreate(
            ['username' => 'admin1'],
            [
                'name' => 'Admin TaskLite',
                'email' => 'admin@tasklite.local',
                'password' => Hash::make('TaskLite123!'),
                'role' => 'admin',
                'department_id' => null,
                'clearance_level' => 3,
            ]
        );

        // 3. Buat akun Manager
        $managerIt = User::updateOrCreate(
            ['username' => 'manager_it'],
            [
                'name' => 'Manager Informatika',
                'email' => 'manager.it@tasklite.local',
                'password' => Hash::make('TaskLite123!'),
                'role' => 'manager',
                'department_id' => $it->id,
                'clearance_level' => 2,
            ]
        );

        $managerHr = User::updateOrCreate(
            ['username' => 'manager_hr'],
            [
                'name' => 'Manager HR',
                'email' => 'manager.hr@tasklite.local',
                'password' => Hash::make('TaskLite123!'),
                'role' => 'manager',
                'department_id' => $hr->id,
                'clearance_level' => 2,
            ]
        );

        // 4. Buat akun Member
        $memberIt = User::updateOrCreate(
            ['username' => 'member_it'],
            [
                'name' => 'Member Informatika',
                'email' => 'member.it@tasklite.local',
                'password' => Hash::make('TaskLite123!'),
                'role' => 'member',
                'department_id' => $it->id,
                'clearance_level' => 1,
            ]
        );

        $memberIt2 = User::updateOrCreate(
            ['username' => 'member_it2'],
            [
                'name' => 'Member Informatika 2',
                'email' => 'member.it2@tasklite.local',
                'password' => Hash::make('TaskLite123!'),
                'role' => 'member',
                'department_id' => $it->id,
                'clearance_level' => 1,
            ]
        );

        $memberHr = User::updateOrCreate(
            ['username' => 'member_hr'],
            [
                'name' => 'Member HR',
                'email' => 'member.hr@tasklite.local',
                'password' => Hash::make('TaskLite123!'),
                'role' => 'member',
                'department_id' => $hr->id,
                'clearance_level' => 1,
            ]
        );

        Task::updateOrCreate(
            [
                'title' => 'Implementasi REST API',
                'department_id' => $it->id,
            ],
            [
                'description' => 'Membuat endpoint CRUD TaskLite.',
                'created_by' => $managerIt->id,
                'assigned_to' => $memberIt->id,
                'status' => 'todo',
                'classification' => 1,
            ]
        );

        Task::updateOrCreate(
            [
                'title' => 'Dokumentasi API Rahasia',
                'department_id' => $it->id,
            ],
            [
                'description' => 'Dokumentasi dengan klasifikasi Confidential.',
                'created_by' => $managerIt->id,
                'assigned_to' => $memberIt->id,
                'status' => 'in_progress',
                'classification' => 2,
            ]
        );

        Task::updateOrCreate(
            [
                'title' => 'Pengujian Postman',
                'department_id' => $it->id,
            ],
            [
                'description' => 'Menguji respons REST API.',
                'created_by' => $managerIt->id,
                'assigned_to' => $memberIt2->id,
                'status' => 'todo',
                'classification' => 1,
            ]
        );

        Task::updateOrCreate(
            [
                'title' => 'Laporan Sprint Selesai',
                'department_id' => $it->id,
            ],
            [
                'description' => 'Laporan pekerjaan yang sudah diselesaikan.',
                'created_by' => $managerIt->id,
                'assigned_to' => $memberIt->id,
                'status' => 'completed',
                'classification' => 1,
            ]
        );

        Task::updateOrCreate(
            [
                'title' => 'Rekrutmen Karyawan',
                'department_id' => $hr->id,
            ],
            [
                'description' => 'Mengelola proses rekrutmen karyawan.',
                'created_by' => $managerHr->id,
                'assigned_to' => $memberHr->id,
                'status' => 'todo',
                'classification' => 1,
            ]
        );
    }
}