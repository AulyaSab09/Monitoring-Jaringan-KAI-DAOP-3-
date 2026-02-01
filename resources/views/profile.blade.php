<x-app-layout>
    {{-- CSS Kustom untuk mengatasi Dropdown Dobel --}}
    <style>
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        select::-ms-expand {
            display: none;
        }

        .profile-card {
            background: #ffffff;
            border-radius: 2.5rem;
            /* Sesuaikan kelengkungan */
            border: 1px solid rgba(0, 29, 75, 0.08);
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            /* Wajib agar header tidak keluar dari border-radius */
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .card-header-box {
            padding: 2.5rem 3rem;
            /* Padding khusus di dalam header */
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .card-body-content {
            padding: 3rem;
            /* Padding khusus untuk isi form */
            flex-grow: 1;
        }
    </style>

    <div class="bg-gray-50 min-h-screen font-sans py-6 text-slate-700">
        <div class="max-w-[98%] mx-auto px-4">

            @include('layouts.alert.session-flash')

            {{-- 1. HEADER SECTION --}}
            <header class="mb-20 flex justify-between items-center">
                <div class="flex items-center gap-6">
                    {{-- Logo KAI Sesuai Preview --}}
                    <img src="{{ asset('assets/images/kai_logo.png') }}" alt="KAI"
                        class="h-24 w-auto object-contain" />
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 tracking-tight text-kai-navy">Profile Admin</h1>
                        <p class="text-base text-gray-500 font-bold uppercase tracking-widest">KAI DAOP 3 Cirebon</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    {{-- Link diarahkan ke route preview --}}
                    <a href="{{ route('preview') }}"
                        class="flex items-center gap-3 px-8 py-3.5 bg-[#001D4B] text-white rounded-2xl hover:opacity-90 transition-all font-black text-sm shadow-xl shadow-blue-900/20 tracking-widest">
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
                            <div
                                class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center border border-white/20 shadow-inner">
                                <i class="fa-solid fa-user-gear text-2xl text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-white uppercase tracking-tight leading-none">
                                    Kredensial Akun</h3>
                                <p
                                    class="text-[10px] text-white/60 mt-2 font-medium italic uppercase tracking-wider leading-relaxed">
                                    Kelola akun login</p>
                            </div>
                        </div>

                        {{-- KONTEN FORM --}}
                        <div class="p-8 md:p-10 bg-white flex-grow">
                            <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                                @csrf
                                @method('PATCH')

                                <div class="space-y-2">
                                    <label
                                        class="text-[15px] font-black uppercase tracking-[0.1em] text-[#001D4B] font-bold">Username
                                        Login</label>
                                    <input type="text" name="username"
                                        value="{{ old('username', Auth::user()->username) }}"
                                        class="w-full px-5 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50/30 text-sm font-bold text-kai-navy focus:border-orange-500 transition-all outline-none"
                                        required>
                                </div>

                                <div class="space-y-2">
                                    <label
                                        class="text-[15px] font-black uppercase tracking-[0.1em] text-[#001D4B] font-bold">Password
                                        Baru</label>
                                    <input type="password" name="password" placeholder="Minimal 8 karakter"
                                        class="w-full px-5 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50/30 text-sm font-bold focus:border-orange-500 transition-all outline-none">
                                </div>

                                <div class="space-y-2">
                                    <label
                                        class="text-[10px] font-black uppercase tracking-[0.2em] text-[#001D4B]/40">Konfirmasi
                                        Password</label>
                                    <input type="password" name="password_confirmation" placeholder="Ulangi password"
                                        class="w-full px-5 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50/30 text-sm font-bold focus:border-orange-500 transition-all outline-none">
                                </div>

                                <div class="flex justify-end pt-4">
                                    <button type="submit"
                                        class="bg-[#FF7300] text-white px-8 py-3.5 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-orange-200 hover:-translate-y-1 transition-all flex items-center gap-2">
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
                            <div
                                class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center border border-white/30 shadow-inner shrink-0">
                                <i class="fa-solid fa-sliders text-2xl text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-white uppercase tracking-tight leading-none">
                                    Konfigurasi Sistem</h3>
                                <p
                                    class="text-[10px] text-white/70 mt-2 font-medium italic uppercase tracking-wider leading-relaxed">
                                    Kustomisasi judul dashboard dan suara notifikasi</p>
                            </div>
                        </div>

                        {{-- KONTEN FORM --}}
                        <div class="p-8 md:p-10 bg-white flex-grow">
                            <form action="{{ route('admin.settings.update') }}" method="POST"
                                enctype="multipart/form-data" class="space-y-8">
                                @csrf
                                {{-- @method('PATCH') --}} {{-- admin.settings.update is POST --}}

                                <div class="space-y-2">
                                    <label
                                        class="text-[15px] font-black uppercase tracking-[0.1em] text-[#001D4B] font-bold">Judul
                                        Aplikasi</label>
                                    <input type="text" name="app_title"
                                        value="{{ old('app_title', $settings['app_title']) }}"
                                        class="w-full px-5 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50/30 text-sm font-bold text-kai-navy focus:border-orange-500 transition-all outline-none">
                                    @error('app_title')
                                        <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Sound Connect --}}
                                <div class="space-y-2">
                                    <label
                                        class="text-[15px] font-black uppercase tracking-[0.1em] text-[#001D4B] font-bold">
                                        Suara Notifikasi - Connect
                                    </label>

                                    @if ($settings['sound_connect'])
                                        <div
                                            class="flex items-center gap-3 mb-2 p-3 bg-green-50 rounded-xl border border-green-100">
                                            <i class="fa-solid fa-music text-green-600"></i>
                                            <span
                                                class="text-xs font-bold text-green-700 flex-1 truncate">{{ basename($settings['sound_connect']) }}</span>
                                            <audio controls class="h-6 w-24">
                                                <source src="{{ asset('storage/' . $settings['sound_connect']) }}"
                                                    type="audio/mpeg">
                                            </audio>
                                        </div>
                                    @else
                                        <div
                                            class="flex items-center gap-3 mb-2 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                            <i class="fa-solid fa-info-circle text-gray-400"></i>
                                            <span class="text-xs font-bold text-gray-500">Default: konek.mp3</span>
                                        </div>
                                    @endif

                                    <input type="file" name="sound_connect" accept=".mp3,audio/mpeg"
                                        class="w-full px-5 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50/30 text-sm font-bold text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-black file:bg-[#001D4B] file:text-white hover:file:bg-opacity-90 transition outline-none">
                                    <p class="text-[#001D4B]/40 text-[10px] uppercase font-black tracking-wider mt-1">
                                        Format: MP3, Maks: 5MB</p>
                                    @error('sound_connect')
                                        <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Sound Disconnect --}}
                                <div class="space-y-2">
                                    <label
                                        class="text-[15px] font-black uppercase tracking-[0.1em] text-[#001D4B] font-bold">
                                        Suara Notifikasi - Disconnect
                                    </label>

                                    @if ($settings['sound_disconnect'])
                                        <div
                                            class="flex items-center gap-3 mb-2 p-3 bg-red-50 rounded-xl border border-red-100">
                                            <i class="fa-solid fa-music text-red-600"></i>
                                            <span
                                                class="text-xs font-bold text-red-700 flex-1 truncate">{{ basename($settings['sound_disconnect']) }}</span>
                                            <audio controls class="h-6 w-24">
                                                <source src="{{ asset('storage/' . $settings['sound_disconnect']) }}"
                                                    type="audio/mpeg">
                                            </audio>
                                        </div>
                                    @else
                                        <div
                                            class="flex items-center gap-3 mb-2 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                            <i class="fa-solid fa-info-circle text-gray-400"></i>
                                            <span class="text-xs font-bold text-gray-500">Default: diskonek.mp3</span>
                                        </div>
                                    @endif

                                    <input type="file" name="sound_disconnect" accept=".mp3,audio/mpeg"
                                        class="w-full px-5 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50/30 text-sm font-bold text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-black file:bg-[#FF7300] file:text-white hover:file:bg-opacity-90 transition outline-none">
                                    <p class="text-[#001D4B]/40 text-[10px] uppercase font-black tracking-wider mt-1">
                                        Format: MP3, Maks: 5MB</p>
                                    @error('sound_disconnect')
                                        <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex justify-end pt-8">
                                    <button type="submit"
                                        class="bg-[#001D4B] text-white px-8 py-3.5 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-blue-100 hover:-translate-y-1 transition-all flex items-center gap-2">
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

                if (selectedSound === 'default') audioPath = "{{ asset('assets/notifications/beep.mp3') }}";
                else if (selectedSound === 'voice_alert') audioPath = "{{ asset('assets/notifications/diskonek.mp3') }}";
                else if (selectedSound === 'siren') audioPath = "{{ asset('assets/notifications/siren.mp3') }}";

                const audio = new Audio(audioPath);
                audio.play().catch(e => alert("File suara tidak ditemukan atau browser memblokir autoplay."));
            }
        </script>
</x-app-layout>
