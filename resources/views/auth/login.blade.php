<x-guest-layout>
    
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Selamat Datang</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Silakan masuk ke akun Anda</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email / Username Address -->
        <div class="mb-5">
            <x-input-label for="email" :value="__('Email / Username')" class="mb-1" />
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-user text-gray-400"></i>
                </div>
                <!-- Menggunakan x-text-input yang sudah diperbaiki style-nya -->
                <x-text-input id="email" class="block mt-1 w-full pl-10 py-2.5" 
                    type="text" 
                    name="email" 
                    :value="old('email')" 
                    required autofocus autocomplete="username" 
                    placeholder="Username atau Email" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mb-5">
            <div class="flex justify-between items-center mb-1">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                    <a class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 hover:underline" href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                @endif
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-lock text-gray-400"></i>
                </div>
                <x-text-input id="password" class="block mt-1 w-full pl-10 py-2.5"
                                type="password"
                                name="password"
                                required autocomplete="current-password"
                                placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mb-6">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-900 shadow-sm focus:ring-blue-900 dark:bg-gray-900 dark:border-gray-600" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Ingat saya') }}</span>
            </label>
        </div>

        <!-- Tombol Login -->
        <div>
            <button type="submit" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-blue-900 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900 transition duration-200 transform hover:scale-[1.02]">
                <i class="fa-solid fa-right-to-bracket mr-2"></i> {{ __('Masuk Sistem') }}
            </button>
        </div>
        
    </form>
</x-guest-layout>