@extends('layouts.public')

@section('title', __('navigation.guides'))

@section('content')
@php
    $locale = app()->getLocale();
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('navigation.guides') }}</h1>
        <p class="mt-2 text-gray-600">{{ __('common.welcome_description') }}</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Seitenleiste mit Kategorien -->
        <aside class="lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-sm font-semibold text-gray-600 tracking-wider uppercase mb-4">{{ __('common.categories') }}</h2>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('guides.index', ['locale' => $locale]) }}"
                           class="{{ !request('category') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} block px-3 py-2 rounded-md text-sm">
                            {{ __('common.all') }}
                        </a>
                    </li>
                    @foreach($categories as $category)
                        @php
                            $catName = $category->getTranslationAttribute('title', $locale) ?? $category->name;
                        @endphp
                        <li>
                            <a href="{{ route('guides.index', ['locale' => $locale, 'category' => $category->id]) }}"
                               class="{{ request('category') == $category->id ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:text-gray-900' }} block px-3 py-2 rounded-md text-sm">
                                {{ $catName }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </aside>

        <!-- Anleitungsliste -->
        <div class="flex-1">
            @if($guides->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($guides as $guide)
                        @php
                            $guideTitle = $guide->getTranslationAttribute('title', $locale) ?? $guide->title;
                            $guideDesc = $guide->getTranslationAttribute('description', $locale) ?? $guide->description;
                            $guideCatName = $guide->category ? ($guide->category->getTranslationAttribute('title', $locale) ?? $guide->category->name) : null;
                        @endphp
                        <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow overflow-hidden">
                            <div class="p-6">
                                @if($guideCatName)
                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full mb-3">
                                        {{ $guideCatName }}
                                    </span>
                                @endif
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    <a href="{{ route('guides.show', ['locale' => $locale, 'slug' => $guide->slug]) }}" class="hover:text-blue-600">
                                        {{ $guideTitle }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600 mb-4 line-clamp-3">{{ $guideDesc }}</p>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>{{ $guide->steps->count() }} {{ __('common.steps') }}</span>
                                    <span>{{ $guide->view_count }} {{ __('common.views') }}</span>
                                    @if($guide->author)
                                        <span>{{ __('common.author') }}: {{ $guide->author->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $guides->withQueryString()->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('common.no_guides') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('common.try_another_search') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
