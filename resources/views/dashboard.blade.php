<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Willkommensbereich -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8 mb-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-4">
                    Willkommen zurück, {{ Auth::user()->name }}!
                </h3>
                <p class="text-gray-600 mb-6">
                    Dies ist Ihr Dashboard für die Vereinsverwaltung-Dokumentationswebsite.
                    Verwalten Sie Anleitungen, Tutorials, Seiten und Kategorien über das Admin-Panel.
                </p>
                <div class="flex space-x-4">
                    <a href="{{ route('admin.dashboard') }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Zum Admin-Panel
                    </a>
                    <a href="{{ route('home.locale', app()->getLocale()) }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Website ansehen
                    </a>
                </div>
            </div>

            <!-- Schnellzugriff-Karten -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="{{ route('admin.guides.index') }}"
                   class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 hover:shadow-2xl transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="ml-5">
                            <p class="text-gray-500 text-sm">Anleitungen</p>
                            <p class="text-lg font-semibold text-gray-800">Verwalten →</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.tutorials.index') }}"
                   class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 hover:shadow-2xl transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="ml-5">
                            <p class="text-gray-500 text-sm">Tutorials</p>
                            <p class="text-lg font-semibold text-gray-800">Verwalten →</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.pages.index') }}"
                   class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 hover:shadow-2xl transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="ml-5">
                            <p class="text-gray-500 text-sm">Seiten</p>
                            <p class="text-lg font-semibold text-gray-800">Verwalten →</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.categories.index') }}"
                   class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 hover:shadow-2xl transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <div class="ml-5">
                            <p class="text-gray-500 text-sm">Kategorien</p>
                            <p class="text-lg font-semibold text-gray-800">Verwalten →</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Nützliche Links -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mt-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Nützliche Links</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <a href="{{ route('admin.comments.index') }}"
                           class="block text-indigo-600 hover:text-indigo-800">
                            → Kommentare moderieren
                        </a>
                        <a href="{{ route('admin.translations.index') }}"
                           class="block text-indigo-600 hover:text-indigo-800">
                            → Übersetzungen verwalten
                        </a>
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('guides.index', app()->getLocale()) }}"
                           class="block text-indigo-600 hover:text-indigo-800">
                            → Anleitungen (öffentlich)
                        </a>
                        <a href="{{ route('tutorials.index', app()->getLocale()) }}"
                           class="block text-indigo-600 hover:text-indigo-800">
                            → Tutorials (öffentlich)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
