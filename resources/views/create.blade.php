<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Device</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans p-8">
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-lg border border-gray-100 p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Tambah IP Device Baru</h2>
        
        <form action="{{ route('monitor.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Device</label>
                <input type="text" name="name" placeholder="Contoh: Jakarta Router" 
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Tipe Device</label>
                <select name="type" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="Router">Router</option>
                    <option value="Switch">Switch</option>
                    <option value="Server">Server</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Lokasi / Kode (Optional)</label>
                <input type="text" name="location" placeholder="Contoh: A, B, C atau Lantai 1" 
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">IP Address</label>
                <input type="text" name="ip_address" placeholder="Contoh: 192.168.1.20" 
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('monitor.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</body>
</html>