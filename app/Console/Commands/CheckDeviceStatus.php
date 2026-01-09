<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Monitor;

class CheckDeviceStatus extends Command
{
    protected $signature = 'device:check';
    protected $description = 'Cek ping realtime high-performance';

    public function handle()
    {
        $this->info('STARTING REALTIME MONITORING (No Sleep Mode)...');
        
        // OPTIMASI 1: Load monitors sekali saja di luar loop jika daftarnya jarang berubah.
        // Jika user nambah device baru, command harus di-restart. 
        // Kalau butuh dynamic, pindahkan lagi ke dalam while, tapi itu nambah beban query.
        $monitors = Monitor::all(); 

        while (true) {
            $startTime = microtime(true); // Hitung waktu mulai siklus

            // Jika butuh refresh list device tanpa restart script, uncomment baris bawah ini:
            // $monitors = Monitor::all(); 

            foreach ($monitors as $monitor) {
                $ip = $monitor->ip_address;
                
                // Tetap 1000ms agar rule "3 digit" tetap valid.
                // Masalah: Jika RTO, dia akan nge-freeze 1 detik per device yg mati.
                $command = "ping -n 1 -w 1000 " . escapeshellarg($ip);
                
                $output = [];
                $statusExec = 0;
                exec($command, $output, $statusExec);
                $outputString = implode(" ", $output);

                $newStatus = 'Disconnected'; 
                $newLatency = 0;

                if (strpos($outputString, 'TTL=') !== false) {
                    if (preg_match('/time[=<](\d+)/i', $outputString, $matches)) {
                        $newLatency = (int)$matches[1];
                    } else {
                        $newLatency = 1; 
                    }

                    if ($newLatency >= 100) {
                        $newStatus = 'Unstable';
                    } else {
                        $newStatus = 'Connected';
                    }
                }

                // --- LOGIKA UPDATE HISTORY ---
                // Kita sederhanakan akses array agar lebih cepat
                $history = $monitor->history ?? [];
                $history[] = ($newStatus !== 'Disconnected') ? $newLatency : 0;
                
                // Jaga array tetap 20 item
                if (count($history) > 20) {
                    array_shift($history); 
                }
                
                // OPTIMASI 2: Hanya update ke database JIKA ada perubahan status/latency signifikan
                // atau setiap beberapa detik sekali. Tapi karena diminta realtime, kita hajar terus.
                // Warning: SQLite bisa "Locked" kalau write terlalu cepat dan banyak.
                
                $monitor->timestamps = false; 
                $monitor->status = $newStatus;
                $monitor->latency = $newLatency;
                $monitor->history = $history; 
                $monitor->updated_at = now();
                $monitor->save();
            }

            $endTime = microtime(true);
            $executionTime = round(($endTime - $startTime), 4);

            // OPTIMASI 3: Hapus sleep(1).
            // Ganti dengan usleep kecil (misal 50ms) cuma biar CPU gak 100% panas.
            // 50000 microsecond = 0.05 detik.
            $this->info('[' . now()->format('H:i:s') . "] Cycle: {$executionTime}s");
            usleep(50000); 
        }
    }
}