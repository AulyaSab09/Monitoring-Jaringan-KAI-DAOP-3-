<div id="chart-tooltip"
     class="hidden fixed z-50 w-[260px] rounded-lg bg-white
            shadow-xl border border-gray-200 p-3 text-xs">

    <div class="font-semibold text-gray-900 mb-2">Detail Perangkat</div>

    <div class="space-y-1">
        <div class="flex justify-between">
            <span class="text-gray-500">Stasiun</span>
            <span id="tt-station" class="font-medium">-</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Perangkat</span>
            <span id="tt-name" class="font-medium">-</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">IP</span>
            <span id="tt-ip" class="font-mono">-</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Latency</span>
            <span id="tt-latency" class="font-semibold">-</span>
        </div>
    </div>

    <div class="mt-2">
        <div id="chart-canvas"></div>
    </div>
</div>
