<section>
    <header>
        <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
            <i class="fa-solid fa-key mr-2 text-blue-900 dark:text-blue-400"></i>
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Password Saat Ini')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-900 focus:ring-blue-900 dark:bg-gray-900 dark:text-white dark:border-gray-600" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('Password Baru')" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-900 focus:ring-blue-900 dark:bg-gray-900 dark:text-white dark:border-gray-600" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Password Baru')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-900 focus:ring-blue-900 dark:bg-gray-900 dark:text-white dark:border-gray-600" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-blue-900 hover:bg-blue-800">
                {{ __('Simpan Password') }}
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 font-bold flex items-center"
                ><i class="fa-solid fa-check mr-1"></i> {{ __('Tersimpan.') }}</p>
            @endif
        </div>
    </form>
</section>