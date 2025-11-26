<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-teal-600 rounded-lg shadow-lg text-white">
                <i class="fa-solid fa-pen-to-square text-xl"></i>
            </div>
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                    {{ __('Edit Pengiriman: ') . $shipment->box_id }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Koreksi data pengiriman jika terjadi kesalahan.</p>
            </div>
        </div>
    </x-slot>

    <x-breadcrumb :links="[
        ['label' => 'Outbound', 'url' => '#'],
        ['label' => 'Riwayat Pengiriman', 'url' => route('shipments.index')],
        ['label' => 'Edit ' . $shipment->box_id, 'url' => '#']
    ]" />

    <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <!-- ... Content Form ... -->
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <form action="{{ route('shipments.update', $shipment->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <p class="text-sm text-gray-500 dark:text-gray-300 uppercase">Sales Order Terkait</p>
                    <p class="text-lg font-bold">{{ $shipment->salesorders->so_number }} - {{ $shipment->salesorders->customer }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Box ID -->
                    <div>
                        <x-input-label for="box_id" :value="__('Box ID / No. Resi')" />
                        <x-text-input id="box_id" class="block mt-1 w-full" type="text" name="box_id" :value="old('box_id', $shipment->box_id)" required />
                        <x-input-error :messages="$errors->get('box_id')" class="mt-2" />
                    </div>

                    <!-- Berat -->
                    <div>
                        <x-input-label for="total_weight_kg" :value="__('Total Berat (Kg)')" />
                        <x-text-input id="total_weight_kg" class="block mt-1 w-full" type="number" step="0.1" name="total_weight_kg" :value="old('total_weight_kg', $shipment->total_weight_kg)" required />
                        <x-input-error :messages="$errors->get('total_weight_kg')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Operator -->
                    <div>
                        <x-input-label for="operator_id" :value="__('Operator Packing')" />
                        <select id="operator_id" name="operator_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            @foreach($operators as $op)
                                <option value="{{ $op->id }}" {{ $shipment->operator_id == $op->id ? 'selected' : '' }}>{{ $op->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tanggal -->
                    <div>
                        <x-input-label for="shipped_at" :value="__('Waktu Kirim')" />
                        <x-text-input id="shipped_at" class="block mt-1 w-full" type="datetime-local" name="shipped_at" :value="\Carbon\Carbon::parse($shipment->shipped_at)->format('Y-m-d\TH:i')" required />
                    </div>
                </div>

                <div class="flex items-center justify-end border-t pt-4 dark:border-gray-700">
                    <a href="{{ route('shipments.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 underline mr-4">Batal</a>
                    <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                        {{ __('Update Data') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>