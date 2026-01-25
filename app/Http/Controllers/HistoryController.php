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
}
