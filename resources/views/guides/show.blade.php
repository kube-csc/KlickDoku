@extends('layouts.public')

@section('title', ($guide->getTranslationAttribute('title', app()->getLocale()) ?? $guide->title) . ' - ' . __('navigation.guides'))
@section('meta_description', $guide->meta_description ?? $guide->getTranslationAttribute('description', app()->getLocale()) ?? $guide->description)
@section('canonical', url()->current())
@section('og_title', ($guide->getTranslationAttribute('title', app()->getLocale()) ?? $guide->title))
@section('og_description', $guide->meta_description ?? $guide->getTranslationAttribute('description', app()->getLocale()) ?? $guide->description)
@section('og_type', 'article')
@if($guide->og_image)
    @section('og_image', asset($guide->og_image))
@endif

@section('content')
@php
    $locale = app()->getLocale();
    $guideTitle = $guide->getTranslationAttribute('title', $locale) ?? $guide->title;
    $guideDesc = $guide->getTranslationAttribute('description', $locale) ?? $guide->description;
    $guideContent = $guide->getTranslationAttribute('content', $locale) ?? $guide->content;
    $guideCatName = $guide->category ? ($guide->category->getTranslationAttribute('title', $locale) ?? $guide->category->name) : null;
@endphp
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6 text-sm text-gray-500" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li><a href="{{ route('home.locale', ['locale' => $locale]) }}" class="hover:text-gray-900">{{ __('navigation.home') }}</a></li>
            <li>/</li>
            <li><a href="{{ route('guides.index', ['locale' => $locale]) }}" class="hover:text-gray-900">{{ __('navigation.guides') }}</a></li>
            <li>/</li>
            <li class="text-gray-900">{{ $guideTitle }}</li>
        </ol>
    </nav>

    <!-- Anleitung Header -->
    <div class="bg-white rounded-lg shadow p-8 mb-8">
        @if($guideCatName)
            <span class="inline-block px-3 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full mb-4">
                {{ $guideCatName }}
            </span>
        @endif
        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $guideTitle }}</h1>
        <p class="text-lg text-gray-600 mb-6">{{ $guideDesc }}</p>

        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 border-t pt-4">
            @if($guide->author)
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    {{ $guide->author->name }}
                </span>
            @endif
            <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                </svg>
                {{ $guide->steps->count() }} {{ __('common.steps') }}
            </span>
            <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                {{ $guide->view_count }} {{ __('common.views') }}
            </span>
            @if($guide->published_at)
                <span>{{ __('common.published', []) }} {{ $guide->published_at->format('d.m.Y') }}</span>
            @endif
        </div>

        @if($guide->tags->count() > 0)
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach($guide->tags as $tag)
                    <span class="inline-block px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">
                        #{{ $tag->name }}
                    </span>
                @endforeach
            </div>
        @endif

        <!-- PDF Export Button -->
        <div class="mt-6 pt-4 border-t">
            <a href="{{ route('guides.pdf', ['locale' => $locale, 'guide' => $guide->slug]) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                {{ __('common.download_pdf') }}
            </a>
        </div>
    </div>

    @if($guideContent)
        <!-- Inhalt -->
        <div class="bg-white rounded-lg shadow p-8 mb-8">
            <div class="prose max-w-none">
                {!! nl2br(e($guideContent)) !!}
            </div>
        </div>
    @endif

    <!-- Schritte -->
    <div class="space-y-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('common.steps') }}</h2>
        @foreach($guide->steps as $step)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="flex items-start gap-4 p-6">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">
                        {{ $step->step_number }}
                    </div>
                    <div class="flex-1">
                        @php
                            $stepTitle = $step->getTranslationAttribute('title', $locale) ?? $step->title;
                            $stepDesc = $step->getTranslationAttribute('description', $locale) ?? $step->description;
                            $stepTip = $step->getTranslationAttribute('tip', $locale) ?? $step->tip;
                            $stepWarning = $step->getTranslationAttribute('warning', $locale) ?? $step->warning;
                        @endphp
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $stepTitle }}</h3>
                        <p class="text-gray-600 mb-4">{{ $stepDesc }}</p>

                        @if($step->images->count() > 0)
                            <div class="space-y-3 mb-4">
                                @foreach($step->images as $image)
                                    <div class="rounded-lg overflow-hidden border">
                                        <img src="{{ asset($image->path) }}"
                                             alt="{{ $image->alt_text ?? $step->title }}"
                                             loading="lazy"
                                             class="w-full h-auto">
                                        @if($image->caption)
                                            <p class="text-sm text-gray-500 p-2 bg-gray-50 text-center">{{ $image->caption }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if($stepTip)
                            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-r">
                                <div class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-green-800">{{ __('common.tip', []) }}</p>
                                        <p class="text-sm text-green-700">{{ $stepTip }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($stepWarning)
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r mt-3">
                                <div class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-yellow-800">{{ __('common.warning', []) }}</p>
                                        <p class="text-sm text-yellow-700">{{ $stepWarning }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Kommentare -->
    <div class="bg-white rounded-lg shadow p-8 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('common.comments') }} ({{ $comments->count() }})</h2>

        @auth
            <!-- Kommentar schreiben -->
            <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold text-gray-700 mb-3">{{ __('common.write_comment') }}</h3>
                <form action="{{ route('guides.comments.store', $guide->slug) }}" method="POST">
                    @csrf
                    <textarea name="body" rows="3" placeholder="{{ __('common.write_comment') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
                    <div class="mt-3 text-right">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            {{ __('common.submit_comment') }}
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="mb-8 p-4 bg-gray-50 rounded-lg text-center">
                <p class="text-gray-600">
                    <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">Anmelden</a>
, um {{ __('common.comment') }} zu schreiben.
                </p>
            </div>
        @endauth

        @if($comments->count() > 0)
            <div class="space-y-6">
                @foreach($comments as $comment)
                    <div class="border-b pb-6 last:border-b-0">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center font-bold text-sm">
                                {{ substr($comment->user->name, 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-gray-900">{{ $comment->user->name }}</span>
                                    <span class="text-sm text-gray-500">{{ $comment->created_at->format('d.m.Y, H:i') }}</span>
                                </div>
                                <p class="text-gray-700">{{ $comment->body }}</p>

                                @auth
                                    @if(auth()->id() === $comment->user_id)
                                        <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" class="inline mt-2"
                                            onsubmit="return confirm('Kommentar wirklich löschen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-red-600 hover:underline">Löschen</button>
                                        </form>
                                    @endif
                                @endauth

                                <!-- Antworten -->
                                @if($comment->replies->count() > 0)
                                    <div class="mt-4 ml-4 space-y-3">
                                        @foreach($comment->replies as $reply)
                                            <div class="flex items-start gap-2 bg-gray-50 p-3 rounded-lg">
                                                <div class="flex-shrink-0 w-7 h-7 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center font-bold text-xs">
                                                    {{ substr($reply->user->name, 0, 1) }}
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="text-sm font-semibold text-gray-900">{{ $reply->user->name }}</span>
                                                        <span class="text-xs text-gray-500">{{ $reply->created_at->format('d.m.Y, H:i') }}</span>
                                                    </div>
                                                    <p class="text-sm text-gray-700">{{ $reply->body }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @auth
                                    <form action="{{ route('guides.comments.store', $guide->slug) }}" method="POST" class="mt-3 flex gap-2">
                                        @csrf
                                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                        <input type="text" name="body" placeholder="Antworten..."
                                            class="flex-1 px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <button type="submit" class="px-3 py-1.5 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                            Antworten
                                        </button>
                                    </form>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">{{ __('common.no_comments', []) }}</p>
        @endif
    </div>

    <!-- Zurück zu Anleitungen -->
    <div class="mb-8">
        <a href="{{ route('guides.index') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Zurück zu allen Anleitungen
        </a>
    </div>
</div>
@endsection
