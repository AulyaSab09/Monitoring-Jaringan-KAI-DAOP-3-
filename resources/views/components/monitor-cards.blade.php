{{-- 
    Tree View Component: Renders device cards in a hierarchical tree structure
    with proper positioning for SVG connecting lines
--}}

@foreach($monitors as $monitor)
    @php
        // Status color mapping
        $statusColors = [
            'Connected' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-300', 'dot' => 'bg-emerald-500', 'text' => 'text-emerald-700', 'badge' => 'UP', 'line' => 'connected'],
            'Unstable' => ['bg' => 'bg-orange-50', 'border' => 'border-orange-300', 'dot' => 'bg-orange-500', 'text' => 'text-orange-700', 'badge' => 'WARNING', 'line' => 'unstable'],
            'Disconnected' => ['bg' => 'bg-red-50', 'border' => 'border-red-300', 'dot' => 'bg-red-500', 'text' => 'text-red-700', 'badge' => 'DOWN', 'line' => 'disconnected'],
            'Pending' => ['bg' => 'bg-gray-50', 'border' => 'border-gray-300', 'dot' => 'bg-gray-400', 'text' => 'text-gray-600', 'badge' => '...', 'line' => 'pending'],
        ];

        $currentStatus = $statusColors[$monitor->status] ?? $statusColors['Pending'];
        $locationCode = $monitor->location ? strtoupper(substr($monitor->location, 0, 1)) : '?';
        
        // Check children
        $hasChildren = $monitor->children && $monitor->children->count() > 0;
        $hasDownChild = $hasChildren && $monitor->children->where('status', 'Disconnected')->count() > 0;
        $hasUnstableChild = $hasChildren && $monitor->children->where('status', 'Unstable')->count() > 0;
        
        // Warning class for parent with problematic children
        $warningClass = '';
        if ($hasDownChild) {
            $warningClass = 'child-down-warning';
        } elseif ($hasUnstableChild) {
            $warningClass = 'child-unstable-warning';
        }
    @endphp

    {{-- TREE NODE CONTAINER --}}
    <div class="tree-node" data-node-id="{{ $monitor->id }}">
        
        {{-- NODE CARD --}}
        <div class="tree-node-card">
            <div id="card-{{ $monitor->id }}"
                 class="monitor-card relative p-4 rounded-xl border-2 {{ $currentStatus['bg'] }} {{ $currentStatus['border'] }} {{ $warningClass }} shadow-md hover:shadow-lg transition-all duration-200 min-w-[200px] max-w-[240px]"
                 data-history="{{ json_encode($monitor->history ?? []) }}"
                 data-ip="{{ $monitor->ip_address }}"
                 data-id="{{ $monitor->id }}"
                 data-status="{{ $currentStatus['line'] }}">

                {{-- Header: Status Badge + Location --}}
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center gap-1.5">
                        <span id="dot-{{ $monitor->id }}" class="w-2.5 h-2.5 rounded-full {{ $currentStatus['dot'] }} animate-pulse"></span>
                        <span id="badge-{{ $monitor->id }}" class="text-[10px] font-bold uppercase tracking-wide {{ $currentStatus['text'] }}">
                            {{ $currentStatus['badge'] }}
                        </span>
                    </div>
                    <div class="w-6 h-6 flex items-center justify-center rounded-full bg-white border border-gray-200 text-[9px] font-bold text-gray-500 shadow-sm">
                        {{ $locationCode }}
                    </div>
                </div>

                {{-- Device Info --}}
                <div class="flex items-start gap-2 mb-2">
                    {{-- Device Icon --}}
                    <div class="p-1.5 bg-white rounded-lg border border-gray-100 shadow-sm text-blue-600 flex-shrink-0">
                        @if($monitor->type == 'Switch')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        @elseif($monitor->type == 'Server')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 01-2 2v4a2 2 0 012 2h14a2 2 0 012-2v-4a2 2 0 01-2-2m-2-4h.01M17 16h.01"></path></svg>
                        @elseif($monitor->type == 'Router')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        @endif
                    </div>

                    {{-- Name & Type --}}
                    <div class="overflow-hidden flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-sm leading-tight mb-0.5 truncate" title="{{ $monitor->name }}">
                            {{ $monitor->name }}
                        </h3>
                        <p class="text-[10px] text-gray-500 uppercase font-medium tracking-wider">
                            {{ $monitor->type }} • <span id="latency-val-{{ $monitor->id }}" class="font-mono font-bold">{{ $monitor->latency }}</span>ms
                        </p>
                    </div>
                </div>

                {{-- Footer: IP + Delete Only --}}
                <div class="flex justify-between items-center border-t border-gray-200/50 pt-2 mt-1">
                    <code class="text-[10px] font-mono text-gray-600 bg-white px-1 py-0.5 rounded border border-gray-100">
                        {{ $monitor->ip_address }}
                    </code>

                    {{-- Delete Button Only --}}
                    <form action="{{ route('monitor.destroy', $monitor->id) }}" method="POST" onsubmit="return confirm('Hapus device ini?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1 text-red-400 hover:text-white hover:bg-red-500 rounded transition-colors" title="Hapus">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
                
                {{-- Hover Add Child Button (Outside Card) --}}
                <a href="{{ route('monitor.create', ['parent_id' => $monitor->id]) }}" 
                   class="hover-add-btn"
                   title="Tambah Device Anak">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                </a>
                
                {{-- Children Count Badge (if has children) --}}
                @if($hasChildren)
                    <div class="absolute -bottom-3 left-1/2 transform -translate-x-1/2 px-2 py-0.5 bg-blue-600 text-white text-[9px] font-bold rounded-full shadow-sm z-10 pointer-events-none">
                        {{ $monitor->children->count() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- CHILDREN NODES --}}
        @if($hasChildren)
            <div class="tree-children">
                @include('components.monitor-cards', ['monitors' => $monitor->children])
            </div>
        @endif
    </div>
@endforeach