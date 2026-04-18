<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user (Lấy từ .env)
        User::create([
            'username' => env('SEEDER_ADMIN_USER', 'admin'),
            'email' => env('SEEDER_ADMIN_EMAIL', 'admin@example.com'),
            'password' => Hash::make(env('SEEDER_ADMIN_PASSWORD', 'password')),
            'full_name' => 'Administrator',
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Staff user
        User::create([
            'username' => 'staff1',
            'email' => 'staff1@gmail.com',
            'password' => Hash::make('password'),
            'full_name' => 'Nguyễn Văn Nhân Viên',
            'role' => 'staff',
            'is_active' => true,
        ]);

        // Teacher user
        User::create([
            'username' => 'teacher1',
            'email' => 'teacher1@gmail.com',
            'password' => Hash::make('password'),
            'full_name' => 'Cô giáo Minh Anh',
            'role' => 'teacher',
            'is_active' => true,
        ]);
    }
}
