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

        // --- TOOLTIP HOVER ---
        const tooltip = document.getElementById('chart-tooltip');
        const ttStation = document.getElementById('tt-station');
        const ttName = document.getElementById('tt-name');
        const ttIp = document.getElementById('tt-ip');
        const ttLatency = document.getElementById('tt-latency');

        document.body.addEventListener('mouseover', function(e) {
            const card = e.target.closest('.monitor-card');
            if (!card) return;

            const historyAttr = card.getAttribute('data-history') || '[]';
            const ip = card.getAttribute('data-ip') || '-';
            const name = card.getAttribute('data-name') || '-';
            const station = card.getAttribute('data-station') || '-';
            const latency = card.getAttribute('data-latency') || '-';

            ttStation.textContent = station;
            ttName.textContent = name;
            ttIp.textContent = ip;
            ttLatency.textContent = latency + ' ms';

            try {
                const historyData = JSON.parse(historyAttr);
                chart.updateSeries([{ data: historyData }]);
            } catch (err) {
                chart.updateSeries([{ data: [] }]);
            }

            tooltip.classList.remove('hidden');
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
