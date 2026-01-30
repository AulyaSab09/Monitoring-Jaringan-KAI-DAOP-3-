<x-app-layout>
    {{-- CSS Kustom untuk mengatasi Dropdown Dobel --}}
    <style>
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
        select::-ms-expand { display: none; }

        .profile-card {
            background: #ffffff;
            border-radius: 2.5rem; /* Sesuaikan kelengkungan */
            border: 1px solid rgba(0, 29, 75, 0.08);
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.05);
            overflow: hidden; /* Wajib agar header tidak keluar dari border-radius */
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .card-header-box {
            padding: 2.5rem 3rem; /* Padding khusus di dalam header */
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .card-body-content {
            padding: 3rem; /* Padding khusus untuk isi form */
            flex-grow: 1;
        }
    </style>

    <div class="bg-gray-50 min-h-screen font-sans py-6 text-slate-700">
        <div class="max-w-[98%] mx-auto px-4">
            
            {{-- 1. HEADER SECTION --}}
            <header class="mb-20 flex justify-between items-center">
                <div class="flex items-center gap-6">
                    {{-- Logo KAI Sesuai Preview --}}
                    <img src="{{ asset('assets/images/kai_logo.png') }}" alt="KAI" class="h-24 w-auto object-contain" />
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 tracking-tight text-kai-navy">Profile Admin</h1>
                        <p class="text-base text-gray-500 font-bold uppercase tracking-widest">KAI DAOP 3 Cirebon</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    {{-- Link diarahkan ke route preview --}}
                    <a href="{{ route('preview') }}" class="flex items-center gap-3 px-8 py-3.5 bg-[#001D4B] text-white rounded-2xl hover:opacity-90 transition-all font-black text-sm shadow-xl shadow-blue-900/20 tracking-widest">
                        <i class="fa-solid fa-gauge-high text-lg"></i>
                        DASHBOARD
                    </a>
                </div>
            </header>

            <div class="max-w-[95%] mx-auto pb-20">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    
                    {{-- KARTU 1: KREDENSIAL AKUN --}}
                    <div class="profile-card">
                        {{-- HEADER CARD NAVY --}}
                        <div class="card-header-box bg-[#001D4B]">
                            <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center border border-white/20 shadow-inner">                    <i class="fa-solid fa-user-gear text-2xl text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-white uppercase tracking-tight leading-none">Kredensial Akun</h3>
                                <p class="text-[10px] text-white/60 mt-2 font-medium italic uppercase tracking-wider leading-relaxed">Kelola akun login</p>
                            </div>
                        </div>

                        {{-- KONTEN FORM --}}
                        <div class="p-8 md:p-10 bg-white flex-grow">
                            <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                                @csrf
                                @method('PATCH')

                                <div class="space-y-2">
                                    <label class="text-[15px] font-black uppercase tracking-[0.1em] text-[#001D4B] font-bold">Username Login</label>
                                    <input type="text" name="username" value="{{ old('username', Auth::user()->username) }}" 
                                        class="w-full px-5 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50/30 text-sm font-bold text-kai-navy focus:border-orange-500 transition-all outline-none" required>
                                </div>
                                
                                <div class="space-y-2">
                                    <label class="text-[15px] font-black uppercase tracking-[0.1em] text-[#001D4B] font-bold">Password Baru</label>
                                    <input type="password" name="password" placeholder="Minimal 8 karakter" 
                                        class="w-full px-5 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50/30 text-sm font-bold focus:border-orange-500 transition-all outline-none">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-[#001D4B]/40">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" placeholder="Ulangi password" 
                                        class="w-full px-5 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50/30 text-sm font-bold focus:border-orange-500 transition-all outline-none">
                                </div>

                                <div class="flex justify-end pt-4">
                                    <button type="submit" class="bg-[#FF7300] text-white px-8 py-3.5 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-orange-200 hover:-translate-y-1 transition-all flex items-center gap-2">
                                        <i class="fa-solid fa-shield-halved"></i> Simpan Akun
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- KARTU 2: KONFIGURASI DASHBOARD --}}
                    <div class="profile-card overflow-hidden border-none shadow-2xl flex flex-col h-full">
                        {{-- HEADER CARD ORANGE --}}
                        <div class="bg-[#FF7300] p-8 flex items-center gap-6 min-h-[140px]">
                            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center border border-white/30 shadow-inner shrink-0">
                                <i class="fa-solid fa-sliders text-2xl text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-white uppercase tracking-tight leading-none">Konfigurasi Sistem</h3>
                                <p class="text-[10px] text-white/70 mt-2 font-medium italic uppercase tracking-wider leading-relaxed">Kustomisasi judul dashboard dan suara notifikasi</p>
                            </div>
                        </div>

                        {{-- KONTEN FORM --}}
                        <div class="p-8 md:p-10 bg-white flex-grow">
                            <form action="" method="POST" class="space-y-8">
                                @csrf
                                @method('PATCH')

                                <div class="space-y-2">
                                    <label class="text-[15px] font-black uppercase tracking-[0.1em] text-[#001D4B] font-bold">Judul Aplikasi</label>
                                    <input type="text" name="app_title" value="Sistem Monitoring Jaringan" 
                                        class="w-full px-5 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50/30 text-sm font-bold text-kai-navy focus:border-orange-500 transition-all outline-none">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[15px] font-black uppercase tracking-[0.1em] text-[#001D4B] font-bold">Notifikasi Suara</label>
                                    <div class="relative">
                                        <select id="alert_sound_select" name="alert_sound" class="w-full appearance-none px-5 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50/30 text-sm font-bold text-kai-navy cursor-pointer outline-none">
                                            <option value="default">🔔 Default Beep</option>
                                            <option value="voice_alert">🗣️ Voice Alert</option>
                                            <option value="siren">🚨 Siren</option>
                                        </select>
                                        <i class="fa-solid fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
                                    </div>
                                    <button type="button" onclick="testAlertSound()" class="flex items-center gap-2 text-[10px] font-black text-[#FF7300] uppercase tracking-tighter mt-2">
                                        <i class="fa-solid fa-circle-play text-lg"></i> Cek Suara
                                    </button>
                                </div>

                                <div class="flex justify-end pt-12">
                                    <button type="submit" class="bg-[#001D4B] text-white px-8 py-3.5 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-blue-100 hover:-translate-y-1 transition-all flex items-center gap-2">
                                        <i class="fa-solid fa-floppy-disk"></i> Simpan Sistem
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                </div>
            </div>

    {{-- Script untuk Memutar Suara --}}
    <script>
        function testAlertSound() {
            const selectedSound = document.getElementById('alert_sound_select').value;
            let audioPath = '';

            if(selectedSound === 'default') audioPath = "{{ asset('assets/notifications/beep.mp3') }}";
            else if(selectedSound === 'voice_alert') audioPath = "{{ asset('assets/notifications/diskonek.mp3') }}";
            else if(selectedSound === 'siren') audioPath = "{{ asset('assets/notifications/siren.mp3') }}";

            const audio = new Audio(audioPath);
            audio.play().catch(e => alert("File suara tidak ditemukan atau browser memblokir autoplay."));
        }
    </script>
</x-app-layout>