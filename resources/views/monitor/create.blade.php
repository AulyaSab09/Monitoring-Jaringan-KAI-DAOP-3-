<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tambah Device - KAI DAOP 3 Cirebon</title>
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
    <div class="hidden lg:flex lg:w-3/12 bg-[#CC5C00] relative flex-col justify-between p-10 text-white overflow-hidden">
        
        {{-- Dekorasi Lingkaran Geometric (Kesan Formal) --}}
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full"></div>
            <div class="absolute top-1/2 -left-20 w-64 h-64 bg-black/5 rounded-full"></div>
            <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-white/10 rounded-full border-4 border-white/5"></div>
        </div>

        {{-- Atas: Logo & Judul (Versi Diperbesar & Terpusat) --}}
        <div class="relative z-10 flex flex-col items-center w-full mt-10"> {{-- items-center membuat Logo & Judul berada di tengah kolom --}}
            
            {{-- 1. Logo KAI --}}
            <img src="{{ asset('assets/images/kai_logo.png') }}" 
                alt="KAI" 
                class="h-[140px] w-auto mb-10 drop-shadow-2xl" /> 

            {{-- 2. TAMBAH DEVICE --}}
            <div class="text-center mb-8 w-full">
                <h1 class="text-5xl font-black uppercase">
                    Tambah Device 
                </h1>
            </div>

            {{-- 3. Keterangan --}}
            <p class="text-lg opacity-95 font-medium leading-relaxed w-full text-justify px-1">
                Perluas jangkauan monitoring jaringan Anda. Masukkan parameter perangkat baru untuk integrasi sistem otomatis guna memastikan performa infrastruktur digital tetap optimal.
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
                <h2 class="text-5xl font-black text-kai-navy uppercase">Detail Perangkat</h2>
                <p class="text-xl text-gray-400 font-medium italic mt-2">Lengkapi informasi di bawah untuk mendaftarkan unit monitoring baru.</p>
            </div>

            <form action="{{ route('monitor.store') }}" method="POST" class="space-y-10">
                @csrf
                
                {{-- Hidden parent_id untuk menandai device sebagai turunan --}}
                @if(request('parent_id'))
                    <input type="hidden" name="parent_id" value="{{ request('parent_id') }}">
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    {{-- Nama Perangkat --}}
                    <div class="space-y-3">
                        <label class="text-lg font-black uppercase tracking-[0.2em] text-kai-navy opacity-80">Nama Perangkat</label>
                        {{-- p-4 dan text-xl membuat input terasa lebih besar dan mewah --}}
                        <input type="text" name="name" placeholder="Contoh: SW-CIREBON-01" 
                              class="w-full px-6 py-5 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-[#FF7300] focus:ring-4 focus:ring-orange-100 transition-all font-bold text-2xl text-kai-navy placeholder:font-normal placeholder:text-gray-300 shadow-sm outline-none" required>
                    </div>

                    {{-- IP Address --}}
                    <div class="space-y-3">
                        <label class="text-lg font-black uppercase tracking-[0.2em] text-kai-navy opacity-80">Alamat IP (IPv4)</label>
                        <input type="text" name="ip_address" placeholder="192.168.x.x" 
                             class="w-full px-6 py-5 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-[#FF7300] focus:ring-4 focus:ring-orange-100 transition-all font-bold text-2xl text-kai-navy placeholder:font-normal placeholder:text-gray-300 shadow-sm outline-none" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    {{-- Lokasi Stasiun --}}
                    <div class="space-y-3">
                        <label class="text-lg font-black uppercase tracking-[0.2em] text-kai-navy opacity-80">Lokasi Stasiun</label>
                        <input type="text" name="location" placeholder="Stasiun Cirebon" 
                              class="w-full px-6 py-5 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-[#FF7300] focus:ring-4 focus:ring-orange-100 transition-all font-bold text-2xl text-kai-navy placeholder:font-normal placeholder:text-gray-300 shadow-sm outline-none" required>
                    </div>

                    {{-- Kode Stasiun --}}
                    <div class="space-y-3">
                        <label class="text-lg font-black uppercase tracking-[0.2em] text-kai-navy opacity-80">Kode Stasiun</label>
                        <input type="text" name="kode_lokasi" placeholder="Contoh: CN / CNP" 
                              class="w-full px-6 py-5 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-[#FF7300] focus:ring-4 focus:ring-orange-100 transition-all font-bold text-2xl text-kai-navy placeholder:font-normal placeholder:text-gray-300 shadow-sm outline-none" required>
                    </div>
                </div>

                <div class="full-width">
                    {{-- Kategori Perangkat --}}
                    <div class="space-y-3">
                        <label class="text-lg font-black uppercase tracking-[0.2em] text-kai-navy opacity-80">Kategori Perangkat</label>
                        <div class="relative">
                            <select name="type" 
                                class="w-full appearance-none px-6 py-5 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-[#FF7300] focus:ring-4 focus:ring-orange-100 transition-all font-bold text-2xl text-kai-navy placeholder:font-normal placeholder:text-gray-300 shadow-sm outline-none transition-all font-bold text-2xl text-kai-navy cursor-pointer shadow-sm">
                                <option value="Router">Router</option>
                                <option value="Switch">Switch</option>
                                <option value="Access Point">Access Point</option>
                                <option value="PC">PC / Client</option>
                                <option value="CCTV">CCTV</option>
                            </select>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-kai-navy opacity-50 pointer-events-none text-lg"></i>
                        </div>
                    </div>

                    {{-- Zona Perangkat (ONLY for root devices, NOT for children) --}}
                    @if(!request('parent_id'))
                    <div class="space-y-3 mt-10">
                        <label class="text-lg font-black uppercase tracking-[0.2em] text-kai-navy opacity-80">Zona Jalur</label>
                        <div class="relative">
                            <select name="zone" 
                                class="w-full appearance-none px-6 py-5 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-[#FF7300] focus:ring-4 focus:ring-orange-100 transition-all font-bold text-2xl text-kai-navy placeholder:font-normal placeholder:text-gray-300 shadow-sm outline-none transition-all font-bold text-2xl text-kai-navy cursor-pointer shadow-sm">
                                <option value="center" {{ old('zone') == 'center' ? 'selected' : '' }}>Pusat (Center)</option>
                                <option value="lintas utara" {{ old('zone') == 'lintas utara' ? 'selected' : '' }}>Lintas Utara</option>
                                <option value="lintas selatan" {{ old('zone') == 'lintas selatan' ? 'selected' : '' }}>Lintas Selatan</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-kai-navy opacity-50 pointer-events-none text-lg"></i>
                        </div>
                        {{-- Error Message for Zone (if any specific error) --}}
                        @if ($errors->has('error'))
                            <p class="text-red-500 font-bold mt-2">{{ $errors->first('error') }}</p>
                        @endif
                    </div>
                    @endif
                </div>

                {{-- Actions Diperbesar --}}
                <div class="pt-12 flex flex-col md:flex-row items-center gap-8">
                    <button type="submit" class="w-full md:w-auto bg-kai-orange hover:bg-orange-600 text-white font-black px-16 py-6 rounded-2xl shadow-2xl shadow-orange-200 transition-all uppercase tracking-widest text-lg flex items-center justify-center gap-4">
                       Tambahkan Device
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