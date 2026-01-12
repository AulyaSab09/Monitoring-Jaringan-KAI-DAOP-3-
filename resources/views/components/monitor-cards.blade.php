@foreach($monitors as $monitor)
    @php
        $statusColors = [
            'Connected' => [
                'bg' => 'bg-[#D4FFE1]',
                'border' => 'border-[#00A63E]',
                'dot' => 'bg-[#00A63E]',
                'text' => 'text-[#0D542B]',
                'badge' => 'UP'
            ],
            'Unstable' => [
                'bg' => 'bg-[#FFECD5]',
                'border' => 'border-[#EA580C]',
                'dot' => 'bg-[#EA580C]',
                'text' => 'text-[#7E2A0C]',
                'badge' => 'WARNING'
            ],
            'Disconnected' => [
                'bg' => 'bg-[#FFDCDC]',
                'border' => 'border-[#DC2626]',
                'dot' => 'bg-[#DC2626]',
                'text' => 'text-[#82181A]',
                'badge' => 'DOWN'
            ],
            'Pending' => [
                'bg' => 'bg-[#E1E1E1]',
                'border' => 'border-[#BDBDBD]',
                'dot' => 'bg-[#9CA3AF]',
                'text' => 'text-[#101828]',
                'badge' => '...'
            ],
        ];

        $currentStatus = $statusColors[$monitor->status] ?? $statusColors['Pending'];

        $deviceCodeMap = [
            'Switch' => 'SW',
            'Router' => 'RT',
            'Access Point' => 'AP',
            'PC' => 'PC',
            'CCTV' => 'CT',
        ];

        $deviceCode = $deviceCodeMap[$monitor->type] ?? strtoupper(substr($monitor->type ?? 'DV', 0, 2));
        $typeLower = strtolower($monitor->type ?? '');
    @endphp

  <div id="card-{{ $monitor->id }}"
     class="monitor-card group relative rounded-md border {{ $currentStatus['border'] }}
            bg-white shadow-sm hover:shadow transition-all duration-150 p-2"
     data-id="{{ $monitor->id }}"
     data-ip="{{ $monitor->ip_address }}"
     data-name="{{ $monitor->name ?? '-' }}"
     data-station="{{ $monitor->stasiun ?? '-' }}"
     data-latency="{{ $monitor->latency ?? 0 }}"
     data-history='@json($monitor->history ?? [])'>

        {{-- HEADER --}}
        <div class="flex items-center justify-between">
            {{-- STATUS BADGE --}}
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold
                         {{ $currentStatus['bg'] }} {{ $currentStatus['text'] }} border {{ $currentStatus['border'] }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $currentStatus['dot'] }}"></span>

                @if($currentStatus['badge'] === 'UP')
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                @elseif($currentStatus['badge'] === 'WARNING')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v4m0 4h.01M5.07 19h13.86L12 4.5 5.07 19z" />
                    </svg>
                @elseif($currentStatus['badge'] === 'DOWN')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                @endif

                <span>{{ $currentStatus['badge'] }}</span>
            </span>

            {{-- DEVICE CODE --}}
            <div class="w-6 h-6 flex items-center justify-center rounded-full bg-gray-50
                        border border-gray-200 text-[10px] font-bold text-gray-600">
                {{ $deviceCode }}
            </div>
        </div>

        {{-- BODY --}}
        <div class="mt-1 flex items-start gap-2">
            {{-- DEVICE ICON --}}
            <div class="shrink-0 p-1.5 bg-white rounded-md border border-gray-100 text-blue-600">
                @if($typeLower === 'switch')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                @elseif($typeLower === 'router')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                    </svg>
                @elseif($typeLower === 'access point')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 18h.01M8.5 14.5a5 5 0 017 0M5.5 11.5a9 9 0 0113 0M3 8a13 13 0 0118 0" />
                    </svg>
                @elseif($typeLower === 'pc')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9.75 17h4.5M4 5h16v10H4V5z" />
                    </svg>
                @elseif($typeLower === 'cctv')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M14 7l7 3-7 3-7-3 7-3zM7 13v4a2 2 0 002 2h6a2 2 0 002-2v-4" />
                    </svg>
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 6v6l4 2" />
                    </svg>
                @endif
            </div>

            {{-- DEVICE INFO --}}
            <div class="min-w-0">
                <h3 class="text-xs font-semibold text-gray-900 leading-tight truncate" title="{{ $monitor->name }}">
                    {{ $monitor->name ?? 'Unknown Device' }}
                </h3>

                <div class="mt-0.5 flex items-center gap-1 text-[10px] text-gray-500 uppercase font-medium tracking-wide">
                    <span class="truncate max-w-[90px]">{{ $monitor->type }}</span>
                    <span class="text-gray-300">•</span>
                    <span id="latency-val-{{ $monitor->id }}" class="font-mono font-bold text-[11px] text-gray-700">
                        {{ $monitor->latency }}
                    </span>
                    <span class="text-[9px]">ms</span>
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="mt-1.5 pt-1.5 border-t border-gray-100 flex items-end justify-between">
            <div class="min-w-0">
                <span class="text-[9px] uppercase text-gray-400 font-semibold block">Stasiun</span>
                <p class="text-xs font-medium text-gray-800 truncate">
                    {{ $monitor->stasiun ?? '-' }}
                </p>
            </div>

            {{-- DELETE (hover) --}}
            <form action="{{ route('monitor.destroy', $monitor->id) }}" method="POST"
                  onsubmit="return confirm('Hapus device ini?');"
                  class="opacity-0 group-hover:opacity-100 transition-opacity">
                @csrf @method('DELETE')
                <button type="submit"
                        class="p-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
@endforeach
