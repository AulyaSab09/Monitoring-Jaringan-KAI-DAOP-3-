<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        .main-container {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.8)), 
                        url("{{ asset('assets/images/lb_kai.jpg') }}");
            background-size: cover;
            background-position: center;
            overflow: hidden;
            font-family: sans-serif;
        }

        .main-container::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: url("{{ asset('assets/images/circuit_jaringan.jpg') }}");
            background-size: cover;
            background-position: center;
            opacity: 0.3;
            pointer-events: none;
            z-index: 1;
        }

        .outer-frame {
            position: relative;
            width: 580px;
            height: 580px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .cable-animation {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 4px solid transparent;
            border-top: 4px solid #38bdf8;
            border-bottom: 4px solid #f97316;
            animation: rotate-cable 8s linear infinite;
        }

        @keyframes rotate-cable {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .login-circle {
            width: 480px;
            height: 480px;
            background: white;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px 40px;
            box-shadow: 0 0 60px rgba(0,0,0,0.6);
            z-index: 20;
        }
    </style>
</head>
<body class="main-container">
    <div class="outer-frame">
        <div class="cable-animation"></div>
        <div class="login-circle">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
