<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tambah Device</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
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
                <i class="fa-solid fa-desktop w-5 h-5"></i>
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
                <i class="fa-solid fa-globe w-5 h-5"></i>
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
                <i class="fa-solid fa-layer-group w-5 h-5"></i>
              </span>

              <!-- select butuh padding kiri + hilangkan default arrow (optional) -->
              <select
                name="type"
                class="w-full appearance-none rounded-xl border border-gray-200 bg-white pl-11 pr-10 py-3
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="Router">Router</option>
                <option value="Switch">Switch</option>
                <option value="Access Point">Access Point</option>
                <option value="PC">PC / Client</option>
                <option value="CCTV">CCTV</option>
              </select>

              <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                <i class="fa-solid fa-chevron-down w-5 h-5"></i>
              </span>
            </div>
          </div>

          {{-- Hidden Parent ID (untuk fitur tambah cabang) --}}
          @if(isset($parentDevice) && $parentDevice)
            <input type="hidden" name="parent_id" value="{{ $parentDevice->id }}">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3">
              <p class="text-sm text-blue-700">
                <span class="font-semibold">Parent Device:</span> {{ $parentDevice->name }} ({{ $parentDevice->ip_address }})
              </p>
            </div>
          @endif

          {{-- Lokasi + Kode Lokasi --}}
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Lokasi</label>
              <div class="relative">
                <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                  <i class="fa-solid fa-location-dot w-5 h-5"></i>
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
                  <i class="fa-solid fa-tag w-5 h-5"></i>
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
              <i class="fa-solid fa-check w-5 h-5"></i>
              Simpan
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</body>
</html>
