<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonitorSeeder extends Seeder
{
    public function run(): void
    {
        // Saya sesuaikan datanya persis dengan file preview.blade.php Anda
        $data = [
            [
                'ip_address' => '192.168.1.1',
                'latency' => 12,
                'status' => 'Connected',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(), // Just now
            ],
            [
                'ip_address' => '192.168.1.15',
                'latency' => 45,
                'status' => 'Connected',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()->subMinutes(1), // 1 min ago
            ],
            [
                'ip_address' => '10.0.0.5',
                'latency' => 0, // Timeout biasanya 0 atau -1
                'status' => 'Disconnected',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()->subMinutes(2), // 2 mins ago
            ],
            [
                'ip_address' => '172.16.0.22',
                'latency' => 120,
                'status' => 'Unstable',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()->subSeconds(30), // 30 secs ago
            ],
            [
                'ip_address' => '192.168.2.100',
                'latency' => 8,
                'status' => 'Connected',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(), // Just now
            ],
        ];

        DB::table('monitors')->insert($data);
    }
}