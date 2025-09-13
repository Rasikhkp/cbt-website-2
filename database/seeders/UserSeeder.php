<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Rasikh Khalil Pasha',
            'email' => 'rasikhonly@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create teacher user
        User::create([
            'name' => 'Committee User',
            'email' => 'committee@example.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        // Create student user
        User::create([
            'name' => 'Examinee User',
            'email' => 'examinee@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);
    }
}
