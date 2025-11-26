<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-green-600 rounded-lg shadow-lg text-white">
                <i class="fa-solid fa-plus text-xl"></i>
            </div>
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                    {{ __('Buat Invoice Baru') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Catat dokumen kedatangan barang baru.</p>
            </div>
        </div>
    </x-slot>

    <x-breadcrumb :links="[
        ['label' => 'Operasional', 'url' => '#'],
        ['label' => 'Penerimaan Barang', 'url' => route('incoming.index')],
        ['label' => 'Buat Baru', 'url' => '#']
    ]" />

    <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <form action="{{ route('incoming.store') }}" method="POST">
                @csrf

                <!-- Invoice Number -->
                <div class="mb-4">
                    <x-input-label for="invoice_number" :value="__('Nomor Invoice')" />
                    <x-text-input id="invoice_number" class="block mt-1 w-full" type="text" name="invoice_number" :value="old('invoice_number')" required autofocus placeholder="Contoh: INV-2025-001" />
                    <x-input-error :messages="$errors->get('invoice_number')" class="mt-2" />
                </div>

                <!-- Supplier -->
                <div class="mb-4">
                    <x-input-label for="supplier" :value="__('Nama Supplier')" />
                    <x-text-input id="supplier" class="block mt-1 w-full" type="text" name="supplier" :value="old('supplier')" placeholder="Contoh: PT. Astra Otoparts" />
                    <x-input-error :messages="$errors->get('supplier')" class="mt-2" />
                </div>

                <!-- Received At -->
                <div class="mb-6">
                    <x-input-label for="received_at" :value="__('Tanggal Masuk')" />
                    <x-text-input id="received_at" class="block mt-1 w-full" type="date" name="received_at" :value="old('received_at', date('Y-m-d'))" required />
                    <x-input-error :messages="$errors->get('received_at')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end">
                    <a href="{{ route('incoming.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 underline mr-4">Batal</a>
                    <x-primary-button>
                        {{ __('Simpan & Lanjut ke Item') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>