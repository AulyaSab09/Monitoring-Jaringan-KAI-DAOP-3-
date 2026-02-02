<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Incident::with('monitor')->orderBy('down_at', 'desc')
            ->whereRaw('TIMESTAMPDIFF(SECOND, down_at, COALESCE(up_at, NOW())) >= 60');

        // 1. Filter Kondisi (Status)
        if ($request->filled('status')) {
            if ($request->status == 'resolved') {
                $query->whereNotNull('up_at');
            } elseif ($request->status == 'ongoing') {
                $query->whereNull('up_at');
            }
        }

        // 2. Filter Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('down_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('down_at', '<=', $request->end_date);
        }

        // 3. Search Device
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('monitor', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $incidents = $query->paginate(10)->withQueryString();

        return view('history', compact('incidents'));
    }

    public function getTableData(Request $request)
    {
        $query = \App\Models\Incident::with('monitor')->orderBy('down_at', 'desc')
            ->whereRaw('TIMESTAMPDIFF(SECOND, down_at, COALESCE(up_at, NOW())) >= 60');

        if ($request->filled('status')) {
            if ($request->status == 'resolved') {
                $query->whereNotNull('up_at');
            } elseif ($request->status == 'ongoing') {
                $query->whereNull('up_at');
            }
        }

        if ($request->filled('start_date')) {
            $query->whereDate('down_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('down_at', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('monitor', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $incidents = $query->paginate(10)->withQueryString();

        return view('components.history-table-rows', compact('incidents'));
    }
    public function reset(Request $request)
    {
        if ($request->filled('period')) {
            $query = \App\Models\Incident::query();
            
            switch ($request->period) {
                case '1_week':
                    $query->where('down_at', '>=', Carbon::now()->subWeek());
                    $msg = 'Riwayat 1 minggu terakhir berhasil dihapus.';
                    break;
                case '1_month':
                    $query->where('down_at', '>=', Carbon::now()->subMonth());
                    $msg = 'Riwayat 1 bulan terakhir berhasil dihapus.';
                    break;
                case '1_year':
                    $query->where('down_at', '>=', Carbon::now()->subYear());
                    $msg = 'Riwayat 1 tahun terakhir berhasil dihapus.';
                    break;
                default:
                    // Jika value aneh, tidak hapus apa-apa atau hapus semua?
                    // Asumsi default aman: tidak hapus apa-apa jika tidak match
                    return redirect()->back()->with('error', 'Periode tidak valid.');
            }
            
            $query->delete();
            return redirect()->route('history.index')->with('success', $msg);
        }

        // Default behavior (jika tidak ada period, misal dari tombol reset all jika ada nanti)
        // Saat ini UI hanya mengirim period. Tapi untuk jaga-jaga kita biarkan truncate kalau params kosong?
        // Atau amannya kita return error aja kalau method ini sekarang specific untuk periode.
        // Tapi lihat user request code: <form action="{{ route('history.reset') }}" ... > <input name="period" ...>
        // Jadi logic lama (truncate all) mungkin tidak terpakai lewat UI ini.
        // Namun, jika user ingin "Reset All", mungkin butuh.
        // Mari kita biarkan truncate jg jika TANPA period (backward comp) tapi UI user pakai period.
        
        \App\Models\Incident::truncate();
        return redirect()->route('history.index')->with('success', 'Semua riwayat insiden berhasil direset.');
    }

    public function export(Request $request)
    {
        $query = \App\Models\Incident::with('monitor')->orderBy('down_at', 'desc');
        // Filter logic same as index
        // 1. Filter Kondisi (Status)
        if ($request->filled('status')) {
            if ($request->status == 'resolved') {
                $query->whereNotNull('up_at');
            } elseif ($request->status == 'ongoing') {
                $query->whereNull('up_at');
            }
        }

        // 2. Filter Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('down_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('down_at', '<=', $request->end_date);
        }

        // 3. Search Device
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('monitor', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=history_insiden_" . date('Y-m-d_H-i-s') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        // Headers sesuai tampilan View
        $columns = array('Perangkat', 'IP Address', 'Lokasi', 'Waktu Down', 'Waktu Up', 'Durasi', 'Status');

        $callback = function() use ($query, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, $columns);

            $query->chunk(100, function($incidents) use ($file) {
                foreach ($incidents as $incident) {
                    $duration = '-';
                    if ($incident->up_at) {
                         $diff = $incident->down_at->diff($incident->up_at);
                         $duration = $diff->format('%Hj %Im %Sd');
                    } else {
                         // Ongoing
                         $diff = $incident->down_at->diff(now());
                         $duration = $diff->format('%Hj %Im %Sd') . ' (Running)';
                    }
                    
                    $row['Perangkat']    = $incident->monitor->name ?? 'N/A';
                    $row['IP Address']   = $incident->monitor->ip_address ?? 'N/A';
                    $row['Lokasi']       = $incident->monitor->location ?? $incident->monitor->kode_lokasi ?? '-';
                    // Format tanggal agar rapih (Excel friendly Y-m-d H:i:s, atau ikut view translatedFormat)
                    // Kita gunakan Y-m-d H:i:s agar bisa disort di Excel
                    $row['Waktu Down']   = $incident->down_at->format('Y-m-d H:i:s');
                    $row['Waktu Up']     = $incident->up_at ? $incident->up_at->format('Y-m-d H:i:s') : 'Sedang Perbaikan...';
                    $row['Durasi']       = $duration;
                    $row['Status']       = $incident->up_at ? ($incident->status ?? 'Resolved') : 'Gangguan Sedang Terjadi';

                    fputcsv($file, array(
                        $row['Perangkat'], 
                        $row['IP Address'], 
                        $row['Lokasi'], 
                        $row['Waktu Down'], 
                        $row['Waktu Up'], 
                        $row['Durasi'], 
                        $row['Status']
                    ));
                }
            });
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
