<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    private function handleReordering($parentId, $afterDeviceId, $excludeId = null)
{
    // Normalisasi parentId agar benar-benar null jika kosong
    $parentId = ($parentId == "") ? null : $parentId;

    if ($afterDeviceId === 'first') {
        $targetOrder = 1;
    } else {
        $reference = Monitor::find($afterDeviceId);
        $targetOrder = $reference ? $reference->sort_order + 1 : 1;
    }

    $query = Monitor::where('sort_order', '>=', $targetOrder);
    
    if (is_null($parentId)) {
        $query->whereNull('parent_id');
    } else {
        $query->where('parent_id', $parentId);
    }

    if ($excludeId) {
        $query->where('id', '!=', $excludeId);
    }

    $affected = $query->get();
    foreach ($affected as $device) {
        $device->increment('sort_order');
    }

    return $targetOrder;
}
    
    public function index()
    {
        // Ambil hanya device parent (tanpa parent_id) dengan children-nya
        // untuk tampilan tree yang proper
        // Ambil device berdasarkan ZONE (hanya yang parent_id NULL alias Root)
        // Load children recursive
        $centers = Monitor::whereNull('parent_id')->center()
            ->with(['children' => function($q) { 
                $q->orderBy('sort_order', 'asc'); // Urutkan anak
            }, 'children.latestIncident', 'latestIncident'])
            ->orderBy('sort_order', 'asc')->get();

        $utaras = Monitor::whereNull('parent_id')->lintasUtara()
            ->with(['children' => function($q) { 
                $q->orderBy('sort_order', 'asc'); 
            }, 'children.latestIncident', 'latestIncident'])
            ->orderBy('sort_order', 'asc')->get();

        $selatans = Monitor::whereNull('parent_id')->lintasSelatan()
            ->with(['children' => function($q) { 
                $q->orderBy('sort_order', 'asc'); 
            }, 'children.latestIncident', 'latestIncident'])
            ->orderBy('sort_order', 'asc')->get();

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
            ->with(['children' => function($q) { $q->orderBy('sort_order', 'asc'); }, 'children.latestIncident', 'latestIncident'])
            ->orderBy('sort_order', 'asc')->get();

        $utaras = Monitor::whereNull('parent_id')->lintasUtara()
            ->with(['children' => function($q) { $q->orderBy('sort_order', 'asc'); }, 'children.latestIncident', 'latestIncident'])
            ->orderBy('sort_order', 'asc')->get();

        $selatans = Monitor::whereNull('parent_id')->lintasSelatan()
            ->with(['children' => function($q) { $q->orderBy('sort_order', 'asc'); }, 'children.latestIncident', 'latestIncident'])
            ->orderBy('sort_order', 'asc')->get();

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

        $allMonitors = Monitor::select('id', 'name')->orderBy('name', 'asc')->get();

        return view('monitor.create', compact('parentDevice', 'allMonitors'));
    }

    // Proses Simpan Data Baru
    public function store(Request $request)
{
    // 1. Identifikasi apakah ini perangkat anak (child)
    $isChildDevice = $request->has('parent_id') && $request->parent_id;

    // 2. Validasi Input Dasar
    $rules = [
        'ip_address' => 'required|ipv4|unique:monitors,ip_address',
        'name' => 'required|string|max:255',
        'type' => 'required|string',
        'after_device_id' => 'required', // Wajib memilih posisi (Awal atau Setelah X)
    ];

    // Zone wajib hanya untuk perangkat ROOT
    if (!$isChildDevice) {
        $rules['zone'] = 'required|in:center,lintas utara,lintas selatan';
    }

    $messages = [
        'ip_address.unique' => 'Sudah ada ip perangkat itu, harap ganti.',
    ];

    $request->validate($rules, $messages);

    // 3. LOGIKA PENENTUAN URUTAN (Layouting)
    // Memanggil handleReordering yang sekarang menerima ID referensi, bukan angka manual
    $autoOrder = $this->handleReordering(
        $request->parent_id, 
        $request->after_device_id
    );

    // 4. VALIDASI LOGIKA BISNIS (Khusus Root Device)
    if (!$isChildDevice && $request->zone !== 'center') {
        $centerExists = Monitor::where('zone', 'center')->exists();
        if (!$centerExists) {
            return back()->withInput()->with('error', 'Anda harus menambahkan Perangkat Pusat (Center) terlebih dahulu sebelum menambahkan jalur Utara/Selatan.');
        }
    }

    // 5. Penentuan Zone (Anak mewarisi zone dari Induk)
    $zone = $request->zone;
    if ($isChildDevice) {
        $parentDevice = Monitor::find($request->parent_id);
        $zone = $parentDevice ? $parentDevice->zone : 'center';
    }

    // 6. Simpan Data ke Database
    Monitor::create([
        'ip_address' => $request->ip_address,
        'name' => $request->name,
        'type' => $request->type,
        'location' => $request->location,
        'kode_lokasi' => $request->kode_lokasi,
        'parent_id' => $request->parent_id ?: null,
        'sort_order' => $autoOrder, // Menggunakan hasil kalkulasi handleReordering
        'zone' => $zone,
        'status' => 'Pending',
        'latency' => 0,
    ]);

    return redirect('/preview')->with('success', 'Device berhasil ditambahkan di posisi yang dipilih!');
}

    // Halaman Form Edit
    public function edit($id)
    {
        $monitor = Monitor::findOrFail($id);
        
        // Ambil semua device kecuali dirinya sendiri agar tidak terjadi error hirarki
        $allMonitors = Monitor::where('id', '!=', $id)
                            ->select('id', 'name')
                            ->orderBy('name', 'asc')
                            ->get();

        return view('monitor.edit', compact('monitor', 'allMonitors'));
    }

    // Proses Update Data
    public function update(Request $request, $id)
{
    $currentDevice = Monitor::findOrFail($id);
    $newParentId = $request->parent_id ?: null;

    $request->validate([
        'name' => 'required|string|max:255',
        'ip_address' => 'required|ipv4',
        'after_device_id' => 'required',
    ]);

    // 1. Hitung urutan baru
    $newSortOrder = $this->handleReordering($newParentId, $request->after_device_id, $id);

    // 2. Tentukan Zona (PENTING: Agar muncul di kolom dashboard yang benar)
    $newZone = $request->zone;
    if ($newParentId) {
        $parent = Monitor::find($newParentId);
        $newZone = $parent->zone;
    }

    $updateData = [
        'name'        => $request->name,
        'ip_address'  => $request->ip_address,
        'type'        => $request->type,
        'location'    => $request->location,
        'kode_lokasi' => $request->kode_lokasi,
        'parent_id'   => $newParentId,
        'sort_order'  => $newSortOrder,
        'zone'        => $newZone,
    ];

    $currentDevice->update($updateData);

    // 3. Sinkronkan semua anak agar ikut pindah zona dashboard
    $this->syncChildrenZone($currentDevice);

    // 4. Pastikan redirect ke path dashboard yang benar
    return redirect('/preview')->with('success', 'Update Berhasil!');
}

// Tambahkan fungsi pembantu baru ini di bawah fungsi update
private function syncChildrenZone($parent)
{
    foreach ($parent->children as $child) {
        $child->update(['zone' => $parent->zone]);
        if ($child->children->count() > 0) {
            $this->syncChildrenZone($child); // Rekursif untuk cucu, cicit, dst.
        }
    }
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
    // WAJIB ganti ID menjadi sort_order agar tampilan kartu konsisten
    $monitors = Monitor::orderBy('sort_order', 'asc')->get();
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