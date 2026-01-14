@foreach($monitors as $monitor)
    @php
        // 1. LOGIKA STATUS & WARNA DASAR
        $colors = [
            'Connected' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'line' => 'connected', 'label' => 'UP'],
            'Unstable'  => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700',  'line' => 'unstable', 'label' => 'UNSTABLE'],
            'Disconnected' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'line' => 'disconnected', 'label' => 'DOWN'],
            'Pending'   => ['bg' => 'bg-gray-50', 'text' => 'text-gray-500', 'line' => 'pending', 'label' => 'PENDING'],
        ];
        $s = $colors[$monitor->status] ?? $colors['Pending'];
        $loc = $monitor->location ? strtoupper(substr($monitor->location, 0, 3)) : 'UNK';
        $hasChildren = $monitor->children && $monitor->children->count() > 0;

        // 2. WARNING LOGIC UNTUK PARENT
        $warningClass = '';
        if ($hasChildren) {
            if ($monitor->children->where('status', 'Disconnected')->count() > 0) $warningClass = 'child-down-warning';
            elseif ($monitor->children->where('status', 'Unstable')->count() > 0) $warningClass = 'child-unstable-warning';
        }
        
        // 3. DETEKSI TIPE PERANGKAT
        $type = strtolower($monitor->type);
        $cardClass = 'bg-white border-2 border-gray-200'; 
        $textColor = 'text-slate-800'; 
        
        if ($type == 'router') {
            $cardClass = 'device-router'; $textColor = 'text-white';
        } elseif ($type == 'switch') {
            $cardClass = 'device-switch'; $textColor = 'text-gray-100';
        } elseif ($type == 'server') {
            $cardClass = 'device-server'; $textColor = 'text-slate-900';
        } elseif ($type == 'pc') {
            $cardClass = 'device-pc'; $textColor = 'text-slate-900';
        } elseif ($type == 'access point' || $type == 'ap') {
            $cardClass = 'device-ap'; $textColor = 'text-slate-600';
        } elseif ($type == 'cctv') {
            $cardClass = 'device-cctv'; $textColor = 'text-white';
        }

        // 4. LOGIKA WARNA LED ROUTER (DYNAMIC PHP)
        // Ini akan digenerate ulang oleh Server setiap kali JS melakukan fetch data
        $ledColorClass = 'bg-slate-600'; // Default mati
        if ($monitor->status == 'Connected') {
            $ledColorClass = 'bg-emerald-500 shadow-[0_0_8px_#10b981]'; // Hijau neon
        } elseif ($monitor->status == 'Unstable') {
            $ledColorClass = 'bg-orange-500 shadow-[0_0_8px_#f97316]'; // Orange neon
        } elseif ($monitor->status == 'Disconnected') {
            $ledColorClass = 'bg-red-500 shadow-[0_0_8px_#ef4444]'; // Merah neon
        }
    @endphp

    <div class="tree-node" id="node-{{ $monitor->id }}" data-node-id="{{ $monitor->id }}">
        
        <div class="tree-node-card">
            
            <div id="card-{{ $monitor->id }}"
                 class="monitor-card relative p-4 shadow-sm hover:shadow-2xl transition-all duration-300 {{ $cardClass }} {{ $warningClass }}"
                 style="min-width: 220px;"
                 data-history="{{ json_encode($monitor->history ?? []) }}"
                 data-ip="{{ $monitor->ip_address }}"
                 data-id="{{ $monitor->id }}"
                 data-type="{{ $monitor->type }}"
                 data-status="{{ $s['line'] }}"
                 data-latency="{{ $monitor->latency }}">

                {{-- DEKORASI VISUAL --}}
                
                {{-- 1. ROUTER (Antena & LED) --}}
                @if($type == 'router')
                    <div class="router-antenna left"></div>
                    <div class="router-antenna right"></div>
                    {{-- Class 'router-leds' ini PENTING untuk target JavaScript --}}
                    <div class="router-leds">
                        {{-- LED Utama (Dinamis dari PHP) --}}
                        <div class="led {{ $ledColorClass }}"></div>
                        {{-- LED Activity (Kedip) --}}
                        <div class="led {{ $ledColorClass }} animate-pulse" style="animation-duration: 0.3s"></div>
                        <div class="led {{ $ledColorClass }} animate-pulse" style="animation-duration: 1.5s"></div>
                        {{-- LED Power (Static) --}}
                        <div class="led bg-slate-600"></div>
                    </div>
                @endif

                {{-- 2. SWITCH (Kuping) --}}
                @if($type == 'switch')
                    <div class="switch-ears left"></div>
                    <div class="switch-ears right"></div>
                @endif

                {{-- 3. PC (Stand) --}}
                @if($type == 'pc')
                    <div class="pc-stand"></div>
                    <div class="pc-base"></div>
                @endif
                
                {{-- 4. ACCESS POINT (Ring & LED) --}}
                @if($type == 'access point' || $type == 'ap')
                    <div class="ap-ring"></div>
                    {{-- Class 'ap-led' ini PENTING untuk target JavaScript --}}
                    <div class="ap-led {{ $monitor->status == 'Connected' ? 'bg-emerald-500 shadow-emerald-500' : ($monitor->status == 'Unstable' ? 'bg-orange-500 shadow-orange-500' : 'bg-red-500 shadow-red-500') }}"></div>
                @endif

                {{-- 5. CCTV (Lensa) --}}
                @if($type == 'cctv')
                    <div class="cctv-lens"></div>
                @endif

                {{-- KONTEN UTAMA --}}
                <div class="flex justify-between items-start mb-2 relative z-10">
                    <div class="flex flex-col">
                        {{-- STATUS BADGE --}}
                        <div class="flex items-center gap-2 mb-1">
                            <span id="dot-{{ $monitor->id }}" class="w-2.5 h-2.5 rounded-full {{ $monitor->status == 'Connected' ? 'bg-emerald-500' : ($monitor->status == 'Unstable' ? 'bg-orange-500' : 'bg-red-500') }} animate-pulse"></span>
                            <span id="badge-{{ $monitor->id }}" class="text-[10px] font-bold tracking-widest uppercase {{ ($type == 'router' || $type == 'cctv') ? ($monitor->status == 'Connected' ? 'text-emerald-400' : ($monitor->status == 'Unstable' ? 'text-orange-400' : 'text-red-400')) : $s['text'] }}">
                                {{ $s['label'] }}
                            </span>
                        </div>

                        <h3 class="font-bold {{ $textColor }} text-sm truncate w-32" title="{{ $monitor->name }}">
                            {{ $monitor->name }}
                        </h3>
                        <p class="text-[10px] {{ $textColor }} opacity-70 font-mono tracking-wider uppercase">
                            {{ $monitor->type }}
                        </p>
                    </div>
                    
                    <div class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ ($type == 'router' || $type == 'cctv') ? 'bg-slate-700 text-white' : 'bg-gray-200 text-gray-600' }}">
                        {{ $loc }}
                    </div>
                </div>

                {{-- SWITCH PORTS --}}
                @if($type == 'switch')
                    <div class="port-grid">
                        @for($i=0; $i<16; $i++)
                            <div class="port {{ $i < 5 ? 'active' : '' }}"></div>
                        @endfor
                    </div>
                @endif

                {{-- FOOTER INFO --}}
                <div class="mt-3 flex justify-between items-end relative z-10">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold {{ $textColor }}">
                                <span id="latency-val-{{ $monitor->id }}" class="text-lg font-mono">{{ $monitor->latency }}</span> ms
                            </span>
                        </div>
                    </div>

                    <div class="flex gap-1">
                        <form action="{{ route('monitor.destroy', $monitor->id) }}" method="POST" onsubmit="return confirm('Hapus device?');">
                            @csrf @method('DELETE')
                            <button class="{{ ($type == 'router' || $type == 'cctv') ? 'text-slate-500 hover:text-red-400' : 'text-gray-400 hover:text-red-500' }} transition p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                        <a href="{{ route('monitor.edit', $monitor->id) }}" class="{{ ($type == 'router' || $type == 'cctv') ? 'text-slate-500 hover:text-blue-400' : 'text-gray-400 hover:text-blue-500' }} transition p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </a>
                    </div>
                </div>

                @if($type == 'server')
                    <div class="server-grill"></div>
                @endif

                <a href="{{ route('monitor.create', ['parent_id' => $monitor->id]) }}" class="hover-add-btn" title="Tambah Cabang">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                </a>

                @if($hasChildren)
                    <button onclick="toggleBranch({{ $monitor->id }})" 
                            class="absolute -bottom-10 left-1/2 transform -translate-x-1/2 bg-white border-2 border-gray-300 text-gray-500 hover:text-blue-600 hover:border-blue-500 rounded-full w-8 h-8 flex items-center justify-center shadow-sm z-30 transition-colors">
                        <svg id="arrow-{{ $monitor->id }}" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div id="badge-hidden-{{ $monitor->id }}" class="hidden absolute -bottom-12 right-0 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-md animate-bounce">! CEK</div>
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