<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    /**
     * Tampilkan Dashboard (Hanya Induk)
     */
    public function index()
    {
        // Ambil device INDUK saja (parent_id NULL), tapi bawa data anaknya (children)
        $monitors = Monitor::whereNull('parent_id')
            ->with('children') 
            ->orderBy('updated_at', 'desc')
            ->get();

        // Jika request datang dari AJAX (auto-refresh dashboard)
        if (request()->wantsJson() || request()->routeIs('monitor.data')) {
            return view('components.monitor-cards', compact('monitors'))->render();
        }

        return view('preview', compact('monitors'));
    }

    /**
     * Tampilkan Form Tambah Device
     * Bisa menerima parameter ?parent_id=123 jika diklik dari tombol +
     */
    public function create(Request $request)
    {
        $parentId = $request->query('parent_id');
        $parentDevice = null;
        
        if ($parentId) {
            $parentDevice = Monitor::find($parentId);
        }

        return view('monitor.create', compact('parentDevice'));
    }

    /**
     * Simpan Device Baru ke Database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip', // Validasi format IP
            'type' => 'required|string',
            'location' => 'nullable|string',
            'parent_id' => 'nullable|exists:monitors,id' // Validasi ID Induk
        ]);

        // Set default status agar tidak error
        $validated['status'] = 'Pending';
        $validated['latency'] = 0;
        $validated['history'] = [];

        Monitor::create($validated);

        return redirect()->route('monitor.index')->with('success', 'Device berhasil ditambahkan!');
    }

    /**
     * Hapus Device
     */
    public function destroy($id)
    {
        $monitor = Monitor::findOrFail($id);
        $monitor->delete();

        return redirect()->back()->with('success', 'Device berhasil dihapus.');
    }
}