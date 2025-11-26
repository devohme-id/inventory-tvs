<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-green-600 rounded-lg shadow-lg text-white">
                <i class="fa-solid fa-plus text-xl"></i>
            </div>
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                    {{ __('Tambah Lokasi Rak') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Daftarkan kode bin baru untuk penyimpanan.</p>
            </div>
        </div>
    </x-slot>

    <x-breadcrumb :links="[
        ['label' => 'Master Data', 'url' => '#'],
        ['label' => 'Lokasi Rak', 'url' => route('storage-bins.index')],
        ['label' => 'Buat Baru', 'url' => '#']
    ]" />

    <!-- ... Content Form (Sama seperti sebelumnya) ... -->
    <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <form action="{{ route('storage-bins.store') }}" method="POST" x-data="{ rack: '', level: '', slot: '' }">
                @csrf

                <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg border border-blue-200 dark:border-blue-700">
                    <label class="block text-sm font-bold text-blue-800 dark:text-blue-200 mb-2">Kode Bin (Preview)</label>
                    <input type="text" name="bin_code" 
                        x-bind:value="rack && level && slot ? `${rack}-${String(level).padStart(2, '0')}-${String(slot).padStart(2, '0')}` : ''"
                        class="w-full text-xl font-mono font-bold text-center border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                        placeholder="A-01-01" readonly required />
                    <p class="text-xs text-blue-600 dark:text-blue-300 mt-2">*Kode digenerate otomatis: [Rak]-[Level]-[Slot]</p>
                    <x-input-error :messages="$errors->get('bin_code')" class="mt-2" />
                </div>

                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <x-input-label for="rack" :value="__('Kode Rak (Area)')" />
                        <x-text-input id="rack" x-model="rack" class="block mt-1 w-full uppercase" type="text" name="rack" maxlength="5" :value="old('rack')" placeholder="A" required />
                        <x-input-error :messages="$errors->get('rack')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="level" :value="__('Level (Tingkat)')" />
                        <x-text-input id="level" x-model="level" class="block mt-1 w-full" type="number" name="level" min="1" :value="old('level')" placeholder="1" required />
                    </div>
                    <div>
                        <x-input-label for="slot" :value="__('Nomor Slot')" />
                        <x-text-input id="slot" x-model="slot" class="block mt-1 w-full" type="number" name="slot" min="1" :value="old('slot')" placeholder="1" required />
                    </div>
                </div>

                <div class="mb-6">
                    <x-input-label for="bin_type" :value="__('Tipe Penyimpanan')" />
                    <select id="bin_type" name="bin_type" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="Standard">Standard</option>
                        <option value="Cold Storage">Cold Storage</option>
                        <option value="Secure">Secure (Barang Mahal)</option>
                        <option value="Bulk">Bulk (Barang Besar)</option>
                    </select>
                    <x-input-error :messages="$errors->get('bin_type')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end">
                    <a href="{{ route('storage-bins.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 underline mr-4">Batal</a>
                    <x-primary-button>
                        {{ __('Simpan Lokasi') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>