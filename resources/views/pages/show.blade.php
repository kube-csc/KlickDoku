@extends('layouts.public')

@section('title', $page->title)
@section('meta_description', $page->meta_description)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-sm text-gray-500" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li><a href="{{ route('home') }}" class="hover:text-gray-900">Startseite</a></li>
            <li>/</li>
            <li class="text-gray-900">{{ $page->title }}</li>
        </ol>
    </nav>

    <!-- Seite Inhalt -->
    <div class="bg-white rounded-lg shadow p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ $page->title }}</h1>

        @if($page->meta_description)
            <p class="text-lg text-gray-600 mb-8">{{ $page->meta_description }}</p>
        @endif

        <div class="prose max-w-none">
            {!! nl2br(e($page->content)) !!}
        </div>

        @if($page->tags->count() > 0)
            <div class="mt-8 pt-6 border-t">
                <div class="flex flex-wrap gap-2">
                    @foreach($page->tags as $tag)
                        <span class="inline-block px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">
                            #{{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        @if($page->author)
            <div class="mt-8 pt-6 border-t flex items-center gap-3 text-sm text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>Verfasst von {{ $page->author->name }}</span>
                <span>•</span>
                <span>{{ $page->updated_at->format('d.m.Y') }}</span>
            </div>
        @endif
    </div>
</div>
@endsection
