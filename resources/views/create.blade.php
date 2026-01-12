<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tambah Device</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen font-sans bg-gradient-to-br from-gray-50 via-blue-50 to-gray-100">
  <div class="min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-md relative">

      {{-- Card --}}
      <div class="relative bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="mb-6">
          <h2 class="text-xl font-bold text-gray-900">Tambah Perangkat</h2>
          <p class="text-sm text-gray-500 mt-1">Masukkan detail perangkat untuk dipantau.</p>
        </div>

        <form action="{{ route('monitor.store') }}" method="POST" class="space-y-4">
          @csrf

          {{-- Nama Perangkat --}}
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Perangkat</label>
            <div class="relative">
              <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                <!-- icon device -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9.75 17h4.5M4 5h16v10H4V5z" />
                </svg>
              </span>
              <input
                type="text"
                name="name"
                placeholder="Contoh: SW-01 / Router Stasiun Malang"
                class="w-full rounded-xl border border-gray-200 bg-white pl-11 pr-4 py-3
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                required
              >
            </div>
          </div>

          {{-- IP Address --}}
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">IP Address</label>
            <div class="relative">
              <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                <!-- icon network/globe -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 21a9 9 0 100-18 9 9 0 000 18zm0 0c2.5-2.3 4-5.2 4-9s-1.5-6.7-4-9m0 18c-2.5-2.3-4-5.2-4-9s1.5-6.7 4-9m-7 9h14" />
                </svg>
              </span>
              <input
                type="text"
                name="ip_address"
                placeholder="Contoh: 192.168.1.20"
                class="w-full rounded-xl border border-gray-200 bg-white pl-11 pr-4 py-3
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                required
              >
            </div>
            <p class="text-xs text-gray-500 mt-2">Gunakan format IPv4, misal 192.168.100.36</p>
          </div>

          {{-- Tipe Perangkat --}}
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Perangkat</label>
            <div class="relative">
              <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                <!-- icon layers -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 3l9 5-9 5-9-5 9-5zm9 10l-9 5-9-5" />
                </svg>
              </span>

              <!-- select butuh padding kiri + hilangkan default arrow (optional) -->
              <select
                name="type"
                class="w-full appearance-none rounded-xl border border-gray-200 bg-white pl-11 pr-10 py-3
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="Router">Router</option>
                <option value="Switch">Switch</option>
              </select>

              <!-- custom arrow -->
              <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
              </span>
            </div>
          </div>

          {{-- Lokasi + Kode Lokasi --}}
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Lokasi</label>
              <div class="relative">
                <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                  <!-- icon map pin -->
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 21s7-4.5 7-11a7 7 0 10-14 0c0 6.5 7 11 7 11z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 10a2 2 0 100-4 2 2 0 000 4z" />
                  </svg>
                </span>
                <input
                  type="text"
                  name="location"
                  placeholder="Contoh: St. Malang"
                  class="w-full rounded-xl border border-gray-200 bg-white pl-11 pr-4 py-3
                         focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Kode Lokasi</label>
              <div class="relative">
                <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                  <!-- icon tag -->
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M7 7h.01M3 10.5V7a4 4 0 014-4h3.5L21 13.5 13.5 21 3 10.5z" />
                  </svg>
                </span>
                <input
                  type="text"
                  name="kode_lokasi"
                  placeholder="Contoh: MLG"
                  class="w-full rounded-xl border border-gray-200 bg-white pl-11 pr-4 py-3
                         focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
              </div>
            </div>
          </div>

          {{-- Actions --}}
          <div class="pt-3 flex items-center justify-end gap-3">
            <a href="{{ route('monitor.index') }}"
               class="px-4 py-2 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition">
              Batal
            </a>

            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white
                           hover:bg-blue-700 shadow-sm transition">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              Simpan
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</body>
</html>
