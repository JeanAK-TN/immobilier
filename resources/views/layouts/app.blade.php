<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        @auth
            @if (Auth::user()->isProprietaire())
                <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-50">
                    {{-- Overlay mobile --}}
                    <div x-show="sidebarOpen"
                         x-transition.opacity
                         @click="sidebarOpen = false"
                         class="fixed inset-0 z-30 bg-gray-900/40 lg:hidden"
                         style="display: none;"></div>

                    @include('layouts.sidebar-proprietaire')

                    <div class="flex min-h-screen flex-col lg:pl-64">
                        {{-- Topbar --}}
                        <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8">
                            <button @click="sidebarOpen = true"
                                    class="-ml-2 rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 lg:hidden">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>

                            @isset($header)
                                <div class="min-w-0 flex-1">
                                    {{ $header }}
                                </div>
                            @endisset

                            <x-notifications.bell
                                :count="$notificationsNonLuesCount ?? 0"
                                :recentes="$notificationsRecentes ?? collect()"
                            />
                        </header>

                        <main class="flex-1">
                            {{ $slot }}
                        </main>

                        <footer class="border-t border-slate-200 bg-white py-4 text-center text-xs text-gray-400">
                            &copy; 2026 Jean Amassongon KODIO, Lomé Business School
                        </footer>
                    </div>
                </div>
            @else
                <div class="flex min-h-screen flex-col bg-slate-50">
                    @include('layouts.navigation')

                    @isset($header)
                        <header class="bg-white border-b border-slate-200">
                            <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <main class="flex-1">
                        {{ $slot }}
                    </main>

                    <footer class="mt-auto border-t border-slate-200 bg-white py-4 text-center text-xs text-gray-400">
                        &copy; 2026 Jean Amassongon KODIO, Lomé Business School
                    </footer>
                </div>
            @endif
        @else
            <div class="flex min-h-screen flex-col bg-slate-50">
                @include('layouts.navigation')

                @isset($header)
                    <header class="bg-white border-b border-slate-200">
                        <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main class="flex-1">
                    {{ $slot }}
                </main>

                <footer class="mt-auto border-t border-slate-200 bg-white py-4 text-center text-xs text-gray-400">
                    &copy; 2026 Jean Amassongon KODIO, Lomé Business School
                </footer>
            </div>
        @endauth
    </body>
</html>
