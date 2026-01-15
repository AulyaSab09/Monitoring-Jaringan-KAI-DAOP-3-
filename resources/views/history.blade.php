
<x-app-layout>
    {{-- CSS untuk menyembunyikan Navbar Laravel --}}
    <style>
        nav { display: none !important; } 
    </style>

    <div class="bg-gray-50 min-h-screen font-sans py-4">
        <div class="max-w-auto mx-auto px-4">
            
            {{-- 1. HEADER SECTION --}}
            <header class="mb-6">
                <div class="grid grid-cols-2 gap-x-6 items-center">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('assets/images/kai_logo.png') }}" alt="KAI" class="h-24 w-auto" />
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 leading-tight">Sistem Monitoring Jaringan</h1>
                            <p class="text-sm text-gray-500">KAI DAOP 3 Cirebon</p>
                        </div>
                    </div>

                    <div class="flex flex-col items-end gap-3">
                        <div class="text-right leading-tight">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Update Terakhir</span>
                            <div class="text-gray-900 text-2xl font-bold">
                                {{ \Carbon\Carbon::parse($histories[0]->waktu)->format('d M Y, H:i:s') }}
                            </div>
                        </div>

                        {{-- Dropdown Profile --}}
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" class="flex items-center space-x-2 bg-white border border-gray-200 py-2 px-4 rounded-lg shadow-sm hover:bg-gray-50 transition">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="text-sm font-semibold text-gray-700">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-md shadow-lg py-1 z-50">
                                <a href="{{ route('preview') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 transition">
                                    <svg class="w-4 h-4 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    Dashboard Preview
                                </a>
                                <hr class="my-1 border-gray-100">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                        <svg class="w-4 h-4 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- 2. JUDUL HISTORY --}}
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-800">History Perangkat Jaringan</h2>
                <p class="text-sm text-gray-500">Log aktivitas status perangkat di seluruh stasiun DAOP 3 Cirebon.</p>
            </div>

            {{-- 3. FILTER SECTION (Update: Focus Border Orange & Date Range) --}}
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6 text-slate-700">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    
                    {{-- Status --}}
                    <div class="md:col-span-2">
                        <label class="text-xs font-bold text-gray-700 uppercase mb-1 block">Status</label>
                        <select class="w-full rounded-md border-gray-300 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition-all">
                            <option>Semua Status</option>
                            <option>UP</option>
                            <option>WARNING</option>
                            <option>DOWN</option>
                        </select>
                    </div>

                    {{-- Date Range --}}
                    <div class="md:col-span-4 flex gap-2">
                        <div class="flex-1">
                            <label class="text-xs font-bold text-gray-700 uppercase mb-1 block">Dari</label>
                            <input type="date" class="w-full rounded-md border-gray-300 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition-all">
                        </div>
                        <div class="flex-1">
                            <label class="text-xs font-bold text-gray-700 uppercase mb-1 block">Sampai</label>
                            <input type="date" class="w-full rounded-md border-gray-300 text-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition-all">
                        </div>
                    </div>

                    {{-- Cari Perangkat (Panjang) --}}
                    <div class="md:col-span-5">
                        <label class="text-xs font-bold text-gray-700 uppercase mb-1 block">Cari Perangkat / Stasiun</label>
                        <div class="relative">
                            <input type="text" placeholder="Masukkan nama perangkat..." 
                                   class="w-full rounded-md border-gray-300 text-sm pl-10 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition-all">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Button Clear --}}
                    <div class="md:col-span-1">
                        <button class="w-full py-2 rounded-md bg-[#FFDCDC] text-[#82181A] border border-[#FEB2B2] hover:bg-red-600 hover:text-white transition-all text-sm font-bold flex justify-center items-center" title="Clear Filter">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- 4. TABEL --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden text-slate-700">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                        <tr>
                            <th class="px-6 py-4 text-left tracking-wider">Waktu</th>
                            <th class="px-6 py-4 text-left tracking-wider">Nama Perangkat</th>
                            <th class="px-6 py-4 text-left tracking-wider">IP Address</th>
                            <th class="px-6 py-4 text-left tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left tracking-wider">Nama Stasiun</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($histories as $data)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm font-mono">{{ $data->waktu }}</td>
                            <td class="px-6 py-4 text-sm font-medium italic text-slate-600">{{ $data->nama_perangkat }}</td>
                            <td class="px-6 py-4 text-sm">{{ $data->ip_address }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-[10px] font-bold bg-[#D4FFE1] text-[#0D542B] rounded-full border border-[#B7EBBF]">UP</span>
                            </td>
                            <td class="px-6 py-4 text-sm uppercase">{{ $data->stasiun }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                {{-- 5. PAGINATION --}}
                <div class="bg-white px-6 py-4 border-t border-gray-200 flex justify-between items-center font-medium">
                    <p class="text-xs text-gray-500 italic">Menampilkan 1 sampai 10 dari 100 data</p>
                    <div class="flex gap-1 text-xs">
                        <button class="w-8 h-8 flex items-center justify-center rounded border bg-gray-50 text-gray-400 cursor-not-allowed"><</button>
                        <button class="w-8 h-8 flex items-center justify-center rounded border bg-slate-800 text-white font-bold shadow-sm">1</button>
                        <button class="w-8 h-8 flex items-center justify-center rounded border bg-white text-gray-600 hover:bg-gray-50 transition">2</button>
                        <button class="w-8 h-8 flex items-center justify-center rounded border bg-white text-gray-600 hover:bg-gray-50 transition">3</button>
                        <button class="w-8 h-8 flex items-center justify-center rounded border bg-white text-gray-600 hover:bg-gray-50 transition">></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
=======
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>History Monitoring - KAI DAOP 3</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen font-sans p-6">

    {{-- HEADER --}}
    <div class="max-w-7xl mx-auto mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">
            Riwayat Monitoring Jaringan
        </h1>

        <a href="{{ route('preview') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
            ← Kembali ke Dashboard
        </a>
    </div>

    {{-- FILTER BAR --}}
    <div class="max-w-7xl mx-auto mb-4 flex flex-wrap gap-3 items-center">
        {{-- Filter Status --}}
        <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
            <option value="">Semua Status</option>
            <option value="up">UP</option>
            <option value="warning">WARNING</option>
            <option value="down">DOWN</option>
        </select>

        {{-- Filter Waktu --}}
        <input type="date"
               class="px-3 py-2 border border-gray-300 rounded-lg text-sm">

        {{-- Search --}}
        <input type="text"
               placeholder="Cari perangkat / IP / stasiun"
               class="px-3 py-2 border border-gray-300 rounded-lg text-sm w-64">
    </div>

    {{-- TABLE --}}
    <div class="max-w-7xl mx-auto bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Waktu</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Perangkat</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">IP Address</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Stasiun</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 text-sm text-gray-700">

                {{-- ROW 1 --}}
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">12-01-2026 19:30</td>
                    <td class="px-4 py-3 font-medium">SW-01</td>
                    <td class="px-4 py-3">192.168.1.1</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                     bg-green-100 text-green-700">
                            UP
                        </span>
                    </td>
                    <td class="px-4 py-3">Stasiun Cirebon</td>
                </tr>

                {{-- ROW 2 --}}
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">12-01-2026 19:28</td>
                    <td class="px-4 py-3 font-medium">RT-02</td>
                    <td class="px-4 py-3">192.168.1.2</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                     bg-red-100 text-red-700">
                            DOWN
                        </span>
                    </td>
                    <td class="px-4 py-3">Stasiun Jatibarang</td>
                </tr>

                {{-- ROW 3 --}}
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">12-01-2026 19:25</td>
                    <td class="px-4 py-3 font-medium">AP-03</td>
                    <td class="px-4 py-3">192.168.1.3</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                     bg-orange-100 text-orange-700">
                            WARNING
                        </span>
                    </td>
                    <td class="px-4 py-3">Stasiun Losari</td>
                </tr>

            </tbody>
        </table>
    </div>

</body>
</html>
>>>>>>> 5d8b39ac6306ec8d6592327752661a83b8c29e24
