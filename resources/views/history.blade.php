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

                <div class="flex items-center">
                    {{-- Link diarahkan ke route preview --}}
                    <a href="{{ route('preview') }}" class="flex items-center gap-3 px-8 py-3.5 bg-kai-navy text-white rounded-2xl hover:opacity-90 transition-all font-black text-sm shadow-xl shadow-blue-900/20 tracking-widest">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        DASHBOARD
                    </a>
                </div>
            </header>

            {{-- 2. FILTER SECTION --}}
            <div class="bg-white p-8 rounded-3xl shadow-md border border-gray-100 mb-8">
                <div class="grid grid-cols-12 gap-6 items-end">
                    <div class="col-span-12 md:col-span-2">
                        <label class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-widest">Kondisi Insiden</label>
                        <select class="w-full h-14 rounded-2xl border-gray-200 text-sm font-bold focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all cursor-pointer">
                            <option>Semua Data</option>
                            <option>Selesai (Resolved)</option>
                            <option>Sedang Terjadi (Ongoing)</option>
                        </select>
                    </div>

                    <div class="col-span-12 md:col-span-4 flex gap-4">
                        <div class="flex-1">
                            <label class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-widest">Dari Tanggal</label>
                            <input type="date" class="w-full h-14 rounded-2xl border-gray-200 text-sm font-bold focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all">
                        </div>
                        <div class="flex-1">
                            <label class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-widest">Sampai Tanggal</label>
                            <input type="date" class="w-full h-14 rounded-2xl border-gray-200 text-sm font-bold focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all">
                        </div>
                    </div>

                    <div class="col-span-12 md:col-span-6">
                        <label class="text-xs font-black text-slate-500 uppercase mb-2 block tracking-widest">Cari Perangkat / Stasiun / IP</label>
                        <div class="flex gap-3">
                            <div class="relative flex-grow">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <input type="text" placeholder="Masukkan nama perangkat..." class="w-full h-14 rounded-2xl border-gray-200 pl-14 pr-12 text-sm font-bold focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all">
                                <button class="absolute inset-y-0 right-0 pr-5 flex items-center text-gray-400 hover:text-red-500 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                            <button class="h-14 px-10 bg-orange-500 text-white rounded-2xl shadow-lg shadow-orange-100 flex items-center gap-3 font-black text-sm tracking-widest hover:bg-orange-600 transition-all uppercase">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                CARI
                            </button>
                        </div>
                    </div>
                </div>
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
                    <tbody class="divide-y divide-gray-100">
                        {{-- Row Dummy 1 --}}
                        <tr class="hover:bg-slate-50 transition-all">
                            <td class="px-8 py-10">
                                <div class="font-black text-slate-900 text-2xl">Router Core Utama</div>
                                <div class="text-sm text-slate-400 font-mono font-black tracking-[0.15em] mt-2">192.168.1.1</div>
                            </td>
                            <td class="px-8 py-10">
                                <span class="px-5 py-2.5 bg-slate-100 rounded-2xl text-xs font-black text-kai-navy uppercase tracking-widest border border-slate-200">STASIUN KEJAKSAN</span>
                            </td>
                            <td class="px-8 py-10 bg-red-50/20">
                                <div class="text-xl font-black text-red-700">Senin, 19 Jan 2026</div>
                                <div class="text-sm font-bold text-red-500 italic mt-1">Pukul 14:00:05 WIB</div>
                            </td>
                            <td class="px-8 py-10 bg-emerald-50/20">
                                <div class="text-xl font-black text-emerald-700">Senin, 19 Jan 2026</div>
                                <div class="text-sm font-bold text-emerald-400 italic mt-1">Pukul 15:30:10 WIB</div>
                            </td>
                            <td class="px-8 py-10 text-center">
                                <span class="inline-block px-8 py-4 rounded-2xl bg-slate-100 text-kai-navy border-2 border-slate-200 font-black text-xl shadow-sm tracking-tighter">01j 30m 05d</span>
                            </td>
                        </tr>

                        {{-- Row Dummy 2 --}}
                        <tr class="hover:bg-slate-50 transition-all bg-red-50/5">
                            <td class="px-8 py-10">
                                <div class="font-black text-slate-900 text-2xl">Switch Distribusi Lt.2</div>
                                <div class="text-sm text-slate-400 font-mono font-black tracking-[0.15em] mt-2">192.168.2.45</div>
                            </td>
                            <td class="px-8 py-10">
                                <span class="px-5 py-2.5 bg-slate-100 rounded-2xl text-xs font-black text-kai-navy uppercase tracking-widest border border-slate-200">STASIUN JATIBARANG</span>
                            </td>
                            <td class="px-8 py-10 bg-red-50/20">
                                <div class="text-xl font-black text-red-700">Senin, 19 Jan 2026</div>
                                <div class="text-sm font-bold text-red-400 italic mt-1">Pukul 20:15:00 WIB</div>
                            </td>
                            <td class="px-8 py-10 bg-emerald-50/20 text-center md:text-left">
                                <div class="flex items-center gap-3 text-orange-500">
                                    <span class="relative flex h-4 w-4">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-4 w-4 bg-orange-600"></span>
                                    </span>
                                    <span class="text-base font-black italic uppercase tracking-tighter animate-pulse">Sedang Perbaikan...</span>
                                </div>
                            </td>
                            <td class="px-8 py-10 text-center">
                                <span class="inline-block px-8 py-4 rounded-2xl bg-red-600 text-white shadow-2xl shadow-red-200 animate-pulse font-black text-xl italic tracking-tighter">02j 30m 00d</span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                {{-- PAGINATION --}}
                <div class="px-10 py-10 bg-slate-50 border-t border-gray-100 flex justify-between items-center font-bold">
                    <p class="text-sm text-slate-400 italic font-medium tracking-wide text-kai-navy">Monitoring Perangkat Jaringan DAOP 3 Cirebon</p>
                    <div class="flex gap-3">
                        <button class="px-6 py-3 rounded-2xl border bg-white text-slate-400 text-sm font-black uppercase tracking-widest cursor-not-allowed">Previous</button>
                        <button class="px-6 py-3 rounded-2xl border bg-kai-navy text-white text-sm font-black shadow-xl">1</button>
                        <button class="px-6 py-3 rounded-2xl border bg-white text-slate-600 text-sm font-black uppercase tracking-widest hover:bg-slate-100 transition-all">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>