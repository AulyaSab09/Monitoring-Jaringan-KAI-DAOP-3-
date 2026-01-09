@foreach($monitors as $monitor)
    @php
        // Logika Warna
        $statusClass = 'bg-green-100 text-green-800 border-green-200';
        $barColor = 'bg-green-500';
        $textColor = 'text-gray-700';
        $latencyText = $monitor->latency . ' ms';
        
        if($monitor->status == 'Disconnected') {
            $statusClass = 'bg-red-100 text-red-800 border-red-200';
            $barColor = 'bg-red-500';
            $textColor = 'text-red-600';
            $latencyText = 'Timeout';
        } 
        elseif($monitor->status == 'Unstable') {
            $statusClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
            $barColor = 'bg-yellow-500';
        }

        $widthPercentage = $monitor->latency > 0 ? min(($monitor->latency / 1000) * 100, 100) : 0;
    @endphp

    {{-- PERHATIKAN BAGIAN TR INI: Ada penambahan data-history dan class --}}
    <tr class="hover:bg-gray-50 transition-colors duration-150 cursor-crosshair monitor-row"
        data-history="{{ json_encode($monitor->history ?? []) }}"
        data-ip="{{ $monitor->ip_address }}">
        
        <td class="px-6 py-4 font-medium text-gray-900">
            {{ $monitor->ip_address }}
        </td>
        
        <td class="px-6 py-4">
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium {{ $textColor }}">
                    {{ $latencyText }}
                </span>
                @if($monitor->status != 'Disconnected')
                    <div class="w-16 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full {{ $barColor }}" style="width: {{ $widthPercentage }}%"></div>
                    </div>
                @endif
            </div>
        </td>

        <td class="px-6 py-4">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusClass }}">
                {{ $monitor->status }}
            </span>
        </td>

        <td class="px-6 py-4 text-right text-sm text-gray-500">
            {{ $monitor->updated_at->diffForHumans() }}
        </td>

        <td class="px-6 py-4 text-right">
            <form action="{{ route('monitor.destroy', $monitor->id) }}" method="POST" onsubmit="return confirm('Hapus IP ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium">
                    Hapus
                </button>
            </form>
        </td>
    </tr>
@endforeach