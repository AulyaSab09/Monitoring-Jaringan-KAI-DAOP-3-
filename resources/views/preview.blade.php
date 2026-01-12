<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Network Monitoring Preview</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<body class="bg-gray-50 text-[#1b1b18] min-h-screen font-sans py-6">
    <div class="max-w-screen-2xl mx-auto px-6">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
                {{ session('success') }}
            </div>
        @endif

        <header class="mb-8">
            <div class="grid grid-cols-2 gap-x-6 gap-y-4">
                {{-- BARIS 1 - KIRI --}}
                <div class="flex items-center gap-4">
                    <img src="{{ asset('assets/images/kai_logo.png') }}" alt="KAI" class="h-20 w-auto" />
                    <h1 class="text-3xl font-semibold text-gray-900">
                        Sistem Monitoring Jaringan
                    </h1>
                </div>

                {{-- BARIS 1 - KANAN --}}
                <div class="text-right leading-tight">
                    <div id="dateText" class="text-gray-700 font-semibold text-xl"></div>
                    <div id="timeText" class="text-gray-900 text-4xl font-bold"></div>
                </div>

                {{-- BARIS 2 - KIRI --}}
                <div class="flex flex-wrap items-center gap-4">
                    <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-[#E1E1E1] text-[#101828]">
                        Total Devices: {{ $total ?? 0 }}
                    </span>

                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-[#D4FFE1] text-[#0D542B]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        UP: {{ $up ?? 0 }}
                    </span>

                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-[#FFECD5] text-[#7E2A0C]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 9v4m0 4h.01M5.07 19h13.86L12 4.5 5.07 19z" />
                        </svg>
                        WARNING: {{ $warning ?? 0 }}
                    </span>

                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-[#FFDCDC] text-[#82181A]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        DOWN: {{ $down ?? 0 }}
                    </span>
                </div>

                {{-- BARIS 2 - KANAN --}}
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('monitor.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium shadow-sm transition-colors">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Device
                    </a>
                </div>
            </div>
        </header>

        {{-- GRID CARD (CUMA SEKALI) --}}
        <div id="monitor-card-grid"
             class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4">
            @include('components.monitor-cards', ['monitors' => $monitors])
        </div>
    </div>

    {{-- TOOLTIP HOVER (CUMA SEKALI) --}}
    @include('components.monitor-hover-tooltip')

    <script>
        // --- AUTO REFRESH CARD ---
        function refreshTable() {
            fetch("{{ route('monitor.data') }}")
                .then(r => r.text())
                .then(html => {
                    const grid = document.getElementById('monitor-card-grid');
                    if (grid) grid.innerHTML = html;
                })
                .catch(console.error);
        }
        setInterval(refreshTable, 2000);

        // --- SETUP APEXCHART (render sekali) ---
        const options = {
            series: [{ name: "Ping", data: [] }],
            chart: {
                type: 'area',
                height: 90,
                sparkline: { enabled: true },
                animations: { enabled: false }
            },
            stroke: { curve: 'smooth', width: 2 },
            fill: { opacity: 0.25 },
            colors: ['#2563eb'],
            tooltip: { enabled: false }
        };

        const chart = new ApexCharts(document.querySelector("#chart-canvas"), options);
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
            if (tooltip.classList.contains('hidden')) return;
            tooltip.style.left = (e.clientX + 16) + 'px';
            tooltip.style.top = (e.clientY + 16) + 'px';
        });

        document.body.addEventListener('mouseout', function(e) {
            const card = e.target.closest('.monitor-card');
            if (!card) return;
            tooltip.classList.add('hidden');
        });

        // --- WIB CLOCK ---
        function updateWIB() {
            const now = new Date();

            const dateFormatter = new Intl.DateTimeFormat('id-ID', {
                timeZone: 'Asia/Jakarta',
                weekday: 'long',
                day: '2-digit',
                month: 'long',
                year: 'numeric',
            });

            const timeFormatter = new Intl.DateTimeFormat('id-ID', {
                timeZone: 'Asia/Jakarta',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
            });

            document.getElementById('dateText').textContent = dateFormatter.format(now);
            document.getElementById('timeText').textContent = timeFormatter.format(now);
        }
        updateWIB();
        setInterval(updateWIB, 1000);
    </script>
</body>
</html>
