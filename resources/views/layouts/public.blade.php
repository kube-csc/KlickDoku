<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'KlickDoku'))</title>
        <meta name="description" content="@yield('meta_description', 'Anleitungen und Tutorials für KlickDoku')">

        <!-- SEO Meta Tags -->
        @hasSection('meta_robots')
            <meta name="robots" content="@yield('meta_robots')">
        @else
            <meta name="robots" content="index, follow">
        @endif

        @hasSection('canonical')
            <link rel="canonical" href="@yield('canonical')">
        @endif

        <!-- OpenGraph Meta Tags -->
        <meta property="og:site_name" content="{{ config('app.name', 'KlickDoku') }}">
        <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
        @hasSection('og_title')
            <meta property="og:title" content="@yield('og_title')">
        @else
            <meta property="og:title" content="@yield('title', config('app.name', 'KlickDoku'))">
        @endif
        @hasSection('og_description')
            <meta property="og:description" content="@yield('og_description')">
        @else
            <meta property="og:description" content="@yield('meta_description', 'Anleitungen und Tutorials für die Vereinsverwaltung')">
        @endif
        @hasSection('og_image')
            <meta property="og:image" content="@yield('og_image')">
        @endif
        @hasSection('og_type')
            <meta property="og:type" content="@yield('og_type')">
        @else
            <meta property="og:type" content="website">
        @endif
        @hasSection('canonical')
            <meta property="og:url" content="@yield('canonical')">
        @endif

        <!-- Twitter Card Meta Tags -->
        <meta name="twitter:card" content="summary_large_image">
        @hasSection('og_title')
            <meta name="twitter:title" content="@yield('og_title')">
        @else
            <meta name="twitter:title" content="@yield('title', config('app.name', 'KlickDoku'))">
        @endif
        @hasSection('og_description')
            <meta name="twitter:description" content="@yield('og_description')">
        @else
            <meta name="twitter:description" content="@yield('meta_description', 'Anleitungen und Tutorials für die Vereinsverwaltung')">
        @endif
        @hasSection('og_image')
            <meta name="twitter:image" content="@yield('og_image')">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="text-xl font-bold text-gray-800 hover:text-blue-600">
                            {{ config('app.name', 'KlickDoku') }}
                        </a>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="{{ route('guides.index', ['locale' => app()->getLocale()]) }}"
                               class="{{ request()->routeIs('guides.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                {{ __('navigation.guides') }}
                            </a>
                            <a href="{{ route('tutorials.index', ['locale' => app()->getLocale()]) }}"
                               class="{{ request()->routeIs('tutorials.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                {{ __('navigation.tutorials') }}
                            </a>
                            <a href="{{ route('pages.show', ['locale' => app()->getLocale(), 'slug' => 'ueber-uns']) }}"
                               class="border-transparent text-gray-500 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                {{ __('navigation.about') }}
                            </a>
                        </div>
                    </div>

                    <!-- Suche & Sprache -->
                    <div class="flex items-center">
                        <form action="{{ route('search', ['locale' => app()->getLocale()]) }}" method="GET" class="mr-4">
                            <div class="relative">
                                <input type="text" name="q" value="{{ request('q') }}"
                                       placeholder="{{ __('common.search_placeholder') }}"
                                       class="w-48 sm:w-64 pl-4 pr-10 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <button type="submit" class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>

                        <!-- Language Switcher -->
                        <div class="relative mr-2">
                            <div class="group relative">
                                <button type="button" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                                    </svg>
                                    {{ app()->getLocale() === 'de' ? 'DE' : 'EN' }}
                                </button>
                                <div class="hidden group-hover:block absolute right-0 mt-2 w-32 bg-white rounded-md shadow-lg border z-50">
                                    <a href="{{ request()->fullUrlWithQuery(['locale' => 'de']) }}"
                                       class="block px-4 py-2 text-sm {{ app()->getLocale() === 'de' ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }} hover:bg-gray-100 first:rounded-t-md">
                                        🇩🇪 Deutsch
                                    </a>
                                    <a href="{{ request()->fullUrlWithQuery(['locale' => 'en']) }}"
                                       class="block px-4 py-2 text-sm {{ app()->getLocale() === 'en' ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }} hover:bg-gray-100 last:rounded-b-md">
                                        🇬🇧 English
                                    </a>
                                </div>
                            </div>
                        </div>

                        @auth
                            <a href="{{ route('admin.dashboard') }}"
                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-gray-700 hover:bg-gray-800 focus:outline-none transition ease-in-out duration-150 mr-2">
                                {{ __('navigation.admin') }}
                            </a>
                            <a href="{{ route('dashboard', ['locale' => app()->getLocale()]) }}"
                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none transition ease-in-out duration-150">
                                {{ __('navigation.dashboard') }}
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-600 bg-white hover:text-blue-700 focus:outline-none transition ease-in-out duration-150">
                                {{ __('navigation.login') }}
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                   class="ml-3 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none transition ease-in-out duration-150">
                                    {{ __('navigation.register') }}
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        @if (session('status'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    {{ session('status') }}
                </div>
            </div>
        @endif

        <!-- Main Content -->
        <main class="min-h-screen">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 tracking-wider uppercase">{{ __('common.navigation') }}</h3>
                        <ul class="mt-4 space-y-2">
                            <li><a href="{{ route('guides.index', ['locale' => app()->getLocale()]) }}" class="text-sm text-gray-500 hover:text-gray-900">{{ __('navigation.guides') }}</a></li>
                            <li><a href="{{ route('tutorials.index', ['locale' => app()->getLocale()]) }}" class="text-sm text-gray-500 hover:text-gray-900">{{ __('navigation.tutorials') }}</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 tracking-wider uppercase">{{ __('common.projects') }}</h3>
                        <ul class="mt-4 space-y-2">
                            <li><a href="{{ route('guides.index', ['locale' => app()->getLocale()]) }}?category=1" class="text-sm text-gray-500 hover:text-gray-900">Vereinsverwaltung</a></li>
                            <li><a href="{{ route('guides.index', ['locale' => app()->getLocale()]) }}?category=2" class="text-sm text-gray-500 hover:text-gray-900">Webseite</a></li>
                            <li><a href="{{ route('guides.index', ['locale' => app()->getLocale()]) }}?category=3" class="text-sm text-gray-500 hover:text-gray-900">Fördermittel</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 tracking-wider uppercase">{{ __('common.about_us') }}</h3>
                        <p class="mt-4 text-sm text-gray-500">
                            {{ __('common.footer_description') }}
                        </p>
                    </div>
                </div>
                <div class="mt-8 border-t pt-8 text-center">
                    <p class="text-sm text-gray-400">&copy; {{ date('Y') }} {{ config('app.name', 'Vereinsverwaltung') }}. {{ __('common.footer_rights') }}</p>
                </div>
            </div>
        </footer>

        @livewireScripts
    </body>
</html>
