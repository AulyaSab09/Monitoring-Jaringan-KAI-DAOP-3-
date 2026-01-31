@php
    $appTitle = \App\Models\AppSetting::get('app_title', 'Sistem Monitoring Jaringan');
    $soundConnect = \App\Models\AppSetting::get('sound_connect');
    $soundDisconnect = \App\Models\AppSetting::get('sound_disconnect');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $appTitle }} - KAI DAOP 3 Cirebon</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />        
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
        <link rel="stylesheet" href="{{ asset('css/monitor-preview.css') }}?v={{ time() }}">
    </head>
    <body class="bg-gray-50 text-[#1b1b18] min-h-screen font-sans py-4" data-monitor-data-url="{{ route('monitor.data') }}">
        <div class="max-w-auto mx-auto px-4">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <header class="mb-4">
                <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('assets/images/kai_logo.png') }}" alt="KAI" class="h-24 w-auto" />
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $appTitle }}</h1>
                            <p class="text-sm text-gray-500">KAI DAOP 3 Cirebon</p>
                        </div>
                    </div>

                    <div class="text-right leading-tight">
                        <div id="dateText" class="text-gray-600 font-medium text-lg"></div>
                        <div id="timeText" class="text-[#001D4B] text-3xl font-bold"></div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-gray-200 text-gray-800">
                            Total: <span id="counter-total" class="ml-1 font-bold">{{ $total ?? 0 }}</span>
                        </span>

                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-[#D4FFE1] text-[#0D542B]">
                            <i class="fa-solid fa-check w-4 h-4 text-center"></i>
                            UP: <span id="counter-up" class="font-bold">{{ $up ?? 0 }}</span>
                        </span>

                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-[#FFECD5] text-[#7E2A0C]">
                            <i class="fa-solid fa-triangle-exclamation w-4 h-4 text-center"></i>
                            WARNING: <span id="counter-warning" class="font-bold">{{ $warning ?? 0 }}</span>
                        </span>

                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-[#FFDCDC] text-[#82181A]">
                            <i class="fa-solid fa-xmark w-4 h-4 text-center"></i>
                            DOWN: <span id="counter-down" class="font-bold">{{ $down ?? 0 }}</span>
                        </span>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('monitor.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#FF7300] text-white rounded-lg hover:opacity-90 transition-all text-lg font-bold shadow-md px-6 border-2 border-[#FF7300]">
                            <i class="fa-solid fa-plus"></i>
                            Tambah Device
                        </a>

                         {{-- 2. Tombol History --}}
                        <a href="{{ route('history.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#001D4B] text-[#001D4B] text-white rounded-lg hover:opacity-90 transition-all text-lg font-bold shadow-md px-6 border-2 border-[#001D4B]">
                            <i class="fa-solid fa-clock-rotate-left w-4 h-4 group-hover:text-white"></i>
                            History
                        </a>
                        
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" 
                                class="flex items-center space-x-2 bg-white border-2 border-[#001D4B] py-2 px-4 rounded-lg shadow-sm transition-all group">
                                <i class="fa-solid fa-user w-5 h-5 text-[#001D4B]"></i>
                                <span class="text-lg font-bold text-[#001D4B]">{{ Auth::user()->name }}</span>
                                <i class="fa-solid fa-chevron-down w-4 h-4 text-[#001D4B] transition-transform" :class="{ 'rotate-180': open }"></i>
                            </button>

                            <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-md shadow-lg py-1 z-50 overflow-hidden">
                                <a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-2 text-lg text-gray-700 hover:bg-[#001D4B] hover:text-white transition">
                                    <i class="fa-solid fa-gear w-4 h-4 mr-3"></i>
                                    Pengaturan
                                </a>
                                <hr class="my-1 border-gray-100">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center px-4 py-2 text-lg text-red-600 hover:bg-red-50 transition">
                                        <i class="fa-solid fa-right-from-bracket w-4 h-4 mr-3 text-red-500"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div id="tree-container" class="tree-container bg-white shadow-sm">
                
                <div class="absolute top-4 right-4 z-30 flex flex-col gap-2 bg-white/90 backdrop-blur-sm rounded-xl p-2 shadow-lg border border-gray-200">
                    <button onclick="zoomIn()" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-blue-500 hover:text-white rounded-lg transition-colors font-bold text-gray-600" title="Zoom In">
                        <i class="fa-solid fa-plus text-sm"></i>
                    </button>
                    <div class="relative flex items-center justify-center">
                        <input type="number" id="zoom-input" value="100" min="10" max="200" 
                            class="w-10 text-center text-[10px] font-bold text-gray-500 bg-transparent border-none p-0 focus:ring-0 appearance-none [-moz-appearance:_textfield] [&::-webkit-inner-spin-button]:m-0 [&::-webkit-inner-spin-button]:appearance-none"
                            onchange="setZoom(this.value)"
                            onkeydown="if(event.key === 'Enter') setZoom(this.value)"
                        >
                        <span class="absolute right-0 text-[8px] text-gray-400 font-bold pointer-events-none">%</span>
                    </div>
                    <button onclick="zoomOut()" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-blue-500 hover:text-white rounded-lg transition-colors font-bold text-gray-600" title="Zoom Out">
                        <i class="fa-solid fa-minus text-sm"></i>
                    </button>
                    <button onclick="resetZoom()" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-[10px] font-bold text-gray-500" title="Reset Zoom">
                        FIT
                    </button>
                </div>
                
                <div id="tree-viewport" class="tree-viewport">
                    <svg id="tree-lines-svg" class="tree-lines-svg"></svg>
                    
                    <div id="tree-wrapper" class="tree-wrapper w-full h-full">
                        @include('components.monitor-zone-wrapper', compact('centers', 'utaras', 'selatans'))
                    </div>
                </div>
            </div>
        </div>
        
        @include('components.monitor-hover-tooltip')

        <audio id="sound-connect" src="{{ $soundConnect ? asset('storage/' . $soundConnect) : asset('assets/notifications/konek.mp3') }}" preload="auto"></audio>
        <audio id="sound-disconnect" src="{{ $soundDisconnect ? asset('storage/' . $soundDisconnect) : asset('assets/notifications/diskonek.mp3') }}" preload="auto"></audio>
        
        <button id="sound-toggle" onclick="enableSound()" class="fixed bottom-4 right-4 z-50 px-4 py-2 bg-gray-800 text-white rounded-full shadow-lg hover:bg-gray-700 transition-all flex items-center gap-2 text-sm font-semibold">
            <span id="sound-label">🔇 Suara On</span>
        </button>

        <!-- Kotak Device Bawah -->

       {{-- BAGIAN ANTREAN DOWN (Revisi Padding & Judul) --}}
        <div class="mt-4 mb-6 px-4"> 
            <div class="flex items-center gap-3 mb-3">
                <div class="w-1.5 h-6 bg-red-600 rounded-full animate-pulse"></div>
                <h2 class="text-lg font-black text-gray-900 tracking-tight uppercase">Perangkat Terdeteksi Down</h2>
            </div>

            {{-- Kontainer Scroll (Dihilangkan padding-left bawaan jika ada agar alignment pas) --}}
            <div id="down-devices-list" class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide" style="scroll-behavior: smooth;">
                <div id="no-down-message" class="w-full py-6 text-center bg-gray-100 rounded-2xl border-2 border-dashed border-gray-300">
                    <p class="text-gray-500 font-bold italic">Sistem Aman: Semua perangkat dalam kondisi normal.</p>
                </div>
            </div>
        </div>
            
        <script src="{{ asset('js/monitor-dashboard.js') }}?v={{ time() }}"></script>
    </body>
</html>