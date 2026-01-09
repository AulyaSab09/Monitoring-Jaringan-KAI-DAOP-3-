<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Device</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            @if(isset($parentDevice))
                Add Child Device to <span class="text-blue-600">{{ $parentDevice->name }}</span>
            @else
                Add New Main Device
            @endif
        </h2>

        <form action="{{ route('monitor.store') }}" method="POST" class="space-y-5">
            @csrf

            <input type="hidden" name="parent_id" value="{{ request('parent_id') }}">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Device Name</label>
                <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" placeholder="e.g. Server Ruang A">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">IP Address</label>
                <input type="text" name="ip_address" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" placeholder="192.168.x.x">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        <option value="Router">Router</option>
                        <option value="Switch">Switch</option>
                        <option value="Server">Server</option>
                        <option value="PC">PC / Client</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" name="location" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g. Cirebon">
                </div>
            </div>

            <div class="pt-4 flex gap-3">
                <a href="{{ route('monitor.index') }}" class="w-1/3 py-2.5 text-center text-gray-600 font-semibold bg-gray-100 rounded-xl hover:bg-gray-200 transition">Cancel</a>
                <button type="submit" class="w-2/3 py-2.5 text-white font-semibold bg-blue-600 rounded-xl hover:bg-blue-700 shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">Save Device</button>
            </div>
        </form>
    </div>

</body>
</html>