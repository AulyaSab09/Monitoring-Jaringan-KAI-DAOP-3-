@forelse($incidents as $incident)
    <tr class="hover:bg-slate-50 transition-all {{ $incident->up_at ? '' : 'bg-red-50/5' }}">
        <td class="px-8 py-10">
            <div class="font-black text-slate-900 text-2xl">{{ $incident->monitor->name }}</div>
            <div class="text-sm text-slate-400 font-mono font-black tracking-[0.15em] mt-2">{{ $incident->monitor->ip_address }}</div>
        </td>
        <td class="px-8 py-10">
            <span class="px-5 py-2.5 bg-slate-100 rounded-2xl text-xs font-black text-kai-navy uppercase tracking-widest border border-slate-200">
                {{ $incident->monitor->location ?? $incident->monitor->kode_lokasi ?? '-' }}
            </span>
        </td>
        <td class="px-8 py-10 bg-red-50/20">
            <div class="text-xl font-black text-red-700">{{ $incident->down_at->translatedFormat('l, d M Y') }}</div>
            <div class="text-sm font-bold text-red-500 italic mt-1">Pukul {{ $incident->down_at->format('H:i:s') }} WIB</div>
        </td>
        <td class="px-8 py-10 bg-emerald-50/20 text-center md:text-left">
            @if($incident->up_at)
                <div class="text-xl font-black text-emerald-700">{{ $incident->up_at->translatedFormat('l, d M Y') }}</div>
                <div class="text-sm font-bold text-emerald-400 italic mt-1">Pukul {{ $incident->up_at->format('H:i:s') }} WIB</div>
            @else
                <div class="flex items-center gap-3 text-orange-500">
                    <span class="relative flex h-4 w-4">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-orange-600"></span>
                    </span>
                    <span class="text-base font-black italic uppercase tracking-tighter animate-pulse">Sedang Perbaikan...</span>
                </div>
            @endif
        </td>
        <td class="px-8 py-10 text-center">
            @php
                $duration = '-';
                if($incident->up_at) {
                    $diff = $incident->down_at->diff($incident->up_at);
                    $duration = $diff->format('%Hj %Im %Sd');
                } else {
                    $diff = $incident->down_at->diff(now());
                    $duration = $diff->format('%Hj %Im %Sd');
                }
            @endphp
            <span class="inline-block px-8 py-4 rounded-2xl {{ $incident->up_at ? 'bg-slate-100 text-kai-navy border-2 border-slate-200 shadow-sm' : 'bg-red-600 text-white shadow-2xl shadow-red-200 animate-pulse' }} font-black text-xl italic tracking-tighter">
                {{ $duration }}
            </span>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="py-10 text-center text-gray-500 font-bold italic">
            Tidak ada data insiden ditemukan.
        </td>
    </tr>
@endforelse
