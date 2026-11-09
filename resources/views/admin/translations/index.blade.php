@extends('admin.layouts.app')

@section('title', 'Übersetzungen')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Übersetzungen verwalten</h1>
            <p class="mt-2 text-gray-600">
                Verwalten Sie die {{ count($locales) - 1 }} zusätzliche(n) Sprache(n) für alle Inhalte.
                Standardsprache ist <strong>{{ strtoupper($defaultLocale) }}</strong>.
            </p>
        </div>

        <!-- Statistiken -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            @foreach(['guides' => 'Anleitungen', 'tutorials' => 'Tutorials', 'pages' => 'Seiten', 'categories' => 'Kategorien'] as $key => $label)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats[$key]['total'] }}</p>
                    </div>
                    <div class="text-3xl">
                        @if($key === 'guides') 📖
                        @elseif($key === 'tutorials') 🎓
                        @elseif($key === 'pages') 📄
                        @else 🏷️
                        @endif
                    </div>
                </div>
                <div class="mt-4 space-y-1">
                    @foreach($stats[$key]['translations'] as $locale => $count)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">{{ strtoupper($locale) }}</span>
                        <span class="font-medium {{ $count < $stats[$key]['total'] ? 'text-yellow-600' : 'text-green-600' }}">
                            {{ $count }}/{{ $stats[$key]['total'] }}
                        </span>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('admin.translations.show', $key) }}"
                   class="mt-4 inline-block text-sm text-indigo-600 hover:text-indigo-800">
                    Übersetzungen bearbeiten →
                </a>
            </div>
            @endforeach
        </div>

        <!-- Schnellübersicht -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Schnellübersicht</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach(['guides' => 'Anleitungen', 'tutorials' => 'Tutorials', 'pages' => 'Seiten', 'categories' => 'Kategorien'] as $key => $label)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <span class="text-2xl">
                                @if($key === 'guides') 📖
                                @elseif($key === 'tutorials') 🎓
                                @elseif($key === 'pages') 📄
                                @else 🏷️
                                @endif
                            </span>
                            <div>
                                <p class="font-medium text-gray-900">{{ $label }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ $stats[$key]['total'] }} Inhalt(e) insgesamt
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('admin.translations.show', $key) }}"
                           class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                            Verwalten
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
