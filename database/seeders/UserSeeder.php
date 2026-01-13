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
        User::updateOrCreate(
            ['email' => 'kai@admin.ac.id'],
            [
                'name' => 'Admin KAI',
                'email' => 'kai@admin.ac.id',
                'password' => Hash::make('123'),
            ]
        );
    }
}
