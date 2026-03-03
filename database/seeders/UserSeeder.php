<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@k7library.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Dr. Amaka Okonkwo',
            'email' => 'faculty@k7library.com',
            'password' => Hash::make('password'),
            'role' => 'faculty',
            'department' => 'Computer Science',
            'institution' => 'K7 University',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'John Student',
            'email' => 'student@k7library.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'student_id' => 'STU2024001',
            'department' => 'Computer Science',
            'institution' => 'K7 University',
            'is_active' => true,
        ]);
    }
}
