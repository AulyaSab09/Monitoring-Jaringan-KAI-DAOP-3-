<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Device - KAI DAOP 3 Cirebon</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />        

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <style>
    .bg-kai-navy { background-color: #001D4B; }
    .text-kai-navy { color: #001D4B; }
    .bg-kai-orange { background-color: #FF7300; }
  </style>
</head>

<body class="bg-white min-h-screen font-sans overflow-hidden">
  <div class="flex min-h-screen">
    
    {{-- KOLOM KIRI --}}
    <div class="hidden lg:flex lg:w-3/12 bg-kai-navy relative flex-col justify-between p-10 text-white overflow-hidden">
        
        {{-- Dekorasi Lingkaran Geometric (Kesan Formal) --}}
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full"></div>
            <div class="absolute top-1/2 -left-20 w-64 h-64 bg-black/5 rounded-full"></div>
            <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-white/10 rounded-full border-4 border-white/5"></div>
        </div>

        {{-- Atas: Logo & Judul (Versi Diperbesar & Terpusat) --}}
        <div class="relative z-10 flex flex-col items-center w-full mt-10">
            
            {{-- 1. Logo KAI --}}
            <img src="{{ asset('assets/images/kai_logo.png') }}" 
                alt="KAI" 
                class="h-[140px] w-auto mb-10 drop-shadow-2xl" /> 

            {{-- 2. EDIT DEVICE --}}
            <div class="text-center mb-8 w-full">
                <h1 class="text-5xl font-black uppercase">
                    Edit Device 
                </h1>
            </div>

            {{-- 3. Keterangan --}}
            <p class="text-lg opacity-95 font-medium leading-relaxed w-full text-justify px-1">
                Perbarui konfigurasi perangkat yang sedang dipantau. Pastikan data yang dimasukkan sesuai dengan spesifikasi infrastruktur jaringan terkini.
            </p>
        </div>

            {{-- Bawah: Visual Kereta & Stasiun (Tampil Permanen & Timbul) --}}
            <div class="absolute bottom-0 left-0 w-full p-0 pointer-events-none overflow-hidden" style="height: 40%;">
                <img src="{{ asset('assets/images/kereta.png') }}" 
                    alt="Visual KAI" 
                    class="w-full h-full object-contain object-bottom transform scale-150 translate-y-8 brightness-105 opacity-90 transition-none" />
            </div>
    </div>

    {{-- KOLOM KANAN --}}
    <div class="w-full lg:w-9/12 flex items-center justify-center p-8 md:p-12 bg-white overflow-y-auto">
        {{-- 1. Kontainer --}}
        <div class="w-full max-w-6xl px-10">
            
            {{-- Header Form diperbesar --}}
            <div class="mb-12">
                <h2 class="text-5xl font-black text-kai-navy uppercase">Edit Perangkat</h2>
                <p class="text-xl text-gray-400 font-medium italic mt-2">Perbarui informasi perangkat: <span class="text-kai-navy font-bold">{{ $monitor->name }}</span></p>
            </div>

            {{-- Flash Message for Errors --}}
            @if(session('error'))
                <div class="bg-red-100 border-2 border-red-400 text-red-700 px-6 py-4 rounded-2xl mb-8 font-bold">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('monitor.update', $monitor->id) }}" method="POST" class="space-y-10">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    {{-- Nama Perangkat --}}
                    <div class="space-y-3">
                        <label class="text-lg font-black uppercase tracking-[0.2em] text-kai-navy opacity-80">Nama Perangkat</label>
                        <input type="text" name="name" value="{{ old('name', $monitor->name) }}" placeholder="Contoh: SW-CIREBON-01" 
                              class="w-full px-6 py-5 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-[#001D4B] focus:ring-4 focus:ring-blue-100 transition-all font-bold text-2xl text-kai-navy placeholder:font-normal placeholder:text-gray-300 shadow-sm outline-none" required>
                        @error('name')
                            <p class="text-red-500 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- IP Address --}}
                    <div class="space-y-3">
                        <label class="text-lg font-black uppercase tracking-[0.2em] text-kai-navy opacity-80">Alamat IP (IPv4)</label>
                        <input type="text" name="ip_address" value="{{ old('ip_address', $monitor->ip_address) }}" placeholder="192.168.x.x" 
                             class="w-full px-6 py-5 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-[#001D4B] focus:ring-4 focus:ring-blue-100 transition-all font-bold text-2xl text-kai-navy placeholder:font-normal placeholder:text-gray-300 shadow-sm outline-none" required>
                        @error('ip_address')
                            <p class="text-red-500 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    {{-- Lokasi Stasiun --}}
                    <div class="space-y-3">
                        <label class="text-lg font-black uppercase tracking-[0.2em] text-kai-navy opacity-80">Lokasi Stasiun</label>
                        <input type="text" name="location" value="{{ old('location', $monitor->location) }}" placeholder="Stasiun Cirebon" 
                              class="w-full px-6 py-5 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-[#001D4B] focus:ring-4 focus:ring-blue-100 transition-all font-bold text-2xl text-kai-navy placeholder:font-normal placeholder:text-gray-300 shadow-sm outline-none">
                    </div>

                    {{-- Kode Stasiun --}}
                    <div class="space-y-3">
                        <label class="text-lg font-black uppercase tracking-[0.2em] text-kai-navy opacity-80">Kode Stasiun</label>
                        <input type="text" name="kode_lokasi" value="{{ old('kode_lokasi', $monitor->kode_lokasi) }}" placeholder="Contoh: CN / CNP" 
                              class="w-full px-6 py-5 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-[#001D4B] focus:ring-4 focus:ring-blue-100 transition-all font-bold text-2xl text-kai-navy placeholder:font-normal placeholder:text-gray-300 shadow-sm outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    {{-- Kategori Perangkat --}}
                    <div class="space-y-3">
                        <label class="text-lg font-black uppercase tracking-[0.2em] text-kai-navy opacity-80">Kategori Perangkat</label>
                        <div class="relative">
                            <select name="type" 
                                class="w-full appearance-none px-6 py-5 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-[#001D4B] focus:ring-4 focus:ring-blue-100 transition-all font-bold text-2xl text-kai-navy cursor-pointer shadow-sm outline-none">
                                <option value="Router" {{ old('type', $monitor->type) == 'Router' ? 'selected' : '' }}>Router</option>
                                <option value="Switch" {{ old('type', $monitor->type) == 'Switch' ? 'selected' : '' }}>Switch</option>
                                <option value="Access Point" {{ old('type', $monitor->type) == 'Access Point' ? 'selected' : '' }}>Access Point</option>
                                <option value="PC" {{ old('type', $monitor->type) == 'PC' ? 'selected' : '' }}>PC / Client</option>
                                <option value="CCTV" {{ old('type', $monitor->type) == 'CCTV' ? 'selected' : '' }}>CCTV</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-kai-navy opacity-50 pointer-events-none text-lg"></i>
                        </div>
                    </div>

                    {{-- Zona Perangkat (ONLY for root devices, hide for children) --}}
                    @if(!$monitor->parent_id)
                    <div class="space-y-3">
                        <label class="text-lg font-black uppercase tracking-[0.2em] text-kai-navy opacity-80">Zona Jalur</label>
                        <div class="relative">
                            <select name="zone" 
                                class="w-full appearance-none px-6 py-5 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-[#001D4B] focus:ring-4 focus:ring-blue-100 transition-all font-bold text-2xl text-kai-navy cursor-pointer shadow-sm outline-none">
                                <option value="center" {{ old('zone', $monitor->zone) == 'center' ? 'selected' : '' }}>Pusat (Center)</option>
                                <option value="lintas utara" {{ old('zone', $monitor->zone) == 'lintas utara' ? 'selected' : '' }}>Lintas Utara</option>
                                <option value="lintas selatan" {{ old('zone', $monitor->zone) == 'lintas selatan' ? 'selected' : '' }}>Lintas Selatan</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-kai-navy opacity-50 pointer-events-none text-lg"></i>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Parent Device Selection --}}
                <div class="space-y-3">
                    <label class="text-lg font-black uppercase tracking-[0.2em] text-kai-navy opacity-80">Parent Device</label>
                    <div class="relative">
                        <select name="parent_id" 
                            class="w-full appearance-none px-6 py-5 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-[#001D4B] focus:ring-4 focus:ring-blue-100 transition-all font-bold text-xl text-kai-navy cursor-pointer shadow-sm outline-none">
                            <option value="">Tidak Ada Parent (Main Device)</option>
                            @foreach($allMonitors as $device)
                                <option value="{{ $device->id }}" {{ old('parent_id', $monitor->parent_id) == $device->id ? 'selected' : '' }}>
                                    {{ $device->name }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-kai-navy opacity-50 pointer-events-none text-lg"></i>
                    </div>
                    <p class="text-sm text-gray-400 italic">Pilih parent device jika ingin menjadikan ini sebagai child/cabang.</p>
                </div>

                {{-- Actions Diperbesar --}}
                <div class="pt-12 flex flex-col md:flex-row items-center gap-8">
                    <button type="submit" class="w-full md:w-auto bg-kai-navy hover:bg-blue-900 text-white font-black px-16 py-6 rounded-2xl shadow-2xl shadow-blue-200 transition-all uppercase tracking-widest text-lg flex items-center justify-center gap-4">
                        <i class="fa-solid fa-check"></i>
                       Update Device
                    </button>
                    <a href="{{ route('monitor.index') }}" class="text-sm font-bold text-gray-400 hover:text-red-500 transition-colors uppercase tracking-[0.3em]">
                        Batalkan
                    </a>
                </div>
            </form>
        </div>
    </div>
  </div>
</body>
</html>
