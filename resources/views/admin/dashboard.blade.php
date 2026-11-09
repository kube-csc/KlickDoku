@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
<!-- Statistikkarten -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="ml-5">
                <p class="text-gray-500 text-sm">Anleitungen</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $stats['guides_published'] }} / {{ $stats['guides'] }}</p>
                <p class="text-xs text-gray-400">veröffentlicht / gesamt</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="ml-5">
                <p class="text-gray-500 text-sm">Tutorials</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $stats['tutorials_published'] }} / {{ $stats['tutorials'] }}</p>
                <p class="text-xs text-gray-400">veröffentlicht / gesamt</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
            <div class="ml-5">
                <p class="text-gray-500 text-sm">Kategorien</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $stats['categories'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <div class="ml-5">
                <p class="text-gray-500 text-sm">Kommentare</p>
                <p class="text-2xl font-semibold text-gray-800">
                    {{ $stats['comments_approved'] }}
                    @if($stats['comments_pending'] > 0)
                        <span class="text-orange-500 text-lg"> ({{ $stats['comments_pending'] }} ausstehend)</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="ml-5">
                <p class="text-gray-500 text-sm">Seiten</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $stats['pages'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </div>
            <div class="ml-5">
                <p class="text-gray-500 text-sm">Gesamtaufrufe</p>
                <p class="text-2xl font-semibold text-gray-800">{{ number_format($stats['total_views']) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-teal-500 rounded-md p-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div class="ml-5">
                <p class="text-gray-500 text-sm">Benutzer</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $stats['users'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-orange-500 rounded-md p-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
            </div>
            <div class="ml-5">
                <p class="text-gray-500 text-sm">Ø Bewertung</p>
                <p class="text-2xl font-semibold text-gray-800">{{ number_format($avgRating, 1) }} / 5</p>
            </div>
        </div>
    </div>
</div>

<!-- Zwei Spalten: Top-Inhalte & Kategorien -->
<div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Top-Anleitungen -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Top-Anleitungen (nach Aufrufen)</h3>
        @if($topGuides->count() > 0)
            <ul class="space-y-3">
                @foreach($topGuides as $i => $guide)
                    <li class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold">{{ $i + 1 }}</span>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $guide->title }}</p>
                                @if($guide->category)
                                    <p class="text-xs text-gray-500">{{ $guide->category->name }}</p>
                                @endif
                            </div>
                        </div>
                        <span class="text-sm text-gray-600">{{ number_format($guide->view_count) }} Aufrufe</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-sm text-gray-500">Noch keine Anleitungen vorhanden.</p>
        @endif
    </div>

    <!-- Kategorien -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Kategorien</h3>
        @if($categoriesWithGuides->count() > 0)
            <ul class="space-y-3">
                @foreach($categoriesWithGuides as $cat)
                    <li class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $cat->name }}</p>
                            <p class="text-xs text-gray-500">{{ $cat->guides_count }} Anleitungen</p>
                        </div>
                        <a href="{{ route('admin.categories.edit', $cat->id) }}" class="text-sm text-blue-600 hover:underline">Bearbeiten</a>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-sm text-gray-500">Noch keine Kategorien vorhanden.</p>
        @endif
    </div>
</div>

<!-- Letzte Kommentare -->
<div class="mt-6 bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Letzte Kommentare</h3>
        @if($stats['comments_pending'] > 0)
            <a href="{{ route('admin.comments.index') }}" class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-sm font-medium hover:bg-orange-200">
                {{ $stats['comments_pending'] }} ausstehend
            </a>
        @endif
    </div>
    @if($recentComments->count() > 0)
        <ul class="space-y-3">
            @foreach($recentComments as $comment)
                <li class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold">
                        {{ substr($comment->user->name ?? '?', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-sm font-medium text-gray-900">{{ $comment->user->name ?? 'Unbekannt' }}</span>
                            <span class="text-xs text-gray-500">{{ $comment->created_at->format('d.m.Y, H:i') }}</span>
                            @if($comment->status === 'pending')
                                <span class="px-1.5 py-0.5 bg-orange-100 text-orange-700 rounded text-xs">Ausstehend</span>
                            @elseif($comment->status === 'approved')
                                <span class="px-1.5 py-0.5 bg-green-100 text-green-700 rounded text-xs">Genehmigt</span>
                            @else
                                <span class="px-1.5 py-0.5 bg-red-100 text-red-700 rounded text-xs">Abgelehnt</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 truncate">{{ $comment->body }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-sm text-gray-500">Noch keine Kommentare vorhanden.</p>
    @endif
</div>

<!-- Schnellaktionen -->
<div class="mt-6 bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Schnellaktionen</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('admin.categories.create') }}" 
           class="flex items-center justify-center px-4 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Neue Kategorie
        </a>
        <a href="{{ route('admin.guides.create') }}" 
           class="flex items-center justify-center px-4 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Neue Anleitung
        </a>
        <a href="{{ route('admin.tutorials.create') }}" 
           class="flex items-center justify-center px-4 py-3 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Neues Tutorial
        </a>
        <a href="{{ route('admin.pages.create') }}" 
           class="flex items-center justify-center px-4 py-3 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Neue Seite
        </a>
    </div>
</div>
@endsection
