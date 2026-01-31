<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengaturan Aplikasi - {{ \App\Models\AppSetting::get('app_title', 'Sistem Monitoring Jaringan') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>

<body class="bg-gray-100 min-h-screen font-sans">
    <div class="max-w-3xl mx-auto py-8 px-4">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('preview') }}" class="text-gray-500 hover:text-gray-700 transition">
                    <i class="fa-solid fa-arrow-left text-xl"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Pengaturan Aplikasi</h1>
            </div>
        </div>

        {{-- Success Message --}}
        @if (session('success'))
            <div
                class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-6 flex items-center gap-2">
                <i class="fa-solid fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Settings Form --}}
        <div class="bg-white rounded-xl shadow-md p-6">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- App Title --}}
                <div class="mb-6">
                    <label for="app_title" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fa-solid fa-heading mr-2 text-blue-500"></i>
                        Judul Aplikasi
                    </label>
                    <input type="text" id="app_title" name="app_title"
                        value="{{ old('app_title', $settings['app_title']) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="Masukkan judul aplikasi" required>
                    @error('app_title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <hr class="my-6 border-gray-200">

                {{-- Sound Connect --}}
                <div class="mb-6">
                    <label for="sound_connect" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fa-solid fa-volume-high mr-2 text-green-500"></i>
                        Suara Notifikasi - Connect (MP3)
                    </label>

                    @if ($settings['sound_connect'])
                        <div class="flex items-center gap-3 mb-3 p-3 bg-green-50 rounded-lg border border-green-200">
                            <i class="fa-solid fa-music text-green-600"></i>
                            <span class="text-sm text-green-700 flex-1">File saat ini: <code
                                    class="bg-green-100 px-2 py-0.5 rounded">{{ basename($settings['sound_connect']) }}</code></span>
                            <audio controls class="h-8">
                                <source src="{{ asset('storage/' . $settings['sound_connect']) }}" type="audio/mpeg">
                            </audio>
                        </div>
                    @else
                        <div class="flex items-center gap-3 mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <i class="fa-solid fa-info-circle text-gray-500"></i>
                            <span class="text-sm text-gray-600">Menggunakan suara default: <code
                                    class="bg-gray-100 px-2 py-0.5 rounded">konek.mp3</code></span>
                        </div>
                    @endif

                    <input type="file" id="sound_connect" name="sound_connect" accept=".mp3,audio/mpeg"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                    <p class="text-gray-500 text-xs mt-1">Format: MP3, Maksimal: 5MB</p>
                    @error('sound_connect')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Sound Disconnect --}}
                <div class="mb-6">
                    <label for="sound_disconnect" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fa-solid fa-volume-xmark mr-2 text-red-500"></i>
                        Suara Notifikasi - Disconnect (MP3)
                    </label>

                    @if ($settings['sound_disconnect'])
                        <div class="flex items-center gap-3 mb-3 p-3 bg-red-50 rounded-lg border border-red-200">
                            <i class="fa-solid fa-music text-red-600"></i>
                            <span class="text-sm text-red-700 flex-1">File saat ini: <code
                                    class="bg-red-100 px-2 py-0.5 rounded">{{ basename($settings['sound_disconnect']) }}</code></span>
                            <audio controls class="h-8">
                                <source src="{{ asset('storage/' . $settings['sound_disconnect']) }}"
                                    type="audio/mpeg">
                            </audio>
                        </div>
                    @else
                        <div class="flex items-center gap-3 mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <i class="fa-solid fa-info-circle text-gray-500"></i>
                            <span class="text-sm text-gray-600">Menggunakan suara default: <code
                                    class="bg-gray-100 px-2 py-0.5 rounded">diskonek.mp3</code></span>
                        </div>
                    @endif

                    <input type="file" id="sound_disconnect" name="sound_disconnect" accept=".mp3,audio/mpeg"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                    <p class="text-gray-500 text-xs mt-1">Format: MP3, Maksimal: 5MB</p>
                    @error('sound_disconnect')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <hr class="my-6 border-gray-200">

                {{-- Submit Button --}}
                <div class="flex justify-end">
                    <button type="submit"
                        class="px-6 py-3 bg-[#001D4B] text-white rounded-lg font-bold hover:opacity-90 transition flex items-center gap-2 shadow-md">
                        <i class="fa-solid fa-save"></i>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
