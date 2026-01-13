<x-guest-layout>
     <!-- LOGO -->
    <div class="flex justify-center mb-6">
        <img
            src="{{ asset('assets/images/kai_logo.png') }}"
            alt="Logo KAI"
            class="h-14 object-contain"
        >
    </div>

    <!-- TITLE -->
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold text-gray-800">
            Welcome Back
        </h1>
        <p class="text-sm text-gray-500 mt-4">
            Silakan masuk untuk mengakses Sistem Monitoring Jaringan KAI DAOP 3
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember -->
        <div class="flex items-center justify-between">
            <label class="flex items-center">
                <input type="checkbox" name="remember"
                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                <span class="ms-2 text-sm text-gray-600">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-indigo-600 hover:underline"
                   href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif
        </div>

        <!-- Button -->
        <x-primary-button class="w-full justify-center py-3">
            LOG IN
        </x-primary-button>
    </form>

</x-guest-layout>
