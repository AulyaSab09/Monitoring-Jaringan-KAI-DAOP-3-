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
        </style>
    </head>
    <body class="bg-[#f8f9fa] text-[#1b1b18] min-h-screen p-6 md:p-8 font-sans selection:bg-blue-100">
        <div class="max-w-7xl mx-auto">
            
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 animate-bounce">
                {{ session('success') }}
            </div>
            @endif

            <header class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold mb-1 text-gray-900 tracking-tight">Network Overview</h1>
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

            <div id="monitor-card-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @include('components.monitor-cards', ['monitors' => $monitors])
            </div>
            
            <div class="mt-8 flex items-center justify-between text-xs text-gray-400 font-medium uppercase tracking-widest">
                <span>Auto-refreshing every 1s</span>
                <span>NativePHP Engine</span>
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
            // --- 1. SETUP APEXCHART (Mini Graph) ---
            var options = {
                series: [{ name: "Ping", data: [] }],
                chart: {
                    type: 'area',
                    height: 80,
                    sparkline: { enabled: true }, 
                    animations: { enabled: false } // Disable animasi chart bawaan agar cepat
                },
                stroke: { curve: 'smooth', width: 2, colors: ['#3b82f6'] },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] } },
                tooltip: { fixed: { enabled: false }, x: { show: false }, marker: { show: false } }
            };
            var chart = new ApexCharts(document.querySelector("#chart-canvas"), options);
            chart.render();

            // --- 2. FUNGSI ANIMASI ANGKA (The "Game Feel") ---
            function animateValue(obj, start, end, duration) {
                if (start === end) return;
                
                let startTimestamp = null;
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    
                    // Hitung angka saat ini
                    const currentVal = Math.floor(progress * (end - start) + start);
                    obj.innerHTML = currentVal;
                    
                    // Ubah warna text secara dinamis saat angka naik
                    if (currentVal >= 100) obj.classList.add('text-red-600');
                    else if (currentVal >= 50) obj.classList.add('text-orange-500');
                    else obj.classList.remove('text-red-600', 'text-orange-500');

                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    } else {
                        obj.innerHTML = end; // Pastikan angka akhir tepat
                    }
                };
                window.requestAnimationFrame(step);
            }

            // --- 3. SMART DOM UPDATE (Agar tidak blink) ---
            function refreshTable() {
                fetch("{{ route('monitor.data') }}")
                    .then(response => response.text())
                    .then(html => {
                        // A. Parse HTML baru di memori (virtual DOM)
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newCards = doc.querySelectorAll('.monitor-card');

                        // B. Loop setiap card baru
                        newCards.forEach(newCard => {
                            const id = newCard.getAttribute('data-id');
                            const currentCard = document.getElementById('card-' + id);

                            if (currentCard) {
                                // 1. UPDATE CLASS PARENT (Untuk ganti warna border/bg jika status berubah)
                                currentCard.className = newCard.className;
                                currentCard.setAttribute('data-history', newCard.getAttribute('data-history'));

                                // 2. ANIMASI ANGKA LATENCY
                                const latencyEl = document.getElementById('latency-val-' + id);
                                const newLatencyText = newCard.querySelector('#latency-val-' + id).innerText;
                                const oldVal = parseInt(latencyEl.innerText);
                                const newVal = parseInt(newLatencyText);
                                
                                // Jalankan animasi angka (duration 500ms biar snappy)
                                animateValue(latencyEl, oldVal, newVal, 500);

                                // 3. UPDATE STATUS DOT & TEXT
                                document.getElementById('dot-' + id).className = newCard.querySelector('#dot-' + id).className;
                                document.getElementById('badge-' + id).className = newCard.querySelector('#badge-' + id).className;
                                document.getElementById('badge-' + id).innerText = newCard.querySelector('#badge-' + id).innerText;

                            } else {
                                // Jika card belum ada (device baru), tambahkan ke grid
                                document.getElementById('monitor-card-grid').appendChild(newCard);
                            }
                        });
                        
                        // Hapus card yang sudah tidak ada di data baru (misal dihapus)
                        const currentCards = document.querySelectorAll('.monitor-card');
                        currentCards.forEach(card => {
                            const existsInNew = doc.getElementById(card.id);
                            if (!existsInNew) card.remove();
                        });

                    })
                    .catch(error => console.error('Error fetching data:', error));
            }

            // Jalankan refresh setiap 1 detik (1000ms) agar terasa realtime
            setInterval(refreshTable, 1000);


            // --- 4. LOGIKA HOVER TOOLTIP ---
            const tooltip = document.getElementById('chart-tooltip');
            
            document.body.addEventListener('mouseover', function(e) {
                const card = e.target.closest('.monitor-card');
                if (card) {
                    const historyAttr = card.getAttribute('data-history');
                    const ip = card.getAttribute('data-ip');
                    
                    if (historyAttr) {
                        try {
                            const historyData = JSON.parse(historyAttr);
                            // Update Chart
                            chart.updateSeries([{ data: historyData }]);
                            document.getElementById('tooltip-ip').innerText = ip;
                            
                            // Show Tooltip
                            tooltip.style.display = 'block';
                            tooltip.style.opacity = '1';
                        } catch (err) { console.error(err); }
                    }
                }
            });

            document.body.addEventListener('mousemove', function(e) {
                // Tooltip mengikuti mouse dengan offset sedikit
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
        </script>
    </body>
</html>