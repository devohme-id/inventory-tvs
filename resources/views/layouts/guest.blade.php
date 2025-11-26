<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TVS WMS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Custom Style untuk menyamakan dengan Dashboard -->
        <style>
            :root {
                --tvs-blue: #1e3a8a; /* Biru industrial */
            }
            .bg-tvs-blue { background-color: var(--tvs-blue); }
            .text-tvs-blue { color: var(--tvs-blue); }
            .border-tvs-blue { border-color: var(--tvs-blue); }
            .ring-tvs-blue { --tw-ring-color: var(--tvs-blue); }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-100 dark:bg-gray-900">
        
        <!-- Background Decoration (Opsional) -->
        <div class="fixed inset-0 z-0 pointer-events-none">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-tvs-blue transform -skew-y-3 origin-top-left opacity-90"></div>
        </div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative z-10 px-4">
            
            <!-- Brand / Logo Area -->
            <div class="mb-6 text-center">
                <a href="/" class="flex flex-col items-center group">
                    <div class="bg-white p-3 rounded-xl shadow-lg mb-2 group-hover:scale-105 transition transform duration-300">
                        <!-- Ganti dengan SVG Logo TVS atau Icon Gudang -->
                        <i class="fa-solid fa-warehouse text-4xl text-tvs-blue"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-white drop-shadow-md">TVS WMS</h1>
                    <p class="text-blue-100 text-sm tracking-widest uppercase font-semibold">Warehouse Management System</p>
                </a>
            </div>

            <!-- Card Content -->
            <div class="w-full sm:max-w-md mt-4 px-8 py-8 bg-white dark:bg-gray-800 shadow-2xl overflow-hidden sm:rounded-2xl border-t-4 border-tvs-blue">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center text-xs text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} TVS Motor Company Indonesia. All rights reserved.
            </div>
        </div>

        <!-- Dark Mode Script (Inline untuk mencegah FOUC) -->
        <script>
            if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </body>
</html>