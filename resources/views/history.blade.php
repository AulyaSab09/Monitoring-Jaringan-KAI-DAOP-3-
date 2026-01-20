<x-app-layout>
    {{-- CSS Khusus untuk Halaman History --}}
    <style>
        nav { display: none !important; } 
        input[type="date"]::-webkit-calendar-picker-indicator { cursor: pointer; filter: invert(0.5); }
        
        /* Warna Identitas KAI */
        .bg-kai-navy { background-color: #001D4B; }
        .text-kai-navy { color: #001D4B; }
        .border-kai-navy { border-color: #001D4B; }
    </style>

    <div class="bg-gray-50 min-h-screen font-sans py-6 text-slate-700">
        <div class="max-w-[98%] mx-auto px-4">
            
            {{-- 1. HEADER SECTION (mb-4 untuk merapatkan jarak ke filter) --}}
            <header class="mb-4 flex justify-between items-center">
                <div class="flex items-center gap-6">
                    {{-- Logo KAI Sesuai Preview --}}
                    <img src="{{ asset('assets/images/kai_logo.png') }}" alt="KAI" class="h-24 w-auto object-contain" />
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 tracking-tight text-kai-navy">Riwayat Insiden Perangkat Jaringan</h1>
                        <p class="text-base text-gray-500 font-bold uppercase tracking-widest">KAI DAOP 3 Cirebon</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    {{-- Link diarahkan ke route preview --}}
                    <a href="{{ route('preview') }}" class="flex items-center gap-3 px-8 py-3.5 bg-kai-navy text-white rounded-2xl hover:opacity-90 transition-all font-black text-sm shadow-xl shadow-blue-900/20 tracking-widest">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        DASHBOARD
                    </a>

                    <form action="{{ route('history.reset') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus SEMUA riwayat insiden? Tindakan ini tidak dapat dibatalkan.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex items-center gap-3 px-8 py-3.5 bg-red-600 text-white rounded-2xl hover:bg-red-700 transition-all font-black text-sm shadow-xl shadow-red-900/20 tracking-widest uppercase">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            RESET
                        </button>
                    </form>
                </div>
            </header>

            {{-- 2. FILTER SECTION --}}
            <div class="bg-white p-8 rounded-3xl shadow-md border border-gray-100 mb-8">
                <form action="{{ route('history.index') }}" method="GET"> 
                    <div class="grid grid-cols-12 gap-6 items-end">
                        <div class="col-span-12 md:col-span-2">
                            <label class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-widest">Kondisi Insiden</label>
                            <select name="status" class="w-full h-14 rounded-2xl border-gray-200 text-sm font-bold focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all cursor-pointer">
                                <option value="">Semua Data</option>
                                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Selesai (Resolved)</option>
                                <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Sedang Terjadi (Ongoing)</option>
                            </select>
                        </div>

                        <div class="col-span-12 md:col-span-4 flex gap-4">
                            <div class="flex-1">
                                <label class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-widest">Dari Tanggal</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full h-14 rounded-2xl border-gray-200 text-sm font-bold focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all">
                            </div>
                            <div class="flex-1">
                                <label class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-widest">Sampai Tanggal</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full h-14 rounded-2xl border-gray-200 text-sm font-bold focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all">
                            </div>
                        </div>

                        <div class="col-span-12 md:col-span-6">
                            <label class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-widest">Cari Perangkat / Stasiun / IP</label>
                            <div class="flex gap-3">
                                <div class="relative flex-grow">
                                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </div>
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Masukkan nama perangkat..." class="w-full h-14 rounded-2xl border-gray-200 pl-14 pr-12 text-sm font-bold focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all">
                                    <button type="button" onclick="window.location.href='{{ route('history.index') }}'" class="absolute inset-y-0 right-0 pr-5 flex items-center text-gray-400 hover:text-red-500 transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </div>
                                <button type="submit" class="h-14 px-10 bg-orange-500 text-white rounded-2xl shadow-lg shadow-orange-100 flex items-center gap-3 font-black text-sm tracking-widest hover:bg-orange-600 transition-all uppercase">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    CARI
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- 3. TABLE SECTION --}}
            <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-kai-navy text-white">
                        <tr>
                            <th class="px-8 py-8 text-left text-sm font-black uppercase tracking-[0.2em]">
                                <div class="flex items-center gap-4">
                                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                                    Perangkat & IP
                                </div>
                            </th>
                            <th class="px-8 py-8 text-left text-sm font-black uppercase tracking-[0.2em]">
                                <div class="flex items-center gap-4">
                                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Lokasi Stasiun
                                </div>
                            </th>
                            <th class="px-8 py-8 text-left text-sm font-black uppercase tracking-[0.2em] bg-red-900/20">
                                <div class="flex items-center gap-4 text-red-200">
                                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z"/></svg>
                                    Waktu DOWN
                                </div>
                            </th>
                            <th class="px-8 py-8 text-left text-sm font-black uppercase tracking-[0.2em] bg-emerald-900/20">
                                <div class="flex items-center gap-4 text-emerald-200">
                                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 11l3 3L22 4m-2 8a8 8 0 11-8-8"/></svg>
                                    Waktu UP
                                </div>
                            </th>
                            <th class="px-8 py-8 text-center text-sm font-black uppercase tracking-[0.2em] bg-kai-navy/80">
                                <div class="flex items-center justify-center gap-4">
                                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Down Time
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="history-table-body" class="divide-y divide-gray-100">
                        @include('components.history-table-rows', ['incidents' => $incidents])
                    </tbody>
                </table>

                {{-- PAGINATION --}}
                <div class="px-10 py-10 bg-slate-50 border-t border-gray-100 flex justify-between items-center font-bold">
                    <p class="text-sm text-slate-400 italic font-medium tracking-wide text-kai-navy">Monitoring Perangkat Jaringan DAOP 3 Cirebon</p>
                    <div class="pagination-custom">
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
</x-app-layout>