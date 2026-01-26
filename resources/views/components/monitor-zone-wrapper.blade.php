<div class="relative w-full min-h-full flex flex-row items-start justify-start p-10 gap-12">
    
    {{-- Container Left: Center Zone --}}
    <div id="zone-center" class="flex flex-row gap-8 items-start justify-start z-10">
        @include('components.monitor-cards', ['monitors' => $centers])
    </div>

    {{-- Container Right: Branches (Utara & Selatan stacked vertically) --}}
    <div class="flex flex-col gap-10 z-10">
        
        {{-- Lintas Utara - items-end so main devices sit at bottom (aligned with Center) --}}
        <div id="zone-utara" class="flex flex-row gap-8 items-end justify-start relative px-8 pt-8 pb-4 bg-blue-50/50 rounded-2xl border border-blue-200 min-h-[100px]">
            <div class="absolute -bottom-3 left-4 bg-blue-500 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider shadow-sm">Lintas Utara</div>
            @if($utaras->count() > 0)
                @include('components.monitor-cards', ['monitors' => $utaras, 'parentZone' => 'lintas utara'])
            @else
                <div class="text-gray-400 italic text-sm py-4">Tidak ada device</div>
            @endif
        </div>

        {{-- Lintas Selatan - items-start so children grow downward --}}
        <div id="zone-selatan" class="flex flex-row gap-8 items-start justify-start relative px-8 pt-8 pb-4 bg-orange-50/50 rounded-2xl border border-orange-200 min-h-[100px]">
            <div class="absolute -top-3 left-4 bg-orange-500 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider shadow-sm">Lintas Selatan</div>
            @if($selatans->count() > 0)
                @include('components.monitor-cards', ['monitors' => $selatans, 'parentZone' => 'lintas selatan'])
            @else
                <div class="text-gray-400 italic text-sm py-4">Tidak ada device</div>
            @endif
        </div>

    </div>

    {{-- SVG Lines Layer (Absolute to Wrapper) --}}
    <svg id="zone-lines-svg" class="absolute inset-0 w-full h-full pointer-events-none overflow-visible" style="z-index: 0;">
        <!-- Lines will be drawn here by JS -->
    </svg>

</div>

<script>
    // Simple script to draw lines on load
    document.addEventListener("DOMContentLoaded", () => {
        drawZoneLines();
    });

    // Re-draw on window resize
    window.addEventListener('resize', drawZoneLines);

    function drawZoneLines() {
        const svg = document.getElementById('zone-lines-svg');
        const centerZone = document.getElementById('zone-center');
        const utaraZone = document.getElementById('zone-utara');
        const selatanZone = document.getElementById('zone-selatan');
        
        if(!svg || !centerZone) return;

        // Clear existing lines
        while (svg.firstChild) {
            svg.removeChild(svg.firstChild);
        }

        // Helper to get center point of element relative to wrapper
        // Helper to get center point of element relative to wrapper
        function getCenter(el) {
            const rect = el.getBoundingClientRect();
            const wrapperRect = svg.getBoundingClientRect();
            return {
                x: (rect.left + rect.width / 2) - wrapperRect.left,
                y: (rect.top + rect.height / 2) - wrapperRect.top,
                right: (rect.right) - wrapperRect.left, // Right edge
                left: (rect.left) - wrapperRect.left,
                bottom: (rect.bottom) - wrapperRect.top, // Bottom Edge
                top: (rect.top) - wrapperRect.top // Top Edge
            };
        }

        // --- CORE LOGIC: Connect to the LAST ROOT device in Center Zone ---
        // Use :scope selector to only get direct descendants, not nested children
        // Structure: zone-center > .tree-node > .tree-node-card
        const centerRootNodes = centerZone.querySelectorAll(':scope > .tree-node');
        let sourcePoint;

        if (centerRootNodes.length > 0) {
            // Get the last ROOT device's card
            const lastRootNode = centerRootNodes[centerRootNodes.length - 1];
            const lastDeviceCard = lastRootNode.querySelector(':scope > .tree-node-card');
            
            if (lastDeviceCard) {
                const lastDeviceRect = getCenter(lastDeviceCard);
                sourcePoint = {
                    x: lastDeviceRect.x,
                    y: lastDeviceRect.y,
                    right: lastDeviceRect.right,
                    left: lastDeviceRect.left
                };
            } else {
                sourcePoint = getCenter(centerZone);
            }
        } else {
            // Fallback if no devices, use center of container
            sourcePoint = getCenter(centerZone);
        }
        
        // Draw Line to Utara - target the FIRST ROOT device in Utara
        const utaraRootNodes = utaraZone ? utaraZone.querySelectorAll(':scope > .tree-node') : [];
        if (utaraRootNodes.length > 0) { 
            const firstUtaraNode = utaraRootNodes[0];
            const firstUtaraCard = firstUtaraNode.querySelector(':scope > .tree-node-card');
            
            if (firstUtaraCard) {
                const utaraPoint = getCenter(firstUtaraCard);
                createPath(sourcePoint.right, sourcePoint.y, utaraPoint.left, utaraPoint.y, '#3b82f6'); 
            }
        }

        // Draw Line to Selatan - target the FIRST ROOT device in Selatan
        const selatanRootNodes = selatanZone ? selatanZone.querySelectorAll(':scope > .tree-node') : [];
        if (selatanRootNodes.length > 0) {
            const firstSelatanNode = selatanRootNodes[0];
            const firstSelatanCard = firstSelatanNode.querySelector(':scope > .tree-node-card');

            if (firstSelatanCard) {
                const selatanPoint = getCenter(firstSelatanCard);
                createPath(sourcePoint.right, sourcePoint.y, selatanPoint.left, selatanPoint.y, '#f97316'); 
            }
        }

        function createPath(x1, y1, x2, y2, color) {
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            // Curvy Line Logic
            const midX = (x1 + x2) / 2;
            const d = `M ${x1} ${y1} C ${midX} ${y1}, ${midX} ${y2}, ${x2} ${y2}`;
            
            path.setAttribute('d', d);
            path.setAttribute('stroke', color);
            path.setAttribute('stroke-width', '4');
            path.setAttribute('fill', 'none');
            path.setAttribute('stroke-linecap', 'round');
            path.setAttribute('class', 'animate-pulse'); // Optional animation
            svg.appendChild(path);
        }
    }
    
    // Call drawZoneLines globally accessible for AJAX updates if needed
    window.drawZoneLines = drawZoneLines;
</script>
