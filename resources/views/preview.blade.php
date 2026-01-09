<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Network Monitoring Dashboard</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        
        <style>
            /* === CHART TOOLTIP === */
            #chart-tooltip {
                display: none;
                position: absolute;
                width: 280px;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(4px);
                border: 1px solid #e5e7eb;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                border-radius: 12px;
                z-index: 50;
                padding: 12px;
                pointer-events: none;
                transition: opacity 0.2s ease;
            }
            
            /* === TREE VIEW STYLES === */
            /* Tree Container with Zoom/Pan */
            .tree-container {
                position: relative;
                overflow: hidden;
                padding: 2rem 1rem;
                min-height: 500px;
                cursor: grab;
            }
            
            .tree-container:active {
                cursor: grabbing;
            }
            
            .tree-viewport {
                transform-origin: 0 0;
                transition: transform 0.1s ease-out;
                min-width: max-content;
                position: relative;
            }
            
            /* SVG Canvas for Lines */
            .tree-lines-svg {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
                z-index: 1;
            }
            
            .tree-line {
                stroke: #94a3b8;
                stroke-width: 2;
                fill: none;
                stroke-linecap: round;
            }
            
            .tree-line.status-connected { stroke: #10b981; }
            .tree-line.status-disconnected { stroke: #ef4444; stroke-dasharray: 5,5; }
            .tree-line.status-unstable { stroke: #f97316; }
            
            /* Tree Structure - NO WRAP for main devices */
            .tree-wrapper {
                display: flex;
                flex-direction: row;
                align-items: flex-start;
                justify-content: flex-start;
                gap: 2rem;
                flex-wrap: nowrap;
                position: relative;
                z-index: 2;
                padding: 1rem;
            }
            
            .tree-level {
                display: flex;
                justify-content: center;
                gap: 1.5rem;
                margin-bottom: 3rem;
                flex-wrap: wrap;
            }
            
            .tree-node {
                display: flex;
                flex-direction: column;
                align-items: center;
                position: relative;
            }
            
            .tree-node-card {
                position: relative;
                z-index: 2;
            }
            
            .tree-children {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 1rem;
                margin-top: 2.5rem;
            }
            
            /* Hover Add Button (Outside Card) */
            .hover-add-btn {
                position: absolute;
                bottom: -16px;
                left: 50%;
                transform: translateX(-50%) scale(0);
                width: 28px;
                height: 28px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                border: 2px solid white;
                border-radius: 50%;
                color: white;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                z-index: 20;
                cursor: pointer;
                opacity: 0;
            }
            
            .hover-add-btn:hover {
                transform: translateX(-50%) scale(1.15);
                box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
            }
            
            .tree-node-card:hover .hover-add-btn {
                transform: translateX(-50%) scale(1);
                opacity: 1;
            }
            
            /* Status Warning Animations */
            @keyframes pulse-warning-down {
                0%, 100% { 
                    border-color: #fca5a5;
                    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
                }
                50% { 
                    border-color: #ef4444;
                    box-shadow: 0 0 8px 2px rgba(239, 68, 68, 0.3);
                }
            }
            .child-down-warning {
                animation: pulse-warning-down 1.5s ease-in-out infinite;
                border-color: #ef4444 !important;
                background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%) !important;
            }
            
            @keyframes pulse-warning-unstable {
                0%, 100% { border-color: #fdba74; }
                50% { 
                    border-color: #f97316;
                    box-shadow: 0 0 6px 1px rgba(249, 115, 22, 0.2);
                }
            }
            .child-unstable-warning {
                animation: pulse-warning-unstable 2s ease-in-out infinite;
                border-color: #f97316 !important;
                background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%) !important;
            }
            
            /* Responsive: Mobile Stack */
            @media (max-width: 768px) {
                .tree-children {
                    flex-direction: column;
                    align-items: center;
                }
                .tree-level {
                    flex-direction: column;
                    align-items: center;
                }
            }
        </style>
    </head>
    <body class="bg-[#f8f9fa] text-[#1b1b18] min-h-screen p-6 md:p-8 font-sans selection:bg-blue-100">
        <div class="max-w-full mx-auto">
            
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 animate-bounce">
                {{ session('success') }}
            </div>
            @endif

            <header class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold mb-1 text-gray-900 tracking-tight">Network Topology</h1>
                    <div class="flex items-center gap-2 text-sm text-gray-500 font-medium">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        Real-time monitoring active
                    </div>
                </div>
                <div class="flex gap-3 items-center">
                    <a href="{{ route('monitor.create') }}" class="flex items-center gap-2 px-5 py-2.5 bg-gray-900 text-white rounded-xl hover:bg-gray-800 text-sm font-semibold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5">
                        <span>+ Add Device</span>
                    </a>
                </div>
            </header>

            {{-- TREE VIEW CONTAINER --}}
            <div id="tree-container" class="tree-container bg-white rounded-2xl border border-gray-200 shadow-sm">
                
                {{-- Zoom Controls --}}
                <div class="absolute top-4 right-4 z-30 flex flex-col gap-2 bg-white/90 backdrop-blur-sm rounded-xl p-2 shadow-lg border border-gray-200">
                    <button onclick="zoomIn()" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-blue-500 hover:text-white rounded-lg transition-colors font-bold text-gray-600" title="Zoom In">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                    <span id="zoom-level" class="text-[10px] font-bold text-gray-500 text-center">100%</span>
                    <button onclick="zoomOut()" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-blue-500 hover:text-white rounded-lg transition-colors font-bold text-gray-600" title="Zoom Out">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"></path></svg>
                    </button>
                    <button onclick="resetZoom()" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-[10px] font-bold text-gray-500" title="Reset Zoom">
                        FIT
                    </button>
                </div>
                
                {{-- Zoomable Viewport --}}
                <div id="tree-viewport" class="tree-viewport">
                    {{-- SVG Canvas for Connecting Lines --}}
                    <svg id="tree-lines-svg" class="tree-lines-svg"></svg>
                    
                    {{-- Tree Structure --}}
                    <div id="tree-wrapper" class="tree-wrapper">
                        @include('components.monitor-cards', ['monitors' => $monitors])
                    </div>
                </div>
            </div>
            
            <div class="mt-8 flex items-center justify-between text-xs text-gray-400 font-medium uppercase tracking-widest">
                <span>Auto-refreshing every 2s • Scroll to zoom, drag to pan</span>
                <span>KAI DAOP 3 Cirebon</span>
            </div>
        </div>

        {{-- Chart Tooltip --}}
        <div id="chart-tooltip">
            <div class="flex justify-between items-center mb-2 border-b border-gray-100 pb-2">
                <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Latency History</h3>
                <span id="tooltip-ip" class="text-[10px] font-mono text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded"></span>
            </div>
            <div id="chart-canvas"></div>
        </div>

        <script>
            // === 1. SETUP APEXCHART ===
            var chartOptions = {
                series: [{ name: "Ping", data: [] }],
                chart: {
                    type: 'area',
                    height: 80,
                    sparkline: { enabled: true },
                    animations: { enabled: false }
                },
                stroke: { curve: 'smooth', width: 2, colors: ['#3b82f6'] },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] } },
                tooltip: { fixed: { enabled: false }, x: { show: false }, marker: { show: false } }
            };
            var chart = new ApexCharts(document.querySelector("#chart-canvas"), chartOptions);
            chart.render();

            // === 1.5 ZOOM & PAN FUNCTIONALITY ===
            let currentZoom = 1;
            let panX = 0, panY = 0;
            let isDragging = false;
            let startX, startY;
            
            const viewport = document.getElementById('tree-viewport');
            const container = document.getElementById('tree-container');
            const zoomLevelEl = document.getElementById('zoom-level');
            
            function updateTransform() {
                viewport.style.transform = `translate(${panX}px, ${panY}px) scale(${currentZoom})`;
                zoomLevelEl.textContent = Math.round(currentZoom * 100) + '%';
                setTimeout(drawTreeLines, 50);
            }
            
            window.zoomIn = function() {
                currentZoom = Math.min(currentZoom + 0.1, 2);
                updateTransform();
            };
            
            window.zoomOut = function() {
                currentZoom = Math.max(currentZoom - 0.1, 0.3);
                updateTransform();
            };
            
            window.resetZoom = function() {
                currentZoom = 1;
                panX = 0;
                panY = 0;
                updateTransform();
            };
            
            // Mouse wheel zoom
            container.addEventListener('wheel', function(e) {
                e.preventDefault();
                const delta = e.deltaY > 0 ? -0.05 : 0.05;
                currentZoom = Math.max(0.3, Math.min(2, currentZoom + delta));
                updateTransform();
            }, { passive: false });
            
            // Drag to pan
            container.addEventListener('mousedown', function(e) {
                if (e.target.closest('a, button, form')) return;
                isDragging = true;
                startX = e.clientX - panX;
                startY = e.clientY - panY;
                container.style.cursor = 'grabbing';
            });
            
            document.addEventListener('mousemove', function(e) {
                if (!isDragging) return;
                panX = e.clientX - startX;
                panY = e.clientY - startY;
                updateTransform();
            });
            
            document.addEventListener('mouseup', function() {
                isDragging = false;
                container.style.cursor = 'grab';
            });

            // === 2. DRAW CONNECTING LINES ===
            function drawTreeLines() {
                const svg = document.getElementById('tree-lines-svg');
                const viewport = document.getElementById('tree-viewport');
                const wrapper = document.getElementById('tree-wrapper');
                if (!svg || !viewport || !wrapper) return;
                
                // Clear existing lines
                svg.innerHTML = '';
                
                // Set SVG size to match viewport content
                const wrapperRect = wrapper.getBoundingClientRect();
                svg.style.width = (wrapperRect.width / currentZoom) + 'px';
                svg.style.height = (wrapperRect.height / currentZoom + 200) + 'px';
                svg.setAttribute('width', wrapperRect.width / currentZoom);
                svg.setAttribute('height', wrapperRect.height / currentZoom + 200);
                
                // Helper: Get element position relative to viewport using offset
                function getElementPosition(el) {
                    let x = 0, y = 0;
                    let current = el;
                    while (current && current !== viewport) {
                        x += current.offsetLeft;
                        y += current.offsetTop;
                        current = current.offsetParent;
                    }
                    return { x, y, width: el.offsetWidth, height: el.offsetHeight };
                }
                
                // Find all parent-child relationships
                const allNodes = document.querySelectorAll('.tree-node');
                
                allNodes.forEach(node => {
                    const parentCard = node.querySelector(':scope > .tree-node-card');
                    const childrenContainer = node.querySelector(':scope > .tree-children');
                    
                    if (!parentCard || !childrenContainer) return;
                    
                    const childNodes = childrenContainer.querySelectorAll(':scope > .tree-node');
                    if (childNodes.length === 0) return;
                    
                    const parentEl = parentCard.querySelector('.monitor-card');
                    if (!parentEl) return;
                    
                    // Calculate parent position
                    const parentPos = getElementPosition(parentEl);
                    const parentBottom = parentPos.y + parentPos.height;
                    const parentCenterX = parentPos.x + parentPos.width / 2;
                    
                    // Calculate child positions
                    const childPositions = [];
                    childNodes.forEach(childNode => {
                        const childCard = childNode.querySelector('.monitor-card');
                        if (childCard) {
                            const childPos = getElementPosition(childCard);
                            const childTop = childPos.y;
                            const childCenterX = childPos.x + childPos.width / 2;
                            const status = childCard.dataset.status || 'pending';
                            childPositions.push({ x: childCenterX, y: childTop, status });
                        }
                    });
                    
                    if (childPositions.length === 0) return;
                    
                    // Draw lines
                    if (childPositions.length === 1) {
                        // Single child: vertical line with horizontal jog if needed
                        const child = childPositions[0];
                        const midY = parentBottom + (child.y - parentBottom) / 2;
                        
                        const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                        // Elbow: down, across, down
                        path.setAttribute('d', `M ${parentCenterX} ${parentBottom} L ${parentCenterX} ${midY} L ${child.x} ${midY} L ${child.x} ${child.y}`);
                        path.setAttribute('class', `tree-line status-${child.status}`);
                        svg.appendChild(path);
                    } else {
                        // Multiple children: elbow lines
                        const midY = parentBottom + (childPositions[0].y - parentBottom) / 2;
                        
                        // Vertical line from parent down to mid
                        const vertLine = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                        vertLine.setAttribute('d', `M ${parentCenterX} ${parentBottom} L ${parentCenterX} ${midY}`);
                        vertLine.setAttribute('class', 'tree-line');
                        svg.appendChild(vertLine);
                        
                        // Horizontal line spanning all children
                        const minX = Math.min(...childPositions.map(c => c.x));
                        const maxX = Math.max(...childPositions.map(c => c.x));
                        const horizLine = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                        horizLine.setAttribute('d', `M ${minX} ${midY} L ${maxX} ${midY}`);
                        horizLine.setAttribute('class', 'tree-line');
                        svg.appendChild(horizLine);
                        
                        // Vertical lines down to each child
                        childPositions.forEach(child => {
                            const childVertLine = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                            childVertLine.setAttribute('d', `M ${child.x} ${midY} L ${child.x} ${child.y}`);
                            childVertLine.setAttribute('class', `tree-line status-${child.status}`);
                            svg.appendChild(childVertLine);
                        });
                    }
                });
            }
            
            // === 3. ANIMATE VALUE ===
            function animateValue(obj, start, end, duration) {
                if (start === end) return;
                let startTimestamp = null;
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    const currentVal = Math.floor(progress * (end - start) + start);
                    obj.innerHTML = currentVal;
                    if (currentVal >= 100) obj.classList.add('text-red-600');
                    else if (currentVal >= 50) obj.classList.add('text-orange-500');
                    else obj.classList.remove('text-red-600', 'text-orange-500');
                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    } else {
                        obj.innerHTML = end;
                    }
                };
                window.requestAnimationFrame(step);
            }

            // === 4. SMART DOM UPDATE ===
            function refreshData() {
                fetch("{{ route('monitor.data') }}")
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newCards = doc.querySelectorAll('.monitor-card');

                        newCards.forEach(newCard => {
                            const id = newCard.getAttribute('data-id');
                            const currentCard = document.getElementById('card-' + id);

                            if (currentCard) {
                                // Update class and attributes
                                currentCard.className = newCard.className;
                                currentCard.setAttribute('data-history', newCard.getAttribute('data-history') || '[]');
                                currentCard.setAttribute('data-status', newCard.getAttribute('data-status') || 'pending');

                                // Animate latency
                                const latencyEl = document.getElementById('latency-val-' + id);
                                const newLatencyEl = newCard.querySelector('#latency-val-' + id);
                                if (latencyEl && newLatencyEl) {
                                    const oldVal = parseInt(latencyEl.innerText) || 0;
                                    const newVal = parseInt(newLatencyEl.innerText) || 0;
                                    animateValue(latencyEl, oldVal, newVal, 80);
                                }

                                // Update status elements
                                const dotEl = document.getElementById('dot-' + id);
                                const badgeEl = document.getElementById('badge-' + id);
                                const newDotEl = newCard.querySelector('#dot-' + id);
                                const newBadgeEl = newCard.querySelector('#badge-' + id);
                                if (dotEl && newDotEl) dotEl.className = newDotEl.className;
                                if (badgeEl && newBadgeEl) {
                                    badgeEl.className = newBadgeEl.className;
                                    badgeEl.innerText = newBadgeEl.innerText;
                                }
                            }
                        });
                        
                        // Redraw lines after DOM update
                        setTimeout(drawTreeLines, 50);
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Run refresh every 2 seconds
            setInterval(refreshData, 2000);

            // === 5. TOOLTIP LOGIC ===
            const tooltip = document.getElementById('chart-tooltip');
            
            document.body.addEventListener('mouseover', function(e) {
                const card = e.target.closest('.monitor-card');
                if (card) {
                    const historyAttr = card.getAttribute('data-history');
                    const ip = card.getAttribute('data-ip');
                    if (historyAttr) {
                        try {
                            const historyData = JSON.parse(historyAttr);
                            chart.updateSeries([{ data: historyData }]);
                            document.getElementById('tooltip-ip').innerText = ip;
                            tooltip.style.display = 'block';
                            tooltip.style.opacity = '1';
                        } catch (err) { console.error(err); }
                    }
                }
            });

            document.body.addEventListener('mousemove', function(e) {
                tooltip.style.left = (e.pageX + 15) + 'px';
                tooltip.style.top = (e.pageY + 15) + 'px';
            });

            document.body.addEventListener('mouseout', function(e) {
                const card = e.target.closest('.monitor-card');
                if (card) {
                    tooltip.style.display = 'none';
                    tooltip.style.opacity = '0';
                }
            });

            // === 6. INITIAL DRAW & RESIZE HANDLER ===
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(drawTreeLines, 100);
            });
            
            window.addEventListener('resize', () => {
                setTimeout(drawTreeLines, 100);
            });
        </script>
    </body>
</html>