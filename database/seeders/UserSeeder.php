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
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "Examinee User {$i}",
                'email' => "examinee{$i}@example.com",
                'password' => Hash::make('password'),
                'role' => 'student',
            ]);
        }
    }
}
