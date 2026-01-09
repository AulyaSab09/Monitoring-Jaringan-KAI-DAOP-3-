@foreach($monitors as $monitor)
    @php
        // Logika Status Colors (Sama seperti sebelumnya)
        $statusColors = [
            'Connected' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-500', 'text' => 'text-emerald-700', 'badge' => 'UP'],
            'Unstable' => ['bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'dot' => 'bg-orange-500', 'text' => 'text-orange-700', 'badge' => 'WARNING'],
            'Disconnected' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'dot' => 'bg-red-500', 'text' => 'text-red-700', 'badge' => 'DOWN'],
            'Pending' => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'dot' => 'bg-gray-400', 'text' => 'text-gray-600', 'badge' => '...'],
        ];

        $currentStatus = $statusColors[$monitor->status] ?? $statusColors['Pending'];
        $locationCode = $monitor->location ? strtoupper(substr($monitor->location, 0, 1)) : '?';
    @endphp

    <div id="card-{{ $monitor->id }}"
         class="monitor-card relative p-5 rounded-xl border-2 {{ $currentStatus['bg'] }} {{ $currentStatus['border'] }} shadow-sm hover:shadow-md transition-all duration-200 group"
         data-history="{{ json_encode($monitor->history ?? []) }}"
         data-ip="{{ $monitor->ip_address }}"
         data-id="{{ $monitor->id }}"> <div class="flex justify-between items-start mb-4">
            <div class="flex items-center gap-2">
                <span id="dot-{{ $monitor->id }}" class="w-2.5 h-2.5 rounded-full {{ $currentStatus['dot'] }} animate-pulse"></span>
                
                <span id="badge-{{ $monitor->id }}" class="text-xs font-bold uppercase tracking-wide {{ $currentStatus['text'] }}">
                    {{ $currentStatus['badge'] }}
                </span>
            </div>
            
            <div class="w-8 h-8 flex items-center justify-center rounded-full bg-white border border-gray-200 text-xs font-bold text-gray-500 shadow-sm">
                {{ $locationCode }}
            </div>
        </div>

        <div class="flex items-start gap-4 mb-4">
            <div class="p-2 bg-white rounded-lg border border-gray-100 shadow-sm text-blue-600">
                @if($monitor->type == 'Switch')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                @elseif($monitor->type == 'Server')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 01-2 2v4a2 2 0 012 2h14a2 2 0 012-2v-4a2 2 0 01-2-2m-2-4h.01M17 16h.01"></path></svg>
                @else
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                @endif
            </div>

            <div>
                <h3 class="font-semibold text-gray-900 leading-tight mb-1 line-clamp-1" title="{{ $monitor->name }}">
                    {{ $monitor->name ?? 'Unknown Device' }}
                </h3>
                <p class="text-xs text-gray-500 uppercase font-medium tracking-wider">
                    {{ $monitor->type }} <span class="mx-1">•</span> 
                    
                    <span id="latency-val-{{ $monitor->id }}" class="font-mono font-bold text-lg transition-colors duration-200">
                        {{ $monitor->latency }}
                    </span> 
                    <span class="text-[10px]">ms</span>
                </p>
            </div>
        </div>

        <div class="flex justify-between items-end border-t border-gray-200 border-opacity-50 pt-3 mt-2">
            <div>
                <span class="text-[10px] uppercase text-gray-400 font-semibold mb-0.5 block">IP Address</span>
                <code class="text-sm font-mono text-gray-700 bg-white px-1.5 py-0.5 rounded border border-gray-100">
                    {{ $monitor->ip_address }}
                </code>
            </div>

            <form action="{{ route('monitor.destroy', $monitor->id) }}" method="POST" onsubmit="return confirm('Hapus device ini?');"
                  class="opacity-0 group-hover:opacity-100 transition-opacity">
                @csrf @method('DELETE')
                <button type="submit" class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </form>
        </div>
    </div>
@endforeach