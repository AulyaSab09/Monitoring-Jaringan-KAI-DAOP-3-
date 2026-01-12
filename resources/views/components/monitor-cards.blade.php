@foreach($monitors as $monitor)
    @php
        // Warna & Status (Sama seperti sebelumnya)
        $colors = [
            'Connected' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-300', 'dot' => 'bg-emerald-500', 'text' => 'text-emerald-700', 'badge' => 'UP', 'line' => 'connected'],
            'Unstable'  => ['bg' => 'bg-orange-50', 'border' => 'border-orange-300', 'dot' => 'bg-orange-500',  'text' => 'text-orange-700',  'badge' => 'CEK', 'line' => 'unstable'],
            'Disconnected' => ['bg' => 'bg-red-50', 'border' => 'border-red-300', 'dot' => 'bg-red-500', 'text' => 'text-red-700', 'badge' => 'DOWN', 'line' => 'disconnected'],
            'Pending'   => ['bg' => 'bg-gray-50', 'border' => 'border-gray-300', 'dot' => 'bg-gray-400', 'text' => 'text-gray-500', 'badge' => '...', 'line' => 'pending'],
        ];

        $s = $colors[$monitor->status] ?? $colors['Pending'];
        // Lokasi disingkat 3 huruf agar mudah dibaca orang tua (misal: CRB)
        $loc = $monitor->location ? strtoupper(substr($monitor->location, 0, 3)) : 'UNK';
        
        $hasChildren = $monitor->children && $monitor->children->count() > 0;
        
        // LOGIKA RAMAH ORANG TUA:
        // Jika anak ada yang MATI, Induknya dapet border Merah Berkedip
        $warningClass = '';
        $childStatusSummary = 'ok'; // ok, warn, down
        
        if ($hasChildren) {
            $downCount = $monitor->children->where('status', 'Disconnected')->count();
            $unstableCount = $monitor->children->where('status', 'Unstable')->count();

            if ($downCount > 0) {
                $warningClass = 'child-down-warning';
                $childStatusSummary = 'down';
            } elseif ($unstableCount > 0) {
                $warningClass = 'child-unstable-warning';
                $childStatusSummary = 'warn';
            }
        }
    @endphp

    <div class="tree-node" id="node-{{ $monitor->id }}" data-node-id="{{ $monitor->id }}">
        
        <div class="tree-node-card">
            <div id="card-{{ $monitor->id }}"
                 class="monitor-card relative w-64 p-5 rounded-2xl border-[3px] shadow-sm hover:shadow-xl transition-all duration-300 bg-white {{ $s['bg'] }} {{ $s['border'] }} {{ $warningClass }}"
                 data-history="{{ json_encode($monitor->history ?? []) }}"
                 data-ip="{{ $monitor->ip_address }}"
                 data-id="{{ $monitor->id }}"
                 data-name="{{ $monitor->name ?? '-' }}"
                 data-type="{{ $monitor->type ?? '-' }}"
                 data-station="{{ $monitor->stasiun ?? $monitor->location ?? '-' }}"
                 data-latency="{{ $monitor->latency ?? 0 }}"
                 data-status="{{ $s['line'] }}">

                <div class="flex justify-between items-center mb-3">
                    <div class="flex items-center gap-3">
                        <span id="dot-{{ $monitor->id }}" class="w-4 h-4 rounded-full {{ $s['dot'] }} animate-pulse shadow-sm"></span>
                        <span id="badge-{{ $monitor->id }}" class="text-xs font-black tracking-widest {{ $s['text'] }}">{{ $s['badge'] }}</span>
                    </div>
                    <div class="px-2 py-1 rounded-md bg-white border border-gray-200 text-xs font-bold text-gray-500 shadow-sm">
                        {{ $loc }}
                    </div>
                </div>

                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-white rounded-xl border border-gray-100 text-slate-700 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    </div>
                    <div class="overflow-hidden">
                        <h3 class="font-bold text-slate-900 text-base truncate w-32" title="{{ $monitor->name }}">{{ $monitor->name }}</h3>
                        <p class="text-xs text-slate-500 font-semibold mt-1">
                            PING: <span id="latency-val-{{ $monitor->id }}" class="text-slate-900 text-lg font-mono font-bold">{{ $monitor->latency }}</span> ms
                        </p>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-3 border-t border-gray-200/60">
                    <!-- <code class="text-xs bg-white px-2 py-1 rounded border border-gray-200 text-slate-600 font-mono font-bold">{{ $monitor->ip_address }}</code> -->
                    
                    <form action="{{ route('monitor.destroy', $monitor->id) }}" method="POST" onsubmit="return confirm('Yakin hapus?');">
                        @csrf @method('DELETE')
                        <button class="text-gray-300 hover:text-red-500 transition p-1" title="Hapus"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </form>
                    <a href="{{ route('monitor.edit', $monitor->id) }}" class="text-gray-300 hover:text-blue-500 transition p-1" title="Edit">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </a>
                </div>

                <a href="{{ route('monitor.create', ['parent_id' => $monitor->id]) }}" class="hover-add-btn" title="Tambah Cabang">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                </a>

                @if($hasChildren)
                    <button onclick="toggleBranch({{ $monitor->id }})" 
                            class="absolute -bottom-10 left-1/2 transform -translate-x-1/2 bg-white border-2 border-gray-300 text-gray-500 hover:text-blue-600 hover:border-blue-500 rounded-full w-8 h-8 flex items-center justify-center shadow-sm z-30 transition-colors"
                            title="Buka/Tutup Cabang">
                        <svg id="arrow-{{ $monitor->id }}" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    
                    <div id="badge-hidden-{{ $monitor->id }}" class="hidden absolute -bottom-12 right-0 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-md animate-bounce">
                        ! CEK
                    </div>
                @endif
            </div>
        </div>

        @if($hasChildren)
            <div id="children-{{ $monitor->id }}" class="tree-children transition-all duration-300 origin-top">
                @include('components.monitor-cards', ['monitors' => $monitor->children])
            </div>
        @endif
    </div>
@endforeach