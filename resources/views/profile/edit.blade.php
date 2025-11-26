<x-app-layout>
    <x-slot name="header">
        {{ __('Pengaturan Profil') }}
    </x-slot>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Grid Layout untuk Profile Info & Password -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Kolom Kiri: Info Profil -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-lg sm:rounded-xl border-t-4 border-blue-900 dark:border-blue-700">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <!-- Kolom Kanan: Update Password -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-lg sm:rounded-xl border-t-4 border-blue-900 dark:border-blue-700">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            <!-- Baris Bawah: Delete Account -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-lg sm:rounded-xl border-t-4 border-red-600">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>