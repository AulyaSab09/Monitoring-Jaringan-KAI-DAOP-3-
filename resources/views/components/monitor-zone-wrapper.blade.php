@php
    // Split Centers: Regular (Horizontal Chain) vs Terminal (The Hub)
    // Jika hanya ada 1 device, dia jadi terminal langsung.
    $regularCenters = $centers->count() > 1 ? $centers->slice(0, $centers->count() - 1) : collect([]);
    $terminalCenter = $centers->last();
@endphp

{{-- MAIN WRAPPER: Flex Row (2 Independent Columns) --}}
{{-- items-start ensures top of Regular Chain aligns with top of Terminal (since Utara is absolute/negative margin) --}}
<div class="relative w-fit min-w-full min-h-full p-10 flex flex-row gap-0 items-start justify-start pt-40">    {{-- pt-40 gives space for the Absolute Utara component above --}}

    {{-- COL 1: Regular Center Chain --}}
    @if ($regularCenters->count() > 0)
        <div id="zone-center-regular" class="flex flex-row gap-8 items-start justify-end z-10">
            @include('components.monitor-cards', ['monitors' => $regularCenters, 'parentZone' => 'center'])
        </div>
    @endif

    {{-- COL 2: HUB (Terminal) --}}
    {{-- Relative container for aligning Hub parts --}}
    <div id="hub-column" class="relative flex flex-col items-start z-10">

        {{-- Lintas Utara: Absolute Top, growing Up --}}
        {{-- bottom-full moves it right above the Terminal --}}
        <div id="zone-utara"
            class="absolute bottom-full mb-60 left-0 flex flex-row gap-8 items-end justify-start px-10 pt-10 pb-8 min-h-[150px] whitespace-nowrap">
            <svg id="svg-utara" class="absolute inset-0 w-full h-full pointer-events-none overflow-visible z-0"></svg>
            <div
                class="absolute -bottom-4 left-1/2 -translate-x-1/2 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-[11px] font-black px-4 py-1.5 rounded-full uppercase tracking-wider shadow-lg z-30">
                Lintas Utara
            </div>
            @if ($utaras->count() > 0)
                @include('components.monitor-cards', [
                    'monitors' => $utaras,
                    'parentZone' => 'lintas utara',
                ])
            @else
                <div class="text-gray-400 italic text-sm py-4">Tidak ada device</div>
            @endif
        </div>

        {{-- Terminal Center (The Pivot) --}}
        @if ($terminalCenter)
            <div id="zone-center-terminal" class="relative z-20">
                @include('components.monitor-cards', [
                    'monitors' => collect([$terminalCenter]),
                    'parentZone' => 'center-terminal',
                ])
            </div>
        @endif

        {{-- Lintas Selatan: Standard Flow Below --}}
        {{-- mt-4 controls distance from Terminal --}}
        <div id="zone-selatan"
            class="mt-40 flex flex-row gap-8 items-start justify-start relative px-10 pt-8 pb-10 min-h-[150px]">
            <svg id="svg-selatan" class="absolute inset-0 w-full h-full pointer-events-none overflow-visible z-0"></svg>
            <div
                class="absolute -top-4 left-1/2 -translate-x-1/2 bg-gradient-to-r from-orange-500 to-orange-600 text-white text-[11px] font-black px-4 py-1.5 rounded-full uppercase tracking-wider shadow-lg z-30">
                Lintas Selatan
            </div>
            @if ($selatans->count() > 0)
                @include('components.monitor-cards', [
                    'monitors' => $selatans,
                    'parentZone' => 'lintas selatan',
                ])
            @else
                <div class="text-gray-400 italic text-sm py-4">Tidak ada device</div>
            @endif
        </div>

    </div>

    {{-- SVG Lines Layer --}}
    <svg id="zone-lines-svg" class="absolute inset-0 w-full h-full pointer-events-none overflow-visible"
        style="z-index: 0;">
        <!-- JS will populate paths -->
    </svg>

</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Use ResizeObserver for more robust detection of container size changes (like zoom)
        const observer = new ResizeObserver(() => {
            requestAnimationFrame(() => drawZoneLines());
        });

        const container = document.querySelector('.relative.w-full.min-h-full');
        if (container) observer.observe(container);

        // Also listen to window resize as fallback
        window.addEventListener('resize', () => {
            requestAnimationFrame(() => drawZoneLines());
        });

        // Initial draw
        drawZoneLines();
    });

    function drawZoneLines() {
        const svgMain = document.getElementById('zone-lines-svg');
        if (!svgMain) return;
        svgMain.innerHTML = '';

        const terminalZone = document.getElementById('zone-center-terminal');
        const utaraZone = document.getElementById('zone-utara');
        const selatanZone = document.getElementById('zone-selatan');

        // 1. HUBUNGKAN TERMINAL (KK) KE LINTAS UTARA & SELATAN
        if (terminalZone) {
            const termCard = terminalZone.querySelector('.monitor-card');
            if (termCard) {
                const termPos = getCenter(termCard, svgMain);

                if (utaraZone) {
                    const firstUtara = utaraZone.querySelector('.monitor-card');
                    if (firstUtara) {
                        const uPos = getCenter(firstUtara, svgMain);
                        // Hubungkan Atas KK ke Bawah Lintas Utara
                        createPath(svgMain, termPos.x, termPos.top, uPos.x, uPos.bottom, '#cbd5e1');
                    }
                }

                if (selatanZone) {
                    const firstSelatan = selatanZone.querySelector('.monitor-card');
                    if (firstSelatan) {
                        const sPos = getCenter(firstSelatan, svgMain);
                        // Hubungkan Bawah KK ke Atas Lintas Selatan
                        createPath(svgMain, termPos.x, termPos.bottom, sPos.x, sPos.top, '#cbd5e1');
                    }
                }
            }
        }

        // 2. (REMOVED) HUBUNGKAN ATASAN KE ANAKAN DI DALAM ZONA
        // Logic ini sekarang ditangani sepenuhnya oleh monitor-dashboard.js (drawTreeLines)
        // agar gaya garis konsisten (lurus/siku) dan tidak double-drawing.
    }

    // FUNGSI KUNCI: Membuat garis lengkung SVG
    function createPath(svg, x1, y1, x2, y2, color, isDashed = false) {
        const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        const midY = (y1 + y2) / 2;

        // Membuat kurva S yang halus
        const d = `M ${x1} ${y1} C ${x1} ${midY}, ${x2} ${midY}, ${x2} ${y2}`;

        path.setAttribute('d', d);
        path.setAttribute('stroke', color);
        path.setAttribute('stroke-width', '4');
        path.setAttribute('fill', 'none');
        path.setAttribute('stroke-linecap', 'round');

        if (isDashed) {
            path.setAttribute('stroke-dasharray', '8,8'); // Efek putus-putus untuk anakan
        }

        svg.appendChild(path);
    }

    function getCenter(el, svg) {
        const rect = el.getBoundingClientRect();
        const svgRect = svg.getBoundingClientRect();

        // Get Zoom Factor (default to 1 if not available)
        const zoom = (typeof window.getCurrentZoom === 'function') ? window.getCurrentZoom() : 1;

        return {
            x: ((rect.left + rect.width / 2) - svgRect.left) / zoom,
            y: ((rect.top + rect.height / 2) - svgRect.top) / zoom,
            top: (rect.top - svgRect.top) / zoom,
            bottom: (rect.bottom - svgRect.top) / zoom
        };
    }

    window.drawZoneLines = drawZoneLines;
</script>
