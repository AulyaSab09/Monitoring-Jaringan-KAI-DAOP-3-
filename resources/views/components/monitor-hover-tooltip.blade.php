<div id="chart-tooltip"
     class="hidden fixed w-[240px] bg-white border border-gray-200
            rounded-lg shadow-lg z-50 p-2 pointer-events-none">

    <div class="text-[11px] font-semibold text-gray-900 mb-1">
        Detail Perangkat
    </div>

    <div class="space-y-1 text-[11px]">
        <div class="flex justify-between gap-3">
            <span class="text-gray-500">Stasiun</span>
            <span id="tt-station" class="font-semibold text-gray-900 text-right truncate max-w-[180px]">-</span>
        </div>

        <div class="flex justify-between gap-3">
            <span class="text-gray-500">Perangkat</span>
            <span id="tt-name" class="font-semibold text-gray-900 text-right truncate max-w-[180px]">-</span>
        </div>

        <div class="flex justify-between gap-3">
            <span class="text-gray-500">IP</span>
            <span id="tt-ip" class="font-mono font-semibold text-gray-900 text-right">-</span>
        </div>

        <div class="flex justify-between gap-3">
            <span class="text-gray-500">Latency</span>
            <span id="tt-latency" class="font-mono font-semibold text-gray-900 text-right">-</span>
        </div>
    </div>
</div>
