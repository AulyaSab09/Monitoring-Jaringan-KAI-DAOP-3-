<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        // Data dummy untuk tampilan Front-End
        $histories = [
            (object)[
                'waktu' => '2026-01-14 15:00:01',
                'nama_perangkat' => 'Router Core Utama',
                'ip_address' => '192.168.1.1',
                'status' => 'UP',
                'stasiun' => 'Stasiun Kejaksan'
            ],
        ];
 
        return view('history', compact('histories'));
    }
}
