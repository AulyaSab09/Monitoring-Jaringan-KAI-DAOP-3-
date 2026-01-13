<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sistem Monitoring Jaringan - KAI DAOP 3 Cirebon</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            
            /* === TREE VIEW STYLES === */
            .tree-container {
                position: relative;
                overflow: hidden;
                padding: 2rem 1rem;
                height: calc(100vh - 200px);
                background-color: #f8fafc;
                border: 2px solid #e2e8f0;
                border-radius: 1rem;
                cursor: grab;
            }
            
            .tree-container:active {
                cursor: grabbing;
            }
            
            .tree-viewport {
                transform-origin: 0 0;
                transition: transform 0.1s ease-out;
                min-width: max-content;
                position: relative;
                padding: 100px;
            }
            
            /* SVG Canvas for Lines */
            .tree-lines-svg {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
                z-index: 1;
            }
            
            .tree-line {
                stroke: #94a3b8;
                stroke-width: 3;
                fill: none;
                stroke-linecap: round;
                transition: stroke 0.4s ease, stroke-width 0.3s ease;
            }
            
            /* Warna Garis Sesuai Status */
            .tree-line.status-connected { stroke: #10b981; }
            .tree-line.status-disconnected { stroke: #ef4444; stroke-dasharray: 6,4; }
            .tree-line.status-unstable { stroke: #f97316; }
            .tree-line.status-pending { stroke: #cbd5e1; }
            
            /* Tree Structure */
            .tree-wrapper {
                display: flex;
                flex-direction: row;
                align-items: flex-start;
                justify-content: flex-start;
                gap: 3rem;
                flex-wrap: nowrap;
                position: relative;
                z-index: 2;
            }
            
            .tree-node {
                display: flex;
                flex-direction: column;
                align-items: center;
                position: relative;
            }
            
            .tree-node-card {
                position: relative;
                z-index: 2;
                margin-bottom: 4rem;
            }
            
            .tree-children {
                display: flex;
                flex-direction: row;
                align-items: flex-start;
                gap: 2rem;
                margin-top: 0.5rem;
            }
            
            /* Hover Add Button */
            .hover-add-btn {
                position: absolute;
                bottom: -16px;
                left: 50%;
                transform: translateX(-50%) scale(0);
                width: 28px;
                height: 28px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                border: 2px solid white;
                border-radius: 50%;
                color: white;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                z-index: 20;
                cursor: pointer;
            }
            
            .hover-add-btn:hover {
                transform: translateX(-50%) scale(1.15);
            }
            
            .tree-node-card:hover .hover-add-btn {
                transform: translateX(-50%) scale(1);
            }
            
            /* Status Warning Animations */
            @keyframes pulse-warning-down {
                0%, 100% { border-color: #fca5a5; }
                50% { border-color: #ef4444; box-shadow: 0 0 8px 2px rgba(239, 68, 68, 0.3); }
            }
            .child-down-warning {
                animation: pulse-warning-down 1.5s ease-in-out infinite;
                border-color: #ef4444 !important;
            }
            
            @keyframes pulse-warning-unstable {
                0%, 100% { border-color: #fdba74; }
                50% { border-color: #f97316; box-shadow: 0 0 6px 1px rgba(249, 115, 22, 0.2); }
            }
            .child-unstable-warning {
                animation: pulse-warning-unstable 2s ease-in-out infinite;
                border-color: #f97316 !important;
            }
        </style>
    </head>
    <body class="bg-gray-50 text-[#1b1b18] min-h-screen font-sans py-4" data-monitor-data-url="{{ route('monitor.data') }}">
        <div class="max-w-auto mx-auto px-4">
            
            {{-- Success Message --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            {{-- HEADER SECTION --}}
          {{-- HEADER SECTION --}}
<header class="mb-4">
    <div class="flex items-center justify-between">

        {{-- KIRI: Logo & Judul --}}
        <div class="flex items-center gap-4">
            <img src="{{ asset('assets/images/kai_logo.png') }}" alt="KAI" class="h-20 w-auto" />
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Sistem Monitoring Jaringan
                </h1>
                <p class="text-sm text-gray-500">
                    KAI DAOP 3 Cirebon
                </p>
            </div>
        </div>

        {{-- KANAN: USER DROPDOWN --}}
        <div class="relative" x-data="{ open: false }">
            <button
                @click="open = !open"
                class="flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 transition"
            >
                {{-- USER ICON --}}
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M5.121 17.804A9 9 0 1118.88 17.8M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>

                {{-- USER NAME --}}
                <span class="text-sm font-semibold text-gray-700">
                    {{ auth()->user()->name }}
                </span>

                {{-- ARROW --}}
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- DROPDOWN --}}
            <div
                x-show="open"
                @click.outside="open = false"
                x-transition
                class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden z-50"
            >

                {{-- HISTORY --}}
                <a href="{{ route('history') }}"
                   class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    History
                </a>

                <div class="border-t border-gray-100"></div>

                {{-- LOGOUT --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                        </svg>
                        Logout
                    </button>
                </form>

            </div>
        </div>
    </div>
</header>


            {{-- TREE VIEW CONTAINER --}}
            <div id="tree-container" class="tree-container bg-white shadow-sm">
                
                {{-- Zoom Controls --}}
                <div class="absolute top-4 right-4 z-30 flex flex-col gap-2 bg-white/90 backdrop-blur-sm rounded-xl p-2 shadow-lg border border-gray-200">
                    <button onclick="zoomIn()" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-blue-500 hover:text-white rounded-lg transition-colors font-bold text-gray-600" title="Zoom In">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                    <span id="zoom-level" class="text-[10px] font-bold text-gray-500 text-center">100%</span>
                    <button onclick="zoomOut()" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-blue-500 hover:text-white rounded-lg transition-colors font-bold text-gray-600" title="Zoom Out">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"></path></svg>
                    </button>
                    <button onclick="resetZoom()" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors text-[10px] font-bold text-gray-500" title="Reset Zoom">
                        FIT
                    </button>
                </div>
                
                {{-- Tree Viewport --}}
                <div id="tree-viewport" class="tree-viewport">
                    <svg id="tree-lines-svg" class="tree-lines-svg"></svg>
                    
                    <div id="tree-wrapper" class="tree-wrapper">
                        @include('components.monitor-cards', ['monitors' => $monitors])
                    </div>
                </div>
            </div>
        </div>
        
        {{-- HOVER TOOLTIP (dari component) --}}
        @include('components.monitor-hover-tooltip')

        {{-- Audio Notifications --}}
        <audio id="sound-connect" src="{{ asset('assets/notifications/konek.mp3') }}" preload="auto"></audio>
        <audio id="sound-disconnect" src="{{ asset('assets/notifications/diskonek.mp3') }}" preload="auto"></audio>
        
        {{-- Sound Enable Button --}}
        <button id="sound-toggle" 
                onclick="enableSound()" 
                class="fixed bottom-4 right-4 z-50 px-4 py-2 bg-gray-800 text-white rounded-full shadow-lg hover:bg-gray-700 transition-all flex items-center gap-2 text-sm font-semibold"
                title="Klik untuk mengaktifkan notifikasi suara">
            <svg id="sound-icon-off" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"></path>
            </svg>
            <svg id="sound-icon-on" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
            </svg>
            <span id="sound-label">🔇 Suara On</span>
        </button>
            
        {{-- External JavaScript --}}
        <script src="{{ asset('js/monitor-dashboard.js') }}"></script>
    </body>
</html>