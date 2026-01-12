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
        // =========================
        // 1) WIB CLOCK
        // =========================
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


        // =========================
        // 2) APEXCHART (render sekali)
        // =========================
        const chartOptions = {
            series: [{ name: "Ping", data: [] }],
            chart: {
                type: 'area',
                height: 80,
                sparkline: { enabled: true },
                animations: { enabled: false }
            },
            stroke: { curve: 'smooth', width: 2 },
            fill: { opacity: 0.25 },
            colors: ['#2563eb'],
            tooltip: { enabled: false }
        };

        const chartEl = document.querySelector("#chart-canvas");
        const chart = new ApexCharts(chartEl, chartOptions);
        chart.render();


        // =========================
        // 3) TOOLTIP ELEMENTS
        // =========================
        const tooltip = document.getElementById('chart-tooltip');
        const ttStation = document.getElementById('tt-station');
        const ttName = document.getElementById('tt-name');
        const ttIp = document.getElementById('tt-ip');
        const ttLatency = document.getElementById('tt-latency');


        // =========================
        // 4) HOVER LOGIC
        // =========================
        document.body.addEventListener('mouseover', (e) => {
            const card = e.target.closest('.monitor-card');
            if (!card) return;

            ttStation.textContent = card.dataset.station || '-';
            ttName.textContent = card.dataset.name || '-';
            ttIp.textContent = card.dataset.ip || '-';
            ttLatency.textContent = (card.dataset.latency || 0) + ' ms';

            try {
                const history = JSON.parse(card.dataset.history || '[]');
                chart.updateSeries([{ data: history }]);
            } catch {
                chart.updateSeries([{ data: [] }]);
            }

            tooltip.classList.remove('hidden');
        });

        document.body.addEventListener('mousemove', (e) => {
            if (tooltip.classList.contains('hidden')) return;
            tooltip.style.left = (e.clientX + 12) + 'px';
            tooltip.style.top = (e.clientY + 12) + 'px';
        });

        document.body.addEventListener('mouseout', (e) => {
            if (e.target.closest('.monitor-card')) {
                tooltip.classList.add('hidden');
            }
        });


        // =========================
        // 5) SMART REFRESH (1x aja)
        // =========================
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

                if (progress < 1) requestAnimationFrame(step);
                else obj.innerHTML = end;
            };
            requestAnimationFrame(step);
        }

        function refreshCardsSmart() {
            fetch("{{ route('monitor.data') }}")
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    const newCards = doc.querySelectorAll('.monitor-card');
                    newCards.forEach(newCard => {
                        const id = newCard.getAttribute('data-id');
                        const currentCard = document.getElementById('card-' + id);

                        if (currentCard) {
                            // update seluruh isi card agar data-* ikut update (buat hover)
                            currentCard.outerHTML = newCard.outerHTML;
                        } else {
                            document.getElementById('monitor-card-grid').appendChild(newCard);
                        }
                    });

                    // hapus yang tidak ada di response
                    document.querySelectorAll('.monitor-card').forEach(card => {
                        const exists = doc.getElementById(card.id);
                        if (!exists) card.remove();
                    });
                })
                .catch(console.error);
        }

        setInterval(refreshCardsSmart, 1000);
    </script>
</body>
</html>
