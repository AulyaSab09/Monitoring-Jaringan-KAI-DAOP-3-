<x-app-layout>
    {{-- CSS Khusus untuk Halaman History --}}
    <style>
        nav {
            display: none !important;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            filter: invert(0.5);
        }

        /* Warna Identitas KAI */
        .bg-kai-navy {
            background-color: #001D4B;
        }

        .text-kai-navy {
            color: #001D4B;
        }

        .border-kai-navy {
            border-color: #001D4B;
        }
    </style>

    {{-- Pastikan FontAwesome Dimuat --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <div class="bg-gray-50 min-h-screen font-sans py-6 text-slate-700">
        <div class="max-w-[98%] mx-auto px-4">

            {{-- 1. HEADER SECTION --}}
            <header class="mb-4 flex flex-col xl:flex-row justify-between items-center gap-6 xl:gap-0">
                <div class="flex flex-col md:flex-row items-center gap-4 md:gap-6 text-center md:text-left">
                    {{-- Logo KAI Sesuai Preview --}}
                    <img src="{{ asset('assets/images/kai_logo.png') }}" alt="KAI"
                        class="h-16 md:h-24 w-auto object-contain" />
                    <div>
                        <h1 class="text-2xl md:text-3xl font-black text-gray-900 tracking-tight text-kai-navy">Riwayat Insiden
                            Perangkat Jaringan</h1>
                        <p class="text-sm md:text-base text-gray-500 font-bold uppercase tracking-widest">KAI DAOP 3 Cirebon</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                    {{-- Link diarahkan ke route preview --}}
                    <a href="{{ route('preview') }}"
                        class="w-full sm:w-auto flex justify-center items-center gap-3 px-6 md:px-8 py-3.5 bg-kai-navy text-white rounded-2xl hover:opacity-90 transition-all font-black text-sm shadow-xl shadow-blue-900/20 tracking-widest">
                        <i class="fa-solid fa-gauge-high text-lg"></i>
                        DASHBOARD
                    </a>

                    {{-- Container Dropdown Reset --}}
                    <div class="relative w-full sm:w-auto inline-block text-left" x-data="{ open: false }">
                        {{-- Tombol Utama --}}
                        <div class="flex flex-col sm:flex-row items-center gap-3">
                            <a href="{{ route('history.export') }}"
                                onclick="this.href='{{ route('history.export') }}'+window.location.search"
                                class="w-full sm:w-auto flex justify-center items-center gap-3 px-6 py-3.5 bg-green-700 text-white rounded-2xl hover:bg-green-800 transition-all font-black text-sm shadow-xl shadow-green-900/20 tracking-widest uppercase">
                                <i class="fa-solid fa-file-excel text-lg"></i>
                                Export Excel
                            </a>

                            <button type="button" @click="open = !open"
                                class="w-full sm:w-auto flex justify-center items-center gap-3 px-6 py-3.5 bg-red-600 text-white rounded-2xl hover:bg-red-700 transition-all font-black text-sm shadow-xl shadow-red-900/20 tracking-widest uppercase">
                                <i class="fa-solid fa-trash-can text-lg"></i>
                                Hapus Riwayat
                                <i class="fa-solid fa-chevron-down text-xs ml-2 transition-transform"
                                    :class="open ? 'rotate-180' : ''"></i>
                            </button>
                        </div>

                        {{-- Panel Dropdown Pilihan Waktu --}}
                        <div x-show="open" @click.away="open = false"
                            class="absolute right-0 mt-3 w-full sm:w-64 bg-white border-2 border-gray-100 rounded-2xl shadow-2xl z-50 overflow-hidden"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100">

                            <div class="p-4 bg-gray-50 border-b border-gray-100">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Pilih
                                    Periode Hapus</span>
                            </div>

                            <div class="p-2">
                                <form action="{{ route('history.reset') }}" method="POST"
                                    onsubmit="return confirm('Hapus riwayat 1 minggu terakhir?')">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="period" value="1_week">
                                    <button type="submit"
                                        class="w-full text-left px-4 py-3 text-sm font-bold text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-xl transition-colors">
                                        <i class="fa-solid fa-calendar-week mr-3 opacity-50"></i> 1 Minggu Terakhir
                                    </button>
                                </form>

                                <form action="{{ route('history.reset') }}" method="POST"
                                    onsubmit="return confirm('Hapus riwayat 1 bulan terakhir?')">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="period" value="1_month">
                                    <button type="submit"
                                        class="w-full text-left px-4 py-3 text-sm font-bold text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-xl transition-colors">
                                        <i class="fa-solid fa-calendar-days mr-3 opacity-50"></i> 1 Bulan Terakhir
                                    </button>
                                </form>

                                <form action="{{ route('history.reset') }}" method="POST"
                                    onsubmit="return confirm('Hapus riwayat 1 tahun terakhir?')">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="period" value="1_year">
                                    <button type="submit"
                                        class="w-full text-left px-4 py-3 text-sm font-bold text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-xl transition-colors">
                                        <i class="fa-solid fa-boxes-packing mr-3 opacity-50"></i> 1 Tahun Terakhir
                                    </button>
                                </form>

                                <div class="my-2 border-t border-gray-100"></div>

                                {{-- Form Hapus Semua (Bahaya) --}}
                                <form action="{{ route('history.reset') }}" method="POST"
                                    onsubmit="return confirm('PERINGATAN: Semua riwayat akan dihapus')">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="period" value="all">
                                    <button type="submit"
                                        class="w-full text-left px-4 py-3 text-sm font-black text-red-600 hover:bg-red-600 hover:text-white rounded-xl transition-all">
                                        <i class="fa-solid fa-triangle-exclamation "></i> Hapus Semua Data
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- 2. FILTER SECTION --}}
            <div class="bg-white p-4 md:p-8 rounded-3xl shadow-md border border-gray-100 mb-8">
                <form action="{{ route('history.index') }}" method="GET">
                    <div class="grid grid-cols-12 gap-4 md:gap-6 items-end">
                        <div class="col-span-12 md:col-span-2">
                            <label
                                class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-widest">Kondisi
                                Insiden</label>
                            <select name="status"
                                class="w-full h-14 rounded-2xl border-gray-200 text-sm font-bold focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all cursor-pointer">
                                <option value="">Semua Data</option>
                                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Selesai
                                    (Resolved)</option>
                                <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Sedang
                                    Terjadi (Ongoing)</option>
                            </select>
                        </div>

                        <div class="col-span-12 md:col-span-4 flex gap-4">
                            <div class="flex-1">
                                <label
                                    class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-widest">Dari
                                    Tanggal</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}"
                                    class="w-full h-14 rounded-2xl border-gray-200 text-sm font-bold focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all">
                            </div>
                            <div class="flex-1">
                                <label
                                    class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-widest">Sampai
                                    Tanggal</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}"
                                    class="w-full h-14 rounded-2xl border-gray-200 text-sm font-bold focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all">
                            </div>
                        </div>

                        <div class="col-span-12 md:col-span-6">
                            <label class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-widest">Cari
                                Perangkat / Stasiun / IP</label>
                            <div class="flex gap-3">
                                <div class="relative flex-grow">
                                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                                    </div>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        placeholder="Masukkan nama perangkat..."
                                        class="w-full h-14 rounded-2xl border-gray-200 pl-14 pr-12 text-sm font-bold focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all">
                                    <button type="button"
                                        onclick="window.location.href='{{ route('history.index') }}'"
                                        class="absolute inset-y-0 right-0 pr-5 flex items-center text-gray-400 hover:text-red-500 transition-colors">
                                        <i class="fa-solid fa-xmark text-lg"></i>
                                    </button>
                                </div>
                                <button type="submit"
                                    class="h-14 px-6 md:px-10 bg-orange-500 text-white rounded-2xl shadow-lg shadow-orange-100 flex items-center gap-3 font-black text-sm tracking-widest hover:bg-orange-600 transition-all uppercase">
                                    <i class="fa-solid fa-search text-lg"></i>
                                    <span class="hidden md:inline">CARI</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- 3. TABLE SECTION --}}
            <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-kai-navy text-white">
                            <tr>
                                <th class="px-4 py-4 md:px-8 md:py-8 text-left text-xs md:text-sm font-black uppercase tracking-[0.2em] whitespace-nowrap">
                                    <div class="flex items-center gap-4">
                                        <i class="fa-solid fa-server text-orange-400 text-base md:text-lg"></i>
                                        Perangkat & IP
                                    </div>
                                </th>
                                <th class="px-4 py-4 md:px-8 md:py-8 text-left text-xs md:text-sm font-black uppercase tracking-[0.2em] whitespace-nowrap">
                                    <div class="flex items-center gap-4">
                                        <i class="fa-solid fa-location-dot text-orange-400 text-base md:text-lg"></i>
                                        Lokasi Stasiun
                                    </div>
                                </th>
                                <th
                                    class="px-4 py-4 md:px-8 md:py-8 text-left text-xs md:text-sm font-black uppercase tracking-[0.2em] bg-red-900/20 whitespace-nowrap">
                                    <div class="flex items-center gap-4 text-red-200">
                                        <i class="fa-solid fa-circle-arrow-down text-red-400 text-base md:text-lg"></i>
                                        Waktu DOWN
                                    </div>
                                </th>
                                <th
                                    class="px-4 py-4 md:px-8 md:py-8 text-left text-xs md:text-sm font-black uppercase tracking-[0.2em] bg-emerald-900/20 whitespace-nowrap">
                                    <div class="flex items-center gap-4 text-emerald-200">
                                        <i class="fa-solid fa-circle-arrow-up text-emerald-400 text-base md:text-lg"></i>
                                        Waktu UP
                                    </div>
                                </th>
                                <th
                                    class="px-4 py-4 md:px-8 md:py-8 text-center text-xs md:text-sm font-black uppercase tracking-[0.2em] bg-kai-navy/80 whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-4">
                                        <i class="fa-solid fa-stopwatch text-orange-400 text-base md:text-lg"></i>
                                        Down Time
                                    </div>
                                </th>
                                <th
                                    class="px-4 py-4 md:px-8 md:py-8 text-center text-xs md:text-sm font-black uppercase tracking-[0.2em] bg-kai-navy/80 whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-4">
                                        <i class="fa-solid fa-circle-info text-orange-400 text-base md:text-lg"></i>
                                        Status
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="history-table-body" class="divide-y divide-gray-100">
                            @include('components.history-table-rows', ['incidents' => $incidents])
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                <div
                    class="px-6 py-6 md:px-10 md:py-10 bg-slate-50 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4 font-bold">
                    <p class="text-xs md:text-sm text-slate-400 italic font-medium tracking-wide text-kai-navy text-center md:text-left">Monitoring
                        Perangkat Jaringan DAOP 3 Cirebon</p>
                    <div class="pagination-custom w-full md:w-auto overflow-x-auto flex justify-center md:justify-end">
                        {{ $incidents->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Auto Refresh Script --}}
    <script>
        const historyDataUrl = "{{ route('history.data') }}";

        setInterval(() => {
            // Cek apakah ada parameter filter di URL
            const urlParams = new URLSearchParams(window.location.search);
            const fetchUrl = historyDataUrl + '?' + urlParams.toString();

            fetch(fetchUrl)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('history-table-body').innerHTML = html;
                })
                .catch(error => console.error('Error refreshing history:', error));
        }, 5000); // Refresh setiap 5 detik
    </script>

    @if(session('error'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'DATA KOSONG',
            text: "{{ session('error') }}",
            confirmButtonColor: '#15803d', // Warna hijau emerald
            background: '#ffffff',
            customClass: {
                popup: 'rounded-3xl',
                title: 'font-black uppercase tracking-widest text-red-600',
                confirmButton: 'rounded-xl px-10 py-3 uppercase tracking-widest font-bold'
            }
        });
    </script>
    @endif
</x-app-layout>
