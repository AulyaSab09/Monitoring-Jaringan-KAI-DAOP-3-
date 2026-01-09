<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Monitor;
use Symfony\Component\Process\Process;

class CheckDeviceStatus extends Command
{
    protected $signature = 'device:check';
    protected $description = 'Extreme Realtime Monitoring - Parallel Chunk Mode for 100+ Devices';

    public function handle()
    {
        $this->info('STARTING EXTREME MONITORING (MySQL Optimized)...');
        $this->info('Mode: Parallel Chunk (25 devices per batch)');

        while (true) {
            $startTime = microtime(true);
            
            // 1. Refresh list dari database agar device baru langsung terbaca
            $monitors = Monitor::all();
            
            // 2. Bagi menjadi beberapa kelompok (Chunk) agar CPU tidak overload
            $chunks = $monitors->chunk(25); 

            foreach ($chunks as $chunk) {
                $batch = [];

                // Lanch Ping secara paralel dalam satu batch
                foreach ($chunk as $monitor) {
                    // Pakai -n 1 agar tetap super cepat
                    $process = new Process(["ping", "-n", "1", "-w", "1000", $monitor->ip_address]);
                    $process->start();
                    
                    $batch[] = [
                        'process' => $process,
                        'monitor' => $monitor
                    ];
                }

                // Tunggu dan proses hasil batch ini
                foreach ($batch as $item) {
                    $process = $item['process'];
                    $monitor = $item['monitor'];

                    $process->wait(); // Menunggu proses ping selesai
                    
                    $outputString = $process->getOutput();
                    $newStatus = 'Disconnected'; 
                    $newLatency = 0;

                    // Logika Penentuan Status
                    if (strpos($outputString, 'TTL=') !== false) {
                        if (preg_match('/time[=<](\d+)/i', $outputString, $matches)) {
                            $newLatency = (int)$matches[1];
                        } else {
                            $newLatency = 1; 
                        }

                        // Rule Mentor: 1-2 digit Connected, 3 digit Unstable
                        $newStatus = ($newLatency >= 100) ? 'Unstable' : 'Connected';
                    }

                    // Update History Array
                    $history = $monitor->history ?? [];
                    $history[] = ($newStatus !== 'Disconnected') ? $newLatency : 0;
                    
                    if (count($history) > 20) {
                        array_shift($history); 
                    }
                    
                    // Simpan ke MySQL
                    $monitor->timestamps = false; 
                    $monitor->status = $newStatus;
                    $monitor->latency = $newLatency;
                    $monitor->history = $history;
                    $monitor->updated_at = now();
                    $monitor->save();
                }
            }

            $executionTime = round(microtime(true) - $startTime, 2);
            $this->info('[' . now()->format('H:i:s') . "] Cycle done in {$executionTime}s. Devices: " . $monitors->count());
            
            // Jeda 0.1 detik agar loop tidak memakan 100% CPU
            usleep(100000); 
        }
    }
}