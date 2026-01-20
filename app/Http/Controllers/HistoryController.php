<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Incident::with('monitor')->orderBy('down_at', 'desc');

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
        $query = \App\Models\Incident::with('monitor')->orderBy('down_at', 'desc');

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
    public function reset()
    {
        \App\Models\Incident::truncate();
        return redirect()->route('history.index')->with('success', 'Riwayat insiden berhasil direset.');
    }
}
