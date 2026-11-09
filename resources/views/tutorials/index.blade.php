@extends('layouts.public')

@section('title', __('navigation.tutorials'))

@section('content')
@php
    $locale = app()->getLocale();
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('navigation.tutorials') }}</h1>
        <p class="mt-2 text-gray-600">{{ __('common.welcome_description') }}</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Seitenleiste mit Kategorien -->
        <aside class="lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-sm font-semibold text-gray-600 tracking-wider uppercase mb-4">{{ __('common.categories') }}</h2>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('tutorials.index', ['locale' => $locale]) }}"
                           class="{{ !request('category') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} block px-3 py-2 rounded-md text-sm">
                            {{ __('common.all') }}
                        </a>
                    </li>
                    @foreach($categories as $category)
                        @php
                            $catName = $category->getTranslationAttribute('title', $locale) ?? $category->name;
                        @endphp
                        <li>
                            <a href="{{ route('tutorials.index', ['locale' => $locale, 'category' => $category->id]) }}"
                               class="{{ request('category') == $category->id ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} block px-3 py-2 rounded-md text-sm">
                                {{ $catName }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </aside>

        <!-- Tutorial-Liste -->
        <div class="flex-1">
            @if($tutorials->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($tutorials as $tutorial)
                        @php
                            $tutTitle = $tutorial->getTranslationAttribute('title', $locale) ?? $tutorial->title;
                            $tutDesc = $tutorial->getTranslationAttribute('description', $locale) ?? $tutorial->description;
                            $tutCatName = $tutorial->category ? ($tutorial->category->getTranslationAttribute('title', $locale) ?? $tutorial->category->name) : null;
                        @endphp
                        <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow overflow-hidden">
                            @if($tutorial->thumbnail_path)
                                <div class="h-48 bg-gray-200 overflow-hidden">
                                    <img src="{{ asset($tutorial->thumbnail_path) }}" alt="{{ $tutTitle }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="h-48 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="p-6">
                                @if($tutCatName)
                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full mb-3">
                                        {{ $tutCatName }}
                                    </span>
                                @endif
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    <a href="{{ route('tutorials.show', ['locale' => $locale, 'tutorial' => $tutorial->slug]) }}" class="hover:text-blue-600">
                                        {{ $tutTitle }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600 mb-4 line-clamp-3">{{ $tutDesc }}</p>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>{{ $tutorial->steps->count() }} {{ __('common.steps') }}</span>
                                    @if($tutorial->estimated_duration_minutes)
                                        <span>~{{ $tutorial->estimated_duration_minutes }} {{ __('common.minutes') }}</span>
                                    @endif
                                    <span>{{ $tutorial->view_count }} {{ __('common.views') }}</span>
                                </div>
                                @auth
                                    @php
                                        $userProgress = \App\Models\TutorialProgress::where('user_id', auth()->id())
                                            ->where('tutorial_id', $tutorial->id)->first();
                                    @endphp
                                    @if($userProgress)
                                        <div class="mt-3">
                                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                                <span>{{ __('common.progress', []) }}</span>
                                                <span>{{ $userProgress->completed ? '100%' : '0%' }}</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $userProgress->completed ? 100 : 0 }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $tutorials->withQueryString()->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('common.no_tutorials') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('common.try_another_search') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
