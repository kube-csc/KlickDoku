@extends('admin.layouts.app')

@section('title', 'Seite bearbeiten')
@section('heading', 'Seite bearbeiten')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('admin.pages.update', $page) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-lg shadow p-6 space-y-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Titel *</label>
                <input type="text" name="title" id="title" value="{{ old('title', $page->title) }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="content" class="block text-sm font-medium text-gray-700">Inhalt *</label>
                <textarea name="content" id="content" rows="12" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('content', $page->content) }}</textarea>
                @error('content')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-4">
                <div class="flex items-center">
                    <input type="checkbox" name="show_in_nav" id="show_in_nav" value="1" 
                           {{ old('show_in_nav', $page->show_in_nav) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <label for="show_in_nav" class="ml-2 block text-sm text-gray-700">In Navigation anzeigen</label>
                </div>

                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700">Reihenfolge in Navigation</label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $page->sort_order) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('sort_order')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-4">
            <a href="{{ route('admin.pages.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Abbrechen
            </a>
            <button type="submit" 
                    class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                Änderungen speichern
            </button>
        </div>
    </form>
</div>
@endsection
