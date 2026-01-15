<x-guest-layout>
    <div class="mb-2 -mt-4">
        <img src="{{ asset('assets/images/kai_logo.png') }}" alt="Logo KAI" class="h-14 mx-auto object-contain">
    </div>

    <div class="text-center mb-6">
        <h2 class="text-lg font-bold text-slate-800 uppercase tracking-tighter leading-tight">Sistem Monitoring Jaringan</h2>
        <div class="flex flex-col text-[10px] text-gray-500 leading-normal mt-1">
            <span>Silakan login untuk mengakses dashboard monitoring</span>
            <span>KAI DAOP 3 Cirebon.</span>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-3 text-center">
            <p class="text-[10px] text-orange-600 font-bold italic uppercase">Kredensial belum lengkap atau salah.</p>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" autocomplete="off" class="w-full space-y-3 px-8">
        @csrf

        <div>
            <input id="email" 
                   autocomplete="off"
                   class="w-full rounded-md text-sm py-2 px-3 bg-gray-50 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500
                   {{ $errors->has('email') ? 'border-2 border-orange-500' : 'border-gray-300' }}" 
                   type="email" name="email" value="{{ old('email') }}" 
                   placeholder="Email" required autofocus />
            @error('email')
                <p class="text-[9px] text-orange-600 mt-1 font-semibold text-left">* Email belum lengkap atau salah isinya.</p>
            @enderror
        </div>

        <div x-data="{ show: false }" class="relative">
            <input :type="show ? 'text' : 'password'" 
                   id="password" 
                   name="password"
                   autocomplete="new-password"
                   class="w-full rounded-md text-sm py-2 px-3 bg-gray-50 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500
                   {{ $errors->has('password') ? 'border-2 border-orange-500' : 'border-gray-300' }}" 
                   placeholder="Password" required />
            
            <button type="button" 
                    @click="show = !show" 
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-orange-500">
                <template x-if="!show">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                </template>
                <template x-if="show">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.025 10.025 0 014.132-5.411m0 0L21 21m-2.102-2.102L12 12m4.839-4.839A9.968 9.968 0 0012 5c-4.478 0-8.268 2.943-9.542 7a10.025 10.025 0 014.132 5.411m4.132-5.411L12 12" /></svg>
                </template>
            </button>
            @error('password')
                <p class="text-[9px] text-orange-600 mt-1 font-semibold text-left">* Password belum lengkap isinya.</p>
            @enderror
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-[#28a745] hover:bg-[#218838] text-white font-bold py-2 rounded transition shadow-md uppercase text-sm tracking-wider">
                Log In
            </button>
        </div>
    </form>
</x-guest-layout>