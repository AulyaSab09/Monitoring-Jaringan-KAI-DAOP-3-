@foreach($monitors as $monitor)
    @php
        $colors = [
            'Connected' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'line' => 'connected', 'label' => 'UP', 'dot' => 'connected'],
            'Unstable'  => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700',  'line' => 'unstable', 'label' => 'UNSTABLE', 'dot' => 'unstable'],
            'Disconnected' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'line' => 'disconnected', 'label' => 'DOWN', 'dot' => 'disconnected'],
            'Pending'   => ['bg' => 'bg-gray-50', 'text' => 'text-gray-500', 'line' => 'pending', 'label' => 'PENDING', 'dot' => 'pending'],
        ];
        $s = $colors[$monitor->status] ?? $colors['Pending'];
        $loc = $monitor->location ? strtoupper(substr($monitor->location, 0, 3)) : 'UNK';
        $hasChildren = $monitor->children && $monitor->children->count() > 0;

        $warningClass = '';
        if ($hasChildren) {
            if ($monitor->children->where('status', 'Disconnected')->count() > 0) $warningClass = 'child-down-warning';
            elseif ($monitor->children->where('status', 'Unstable')->count() > 0) $warningClass = 'child-unstable-warning';
        }
        
        $type = strtolower($monitor->type);
        $cardClass = '';
        
        if ($type == 'router') {
            $cardClass = 'device-router';
        } elseif ($type == 'switch') {
            $cardClass = 'device-switch';
        } elseif ($type == 'server') {
            $cardClass = 'device-server';
        } elseif ($type == 'pc') {
            $cardClass = 'device-pc';
        } elseif ($type == 'access point' || $type == 'ap') {
            $cardClass = 'device-ap';
        } elseif ($type == 'cctv') {
            $cardClass = 'device-cctv';
        }

        // Logika warna lampu LED
        $ledColorClass = 'bg-slate-300';
        if ($monitor->status == 'Connected') {
            $ledColorClass = 'bg-emerald-500 shadow-[0_0_10px_#10b981]';
        } elseif ($monitor->status == 'Unstable') {
            $ledColorClass = 'bg-orange-500 shadow-[0_0_10px_#f97316]';
        } elseif ($monitor->status == 'Disconnected') {
            $ledColorClass = 'bg-red-500 shadow-[0_0_10px_#ef4444]';
        }

        $statusBorderClass = '';
        $latencyAnimClass = '';

        if($monitor->status == 'Connected') {
            $statusBorderClass = 'border-emerald-500 border-4';
        } elseif($monitor->status == 'Unstable') {
            $statusBorderClass = 'border-orange-500 border-4';
        } elseif($monitor->status == 'Disconnected') {
            // Gunakan class animasi baru yang sudah dibuat di style atas
            $statusBorderClass = 'border-[#ef4444] border-4 glow-animate-danger';
            $latencyAnimClass = 'latency-danger-pulse'; 
        }

        // Logic ukuran font nama device
        $nameLen = strlen($monitor->name);
        $nameSizeClass = 'text-xl';
        if ($nameLen > 20) {
            $nameSizeClass = 'text-sm';
        } elseif ($nameLen > 15) {
            $nameSizeClass = 'text-base';
        }
    @endphp

    <div class="tree-node" id="node-{{ $monitor->id }}" data-node-id="{{ $monitor->id }}">
        <div class="tree-node-card group relative">
            {{-- FLOATING IDENTITY LABEL --}}       
            <div class="floating-identity">
                {{ $monitor->kode_lokasi ?? $loc }}
            </div>          
            <div id="card-{{ $monitor->id }}"
                 class="monitor-card relative shadow-md hover:shadow-2xl transition-all duration-300 {{ $statusBorderClass }} bg-white {{ $cardClass }} {{ $warningClass }}"
                 style="border-style: solid;"           data-history="{{ json_encode($monitor->history ?? []) }}"
                 data-ip="{{ $monitor->ip_address }}"
                 data-id="{{ $monitor->id }}"
                 data-type="{{ $monitor->type }}"
                 data-status="{{ $s['line'] }}"
                 data-latency="{{ $monitor->latency }}"
                 data-down-since="{{ $monitor->latestIncident && $monitor->status == 'Disconnected' ? $monitor->latestIncident->down_at : $monitor->updated_at }}">

                {{-- DEVICE SPECIFIC DECORATIONS --}}
                
                @if($type == 'router')
                    <div class="router-antenna left"></div>
                    <div class="router-antenna right"></div>
                    <div class="router-body">
                        <div class="router-leds">
                            <div class="led {{ $ledColorClass }}"></div>
                            <div class="led {{ $ledColorClass }} animate-pulse" style="animation-duration: 1s"></div>
                            <div class="led {{ $ledColorClass }} animate-pulse" style="animation-duration: 2s"></div>
                            <div class="led bg-blue-400"></div>
                        </div>
                @endif

                @if($type == 'switch')
                    <div class="switch-body">
                @endif

                @if($type == 'server')
                    <div class="server-grill"></div>
                    <div class="server-body">
                        <div class="server-led-strip">
                            <div class="server-led {{ $ledColorClass }}"></div>
                            <div class="server-led {{ $ledColorClass }}"></div>
                            <div class="server-led bg-blue-400"></div>
                            <div class="server-led bg-slate-300"></div>
                        </div>
                @endif

                @if($type == 'pc')
                    <div class="pc-screen">
                @endif

                @if($type == 'access point' || $type == 'ap')
                    <div class="ap-rings">
                        <div class="ap-ring"></div><div class="ap-ring"></div><div class="ap-ring"></div>
                    </div>
                    <div class="ap-center">
                        <div class="ap-led {{ $monitor->status == 'Connected' ? 'bg-emerald-500 shadow-emerald-500' : ($monitor->status == 'Unstable' ? 'bg-orange-500 shadow-orange-500' : 'bg-red-500 shadow-red-500') }}"></div>
                @endif

                @if($type == 'cctv')
                    <div class="cctv-led"></div>
                    <div class="cctv-body">
                @endif

                {{-- MAIN CONTENT --}}
                <div class="@if($type != 'access point' && $type != 'ap') {{ in_array($type, ['router', 'switch', 'server', 'cctv']) ? '' : 'p-4' }} @endif">
                    
                    {{-- STATUS BADGE (Teks UP/DOWN/WARNING) --}}
                    <div class="status-badge-container">
                        <div id="dot-{{ $monitor->id }}" class="status-dot {{ $s['dot'] }}"></div>
                        <span id="badge-{{ $monitor->id }}" class="status-text {{ $s['dot'] }}">
                            {{ $s['label'] }}
                        </span>
                        <div class="ml-auto">
                            <span class="location-badge">{{ $loc }}</span>
                        </div>
                    </div>

                    {{-- DEVICE INFO - REVISED VERSION --}}
                    <div class="mb-4 px-6 text-center md:text-left">
                        <h3 class="device-title {{ $nameSizeClass }} font-black truncate leading-tight" title="{{ $monitor->name }}">
                            {{ $monitor->name }}
                        </h3>
                        <p class="device-type text-[10px] font-black uppercase tracking-[0.2em] opacity-70 mt-1">
                            {{ strtoupper($monitor->type) }}
                        </p>
                    </div>

                    {{-- SWITCH PORTS (Hanya menyala jika Connected) --}}
                    @if($type == 'switch')
                        <div class="port-grid mx-4">
                            @for($i=0; $i<16; $i++)
                                <div class="port {{ $i < 6 && $monitor->status == 'Connected' ? 'active' : '' }}"></div>
                            @endfor
                        </div>
                    @endif

                    {{-- LATENCY DISPLAY (Berubah warna sesuai status) --}}
                    {{-- LATENCY DISPLAY - REVISED (Hapus variabel $s['text']) --}}
                    <div class="latency-display mx-4 {{ $s['bg'] }} border {{ $latencyAnimClass }} {{ $statusBorderClass }}">
                        <span id="latency-val-{{ $monitor->id }}" class="latency-value font-black">
                            {{ $monitor->latency }}
                        </span>
                        <span class="latency-unit font-bold">ms</span>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="flex justify-end gap-3 mt-4 px-4 pb-4">
                        <a href="{{ route('monitor.create', ['parent_id' => $monitor->id]) }}" class="action-btn-add text-gray-400 p-2 rounded-lg transition-all" title="Tambah Turunan">
                            <i class="fa-solid fa-plus w-6 h-6"></i>
                        </a>
                        <form action="{{ route('monitor.destroy', $monitor->id) }}" method="POST" onsubmit="return confirm('Hapus device ini?');">
                            @csrf @method('DELETE')
                            <button class="action-btn-delete text-gray-400 p-2 rounded-lg transition-all">
                                <i class="fa-solid fa-trash w-6 h-6"></i>
                            </button>
                        </form>
                        <a href="{{ route('monitor.edit', $monitor->id) }}" class="action-btn-edit text-gray-400 p-2 rounded-lg transition-all">
                            <i class="fa-solid fa-pen-to-square w-6 h-6"></i>
                        </a>
                    </div>
                </div>

                {{-- CLOSE WRAPPERS --}}
                @if(in_array($type, ['router', 'switch', 'server', 'pc', 'ap', 'access point', 'cctv'])) </div> @endif
                @if($type == 'pc') <div class="pc-bezel"><div class="pc-power-led"></div></div><div class="pc-stand"></div><div class="pc-base"></div> @endif
                @if($type == 'cctv') <div class="cctv-lens"></div> @endif

                @if($hasChildren)
                    <button onclick="toggleBranch({{ $monitor->id }})" class="absolute -bottom-10 left-1/2 transform -translate-x-1/2 bg-white border-2 border-gray-300 text-gray-600 rounded-full w-10 h-10 flex items-center justify-center shadow-md z-30"><i id="arrow-{{ $monitor->id }}" class="fa-solid fa-chevron-down w-5 h-5 transition-transform"></i></button>
                    <div id="badge-hidden-{{ $monitor->id }}" class="hidden absolute -bottom-18 right-0 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full ">! CEK</div>
                @endif
            </div>
        </div>

        @if($hasChildren)
            <div id="children-{{ $monitor->id }}" class="tree-children transition-all origin-top">
                @include('components.monitor-cards', ['monitors' => $monitor->children])
            </div>
        @endif
    </div>
@endforeach