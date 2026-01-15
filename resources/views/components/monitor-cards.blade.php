@foreach($monitors as $monitor)
    @php
        $colors = [
            'Connected' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'line' => 'connected', 'label' => 'ONLINE', 'dot' => 'connected'],
            'Unstable'  => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700',  'line' => 'unstable', 'label' => 'UNSTABLE', 'dot' => 'unstable'],
            'Disconnected' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'line' => 'disconnected', 'label' => 'OFFLINE', 'dot' => 'disconnected'],
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
        $cardClass = 'bg-white border-2 border-gray-200';
        
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

        $ledColorClass = 'bg-slate-300';
        if ($monitor->status == 'Connected') {
            $ledColorClass = 'bg-emerald-500 shadow-[0_0_10px_#10b981]';
        } elseif ($monitor->status == 'Unstable') {
            $ledColorClass = 'bg-orange-500 shadow-[0_0_10px_#f97316]';
        } elseif ($monitor->status == 'Disconnected') {
            $ledColorClass = 'bg-red-500 shadow-[0_0_10px_#ef4444]';
        }
    @endphp

    <div class="tree-node" id="node-{{ $monitor->id }}" data-node-id="{{ $monitor->id }}">
        
        <div class="tree-node-card">
            
            <div id="card-{{ $monitor->id }}"
                 class="monitor-card relative shadow-md hover:shadow-2xl transition-all duration-300 {{ $cardClass }} {{ $warningClass }}"
                 style="min-width: 240px;"
                 data-history="{{ json_encode($monitor->history ?? []) }}"
                 data-ip="{{ $monitor->ip_address }}"
                 data-id="{{ $monitor->id }}"
                 data-type="{{ $monitor->type }}"
                 data-status="{{ $s['line'] }}"
                 data-latency="{{ $monitor->latency }}">

                {{-- DEVICE SPECIFIC DECORATIONS --}}
                
                {{-- ROUTER --}}
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

                {{-- SWITCH --}}
                @if($type == 'switch')
                    <div class="switch-ears left"></div>
                    <div class="switch-ears right"></div>
                    <div class="switch-body">
                @endif

                {{-- SERVER --}}
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

                {{-- PC --}}
                @if($type == 'pc')
                    <div class="pc-screen">
                @endif

                {{-- ACCESS POINT --}}
                @if($type == 'access point' || $type == 'ap')
                    <div class="ap-rings">
                        <div class="ap-ring"></div>
                        <div class="ap-ring"></div>
                        <div class="ap-ring"></div>
                    </div>
                    <div class="ap-center">
                        <div class="ap-led {{ $monitor->status == 'Connected' ? 'bg-emerald-500 shadow-emerald-500' : ($monitor->status == 'Unstable' ? 'bg-orange-500 shadow-orange-500' : 'bg-red-500 shadow-red-500') }}"></div>
                @endif

                {{-- CCTV --}}
                @if($type == 'cctv')
                    <div class="cctv-led"></div>
                    <div class="cctv-body">
                @endif

                {{-- MAIN CONTENT (Universal for all devices) --}}
                <div class="@if($type != 'access point' && $type != 'ap') {{ $type == 'router' || $type == 'switch' || $type == 'server' || $type == 'cctv' ? '' : 'p-4' }} @endif">
                    
                    {{-- STATUS BADGE --}}
                    <div class="status-badge-container">
                        <div id="dot-{{ $monitor->id }}" class="status-dot {{ $s['dot'] }}"></div>
                        <span id="badge-{{ $monitor->id }}" class="status-text {{ $s['dot'] }}">
                            {{ $s['label'] }}
                        </span>
                        <div class="ml-auto">
                            <span class="location-badge">{{ $loc }}</span>
                        </div>
                    </div>

                    {{-- DEVICE INFO --}}
                    <div class="mb-3">
                        <h3 class="device-title truncate" title="{{ $monitor->name }}">
                            {{ $monitor->name }}
                        </h3>
                        <p class="device-type">{{ $monitor->type }}</p>
                    </div>

                    {{-- SWITCH PORTS --}}
                    @if($type == 'switch')
                        <div class="port-grid">
                            @for($i=0; $i<16; $i++)
                                <div class="port {{ $i < 6 ? 'active' : '' }}"></div>
                            @endfor
                        </div>
                    @endif

                    {{-- LATENCY DISPLAY --}}
                    <div class="latency-display">
                        <span id="latency-val-{{ $monitor->id }}" class="latency-value">{{ $monitor->latency }}</span>
                        <span class="latency-unit">ms</span>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="flex justify-end gap-2 mt-3">
                        <form action="{{ route('monitor.destroy', $monitor->id) }}" method="POST" onsubmit="return confirm('Hapus device ini?');">
                            @csrf @method('DELETE')
                            <button class="text-gray-400 hover:text-red-500 transition-colors p-1.5 rounded-lg hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                        <a href="{{ route('monitor.edit', $monitor->id) }}" class="text-gray-400 hover:text-blue-500 transition-colors p-1.5 rounded-lg hover:bg-blue-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- CLOSE DEVICE WRAPPERS --}}
                @if($type == 'router' || $type == 'switch' || $type == 'server')
                    </div>
                @endif

                @if($type == 'pc')
                    </div>
                    <div class="pc-bezel">
                        <div class="pc-power-led"></div>
                    </div>
                    <div class="pc-stand"></div>
                    <div class="pc-base"></div>
                @endif

                @if($type == 'access point' || $type == 'ap')
                    </div>
                @endif

                @if($type == 'cctv')
                    </div>
                    <div class="cctv-lens"></div>
                @endif

                {{-- ADD CHILD BUTTON --}}
                <a href="{{ route('monitor.create', ['parent_id' => $monitor->id]) }}" class="hover-add-btn" title="Tambah Device">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                </a>

                {{-- COLLAPSE BUTTON --}}
                @if($hasChildren)
                    <button onclick="toggleBranch({{ $monitor->id }})" 
                            class="absolute -bottom-10 left-1/2 transform -translate-x-1/2 bg-white border-2 border-gray-300 text-gray-600 hover:text-blue-600 hover:border-blue-500 hover:bg-blue-50 rounded-full w-10 h-10 flex items-center justify-center shadow-md z-30 transition-all">
                        <svg id="arrow-{{ $monitor->id }}" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div id="badge-hidden-{{ $monitor->id }}" class="hidden absolute -bottom-12 right-0 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-md animate-bounce">! CEK</div>
                @endif
            </div>
        </div>

        {{-- CHILDREN --}}
        @if($hasChildren)
            <div id="children-{{ $monitor->id }}" class="tree-children transition-all duration-300 origin-top">
                @include('components.monitor-cards', ['monitors' => $monitor->children])
            </div>
        @endif
    </div>
@endforeach