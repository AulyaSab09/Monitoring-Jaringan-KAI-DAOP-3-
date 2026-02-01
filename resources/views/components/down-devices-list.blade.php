<div class="mt-4 mb-6 px-4">
    <div class="flex items-center gap-3 mb-3">
        <div class="w-1.5 h-6 bg-red-600 rounded-full animate-pulse"></div>
        <h2 class="text-lg font-black text-gray-900 tracking-tight uppercase">Perangkat Terdeteksi Down</h2>
    </div>

    {{-- Kontainer Scroll --}}
    <div id="down-devices-list" class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide" style="scroll-behavior: smooth;">
        <div id="no-down-message"
            class="w-full py-6 text-center bg-gray-100 rounded-2xl border-2 border-dashed border-gray-300">
            <p class="text-gray-500 font-bold italic">Sistem Aman: Semua perangkat dalam kondisi normal.</p>
        </div>
    </div>
</div>
