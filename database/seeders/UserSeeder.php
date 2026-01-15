<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pakai updateOrCreate agar kalau dijalankan berkali-kali tidak error "Duplicate Entry"
        User::updateOrCreate(
            ['email' => 'kai@admin.ac.id'], // Cek berdasarkan email
            [
                'name' => 'Admin KAI DAOP 3',
                'password' => Hash::make('123'), // Password di-hash
                'email_verified_at' => now(), // Otomatis verified biar langsung bisa login
            ]
        );
    }
}