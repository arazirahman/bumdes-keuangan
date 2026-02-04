<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Village;

class VillageAndOperatorSeeder extends Seeder
{
    public function run(): void
    {
        $desaList = [
            "Desa Sungai Tabukan",
            "Desa Nelayan",
            "Desa Pematang Benteng",
            "Desa Pematang Benteng Hilir",
            "Desa Galagah Hulu",
            "Desa Galagah",
            "Desa Teluk Cati",
            "Desa Banua Hanyar",
            "Desa Pasar Sabtu",
            "Desa Hilir Mesjid",
            "Desa Gampa Raya",
            "Desa Sungai Haji",
            "Desa Rantau Bujur Hulu",
            "Desa Rantau Bujur Tengah",
            "Desa Rantau Bujur Hilir",
            "Desa Rantau Bujur Darat",
            "Desa Tambalang Raya",
        ];

        foreach ($desaList as $name) {
            Village::firstOrCreate(['name' => $name], ['is_active' => true]);
        }

        // Buat 17 operator (email unik)
        // Password default: operator123
        foreach (Village::orderBy('name')->get() as $village) {
            $email = 'operator.' . $village->id . '@simkeu.local';

            User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => 'Operator ' . $village->name,
                    'role' => 'operator',
                    'village_id' => $village->id,
                    'password' => Hash::make('operator123'),
                ]
            );
        }

        // Pastikan superadmin ada (password: superadmin123)
        User::firstOrCreate(
            ['email' => 'superadmin@simkeu.local'],
            [
                'name' => 'Super Admin',
                'role' => 'superadmin',
                'village_id' => null,
                'password' => Hash::make('superadmin123'),
            ]
        );
    }
}
