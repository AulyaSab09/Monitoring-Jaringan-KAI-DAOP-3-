<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sistem Monitoring Jaringan - KAI DAOP 3 Cirebon</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />        
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <link rel="stylesheet" href="{{ asset('css/monitor-preview.css') }}">
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
                            <h1 class="text-2xl font-bold text-gray-900">Sistem Monitoring Jaringan</h1>
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
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            UP: <span id="counter-up" class="font-bold">{{ $up ?? 0 }}</span>
                        </span>

                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-[#FFECD5] text-[#7E2A0C]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M5.07 19h13.86L12 4.5 5.07 19z" />
                            </svg>
                            WARNING: <span id="counter-warning" class="font-bold">{{ $warning ?? 0 }}</span>
                        </span>

                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-[#FFDCDC] text-[#82181A]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            DOWN: <span id="counter-down" class="font-bold">{{ $down ?? 0 }}</span>
                        </span>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('monitor.create') }}" class="inline-flex items-center  bg-[#FF7300] text-white rounded-lg hover:opacity-90 transition-all text-sm font-bold shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Device
                        </a>
                        
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" 
                                class="flex items-center space-x-2 bg-white border-2 border-[#001D4B] py-2 px-4 rounded-lg shadow-sm hover:bg-[#001D4B] hover:text-white transition-all group">
                                <svg class="w-5 h-5 text-[#001D4B] group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="text-sm font-bold text-[#001D4B] group-hover:text-white">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-[#001D4B] group-hover:text-white transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-md shadow-lg py-1 z-50 overflow-hidden">
                                <a href="{{ route('history.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-[#001D4B] hover:text-white transition">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    History
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

            <div id="tree-container" class="tree-container bg-white shadow-sm">
                
                <div class="absolute top-4 right-4 z-30 flex flex-col gap-2 bg-white/90 backdrop-blur-sm rounded-xl p-2 shadow-lg border border-gray-200">
                    <button onclick="zoomIn()" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-blue-500 hover:text-white rounded-lg transition-colors font-bold text-gray-600" title="Zoom In">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
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
        </div>
        
        @include('components.monitor-hover-tooltip')

        <audio id="sound-connect" src="{{ asset('assets/notifications/konek.mp3') }}" preload="auto"></audio>
        <audio id="sound-disconnect" src="{{ asset('assets/notifications/diskonek.mp3') }}" preload="auto"></audio>
        
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
            
        <script src="{{ asset('js/monitor-dashboard.js') }}"></script>
    </body>
</html>