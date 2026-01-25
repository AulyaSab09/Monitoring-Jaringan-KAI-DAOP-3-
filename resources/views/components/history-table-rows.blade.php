@php
    // Separate incidents into Ongoing (Down) and Resolved (Up)
    $ongoing = $incidents->filter(fn($i) => is_null($i->up_at));
    $resolved = $incidents->filter(fn($i) => !is_null($i->up_at));
@endphp

{{-- SECTION 1: ONGOING INCIDENTS (TERBARU / DOWN) --}}
@if($ongoing->isNotEmpty())
    <tr>
        <td colspan="6" class="px-8 py-4 bg-red-50 border-b border-red-100">
            <div class="flex items-center gap-3 text-red-700 font-black tracking-widest uppercase text-sm">
                <span class="flex h-3 w-3 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-600"></span>
                </span>
                Gangguan Sedang Terjadi (Live)
            </div>
        </td>
    </tr>
    @foreach($ongoing as $incident)
        <tr class="hover:bg-red-50/10 transition-all bg-red-50/5 border-l-4 border-l-red-500">
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
                <div class="flex items-center gap-3 text-orange-500">
                    <span class="relative flex h-4 w-4">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-orange-600"></span>
                    </span>
                    <span class="text-base font-black italic uppercase tracking-tighter animate-pulse">Sedang Perbaikan...</span>
                </div>
            </td>
            <td class="px-8 py-10 text-center">
                @php
                    $diff = $incident->down_at->diff(now());
                    $duration = $diff->format('%Hj %Im %Sd');
                @endphp
                <span class="inline-block px-8 py-4 rounded-2xl bg-red-600 text-white shadow-2xl shadow-red-200 animate-pulse font-black text-xl italic tracking-tighter">
                    {{ $duration }}
                </span>
            </td>
            <td class="px-8 py-10 text-center">
                 <span class="px-5 py-2.5 rounded-2xl text-xs font-black uppercase tracking-widest border bg-red-100 text-red-700 border-red-200">
                    {{ $incident->status ?? 'DOWN' }}
                </span>
            </td>
        </tr>
    @endforeach
@endif

{{-- SECTION 2: RESOLVED INCIDENTS (SELESAI) --}}
@if($resolved->isNotEmpty())
    <tr>
        <td colspan="6" class="px-8 py-4 bg-slate-50 border-b border-slate-100">
            <div class="flex items-center gap-3 text-slate-500 font-black tracking-widest uppercase text-sm">
                <i class="fa-solid fa-clock-rotate-left"></i>
                Riwayat Terselesaikan
            </div>
        </td>
    </tr>
    @foreach($resolved as $incident)
        <tr class="hover:bg-slate-50 transition-all">
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
                <div class="text-xl font-black text-emerald-700">{{ $incident->up_at->translatedFormat('l, d M Y') }}</div>
                <div class="text-sm font-bold text-emerald-400 italic mt-1">Pukul {{ $incident->up_at->format('H:i:s') }} WIB</div>
            </td>
            <td class="px-8 py-10 text-center">
                @php
                    $diff = $incident->down_at->diff($incident->up_at);
                    $duration = $diff->format('%Hj %Im %Sd');
                @endphp
                <span class="inline-block px-8 py-4 rounded-2xl bg-slate-100 text-kai-navy border-2 border-slate-200 shadow-sm font-black text-xl italic tracking-tighter">
                    {{ $duration }}
                </span>
            </td>
             <td class="px-8 py-10 text-center">
                @php
                    $statusColor = 'bg-slate-100 text-slate-500';
                    $statusText = $incident->status ?? 'Unknown';
                    
                    if (strtolower($statusText) === 'connected') {
                        $statusColor = 'bg-emerald-100 text-emerald-700 border-emerald-200';
                    } elseif (strtolower($statusText) === 'disconnected' || strtolower($statusText) === 'down') {
                        $statusColor = 'bg-red-100 text-red-700 border-red-200';
                    }
                @endphp
                 <span class="px-5 py-2.5 rounded-2xl text-xs font-black uppercase tracking-widest border {{ $statusColor }}">
                    {{ $statusText }}
                </span>
            </td>
        </tr>
    @endforeach
@endif

@if($incidents->isEmpty())
    <tr>
        <td colspan="6" class="py-10 text-center text-gray-500 font-bold italic">
            Tidak ada data insiden ditemukan.
        </td>
    </tr>
@endif
