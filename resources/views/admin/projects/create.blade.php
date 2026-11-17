@extends('admin.layouts.app')

@section('title', 'Neues Projekt')
@section('heading', 'Neues Projekt erstellen')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('admin.projects.store') }}" method="POST">
        @csrf

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Grunddaten</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Projektname *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug *</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" required
                           placeholder="z.B. sportverein-datteln"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('slug')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">App-Name *</label>
                    <input type="text" name="app_name" value="{{ old('app_name') }}" required
                           placeholder="z.B. Vereinsverwaltung"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('app_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vereinsname *</label>
                    <input type="text" name="club_name" value="{{ old('club_name') }}" required
                           placeholder="z.B. Sportverein Datteln"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('club_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Domain & Subdomain</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subdomain</label>
                    <input type="text" name="subdomain" value="{{ old('subdomain') }}"
                           placeholder="z.B. projekt-a"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Optional – für Projekt-Erkennung auf derselben Domain</p>
                    @error('subdomain')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Externe Domain</label>
                    <input type="text" name="domain" value="{{ old('domain') }}"
                           placeholder="z.B. projekt-a.de"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Optional – für Projekt-Erkennung auf externer Domain</p>
                    @error('domain')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Design</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Primärfarbe</label>
                    <input type="color" name="primary_color" value="{{ old('primary_color', '#1a73e8') }}"
                           class="w-full h-10 border border-gray-300 rounded-lg">
                    @error('primary_color')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sekundärfarbe</label>
                    <input type="color" name="secondary_color" value="{{ old('secondary_color') }}"
                           class="w-full h-10 border border-gray-300 rounded-lg">
                    @error('secondary_color')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo-Pfad</label>
                    <input type="text" name="logo_path" value="{{ old('logo_path') }}"
                           placeholder="z.B. storage/logos/projekt-a.png"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('logo_path')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" 
                       {{ old('is_active', true) ? 'checked' : '' }}
                       class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-700">Projekt aktivieren</label>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.projects.index') }}" 
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Abbrechen
            </a>
            <button type="submit" 
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                Projekt erstellen
            </button>
        </div>
    </form>
</div>
@endsection
