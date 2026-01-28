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
            class="absolute bottom-full mb-8 left-1/2 -translate-x-1/2 flex flex-row gap-8 items-end justify-center px-8 pt-8 pb-6 bg-gradient-to-br from-blue-50/80 to-blue-100/50 rounded-2xl border border-blue-200/80 min-h-[120px] shadow-sm whitespace-nowrap">
            <div
                class="absolute -bottom-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider shadow-md">
                Lintas Utara</div>
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
            class="mt-8 flex flex-row gap-8 items-start justify-center relative px-8 pt-6 pb-8 bg-gradient-to-br from-orange-50/80 to-orange-100/50 rounded-2xl border border-orange-200/80 min-h-[120px] shadow-sm">
            <div
                class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-orange-500 to-orange-600 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider shadow-md">
                Lintas Selatan</div>
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
        const svg = document.getElementById('zone-lines-svg');
        const regularZone = document.getElementById('zone-center-regular');
        const terminalZone = document.getElementById('zone-center-terminal');
        const utaraZone = document.getElementById('zone-utara');
        const selatanZone = document.getElementById('zone-selatan');

        if (!svg) return;

        // Clear existing lines
        while (svg.firstChild) {
            svg.removeChild(svg.firstChild);
        }

        function getCenter(el) {
            if (!el) return {
                x: 0,
                y: 0,
                right: 0,
                left: 0,
                bottom: 0,
                top: 0
            };
            const rect = el.getBoundingClientRect();
            const wrapperRect = svg.getBoundingClientRect();
            return {
                x: (rect.left + rect.width / 2) - wrapperRect.left,
                y: (rect.top + rect.height / 2) - wrapperRect.top,
                right: (rect.right) - wrapperRect.left,
                left: (rect.left) - wrapperRect.left,
                bottom: (rect.bottom) - wrapperRect.top,
                top: (rect.top) - wrapperRect.top
            };
        }

        let hubInputPoint = null;

        // 1. Connect Regular Chain to Terminal -> DISABLED As per user request
        // if (regularZone && terminalZone) { ... }

        // 2. Connect Terminal to Utara and Selatan
        if (terminalZone) {
            const terminalRootNodes = terminalZone.querySelectorAll(':scope > .tree-node');
            const terminalCard = terminalRootNodes[0]?.querySelector(':scope > .tree-node-card');

            if (terminalCard) {
                const termPos = getCenter(terminalCard);

                // To Utara (Connect Top of Terminal to Bottom of First Utara)
                if (utaraZone) {
                    const utaraNodes = utaraZone.querySelectorAll(':scope > .tree-node');
                    if (utaraNodes.length > 0) {
                        const firstUtaraCard = utaraNodes[0].querySelector(':scope > .tree-node-card');
                        if (firstUtaraCard) {
                            const uPos = getCenter(firstUtaraCard);
                            createPath(termPos.x, termPos.top, uPos.x, uPos.bottom, '#3b82f6'); // Blue
                        }
                    }
                }

                // To Selatan (Connect Bottom of Terminal to Top of First Selatan)
                if (selatanZone) {
                    const selatanNodes = selatanZone.querySelectorAll(':scope > .tree-node');
                    if (selatanNodes.length > 0) {
                        const firstSelatanCard = selatanNodes[0].querySelector(':scope > .tree-node-card');
                        if (firstSelatanCard) {
                            const sPos = getCenter(firstSelatanCard);
                            createPath(termPos.x, termPos.bottom, sPos.x, sPos.top, '#f97316'); // Orange
                        }
                    }
                }
            }
        }

        function createPath(x1, y1, x2, y2, color) {
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            // Logic for vertical vs horizontal curves
            // If vertically aligned (x similar), straight line or slight S
            const isVertical = Math.abs(x1 - x2) < 50;

            let d = '';
            if (isVertical) {
                const midY = (y1 + y2) / 2;
                d = `M ${x1} ${y1} C ${x1} ${midY}, ${x2} ${midY}, ${x2} ${y2}`;
            } else {
                const midX = (x1 + x2) / 2;
                d = `M ${x1} ${y1} C ${midX} ${y1}, ${midX} ${y2}, ${x2} ${y2}`;
            }

            path.setAttribute('d', d);
            path.setAttribute('stroke', color);
            path.setAttribute('stroke-width', '4');
            path.setAttribute('fill', 'none');
            path.setAttribute('stroke-linecap', 'round');
            path.setAttribute('class', 'animate-pulse');
            svg.appendChild(path);
        }
    }

    window.drawZoneLines = drawZoneLines;
</script>
