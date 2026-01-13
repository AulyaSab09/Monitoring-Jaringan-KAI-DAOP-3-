<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>History Monitoring - KAI DAOP 3</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen font-sans p-6">

    {{-- HEADER --}}
    <div class="max-w-7xl mx-auto mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">
            Riwayat Monitoring Jaringan
        </h1>

        <a href="{{ route('preview') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
            ← Kembali ke Dashboard
        </a>
    </div>

    {{-- FILTER BAR --}}
    <div class="max-w-7xl mx-auto mb-4 flex flex-wrap gap-3 items-center">
        {{-- Filter Status --}}
        <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
            <option value="">Semua Status</option>
            <option value="up">UP</option>
            <option value="warning">WARNING</option>
            <option value="down">DOWN</option>
        </select>

        {{-- Filter Waktu --}}
        <input type="date"
               class="px-3 py-2 border border-gray-300 rounded-lg text-sm">

        {{-- Search --}}
        <input type="text"
               placeholder="Cari perangkat / IP / stasiun"
               class="px-3 py-2 border border-gray-300 rounded-lg text-sm w-64">
    </div>

    {{-- TABLE --}}
    <div class="max-w-7xl mx-auto bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Waktu</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Perangkat</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">IP Address</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Stasiun</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 text-sm text-gray-700">

                {{-- ROW 1 --}}
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">12-01-2026 19:30</td>
                    <td class="px-4 py-3 font-medium">SW-01</td>
                    <td class="px-4 py-3">192.168.1.1</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                     bg-green-100 text-green-700">
                            UP
                        </span>
                    </td>
                    <td class="px-4 py-3">Stasiun Cirebon</td>
                </tr>

                {{-- ROW 2 --}}
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">12-01-2026 19:28</td>
                    <td class="px-4 py-3 font-medium">RT-02</td>
                    <td class="px-4 py-3">192.168.1.2</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                     bg-red-100 text-red-700">
                            DOWN
                        </span>
                    </td>
                    <td class="px-4 py-3">Stasiun Jatibarang</td>
                </tr>

                {{-- ROW 3 --}}
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">12-01-2026 19:25</td>
                    <td class="px-4 py-3 font-medium">AP-03</td>
                    <td class="px-4 py-3">192.168.1.3</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                     bg-orange-100 text-orange-700">
                            WARNING
                        </span>
                    </td>
                    <td class="px-4 py-3">Stasiun Losari</td>
                </tr>

            </tbody>
        </table>
    </div>

</body>
</html>
