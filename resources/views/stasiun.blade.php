<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Stasiun DAOP 3 Cirebon</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Instrument Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-[#1b1b18] min-h-screen p-8 font-sans">
    <div class="max-w-6xl mx-auto">
        
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
            <strong class="font-bold">Berhasil!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        <header class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-semibold mb-2 text-gray-900">Stasiun DAOP 3 Cirebon</h1>
                <p class="text-gray-500">Daftar stasiun aktif dan teknis di bawah naungan wilayah Cirebon</p>
            </div>
            <div class="flex gap-4 items-center">
                <a href="{{ route('monitor.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 text-sm font-medium transition-colors">
                    ← Kembali ke Monitor
                </a>
                <button onclick="document.getElementById('modal-tambah').classList.remove('hidden')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium shadow-sm transition-colors">
                    + Tambah Stasiun
                </button>
            </div>
        </header>

        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500 font-semibold">
                            <th class="px-6 py-4 w-16">No</th>
                            <th class="px-6 py-4">Nama Stasiun</th>
                            <th class="px-6 py-4">Kode Stasiun</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($stasiuns as $index => $s)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 text-gray-500 text-sm">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $s->nama_stasiun }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-md text-sm font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ $s->kode_stasiun }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('stasiun.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus data stasiun ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                Belum ada data stasiun. Klik "Tambah Stasiun" untuk memulai.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                <span class="text-sm text-gray-500 font-medium">
                    Total: {{ count($stasiuns) }} Stasiun Terdaftar
                </span>
            </div>
        </div>
    </div>

    <div id="modal-tambah" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
            <h2 class="text-xl font-bold mb-4 text-gray-900">Tambah Stasiun Baru</h2>
            <form action="{{ route('stasiun.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Stasiun</label>
                    <input type="text" name="nama_stasiun" placeholder="Contoh: Stasiun Cirebon" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Stasiun</label>
                    <input type="text" name="kode_stasiun" placeholder="Contoh: CN" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none uppercase" required>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modal-tambah').classList.add('hidden')" 
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Simpan Stasiun</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>