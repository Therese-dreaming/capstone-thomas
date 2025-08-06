<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@pcc.edu.ph',
            'password' => Hash::make('password123'),
            'role' => 'Admin',
            'email_verified_at' => now(),
        ]);

        // Regular User
        User::create([
            'name' => 'John Doe',
            'email' => 'user@pcc.edu.ph',
            'password' => Hash::make('password123'),
            'role' => 'User',
            'email_verified_at' => now(),
        ]);

        // IOSA User
        User::create([
            'name' => 'IOSA Representative',
            'email' => 'iosa@pcc.edu.ph',
            'password' => Hash::make('password123'),
            'role' => 'IOSA',
            'email_verified_at' => now(),
        ]);

        // Create Mhadel user
        User::create([
            'name' => 'Ms. Mhadel',
            'email' => 'mhadel@pcc.edu.ph',
            'password' => Hash::make('password'),
            'role' => 'Ms. Mhadel',
            'email_verified_at' => now(),
        ]);

        // OTP (Office of the President) User
        User::create([
            'name' => 'OTP Representative',
            'email' => 'otp@pcc.edu.ph',
            'password' => Hash::make('password123'),
            'role' => 'OTP',
            'email_verified_at' => now(),
        ]);

        // Additional sample users
        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane.smith@pcc.edu.ph',
            'password' => Hash::make('password123'),
            'role' => 'User',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Bob Johnson',
            'email' => 'bob.johnson@pcc.edu.ph',
            'password' => Hash::make('password123'),
            'role' => 'User',
            'email_verified_at' => now(),
        ]);

        // Create Dr. Javier user
        User::create([
            'name' => 'Dr. Javier',
            'email' => 'drjavier@pcc.edu.ph',
            'password' => Hash::make('password'),
            'role' => 'Dr. Javier',
        ]);
    }
}
