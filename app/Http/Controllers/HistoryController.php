<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Incident;

class HistoryController extends Controller
{
    // =================================================================
    // 1. HALAMAN UTAMA HISTORY
    // =================================================================
    public function index(Request $request)
    {
        // Query Dasar
        $query = Incident::with('monitor')->orderBy('down_at', 'desc');

        // Filter Status
        if ($request->filled('status')) {
            if ($request->status == 'resolved') {
                $query->whereNotNull('up_at');
            } elseif ($request->status == 'ongoing') {
                $query->whereNull('up_at');
            }
        }

        // Filter Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('down_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('down_at', '<=', $request->end_date);
        }

        // Search Device
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

    // =================================================================
    // 2. LOAD DATA UNTUK TABEL (AJAX)
    // =================================================================
    public function getTableData(Request $request)
    {
        $query = Incident::with('monitor')->orderBy('down_at', 'desc');

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

        $incidents = $query->paginate(100)->withQueryString();

        return view('components.history-table-rows', compact('incidents'));
    }

    // =================================================================
    // 3. RESET DATA HISTORY
    // =================================================================
    public function reset(Request $request)
    {
        if ($request->filled('period')) {
            $query = Incident::query();
            
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
                    return redirect()->back()->with('error', 'Periode tidak valid.');
            }
            
            $query->delete();
            return redirect()->route('history.index')->with('success', $msg);
        }

        Incident::truncate();
        return redirect()->route('history.index')->with('success', 'Semua riwayat insiden berhasil direset.');
    }

    // Method export dipindahkan ke ExportController
}