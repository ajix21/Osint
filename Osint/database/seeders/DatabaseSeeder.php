<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'      => 'Administrator',
                'username'  => 'admin',
                'email'     => 'admin@leakosint.local',
                'password'  => Hash::make('Admin@12345'),
                'role'      => 'admin',
                'is_active' => true,
            ],
            [
                'name'      => 'Operator OSINT',
                'username'  => 'operator',
                'email'     => 'operator@leakosint.local',
                'password'  => Hash::make('Operator@12345'),
                'role'      => 'operator',
                'is_active' => true,
            ],
            [
                'name'      => 'Viewer Only',
                'username'  => 'viewer',
                'email'     => 'viewer@leakosint.local',
                'password'  => Hash::make('Viewer@12345'),
                'role'      => 'viewer',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['username' => $userData['username']],
                $userData
            );
        }

        $this->command->info('✓ Default users created:');
        $this->command->table(
            ['Username', 'Password', 'Role'],
            [
                ['admin',    'Admin@12345',    'admin'],
                ['operator', 'Operator@12345', 'operator'],
                ['viewer',   'Viewer@12345',   'viewer'],
            ]
        );
        $this->command->warn('!! Segera ganti password setelah login pertama !!');
    }
}
