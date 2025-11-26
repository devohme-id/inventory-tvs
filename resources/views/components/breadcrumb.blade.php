@props(['links' => []])

<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse bg-white dark:bg-gray-800 px-4 py-2 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        
        <!-- Home Icon -->
        <li class="inline-flex items-center">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white transition-colors">
                <i class="fa-solid fa-house mr-2"></i>
                Dashboard
            </a>
        </li>

        @foreach($links as $link)
            <li>
                <div class="flex items-center">
                    <!-- Chevron Separator -->
                    <i class="fa-solid fa-chevron-right text-gray-400 mx-1 text-xs"></i>
                    
                    @if(!$loop->last)
                        <!-- Link Aktif (Intermediate) -->
                        <a href="{{ $link['url'] ?? '#' }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white transition-colors">
                            {{ $link['label'] }}
                        </a>
                    @else
                        <!-- Halaman Saat Ini (Disabled/Bold) -->
                        <span class="ms-1 text-sm font-bold text-blue-600 md:ms-2 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-2 py-0.5 rounded">
                            {{ $link['label'] }}
                        </span>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</nav>