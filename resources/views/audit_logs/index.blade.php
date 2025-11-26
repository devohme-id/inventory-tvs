<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-gray-600 rounded-lg shadow-lg text-white">
                <i class="fa-solid fa-clock-rotate-left text-xl"></i>
            </div>
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-white leading-tight">
                    {{ __('Laporan Audit (Activity Log)') }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Jejak rekam aktivitas pengguna dan sistem.</p>
            </div>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            
            <!-- Filter Section -->
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                <form action="{{ route('audit-logs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    
                    <!-- Search -->
                    <div>
                        <x-input-label for="search" :value="__('Cari Aktivitas')" />
                        <x-text-input id="search" name="search" value="{{ request('search') }}" class="block mt-1 w-full text-sm" placeholder="Contoh: Update Stok..." />
                    </div>

                    <!-- Filter User -->
                    <div>
                        <x-input-label for="user_id" :value="__('User')" />
                        <select id="user_id" name="user_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm">
                            <option value="">-- Semua User --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->role }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tanggal Mulai -->
                    <div>
                        <x-input-label for="start_date" :value="__('Dari Tanggal')" />
                        <x-text-input id="start_date" type="date" name="start_date" value="{{ request('start_date') }}" class="block mt-1 w-full text-sm" />
                    </div>

                    <!-- Tombol Filter -->
                    <div class="flex items-end gap-2">
                        <x-primary-button class="w-full justify-center h-[42px]">
                            <i class="fa-solid fa-filter mr-2"></i> {{ __('Filter') }}
                        </x-primary-button>
                        <a href="{{ route('audit-logs.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 h-[42px]">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">User (Pelaku)</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($logs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $log->created_at->format('d M Y H:i:s') }}
                                <br>
                                <span class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 dark:text-blue-300 font-bold text-xs">
                                            {{ substr($log->user->name ?? 'S', 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $log->user->name ?? 'System / Deleted User' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $log->user->role ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $color = match(true) {
                                        str_contains($log->action, 'Delete') || str_contains($log->action, 'Hapus') => 'text-red-600 bg-red-100',
                                        str_contains($log->action, 'Create') || str_contains($log->action, 'Tambah') => 'text-green-600 bg-green-100',
                                        str_contains($log->action, 'Update') || str_contains($log->action, 'Edit') => 'text-blue-600 bg-blue-100',
                                        default => 'text-gray-600 bg-gray-100',
                                    };
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                {{ $log->details }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                <i class="fa-solid fa-clipboard-list text-4xl mb-2 opacity-50"></i>
                                <p>Belum ada catatan aktivitas.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $logs->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>