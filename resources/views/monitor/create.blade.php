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

<body class="bg-white min-h-screen overflow-y-auto">
  <div class="flex flex-col lg:flex-row min-h-screen">
    
    {{-- KOLOM KIRI --}}
    <div class="hidden lg:flex lg:w-3/12 bg-[#CC5C00] relative flex-col justify-between p-10 text-white overflow-hidden">
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full"></div>
            <div class="absolute top-1/2 -left-20 w-64 h-64 bg-black/5 rounded-full"></div>
            <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-white/10 rounded-full border-4 border-white/5"></div>
        </div>

        <div class="relative z-10 flex flex-col items-center w-full mt-10">
            <img src="{{ asset('assets/images/kai_logo.png') }}" alt="KAI" class="h-[140px] w-auto mb-10 drop-shadow-2xl" /> 
            <div class="text-center mb-8 w-full">
                <h1 class="text-5xl font-black uppercase">
                    {{ request('parent_id') ? 'Tambah Anakan' : 'Tambah Device' }}                
                </h1>
            </div>
            <p class="text-lg opacity-95 font-medium leading-relaxed w-full text-justify px-1">
                @if(request('parent_id'))
                    Anda sedang menambahkan perangkat turunan (downstream). Perangkat ini akan terhubung secara hirarki dan memantau jalur distribusi dari perangkat induknya.
                @else
                    Perluas jangkauan monitoring jaringan Anda. Masukkan parameter perangkat baru untuk integrasi sistem otomatis guna memastikan performa infrastruktur digital tetap optimal.
                @endif
            </p>
        </div>

        <div class="absolute bottom-0 left-0 w-full p-0 pointer-events-none overflow-hidden" style="height: 40%;">
            <img src="{{ asset('assets/images/kereta.png') }}" alt="Visual KAI" class="w-full h-full object-contain object-bottom transform scale-150 translate-y-8 brightness-105 opacity-90 transition-none" />
        </div>
    </div>

    {{-- KOLOM KANAN --}}
    <div class="w-full lg:w-9/12 flex items-start justify-center p-8 md:p-12 bg-white min-h-full">
        <div class="w-full max-w-6xl px-10 py-10">
            
            <div class="mb-12">
                @if($errors->has('ip_address'))
                    @include('layouts.alert.notification', ['type' => 'error', 'message' => $errors->first('ip_address')])
                @endif
                
                <h2 class="text-5xl font-black text-kai-navy uppercase">Detail Perangkat</h2>
                @if(request('parent_id'))
                    @php $parent = \App\Models\Monitor::find(request('parent_id')); @endphp
                    <div class="mt-4 inline-flex items-center gap-3 px-6 py-3 bg-blue-50 border-l-4 border-blue-500 rounded-r-xl">
                        <i class="fa-solid fa-sitemap text-blue-600"></i>
                        <span class="text-blue-800 font-bold">
                            Menambahkan anakan untuk induk: 
                            <span class="uppercase underline decoration-2">{{ $parent ? $parent->name : 'Unknown Device' }}</span>
                        </span>
                    </div>
                @else
                    <p class="text-xl text-gray-400 font-medium italic mt-2">Lengkapi informasi di bawah untuk mendaftarkan unit monitoring baru.</p>
                @endif
            </div>

            <form action="{{ route('monitor.store') }}" method="POST" class="space-y-10">
                @csrf
                
                {{-- Induk Perangkat (Parent) --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-black mb-2 uppercase text-xs tracking-widest opacity-70">Induk Perangkat (Parent)</label>
                    <div class="relative">
                        <select name="parent_id" class="w-full p-5 bg-slate-50 border-2 border-slate-200 rounded-2xl font-bold text-xl text-kai-navy outline-none focus:border-blue-500 appearance-none cursor-pointer">
                            <option value="">-- Jadikan Root (Tanpa Induk) --</option>
                            @foreach($allMonitors as $p)
                                <option value="{{ $p->id }}" {{ (isset($parentDevice) && $parentDevice->id == $p->id) || old('parent_id') == $p->id || request('parent_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>

                {{-- DROP DOWN POSISI LAYOUT (PENGGANTI ANGKA MANUAL) --}}
                <div class="space-y-3">
                    <label class="text-lg font-black uppercase tracking-[0.2em] text-kai-navy opacity-80">Posisi di Dashboard (Urutan)</label>
                    <div class="relative bg-emerald-50 p-6 rounded-3xl border-2 border-emerald-100">
                        <select name="after_device_id" class="w-full appearance-none px-6 py-5 bg-white border-2 border-emerald-400 rounded-2xl font-black text-xl text-emerald-700 shadow-sm outline-none focus:ring-4 focus:ring-emerald-100 cursor-pointer">
                            <option value="first">--- Letakkan di Paling Kiri (Urutan Pertama) ---</option>
                            @foreach($allMonitors as $ref)
                                <option value="{{ $ref->id }}">
                                    Setelah {{ $ref->name }} ({{ $ref->kode_lokasi }})
                                </option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-map-location-dot absolute right-12 top-1/2 -translate-y-1/2 text-emerald-600 opacity-50 text-2xl"></i>
                        <p class="text-[11px] text-emerald-600 mt-3 font-medium italic">
                            * Pilih perangkat yang akan berada di sebelah kiri perangkat baru ini.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    {{-- Nama Perangkat --}}
                    <div class="space-y-3">
                        <label class="text-lg font-black uppercase tracking-[0.2em] text-kai-navy opacity-80">Nama Perangkat</label>
                        <input type="text" name="name" placeholder="Contoh: SW-CIREBON-01" value="{{ old('name') }}"
                              class="w-full px-6 py-5 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-[#FF7300] focus:ring-4 focus:ring-orange-100 transition-all font-bold text-2xl text-kai-navy outline-none" required>
                    </div>

                    {{-- IP Address --}}
                    <div class="space-y-3">
                        <label class="text-lg font-black uppercase tracking-[0.2em] text-kai-navy opacity-80">Alamat IP (IPv4)</label>
                        <input type="text" name="ip_address" placeholder="10.x.x.x" value="{{ old('ip_address') }}"
                             class="w-full px-6 py-5 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-[#FF7300] focus:ring-4 focus:ring-orange-100 transition-all font-bold text-2xl text-kai-navy outline-none" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-3">
                        <label class="text-lg font-black uppercase tracking-[0.2em] text-kai-navy opacity-80">Lokasi Stasiun</label>
                        <input type="text" name="location" placeholder="Stasiun Cirebon" value="{{ old('location') }}"
                               class="w-full px-6 py-5 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-[#FF7300] focus:ring-4 focus:ring-orange-100 transition-all font-bold text-2xl text-kai-navy outline-none">
                    </div>

                    <div class="space-y-3">
                        <label class="text-lg font-black uppercase tracking-[0.2em] text-kai-navy opacity-80">Kode Stasiun</label>
                        <input type="text" name="kode_lokasi" placeholder="Contoh: CN / CNP" value="{{ old('kode_lokasi') }}"
                               class="w-full px-6 py-5 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-[#FF7300] focus:ring-4 focus:ring-orange-100 transition-all font-bold text-2xl text-kai-navy outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-3">
                        <label class="text-lg font-black uppercase tracking-[0.2em] text-kai-navy opacity-80">Kategori Perangkat</label>
                        <div class="relative">
                            <select name="type" class="w-full appearance-none px-6 py-5 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-[#FF7300] focus:ring-4 focus:ring-orange-100 font-bold text-2xl text-kai-navy cursor-pointer outline-none">
                                <option value="Router">Router</option>
                                <option value="Switch">Switch</option>
                                <option value="Access Point">Access Point</option>
                                <option value="PC">PC / Client</option>
                                <option value="CCTV">CCTV</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-kai-navy opacity-50 pointer-events-none text-lg"></i>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-lg font-black uppercase tracking-[0.2em] text-kai-navy opacity-80">Zona Jalur</label>
                        <div class="relative">
                            <select name="zone" class="w-full appearance-none px-6 py-5 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-[#FF7300] focus:ring-4 focus:ring-orange-100 font-bold text-2xl text-kai-navy cursor-pointer outline-none">
                                <option value="center">Pusat (Center)</option>
                                <option value="lintas utara">Lintas Utara</option>
                                <option value="lintas selatan">Lintas Selatan</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-kai-navy opacity-50 pointer-events-none text-lg"></i>
                        </div>
                    </div>
                </div>

                <div class="pt-12 flex flex-col md:flex-row items-center gap-8">
                    <button type="submit" class="w-full md:w-auto bg-kai-orange hover:bg-orange-600 text-white font-black px-16 py-6 rounded-2xl shadow-2xl shadow-orange-200 transition-all uppercase tracking-widest text-lg flex items-center justify-center gap-4">
                       {{ request('parent_id') ? 'Hubungkan Turunan' : 'Tambahkan Device' }}
                    </button>
                    <a href="{{ route('monitor.index') }}" class="text-lg font-bold text-gray-400 hover:text-red-600 transition-colors uppercase tracking-[0.3em]">
                        Batalkan
                    </a>
                </div>
            </form>
        </div>
    </div>
  </div>
</body>
</html>