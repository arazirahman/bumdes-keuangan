<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        // ===============================
        // SUPER ADMIN (FULL AKSES)
        // ===============================
        User::updateOrCreate(
            ['email' => 'superadmin@bumdes.test'],
            [
                'name' => 'Super Admin BUMDes',
                'password' => Hash::make('password123'),
                'role' => 'superadmin',
            ]
        );

        // ===============================
        // ADMIN OPERATOR (INPUT + EDIT)
        // ===============================
        User::updateOrCreate(
            ['email' => 'operator@bumdes.test'],
            [
                'name' => 'Admin Operator',
                'password' => Hash::make('password123'),
                'role' => 'operator',
            ]
        );
    }
}
