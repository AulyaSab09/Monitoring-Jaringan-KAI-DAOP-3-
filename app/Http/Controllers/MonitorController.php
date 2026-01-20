<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    public function index()
    {
        // Ambil hanya device parent (tanpa parent_id) dengan children-nya
        // untuk tampilan tree yang proper
        $monitors = Monitor::whereNull('parent_id')
            ->with(['children' => function($q) {
                $q->orderBy('id', 'asc');
            }, 'children.latestIncident', 'latestIncident']) // Load latestIncident untuk parent & children
            ->orderBy('id', 'asc')
            ->get();
        
        // Hitung statistik untuk header - Optimized
        $total = Monitor::count();
        $up = Monitor::where('status', 'Connected')->count();
        $warning = Monitor::where('status', 'Unstable')->count();
        $down = Monitor::where('status', 'Disconnected')->count();
        
        return view('preview', compact('monitors', 'total', 'up', 'warning', 'down'));
    }

    public function data()
    {
        // Ambil hanya device parent (tanpa parent_id) dengan children-nya
        // untuk tampilan tree yang proper (sama seperti index)
        $monitors = Monitor::whereNull('parent_id')
            ->with(['children' => function($q) {
                $q->orderBy('id', 'asc');
            }, 'children.latestIncident', 'latestIncident']) // Load latestIncident untuk parent & children
            ->orderBy('id', 'asc')
            ->get();

        return view('components.monitor-cards', [
            'monitors' => $monitors,
        ]);
    }

    // Halaman Form Tambah Data
    public function create()
    {
        $parentDevice = null;
        if (request()->has('parent_id')) {
            $parentDevice = Monitor::find(request('parent_id'));
        }
        return view('monitor.create', compact('parentDevice'));
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
            'kode_lokasi' => $request->kode_lokasi,
            'parent_id' => $request->parent_id, // Untuk fitur tambah cabang
            'status' => 'Pending', // Status awal
            'latency' => 0,
        ]);

        return redirect('/preview')->with('success', 'Device berhasil ditambahkan!');
    }

    // Halaman Form Edit
    public function edit($id)
    {
        $monitor = Monitor::findOrFail($id);
        // Ambil semua device lain untuk pilihan parent (exclude device ini sendiri dan children-nya)
        // Optimized: Only select id and name
        $allMonitors = Monitor::where('id', '!=', $id)->select('id', 'name')->get();
        return view('monitor.edit', compact('monitor', 'allMonitors'));
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
            'kode_lokasi' => $request->kode_lokasi,
            'parent_id' => $request->parent_id ?: null, // Update parent device
            // Reset status agar dicek ulang
            'status' => 'Pending', 
            'latency' => 0
        ]);

        return redirect('/preview')->with('success', 'Device berhasil diupdate!');
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
        $monitors = Monitor::orderBy('id', 'asc')->get();
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