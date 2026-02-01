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
        // Ambil device berdasarkan ZONE (hanya yang parent_id NULL alias Root)
        // Load children recursive
        $centers = Monitor::whereNull('parent_id')->center()
            ->with(['children' => function($q) { $q->orderBy('id', 'asc'); }, 'children.latestIncident', 'latestIncident'])
            ->orderBy('id', 'asc')->get();

        $utaras = Monitor::whereNull('parent_id')->lintasUtara()
            ->with(['children' => function($q) { $q->orderBy('id', 'asc'); }, 'children.latestIncident', 'latestIncident'])
            ->orderBy('id', 'asc')->get();

        $selatans = Monitor::whereNull('parent_id')->lintasSelatan()
            ->with(['children' => function($q) { $q->orderBy('id', 'asc'); }, 'children.latestIncident', 'latestIncident'])
            ->orderBy('id', 'asc')->get();
        
        // Hitung statistik untuk header - Optimized
        $total = Monitor::count();
        $up = Monitor::where('status', 'Connected')->count();
        $warning = Monitor::where('status', 'Unstable')->count();
        $down = Monitor::where('status', 'Disconnected')->count();
        
        return view('preview', compact('centers', 'utaras', 'selatans', 'total', 'up', 'warning', 'down'));
    }

    public function data()
    {
        // Ambil hanya device parent (tanpa parent_id) dengan children-nya
        // untuk tampilan tree yang proper (sama seperti index)
        // Sama seperti index, pisahkan by zone
        $centers = Monitor::whereNull('parent_id')->center()
            ->with(['children' => function($q) { $q->orderBy('id', 'asc'); }, 'children.latestIncident', 'latestIncident'])
            ->orderBy('id', 'asc')->get();

        $utaras = Monitor::whereNull('parent_id')->lintasUtara()
            ->with(['children' => function($q) { $q->orderBy('id', 'asc'); }, 'children.latestIncident', 'latestIncident'])
            ->orderBy('id', 'asc')->get();

        $selatans = Monitor::whereNull('parent_id')->lintasSelatan()
            ->with(['children' => function($q) { $q->orderBy('id', 'asc'); }, 'children.latestIncident', 'latestIncident'])
            ->orderBy('id', 'asc')->get();

        // Kita bisa return array view render atau kirim structure
        // Tapi component 'monitor-cards' expect variable $monitors.
        // Kita modif dulu component/monitor-cards agar bisa handle structure baru ATAU
        // Kita return view yang berbeda utk AJAX?
        // Solusi: Kirim array HTML terpisah atau satu view wrapper.
        // Mari kita buat wrapper view baru 'components.monitor-zone-wrapper' atau modif 'preview' structure.
        // Namun AJAX 'monitor.data' biasa merefresh seluruh #tree-wrapper.
        
        return view('components.monitor-zone-wrapper', compact('centers', 'utaras', 'selatans'));
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
        // Check if this is a child device
        $isChildDevice = $request->has('parent_id') && $request->parent_id;

        // Base validation rules
        $rules = [
            'ip_address' => 'required|ipv4|unique:monitors,ip_address',
            'name' => 'required|string|max:255',
            'type' => 'required|string',
        ];

        // Zone is only required for ROOT devices (no parent)
        if (!$isChildDevice) {
            $rules['zone'] = 'required|in:center,lintas utara,lintas selatan';
        }

        $messages = [
            'ip_address.unique' => 'Sudah ada ip perangkat itu, harap ganti.',
        ];

        $request->validate($rules, $messages);

        // VALIDASI LOGIKA BISNIS (only for root devices):
        if (!$isChildDevice && $request->zone !== 'center') {
            $centerExists = Monitor::where('zone', 'center')->exists();
            if (!$centerExists) {
                return back()->withInput()->with('error', 'Anda harus menambahkan Perangkat Pusat (Center) terlebih dahulu sebelum menambahkan jalur Utara/Selatan.');
            }
        }

        // For child devices, inherit zone from parent
        $zone = $request->zone;
        if ($isChildDevice) {
            $parentDevice = Monitor::find($request->parent_id);
            $zone = $parentDevice ? $parentDevice->zone : 'center';
        }

        Monitor::create([
            'ip_address' => $request->ip_address,
            'name' => $request->name,
            'type' => $request->type,
            'location' => $request->location,
            'kode_lokasi' => $request->kode_lokasi,
            'parent_id' => $request->parent_id,
            'zone' => $zone,
            'status' => 'Pending',
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
        // Check if this is a child device (has parent_id in DB or being set now)
        $currentDevice = Monitor::findOrFail($id);
        $isChildDevice = $currentDevice->parent_id || $request->parent_id;

        // Base validation rules
        $rules = [
            'ip_address' => 'required|ipv4',
            'name' => 'required|string|max:255',
            'type' => 'required|string',
        ];

        // Zone is only required for ROOT devices (no parent)
        if (!$isChildDevice) {
            $rules['zone'] = 'required|in:center,lintas utara,lintas selatan';
        }

        $request->validate($rules);

        // VALIDASI LOGIKA BISNIS (UPDATE) - Only for root devices with zone
        if (!$isChildDevice && $request->zone !== 'center') {
            $otherCenterExists = Monitor::where('zone', 'center')->where('id', '!=', $id)->exists();
            $isCurrentlyCenter = $currentDevice->zone === 'center';
            
            if ($isCurrentlyCenter) {
                if (!$otherCenterExists) {
                     return back()->withInput()->with('error', 'Ini adalah satu-satunya device Center. Tambahkan device Center lain sebelum mengubah zona device ini.');
                }
            } else {
                if (!Monitor::where('zone', 'center')->exists()) {
                     return back()->withInput()->with('error', 'Anda harus memiliki setidaknya satu Perangkat Pusat (Center).');
                }
            }
        }

        // Prepare update data
        $updateData = [
            'ip_address' => $request->ip_address,
            'name' => $request->name,
            'type' => $request->type,
            'location' => $request->location,
            'kode_lokasi' => $request->kode_lokasi,
            'parent_id' => $request->parent_id ?: null,
            'status' => 'Pending', 
            'latency' => 0
        ];

        // Only update zone for root devices
        if (!$isChildDevice && $request->has('zone')) {
            $updateData['zone'] = $request->zone;
        }

        $currentDevice->update($updateData);

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