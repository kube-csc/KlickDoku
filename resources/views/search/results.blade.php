@extends('layouts.public')

@section('title', 'Suche: ' . ($query ?? '') . ' - Ergebnisse')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Suche</h1>
        @if($query && strlen($query) >= 2)
            <p class="mt-2 text-gray-600">
                {{ $totalResults ?? 0 }} Ergebnisse für: <span class="font-semibold text-gray-900">"{{ $query }}"</span>
            </p>
        @else
            <p class="mt-2 text-gray-600">Geben Sie einen Suchbegriff ein (mindestens 2 Zeichen).</p>
        @endif
    </div>

    <!-- Suchformular mit Filtern -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form action="{{ route('search') }}" method="GET" class="space-y-4">
            <div class="flex gap-3">
                <input type="text" name="q" value="{{ $query ?? '' }}" placeholder="Suchen..."
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    Suchen
                </button>
            </div>
            <div class="flex flex-wrap gap-4">
                <!-- Typ-Filter -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Typ</label>
                    <select name="type" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">Alle Typen</option>
                        <option value="guide" {{ ($type ?? '') === 'guide' ? 'selected' : '' }}>Anleitungen</option>
                        <option value="tutorial" {{ ($type ?? '') === 'tutorial' ? 'selected' : '' }}>Tutorials</option>
                        <option value="page" {{ ($type ?? '') === 'page' ? 'selected' : '' }}>Seiten</option>
                        <option value="category" {{ ($type ?? '') === 'category' ? 'selected' : '' }}>Kategorien</option>
                    </select>
                </div>
                <!-- Kategorie-Filter -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Kategorie</label>
                    <select name="category_id" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">Alle Kategorien</option>
                        @foreach($allCategories ?? [] as $cat)
                            <option value="{{ $cat->id }}" {{ ($categoryId ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>

    @if(!$query || strlen($query) < 2)
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Suchbegriff eingeben</h3>
            <p class="mt-1 text-sm text-gray-500">Bitte geben Sie mindestens 2 Zeichen ein.</p>
        </div>
    @elseif((($guides->count() ?? 0) + ($tutorials->count() ?? 0) + ($pages->count() ?? 0) + ($categories->count() ?? 0)) === 0)
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M9 14h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Keine Ergebnisse gefunden</h3>
            <p class="mt-1 text-sm text-gray-500">Versuchen Sie es mit einem anderen Suchbegriff oder ändern Sie die Filter.</p>
        </div>
    @else
        <!-- Kategorien -->
        @if(($categories->count() ?? 0) > 0)
            <div class="mb-10">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    Kategorien ({{ $categories->count() }})
                </h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($categories as $category)
                        <a href="{{ route('search', ['q' => $query, 'category_id' => $category->id]) }}"
                           class="px-3 py-1.5 bg-amber-100 text-amber-700 rounded-full text-sm font-medium hover:bg-amber-200 transition">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Anleitungen -->
        @if(($guides->count() ?? 0) > 0)
            <div class="mb-10">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Anleitungen ({{ $guides->count() }})
                </h2>
                <div class="space-y-4">
                    @foreach($guides as $guide)
                        <div class="bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                        <a href="{{ route('guides.show', $guide->slug) }}" class="hover:text-blue-600">
                                            {{ $guide->title }}
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-2 line-clamp-2">{{ $guide->description }}</p>
                                    <div class="flex items-center gap-3 text-xs text-gray-500">
                                        @if($guide->category)
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full">{{ $guide->category->name }}</span>
                                        @endif
                                        <span>{{ $guide->view_count ?? 0 }} Aufrufe</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Tutorials -->
        @if(($tutorials->count() ?? 0) > 0)
            <div class="mb-10">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Tutorials ({{ $tutorials->count() }})
                </h2>
                <div class="space-y-4">
                    @foreach($tutorials as $tutorial)
                        <div class="bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                        <a href="{{ route('tutorials.show', $tutorial->slug) }}" class="hover:text-purple-600">
                                            {{ $tutorial->title }}
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-2 line-clamp-2">{{ $tutorial->description }}</p>
                                    <div class="flex items-center gap-3 text-xs text-gray-500">
                                        @if($tutorial->category)
                                            <span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full">{{ $tutorial->category->name }}</span>
                                        @endif
                                        @if($tutorial->estimated_duration_minutes)
                                            <span>~{{ $tutorial->estimated_duration_minutes }} Min.</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Seiten -->
        @if(($pages->count() ?? 0) > 0)
            <div class="mb-10">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Seiten ({{ $pages->count() }})
                </h2>
                <div class="space-y-4">
                    @foreach($pages as $page)
                        <div class="bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                        <a href="{{ route('pages.show', $page->slug) }}" class="hover:text-green-600">
                                            {{ $page->title }}
                                        </a>
                                    </h3>
                                    @if($page->meta_description)
                                        <p class="text-sm text-gray-600 mb-2">{{ $page->meta_description }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
