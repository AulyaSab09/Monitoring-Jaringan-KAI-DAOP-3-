<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sistem Monitoring Jaringan - KAI DAOP 3 Cirebon</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            /* === CORE STYLES === */
            .tree-container {
                position: relative; overflow: hidden; padding: 2rem 1rem;
                height: calc(100vh - 240px); background-color: #f0f4f8;
                border: 2px solid #e2e8f0; border-radius: 1rem; cursor: grab;
            }
            .tree-container:active { cursor: grabbing; }
            .tree-viewport { transform-origin: 0 0; min-width: max-content; position: relative; padding: 100px; }
            
            .tree-lines-svg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1; }
            .tree-line { stroke: #94a3b8; stroke-width: 3; fill: none; stroke-linecap: round; transition: stroke 0.4s ease; }
            .tree-line.status-connected { stroke: #10b981; }
            .tree-line.status-disconnected { stroke: #ef4444; stroke-dasharray: 6,4; }
            .tree-line.status-unstable { stroke: #f97316; }
            .tree-line.status-pending { stroke: #cbd5e1; }
            
            .tree-wrapper { display: flex; gap: 4rem; position: relative; z-index: 2; }
            .tree-node { display: flex; flex-direction: column; align-items: center; position: relative; }
            .tree-node-card { position: relative; z-index: 2; margin-bottom: 5rem; }
            .tree-children { display: flex; gap: 3rem; margin-top: 0.5rem; }

            /* === LITERAL DEVICE STYLES === */
            
            /* 1. ROUTER (Hitam, Antena) */
            .device-router {
                background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
                border-radius: 20px; color: white; border-bottom: 6px solid #020617;
                box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3);
            }
            .router-antenna { position: absolute; top: -25px; width: 8px; height: 35px; background: #334155; border-radius: 4px 4px 0 0; border: 1px solid #475569; z-index: -1; }
            .router-antenna.left { left: 20px; transform: rotate(-10deg); }
            .router-antenna.right { right: 20px; transform: rotate(10deg); }
            .router-leds { display: flex; gap: 4px; margin-bottom: 8px; }
            .led { width: 6px; height: 6px; border-radius: 50%; background: #334155; }
            .led.active { background: #22c55e; box-shadow: 0 0 5px #22c55e; }
            .led.error { background: #ef4444; box-shadow: 0 0 5px #ef4444; }

            /* 2. SWITCH (Lebar, Port RJ45) */
            .device-switch {
                background: #334155; border-radius: 4px; color: white; border: 1px solid #475569;
                box-shadow: 0 4px 6px -1px rgba(0,0,0,0.3); min-width: 280px;
            }
            .switch-ears { position: absolute; top: 50%; width: 10px; height: 80%; background: #94a3b8; transform: translateY(-50%); border-radius: 2px; }
            .switch-ears.left { left: -10px; border-right: 1px solid #64748b; }
            .switch-ears.right { right: -10px; border-left: 1px solid #64748b; }
            .port-grid { display: grid; grid-template-columns: repeat(8, 1fr); gap: 3px; background: #1e293b; padding: 4px; border-radius: 4px; margin-top: 8px; }
            .port { height: 10px; background: #0f172a; border-radius: 1px; position: relative; }
            .port.active::after { content:''; position: absolute; top:1px; left:1px; width:2px; height:2px; background:#22c55e; }

            /* 3. SERVER (Tower Metalik) */
            .device-server {
                background: linear-gradient(90deg, #d1d5db 0%, #f3f4f6 20%, #d1d5db 100%);
                border-radius: 6px; color: #1e293b; border: 1px solid #9ca3af;
                box-shadow: 4px 4px 10px rgba(0,0,0,0.1); position: relative;
            }
            .server-grill { width: 100%; height: 100%; background-image: repeating-linear-gradient(0deg, transparent, transparent 9px, #9ca3af 10px); position: absolute; top: 0; left: 0; opacity: 0.1; pointer-events: none; }

            /* 4. PC (Monitor Style) */
            .device-pc {
                background: #f8fafc; border: 4px solid #334155; border-radius: 8px; color: #1e293b;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1); position: relative;
            }
            .pc-stand {
                position: absolute; bottom: -15px; left: 50%; transform: translateX(-50%);
                width: 60px; height: 15px; background: #334155; 
                clip-path: polygon(20% 0%, 80% 0%, 100% 100%, 0% 100%);
            }
            .pc-base {
                position: absolute; bottom: -18px; left: 50%; transform: translateX(-50%);
                width: 100px; height: 4px; background: #1e293b; border-radius: 2px;
            }

            /* 5. ACCESS POINT (Bulat/UFO Style) */
            .device-ap {
                background: #ffffff; border-radius: 50%; color: #334155;
                box-shadow: 0 8px 20px rgba(0,0,0,0.1); border: 4px solid #e2e8f0;
                width: 200px; height: 200px; display: flex; flex-direction: column; justify-content: center; align-items: center;
                text-align: center;
            }
            .ap-ring {
                position: absolute; top: 10px; left: 10px; right: 10px; bottom: 10px;
                border: 2px solid #dee3e9ff; border-radius: 50%; pointer-events: none;
            }
            .ap-led {
                width: 8px; height: 8px; background: #22c55e; border-radius: 50%;
                box-shadow: 0 0 10px #22c55e; margin-bottom: 10px;
            }

            /* 6. CCTV (Dome Camera Style) */
            .device-cctv {
                background: #1f2937; border-radius: 0 0 50px 50px; color: white;
                box-shadow: 0 10px 15px rgba(0,0,0,0.3); border-top: 4px solid #374151;
            }
            .cctv-lens {
                position: absolute; bottom: 15px; left: 50%; transform: translateX(-50%);
                width: 40px; height: 40px; background: #000; border-radius: 50%; border: 2px solid #4b5563;
                background: radial-gradient(circle at 30% 30%, #4b5563 0%, #000 60%);
            }
            
            /* UTILS */
            .hover-add-btn {
                position: absolute; bottom: -16px; left: 50%; transform: translateX(-50%) scale(0);
                width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;
                background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                border: 2px solid white; border-radius: 50%; color: white;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); z-index: 20; cursor: pointer;
            }
            .tree-node-card:hover .hover-add-btn { transform: translateX(-50%) scale(1); }

            /* WARNING ANIMATIONS */
            .child-down-warning { animation: pulse-red 1.5s infinite; border-color: #ef4444 !important; border-width: 3px !important; }
            .child-unstable-warning { animation: pulse-orange 1.5s infinite; border-color: #f97316 !important; border-width: 3px !important; }
            @keyframes pulse-red { 0%,100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); } 70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); } }
            @keyframes pulse-orange { 0%,100% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.7); } 70% { box-shadow: 0 0 0 10px rgba(249, 115, 22, 0); } }
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
            <header class="mb-4">
                <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                    {{-- BARIS 1 - KIRI: Logo & Judul --}}
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('assets/images/kai_logo.png') }}" alt="KAI" class="h-24 w-auto" />
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Sistem Monitoring Jaringan</h1>
                            <p class="text-sm text-gray-500">KAI DAOP 3 Cirebon</p>
                        </div>
                    </div>

                    {{-- BARIS 1 - KANAN: Jam WIB --}}
                    <div class="text-right leading-tight">
                        <div id="dateText" class="text-gray-600 font-medium text-lg"></div>
                        <div id="timeText" class="text-gray-900 text-3xl font-bold"></div>
                    </div>

                    {{-- BARIS 2 - KIRI: Status Counter --}}
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

                    {{-- BARIS 2 - KANAN: Button --}}
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('monitor.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium shadow-sm transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Device
                        </a>
                        
                        {{-- Logout Button --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium shadow-sm transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </button>
                        </form>
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
        
        {{-- HOVER TOOLTIP --}}
        @include('components.monitor-hover-tooltip')

        {{-- Audio Notifications --}}
        <audio id="sound-connect" src="{{ asset('assets/notifications/konek.mp3') }}" preload="auto"></audio>
        <audio id="sound-disconnect" src="{{ asset('assets/notifications/diskonek.mp3') }}" preload="auto"></audio>
        
        {{-- Sound Button --}}
        <button id="sound-toggle" onclick="enableSound()" class="fixed bottom-4 right-4 z-50 px-4 py-2 bg-gray-800 text-white rounded-full shadow-lg hover:bg-gray-700 transition-all flex items-center gap-2 text-sm font-semibold">
            <span id="sound-label">🔇 Suara On</span>
        </button>
            
        <script src="{{ asset('js/monitor-dashboard.js') }}"></script>
    </body>
</html>