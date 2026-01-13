<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">

        <!-- LEFT : LOGIN FORM -->
        <div class="w-full md:w-1/2 flex items-center justify-center bg-white">
            <div class="w-full max-w-md px-8">
                {{ $slot }}
            </div>
        </div>

        <!-- RIGHT : INFO / BRANDING -->
        <div class="hidden md:flex w-1/2 bg-indigo-600 text-white items-center justify-center relative">
            <div class="max-w-md px-10 text-center">
                <h2 class="text-3xl font-bold mb-4">
                    Effortlessly manage your network
                </h2>
                <!-- <p class="text-indigo-100 mb-6">
                    Login untuk mengakses sistem Monitoring Jaringan KAI DAOP 3 secara real-time dan terpusat.
                </p> -->

                <!-- Dummy preview box -->
                <!-- <div class="bg-white/10 rounded-xl p-6">
                    <p class="text-sm text-indigo-100">
                        ✔ Monitoring status jaringan  
                        <br>✔ Latency & uptime  
                        <br>✔ Dashboard terpusat
                    </p>
                </div> -->
            </div>
        </div>

    </div>
</body>
</html>
