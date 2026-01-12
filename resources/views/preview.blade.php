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
            .tree-container {
                position: relative;
                overflow: hidden;
                padding: 2rem 1rem;
                height: 85vh;
                background-color: #f8fafc;
                border: 2px solid #e2e8f0;
                border-radius: 1rem;
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
                padding: 100px;
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
                stroke-width: 3;
                fill: none;
                stroke-linecap: round;
                transition: stroke 0.4s ease, stroke-width 0.3s ease;
            }
            
            /* Warna Garis Sesuai Status - UPDATE DISINI PENTING */
            .tree-line.status-connected { stroke: #10b981; } /* Emerald-500 */
            .tree-line.status-disconnected { stroke: #ef4444; stroke-dasharray: 6,4; } /* Red-500 Putus-putus */
            .tree-line.status-unstable { stroke: #f97316; } /* Orange-500 */
            .tree-line.status-pending { stroke: #cbd5e1; } /* Slate-300 */
            
            /* Tree Structure */
            .tree-wrapper {
                display: flex;
                flex-direction: row;
                align-items: flex-start;
                justify-content: flex-start;
                gap: 3rem;
                flex-wrap: nowrap;
                position: relative;
                z-index: 2;
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
                margin-bottom: 4rem;
            }
            
            .tree-children {
                display: flex;
                flex-direction: row;
                align-items: flex-start;
                gap: 2rem;
                margin-top: 0.5rem;
            }
            
            /* Hover Add Button */
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
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                z-index: 20;
                cursor: pointer;
            }
            
            .hover-add-btn:hover {
                transform: translateX(-50%) scale(1.15);
            }
            
            .tree-node-card:hover .hover-add-btn {
                transform: translateX(-50%) scale(1);
            }
            
            /* Status Warning Animations */
            @keyframes pulse-warning-down {
                0%, 100% { border-color: #fca5a5; }
                50% { border-color: #ef4444; box-shadow: 0 0 8px 2px rgba(239, 68, 68, 0.3); }
            }
            .child-down-warning {
                animation: pulse-warning-down 1.5s ease-in-out infinite;
                border-color: #ef4444 !important;
            }
            
            @keyframes pulse-warning-unstable {
                0%, 100% { border-color: #fdba74; }
                50% { border-color: #f97316; box-shadow: 0 0 6px 1px rgba(249, 115, 22, 0.2); }
            }
            .child-unstable-warning {
                animation: pulse-warning-unstable 2s ease-in-out infinite;
                border-color: #f97316 !important;
            }
        </style>
    </head>
    <body class="bg-[#f8f9fa] text-[#1b1b18] min-h-screen p-4 font-sans selection:bg-blue-100 flex flex-col">
        
        <header class="mb-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 px-2">
            <div>
                <h1 class="text-2xl font-bold mb-1 text-gray-900 tracking-tight">Network Topology</h1>
                <div class="flex items-center gap-2 text-xs text-gray-500 font-medium">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Live Monitoring (Sync: 1.5s)
                </div>
            </div>
            <div class="flex gap-3 items-center">
                <a href="{{ route('monitor.create') }}" class="flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 text-sm font-semibold shadow hover:shadow-lg transition-all duration-300">
                    <span>+ Add Device</span>
                </a>
            </div>
        </header>

        <div id="tree-container" class="tree-container bg-white shadow-sm">
            
            <div class="absolute top-4 right-4 z-30 flex flex-col gap-2 bg-white/90 backdrop-blur-sm rounded-xl p-2 shadow-lg border border-gray-200">
                <button onclick="zoomIn()" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-blue-500 hover:text-white rounded-lg transition-colors font-bold text-gray-600" title="Zoom In">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                </button>
                <span id="zoom-level" class="text-[10px] font-bold text-gray-500 text-center">100%</span>
                <button onclick="zoomOut()" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-blue-500 hover:text-white rounded-lg transition-colors font-bold text-gray-600" title="Zoom Out">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"></path></svg>
                </button>
                <button onclick="resetZoom()" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-[10px] font-bold text-gray-500" title="Reset Zoom">
                    FIT
                </button>
            </div>
            
            <div id="tree-viewport" class="tree-viewport">
                <svg id="tree-lines-svg" class="tree-lines-svg"></svg>
                
                <div id="tree-wrapper" class="tree-wrapper">
                    @include('components.monitor-cards', ['monitors' => $monitors])
                </div>
            </div>
        </div>
        
        <div id="chart-tooltip">
            <div class="flex justify-between items-center mb-2 border-b border-gray-100 pb-2">
                <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Latency History</h3>
                <span id="tooltip-ip" class="text-[10px] font-mono text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded"></span>
            </div>
            <div id="chart-canvas"></div>
        </div>

        <script>
            // === 1. FITUR BUKA TUTUP (COLLAPSE) ===
            function toggleBranch(id) {
                const childContainer = document.getElementById('children-' + id);
                const arrow = document.getElementById('arrow-' + id);
                
                if (childContainer) {
                    if (childContainer.style.display === 'none') {
                        childContainer.style.display = 'flex';
                        arrow.style.transform = 'rotate(0deg)';
                    } else {
                        childContainer.style.display = 'none';
                        arrow.style.transform = 'rotate(-90deg)';
                    }
                    setTimeout(drawTreeLines, 50);
                }
            }

            // === 2. APEXCHART SETUP ===
            var chart = new ApexCharts(document.querySelector("#chart-canvas"), {
                series: [{ data: [] }],
                chart: { type: 'area', height: 80, sparkline: { enabled: true } },
                stroke: { curve: 'monotoneCubic', width: 2, colors: ['#3b82f6'] },
                fill: { type: 'gradient', gradient: { opacityFrom: 0.5, opacityTo: 0.1 } },
                tooltip: { fixed: { enabled: false }, x: { show: false }, marker: { show: false } }
            });
            chart.render();

            // === 3. ZOOM & PAN ===
            let currentZoom = 1, panX = 0, panY = 0, isDragging = false, startX, startY;
            const viewport = document.getElementById('tree-viewport');
            const container = document.getElementById('tree-container');
            const zoomLabel = document.getElementById('zoom-level');

            function updateTransform() {
                viewport.style.transform = `translate(${panX}px, ${panY}px) scale(${currentZoom})`;
                if(zoomLabel) zoomLabel.innerText = Math.round(currentZoom * 100) + '%';
                if (!isDragging) requestAnimationFrame(drawTreeLines);
            }

            container.addEventListener('mousedown', e => {
                if (e.target.closest('button, a')) return;
                isDragging = true; startX = e.clientX - panX; startY = e.clientY - panY;
                container.style.cursor = 'grabbing';
            });

            window.addEventListener('mousemove', e => {
                if (!isDragging) return;
                panX = e.clientX - startX; panY = e.clientY - startY;
                viewport.style.transform = `translate(${panX}px, ${panY}px) scale(${currentZoom})`;
            });

            window.addEventListener('mouseup', () => { isDragging = false; container.style.cursor = 'grab'; drawTreeLines(); });
            container.addEventListener('wheel', e => { e.preventDefault(); currentZoom += e.deltaY > 0 ? -0.1 : 0.1; updateTransform(); });

            window.zoomIn = () => { currentZoom += 0.2; updateTransform(); };
            window.zoomOut = () => { currentZoom -= 0.2; updateTransform(); };
            window.resetZoom = () => { currentZoom = 1; panX = 0; panY = 0; updateTransform(); };

            // === 4. GAMBAR GARIS OTOMATIS WARNA ===
            function drawTreeLines() {
                const svg = document.getElementById('tree-lines-svg');
                const wrapper = document.getElementById('tree-wrapper');
                if(!svg || !wrapper) return;
                svg.innerHTML = ''; 

                const getPos = (el) => {
                    let x = 0, y = 0, w = el.offsetWidth, h = el.offsetHeight;
                    while(el && el !== viewport) { x += el.offsetLeft; y += el.offsetTop; el = el.offsetParent; }
                    return { x, y, w, h, cx: x + w/2, cy: y + h/2 };
                };

                document.querySelectorAll('.tree-node').forEach(node => {
                    const parentCard = node.querySelector(':scope > .tree-node-card .monitor-card');
                    const childrenContainer = node.querySelector(':scope > .tree-children');
                    
                    if (parentCard && childrenContainer && childrenContainer.style.display !== 'none') {
                        const pPos = getPos(parentCard);
                        const children = childrenContainer.querySelectorAll(':scope > .tree-node > .tree-node-card .monitor-card');
                        
                        if (children.length > 0) {
                            let minX = Infinity, maxX = -Infinity;
                            const midY = pPos.y + pPos.h + 20;
                            
                            children.forEach(child => {
                                const cPos = getPos(child);
                                minX = Math.min(minX, cPos.cx);
                                maxX = Math.max(maxX, cPos.cx);
                                
                                // AMBIL STATUS DARI CARD (CONNECTED/UNSTABLE/DISCONNECTED)
                                const status = child.dataset.status || 'pending';
                                
                                // Garis Vertikal Anak (Mengikuti Status Anak)
                                createPath(`M ${cPos.cx} ${cPos.y} L ${cPos.cx} ${midY}`, status);
                            });

                            // Garis Induk Turun (Warna Netral/Pending - atau bisa diubah ke parent status)
                            createPath(`M ${pPos.cx} ${pPos.y + pPos.h} L ${pPos.cx} ${midY}`, 'pending');

                            // Garis Horizontal (Warna Netral/Pending)
                            if (minX !== Infinity) createPath(`M ${minX} ${midY} L ${maxX} ${midY}`, 'pending');
                        }
                    }
                });

                function createPath(d, status) {
                    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                    path.setAttribute('d', d);
                    // Ini yang menghubungkan CSS .status-connected dengan elemen garis
                    path.setAttribute('class', `tree-line status-${status}`);
                    svg.appendChild(path);
                }
            }

            // === 5. REALTIME DATA UPDATE (OPTIMAL VERSION) ===
            // Inisialisasi parser di LUAR loop untuk hemat memori
            const parser = new DOMParser();

            function refreshData() {
                if (isDragging) return;
                
                // Tambahkan Timestamp (?t=) agar browser TIDAK melakukan cache (Fix Manual Refresh)
                fetch("{{ route('monitor.data') }}?t=" + new Date().getTime())
                    .then(r => r.text())
                    .then(html => {
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        doc.querySelectorAll('.monitor-card').forEach(newCard => {
                            const id = newCard.dataset.id;
                            const oldCard = document.getElementById('card-' + id);
                            
                            if (oldCard) {
                                // 1. Update Tampilan Card (Warna BG & Border)
                                oldCard.className = newCard.className;
                                
                                // 2. Update Data History Grafik
                                oldCard.dataset.history = newCard.dataset.history;
                                
                                // 3. [PENTING] Update Status untuk Garis
                                // Pastikan ini terupdate agar drawTreeLines membaca status baru
                                oldCard.dataset.status = newCard.dataset.status;

                                // 4. Update Latency Angka
                                const oldLat = oldCard.querySelector('[id^="latency-val-"]');
                                const newLat = newCard.querySelector('[id^="latency-val-"]');
                                if (oldLat && newLat) oldLat.innerHTML = newLat.innerHTML;

                                // 5. Update Badge & Dot
                                const oldBadge = oldCard.querySelector('[id^="badge-"]');
                                const newBadge = newCard.querySelector('[id^="badge-"]');
                                if(oldBadge && newBadge) {
                                    oldBadge.innerHTML = newBadge.innerHTML;
                                    oldBadge.className = newBadge.className;
                                }
                                
                                const oldDot = oldCard.querySelector('[id^="dot-"]');
                                const newDot = newCard.querySelector('[id^="dot-"]');
                                if(oldDot && newDot) oldDot.className = newDot.className;
                                
                                // 6. Update Indikator Cabang (Warning Merah Kecil di Bawah)
                                const oldWarn = document.getElementById('badge-hidden-' + id);
                                const newWarn = doc.getElementById('badge-hidden-' + id); // Ambil dari doc baru
                                if(oldWarn && newWarn) {
                                    oldWarn.className = newWarn.className; // Update class hidden/block
                                }
                            }
                        });

                        // Gambar ulang garis SETELAH semua data status diperbarui
                        if(!isDragging) requestAnimationFrame(drawTreeLines);
                    })
                    .catch(err => console.error("Gagal refresh:", err));
            }

            // Set Interval 1500ms (1.5 detik)
            // 10ms terlalu cepat dan akan mematikan browser jika data banyak
            setInterval(refreshData, 15);
            
            // Init awal
            window.onload = () => { setTimeout(drawTreeLines, 100); };
            window.onresize = () => setTimeout(drawTreeLines, 100);

            // Tooltip Logic
            document.body.addEventListener('mouseover', e => {
                const card = e.target.closest('.monitor-card');
                if (card) {
                    const tooltip = document.getElementById('chart-tooltip');
                    document.getElementById('tooltip-ip').innerText = card.dataset.ip;
                    try { chart.updateSeries([{ data: JSON.parse(card.dataset.history) }]); } catch(e){}
                    tooltip.style.display = 'block';
                }
            });
            document.body.addEventListener('mousemove', e => {
                const tooltip = document.getElementById('chart-tooltip');
                tooltip.style.left = (e.pageX + 15) + 'px';
                tooltip.style.top = (e.pageY + 15) + 'px';
            });
            document.body.addEventListener('mouseout', e => {
                if(e.target.closest('.monitor-card')) document.getElementById('chart-tooltip').style.display = 'none';
            });
        </script>
    </body>
</html>