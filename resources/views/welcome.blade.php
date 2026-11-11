@extends('layouts.public')

@section('title', __('common.welcome') . ' — ' . config('app.name', 'KlickDoku'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Willkommensbereich -->
    <div class="text-center mb-16">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            {{ __('common.welcome') }}
        </h1>
        <p class="text-xl text-gray-600 mb-2">
            {{ __('common.welcome_description') }}
        </p>
        <p class="text-sm text-green-600 font-medium">
            {{ __('common.no_login_required') }}
        </p>
    </div>

    <!-- Navigationskacheln -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">

        <!-- Anleitungen Kachel -->
        <a href="{{ route('guides.index', ['locale' => app()->getLocale()]) }}"
           class="group block bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border-2 border-transparent hover:border-blue-500">
            <div class="bg-gradient-to-br from-blue-500 to-blue-700 p-8 text-white">
                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h2 class="text-2xl font-bold mb-2">
                    {{ __('common.explore_guides') }}
                </h2>
                <p class="text-blue-100">
                    {{ __('common.explore_guides_description') }}
                </p>
            </div>
            <div class="p-6 bg-blue-50">
                <span class="inline-flex items-center text-blue-600 font-semibold group-hover:underline">
                    {{ __('navigation.guides') }}
                    <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </span>
            </div>
        </a>

        <!-- Tutorials Kachel -->
        <a href="{{ route('tutorials.index', ['locale' => app()->getLocale()]) }}"
           class="group block bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border-2 border-transparent hover:border-green-500">
            <div class="bg-gradient-to-br from-green-500 to-green-700 p-8 text-white">
                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h2 class="text-2xl font-bold mb-2">
                    {{ __('common.explore_tutorials') }}
                </h2>
                <p class="text-green-100">
                    {{ __('common.explore_tutorials_description') }}
                </p>
            </div>
            <div class="p-6 bg-green-50">
                <span class="inline-flex items-center text-green-600 font-semibold group-hover:underline">
                    {{ __('navigation.tutorials') }}
                    <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </span>
            </div>
        </a>

        <!-- Projekt Kachel -->
        <a href="{{ route('pages.show', ['locale' => app()->getLocale(), 'slug' => 'ueber-das-projekt']) }}"
           class="group block bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border-2 border-transparent hover:border-purple-500">
            <div class="bg-gradient-to-br from-purple-500 to-purple-700 p-8 text-white">
                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
                <h2 class="text-2xl font-bold mb-2">
                    {{ __('common.explore_project') }}
                </h2>
                <p class="text-purple-100">
                    {{ __('common.explore_project_description') }}
                </p>
            </div>
            <div class="p-6 bg-purple-50">
                <span class="inline-flex items-center text-purple-600 font-semibold group-hover:underline">
                    {{ __('navigation.about') }}
                    <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </span>
            </div>
        </a>
    </div>

    <!-- Schnellzugriff-Hinweis -->
    <div class="bg-gray-100 rounded-xl p-6 text-center">
        <p class="text-gray-600">
            <svg class="w-5 h-5 inline mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ app()->getLocale() === 'de'
                ? 'Nutzen Sie die Suche oben oder die Navigation, um schnell zu den gewünschten Anleitungen zu gelangen.'
                : 'Use the search bar above or the navigation to quickly find the guides you need.' }}
        </p>
    </div>
</div>
@endsection
