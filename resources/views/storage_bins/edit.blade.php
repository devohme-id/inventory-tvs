<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-yellow-500 rounded-lg shadow-lg text-white">
                <i class="fa-solid fa-pen-to-square text-xl"></i>
            </div>
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                    {{ __('Edit Lokasi Rak') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Perbarui konfigurasi atau tipe rak.</p>
            </div>
        </div>
    </x-slot>

    <x-breadcrumb :links="[
        ['label' => 'Master Data', 'url' => '#'],
        ['label' => 'Lokasi Rak', 'url' => route('storage-bins.index')],
        ['label' => 'Edit: ' . $storageBin->bin_code, 'url' => '#']
    ]" />

    <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <form action="{{ route('storage-bins.update', $storageBin->id) }}" method="POST" 
                  x-data="{ 
                      rack: '{{ $storageBin->rack }}', 
                      level: '{{ $storageBin->level }}', 
                      slot: '{{ $storageBin->slot }}' 
                  }">
                @csrf
                @method('PUT')

                <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Kode Bin (Preview)</label>
                    <input type="text" name="bin_code" 
                        x-bind:value="rack && level && slot ? `${rack}-${String(level).padStart(2, '0')}-${String(slot).padStart(2, '0')}` : '{{ $storageBin->bin_code }}'"
                        class="w-full text-xl font-mono font-bold text-center border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-gray-200 dark:bg-gray-600 dark:text-white cursor-not-allowed"
                        readonly required />
                </div>

                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <x-input-label for="rack" :value="__('Kode Rak')" />
                        <x-text-input id="rack" x-model="rack" class="block mt-1 w-full uppercase" type="text" name="rack" maxlength="5" required />
                        <x-input-error :messages="$errors->get('rack')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="level" :value="__('Level')" />
                        <x-text-input id="level" x-model="level" class="block mt-1 w-full" type="number" name="level" min="1" required />
                    </div>
                    <div>
                        <x-input-label for="slot" :value="__('Slot')" />
                        <x-text-input id="slot" x-model="slot" class="block mt-1 w-full" type="number" name="slot" min="1" required />
                    </div>
                </div>

                <div class="mb-6">
                    <x-input-label for="bin_type" :value="__('Tipe Penyimpanan')" />
                    <select id="bin_type" name="bin_type" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="Standard" {{ $storageBin->bin_type == 'Standard' ? 'selected' : '' }}>Standard</option>
                        <option value="Cold Storage" {{ $storageBin->bin_type == 'Cold Storage' ? 'selected' : '' }}>Cold Storage</option>
                        <option value="Secure" {{ $storageBin->bin_type == 'Secure' ? 'selected' : '' }}>Secure</option>
                        <option value="Bulk" {{ $storageBin->bin_type == 'Bulk' ? 'selected' : '' }}>Bulk</option>
                    </select>
                </div>

                <div class="flex items-center justify-end">
                    <a href="{{ route('storage-bins.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 underline mr-4">Batal</a>
                    <x-primary-button>
                        {{ __('Update Lokasi') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>