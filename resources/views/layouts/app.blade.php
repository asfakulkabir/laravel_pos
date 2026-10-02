<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>POS System</title>
        <link rel="icon" type="image/webp" href="{{ asset('mamata_logo.webp') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="antialiased bg-slate-50 min-h-screen">
            @include('layouts.navigation')

            <main class="p-4 md:ml-64 h-auto pt-20">
                @isset($header)
                    <header class="mb-6">
                        {{ $header }}
                    </header>
                @endisset
                {{ $slot }}
            </main>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Drawer Logic
                const drawer = document.getElementById('drawer-navigation');
                if (drawer) {
                    const overlay = document.createElement('div');
                    overlay.className = 'fixed inset-0 bg-black/30 z-20 hidden transition-opacity opacity-0';
                    document.body.appendChild(overlay);
        
                    const openButtons = document.querySelectorAll('[data-drawer-toggle="drawer-navigation"]');
                    const closeButtons = document.querySelectorAll('[data-drawer-close]');
        
                    const openDrawer = () => {
                        drawer.classList.remove('-translate-x-full');
                        overlay.classList.remove('hidden');
                        setTimeout(() => overlay.classList.remove('opacity-0'), 10);
                    };
        
                    const closeDrawer = () => {
                        drawer.classList.add('-translate-x-full');
                        overlay.classList.add('opacity-0');
                        setTimeout(() => overlay.classList.add('hidden'), 300);
                    };
        
                    openButtons.forEach(btn => btn.addEventListener('click', openDrawer));
                    closeButtons.forEach(btn => btn.addEventListener('click', closeDrawer));
                    overlay.addEventListener('click', closeDrawer);
                }
        
                // Dropdown Logic
                const dropdownBtn = document.getElementById('user-menu-button');
                const dropdownMenu = document.getElementById('dropdown');
        
                if (dropdownBtn && dropdownMenu) {
                    dropdownBtn.addEventListener('click', function (e) {
                        e.stopPropagation();
                        dropdownMenu.classList.toggle('hidden');
                    });
        
                    document.addEventListener('click', function (e) {
                        if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                            dropdownMenu.classList.add('hidden');
                        }
                    });
                }
            });
        </script>
        @stack('scripts')
    </body>
</html>
