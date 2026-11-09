<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin-Panel') - Vereinsverwaltung</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
<div class="min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 text-white flex-shrink-0">
        <div class="p-4">
            <h1 class="text-xl font-bold">Admin-Panel</h1>
        </div>
        <nav class="mt-4">
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : '' }} hover:bg-gray-700 transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m1-5h3m-4 5h3m-3-5v5m3-5v5"/>
                </svg>
                Dashboard
            </a>
            <a href="{{ route('admin.categories.index') }}" 
               class="flex items-center px-4 py-3 {{ request()->routeIs('admin.categories.*') ? 'bg-gray-700' : '' }} hover:bg-gray-700 transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Kategorien
            </a>
            <a href="{{ route('admin.guides.index') }}" 
               class="flex items-center px-4 py-3 {{ request()->routeIs('admin.guides.*') ? 'bg-gray-700' : '' }} hover:bg-gray-700 transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Anleitungen
            </a>
            <a href="{{ route('admin.tutorials.index') }}" 
               class="flex items-center px-4 py-3 {{ request()->routeIs('admin.tutorials.*') ? 'bg-gray-700' : '' }} hover:bg-gray-700 transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                Tutorials
            </a>
            <a href="{{ route('admin.pages.index') }}" 
               class="flex items-center px-4 py-3 {{ request()->routeIs('admin.pages.*') ? 'bg-gray-700' : '' }} hover:bg-gray-700 transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Seiten
            </a>
            <a href="{{ route('admin.comments.index') }}" 
               class="flex items-center px-4 py-3 {{ request()->routeIs('admin.comments.*') ? 'bg-gray-700' : '' }} hover:bg-gray-700 transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Kommentare
            </a>
            <a href="{{ route('admin.translations.index') }}" 
               class="flex items-center px-4 py-3 {{ request()->routeIs('admin.translations.*') ? 'bg-gray-700' : '' }} hover:bg-gray-700 transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.018 5H20M3 12h12l-2.018 4H20V5"/>
                </svg>
                Übersetzungen
            </a>
        </nav>
        <div class="absolute bottom-0 w-64 p-4 border-t border-gray-700">
            <a href="{{ route('home') }}" class="text-gray-400 hover:text-white text-sm flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Zur Webseite
            </a>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-white text-sm flex items-center w-full">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Abmelden
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-auto">
        <!-- Top Bar -->
        <header class="bg-white shadow">
            <div class="px-6 py-4 flex justify-between items-center">
                <h2 class="text-2xl font-semibold text-gray-800">@yield('heading', 'Dashboard')</h2>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>
</body>
</html>
