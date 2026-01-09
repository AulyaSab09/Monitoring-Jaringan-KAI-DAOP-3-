<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    public function index()
{
    // Hanya ambil data, TIDAK ADA proses ping disini.
    // Jadi loading halaman akan instan/cepat.
    $monitors = Monitor::orderBy('updated_at', 'desc')->get();
    
    return view('preview', compact('monitors'));
}

    // Halaman Form Tambah Data
    public function create()
    {
        return view('create');
    }

    // Proses Simpan Data Baru
    public function store(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ipv4|unique:monitors,ip_address',
            'name' => 'required|string|max:255',
            'type' => 'required|string',
        ]);

        Monitor::create([
            'ip_address' => $request->ip_address,
            'name' => $request->name,
            'type' => $request->type,
            'location' => $request->location,
            'status' => 'Pending', // Status awal
            'latency' => 0,
        ]);

        return redirect('/preview')->with('success', 'IP Berhasil ditambahkan!');
    }

    // Halaman Form Edit
    public function edit($id)
    {
        $monitor = Monitor::findOrFail($id);
        return view('edit', compact('monitor'));
    }

    // Proses Update Data
    public function update(Request $request, $id)
    {
        $request->validate([
            'ip_address' => 'required|ipv4',
            'name' => 'required|string|max:255',
            'type' => 'required|string',
        ]);

        $monitor = Monitor::findOrFail($id);
        $monitor->update([
            'ip_address' => $request->ip_address,
            'name' => $request->name,
            'type' => $request->type,
            'location' => $request->location,
            // Reset status agar dicek ulang
            'status' => 'Pending', 
            'latency' => 0
        ]);

        return redirect('/preview')->with('success', 'IP Berhasil diupdate!');
    }

    // Proses Hapus Data
    public function destroy($id)
    {
        $monitor = Monitor::findOrFail($id);
        $monitor->delete();

        return redirect('/preview')->with('success', 'Data dihapus!');
    }

    // Method baru untuk AJAX
    public function getTableData()
    {
        $monitors = Monitor::orderBy('updated_at', 'desc')->get();
        // Kita return view yang POTONGAN tadi (components/monitor-rows)
        return view('components.monitor-cards', compact('monitors'));
    }

    // Method baru khusus untuk update realtime
    public function getMonitorJson()
    {
        // Kita hanya butuh data penting untuk update UI
        $data = \App\Models\Monitor::select('id', 'status', 'latency', 'history', 'ip_address')->get();
        return response()->json($data);
    }
}