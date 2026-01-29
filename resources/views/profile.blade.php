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
            border-radius: 2rem;
            border: 1px solid rgba(0, 29, 75, 0.05);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04);
            padding: 2.5rem; 
        }
    </style>

    <div class="bg-gray-50 min-h-screen font-sans py-6 text-slate-700">
        <div class="max-w-[98%] mx-auto px-4">
            
            {{-- 1. HEADER SECTION --}}
            <header class="mb-4 flex justify-between items-center">
                <div class="flex items-center gap-6">
                    {{-- Logo KAI Sesuai Preview --}}
                    <img src="{{ asset('assets/images/kai_logo.png') }}" alt="KAI" class="h-24 w-auto object-contain" />
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 tracking-tight text-kai-navy">Profile</h1>
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

            {{-- BAGIAN 1: INFORMASI AKUN (Username & Password) --}}
            <div class="profile-card">
                <div class="flex items-center gap-4 mb-10">
                    <div class="w-12 h-12 bg-[#001D4B] rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fa-solid fa-user-gear text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-[#001D4B] uppercase tracking-tight leading-none">Kredensial Akun</h3>
                        <p class="text-[11px] text-gray-400 mt-1 font-medium italic">Kelola identitas login dan password admin</p>
                    </div>
                </div>
                
                <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-[#001D4B]/40">Username Login</label>
                        <input type="text" name="username" value="{{ old('username', Auth::user()->username) }}" 
                               class="w-full px-5 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-sm font-bold text-kai-navy focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-100 transition-all outline-none" required>
                        @error('username') <p class="text-[9px] text-red-500 font-bold uppercase mt-1 italic">* {{ $message }}</p> @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#001D4B]/40">Password Baru <span class="lowercase font-medium italic opacity-60">(Opsional)</span></label>
                            <input type="password" name="password" placeholder="Minimal 8 karakter" 
                                   class="w-full px-5 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-sm font-bold focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-100 transition-all outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#001D4B]/40">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" placeholder="Ulangi password" 
                                   class="w-full px-5 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-sm font-bold focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-100 transition-all outline-none">
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="group bg-[#FF7300] text-white px-8 py-3.5 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-orange-200 hover:-translate-y-1 transition-all duration-300 flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved"></i> Simpan Akun
                        </button>
                    </div>
                </form>
            </div>

            {{-- BAGIAN 2: PENGATURAN SISTEM --}}
            <div class="profile-card">
                <div class="flex items-center gap-4 mb-10">
                    <div class="w-12 h-12 bg-[#FF7300] rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fa-solid fa-sliders text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-[#001D4B] uppercase tracking-tight leading-none">Konfigurasi Dashboard</h3>
                        <p class="text-[11px] text-gray-400 mt-1 font-medium italic">Kustomisasi judul dan notifikasi peringatan</p>
                    </div>
                </div>

                <form action="" method="POST" class="space-y-8">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Ganti Judul Sistem --}}
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#001D4B]/40">Judul Aplikasi Utama</label>
                            <input type="text" name="app_title" value="Sistem Monitoring Jaringan" 
                                   class="w-full px-5 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-sm font-bold text-kai-navy focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-100 transition-all outline-none">
                            <p class="text-[9px] text-gray-400 italic flex items-center gap-1.5 px-1">
                                <i class="fa-solid fa-info-circle text-orange-400"></i> Judul ini tampil di header dashboard utama.
                            </p>
                        </div>

                        {{-- Ganti Sound Notifikasi --}}
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-[#001D4B]/40">Suara Peringatan Down</label>
                            <div class="relative">
                                <select id="alert_sound_select" name="alert_sound" class="w-full appearance-none px-5 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-sm font-bold text-kai-navy focus:border-[#FF7300] focus:bg-white focus:ring-4 focus:ring-orange-100 transition-all outline-none cursor-pointer">
                                    <option value="default">🔔 Default Alert (Beep)</option>
                                    <option value="voice_alert">🗣️ Voice Alert (Terputus)</option>
                                    <option value="siren">🚨 Emergency Siren</option>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-[10px]"></i>
                            </div>
                            <button type="button" onclick="testAlertSound()" class="group flex items-center gap-2 text-[10px] font-black text-[#FF7300] uppercase tracking-tighter hover:text-orange-600 transition-colors mt-2">
                                <i class="fa-solid fa-circle-play text-sm group-hover:scale-110 transition-transform"></i> Tes Suara
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-50">
                        <button type="submit" class="group bg-[#001D4B] text-white px-8 py-3.5 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-blue-100 hover:-translate-y-1 transition-all duration-300 flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Konfigurasi
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