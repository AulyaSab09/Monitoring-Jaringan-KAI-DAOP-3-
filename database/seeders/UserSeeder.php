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

        // 2. Baru jalankan updateOrCreate dengan key username
        User::updateOrCreate(
            ['username' => 'admin_kai'], 
            [
                'name' => 'Admin KAI DAOP 3',
                'password' => Hash::make('123'), 
                // 'email' => 'kai@admin.ac.id', // Removed column
                'email_verified_at' => now(), 
            ]
        );
    }
}