@php
    // Split Centers: Regular (Horizontal Chain) vs Terminal (The Hub)
    // Jika hanya ada 1 device, dia jadi terminal langsung.
    $regularCenters = $centers->count() > 1 ? $centers->slice(0, $centers->count() - 1) : collect([]);
    $terminalCenter = $centers->last();
@endphp

{{-- MAIN WRAPPER: Flex Row (2 Independent Columns) --}}
{{-- items-start ensures top of Regular Chain aligns with top of Terminal (since Utara is absolute/negative margin) --}}
<div class="relative w-full min-h-full p-10 flex flex-row gap-8 items-start justify-start pt-40">
    {{-- pt-40 gives space for the Absolute Utara component above --}}

    {{-- COL 1: Regular Center Chain --}}
    @if ($regularCenters->count() > 0)
        <div id="zone-center-regular" class="flex flex-row gap-8 items-start justify-end z-10">
            @include('components.monitor-cards', ['monitors' => $regularCenters, 'parentZone' => 'center'])
        </div>
    @endif

    {{-- COL 2: HUB (Terminal) --}}
    {{-- Relative container for aligning Hub parts --}}
    <div id="hub-column" class="relative flex flex-col items-center z-10">

        {{-- Lintas Utara: Absolute Top, growing Up --}}
        {{-- bottom-full moves it right above the Terminal --}}
        <div id="zone-utara"
            class="absolute bottom-full mb-60 left-1/2 -translate-x-1/2 flex flex-row gap-8 items-end justify-center px-10 pt-10 pb-8 bg-gradient-to-br from-blue-50/80 to-blue-100/50 rounded-[2.5rem] border-2 border-blue-200/80 min-h-[150px] shadow-sm whitespace-nowrap">
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
            class="mt-40 flex flex-row gap-8 items-start justify-center relative px-10 pt-8 pb-10 bg-gradient-to-br from-orange-50/80 to-orange-100/50 rounded-[2.5rem] border-2 border-orange-200/80 min-h-[150px] shadow-sm">
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
        drawZoneLines();
    });
    window.addEventListener('resize', drawZoneLines);

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
                        createPath(svgMain, termPos.x, termPos.top, uPos.x, uPos.bottom, '#3b82f6');
                    }
                }

                if (selatanZone) {
                    const firstSelatan = selatanZone.querySelector('.monitor-card');
                    if (firstSelatan) {
                        const sPos = getCenter(firstSelatan, svgMain);
                        // Hubungkan Bawah KK ke Atas Lintas Selatan
                        createPath(svgMain, termPos.x, termPos.bottom, sPos.x, sPos.top, '#f97316');
                    }
                }
            }
        }

        // 2. HUBUNGKAN ATASAN KE ANAKAN DI DALAM ZONA
        const localZones = [
            { id: 'zone-utara', svg: 'svg-utara', color: '#3b82f6', dir: 'utara' },
            { id: 'zone-selatan', svg: 'svg-selatan', color: '#f97316', dir: 'selatan' }
        ];

        localZones.forEach(zone => {
            const zoneEl = document.getElementById(zone.id);
            const svgEl = document.getElementById(zone.svg);
            if (!zoneEl || !svgEl) return;
            svgEl.innerHTML = ''; 

            const parents = zoneEl.querySelectorAll('.tree-node');
            parents.forEach(p => {
                const pCard = p.querySelector(':scope > .tree-node-card .monitor-card');
                const cContainer = p.querySelector(':scope > .tree-children');

                if (pCard && cContainer && !cContainer.classList.contains('hidden')) {
                    const childCards = cContainer.querySelectorAll(':scope > .tree-node > .tree-node-card .monitor-card');
                    childCards.forEach(cCard => {
                        const pPos = getCenter(pCard, svgEl);
                        const cPos = getCenter(cCard, svgEl);

                        if (zone.dir === 'utara') {
                            // Utara: Dari Atas Parent ke Bawah Child (karena alur ke atas)
                            createPath(svgEl, pPos.x, pPos.top, cPos.x, cPos.bottom, zone.color, true);
                        } else {
                            // Selatan: Dari Bawah Parent ke Atas Child
                            createPath(svgEl, pPos.x, pPos.bottom, cPos.x, cPos.top, zone.color, true);
                        }
                    });
                }
            });
        });
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
        return {
            x: (rect.left + rect.width / 2) - svgRect.left,
            y: (rect.top + rect.height / 2) - svgRect.top,
            top: rect.top - svgRect.top,
            bottom: rect.bottom - svgRect.top
        };
    }

    window.drawZoneLines = drawZoneLines;
</script>