@extends('admin.layouts.app')

@section('title', 'Projekt bearbeiten')
@section('heading', 'Projekt: ' . $project->name)

@section('content')
<div class="max-w-4xl">
    <!-- Erfolgsmeldung (global, über allen Tabs) -->
    @if(session('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
        ✓ {{ session('success') }}
    </div>
    @endif

    <!-- Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <a href="#general" class="tab-link active py-4 px-1 border-b-2 border-blue-500 text-blue-600 font-medium text-sm" data-tab="general">
                Grunddaten
            </a>
            @can('editTerms', $project)
            <a href="#terms" class="tab-link py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm" data-tab="terms">
                Begriffe
            </a>
            @endcan
            @can('manageContent', $project)
            <a href="#content" class="tab-link py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm" data-tab="content">
                Inhaltsfreigabe
            </a>
            @endcan
            @can('manageScreenshots', $project)
            <a href="#screenshots" class="tab-link py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm" data-tab="screenshots">
                Screenshots
            </a>
            @endcan
            @can('manageUsers', $project)
            <a href="#users" class="tab-link py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm" data-tab="users">
                Users
            </a>
            @endcan
        </nav>
    </div>

    <!-- General Tab -->
    @can('update', $project)
    <div id="general" class="tab-content">
        <form action="{{ route('admin.projects.update', $project) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="tab" value="general">

            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Grunddaten</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Projektname *</label>
                        <input type="text" name="name" value="{{ old('name', $project->name) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug *</label>
                        <input type="text" name="slug" value="{{ old('slug', $project->slug) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('slug')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">App-Name *</label>
                        <input type="text" name="app_name" value="{{ old('app_name', $project->app_name) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('app_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vereinsname *</label>
                        <input type="text" name="club_name" value="{{ old('club_name', $project->club_name) }}" required
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
                        <input type="text" name="subdomain" value="{{ old('subdomain', $project->subdomain) }}"
                               placeholder="z.B. projekt-a"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Optional – für Projekt-Erkennung auf derselben Domain</p>
                        @error('subdomain')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Externe Domain</label>
                        <input type="text" name="domain" value="{{ old('domain', $project->domain) }}"
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
                        <input type="color" name="primary_color" value="{{ old('primary_color', $project->primary_color ?? '#1a73e8') }}"
                               class="w-full h-10 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sekundärfarbe</label>
                        <input type="color" name="secondary_color" value="{{ old('secondary_color', $project->secondary_color ?? '') }}"
                               class="w-full h-10 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Logo-Pfad</label>
                        <input type="text" name="logo_path" value="{{ old('logo_path', $project->logo_path) }}"
                               placeholder="z.B. storage/logos/projekt-a.png"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" 
                           {{ old('is_active', $project->is_active) ? 'checked' : '' }}
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
                    Speichern
                </button>
            </div>
        </form>
    </div>
    @endcan

    <!-- Terms Tab -->
    @can('editTerms', $project)
    <div id="terms" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Begriffe & Platzhalter</h3>
                <div class="space-x-2">
                    <button type="button" id="suggestTermsBtn"
                            class="px-3 py-1 bg-purple-500 text-white rounded-lg hover:bg-purple-600 text-sm transition">
                        Vorschläge laden
                    </button>
                    <button type="button" id="importTermsBtn"
                            class="px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600 text-sm transition">
                        Aus Language Files importieren
                    </button>
                </div>
            </div>

            <!-- Export/Import Aktionen -->
            <div class="flex flex-wrap gap-3 mb-4 p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600 self-center">Export:</span>
                <a href="{{ route('admin.projects.terms.export.json', $project) }}"
                   class="px-3 py-1.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm transition inline-flex items-center gap-1">
                    📄 JSON
                </a>
                <a href="{{ route('admin.projects.terms.export.csv', $project) }}"
                   class="px-3 py-1.5 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 text-sm transition inline-flex items-center gap-1">
                    📊 CSV
                </a>

                <span class="text-sm font-medium text-gray-600 self-center ml-4">Import:</span>
                <form action="{{ route('admin.projects.terms.import.json', $project) }}" method="POST" enctype="multipart/form-data" class="inline-flex items-center gap-2">
                    @csrf
                    <input type="hidden" name="tab" value="terms">
                    <label class="px-3 py-1.5 bg-orange-500 text-white rounded-lg hover:bg-orange-600 text-sm transition cursor-pointer inline-flex items-center gap-1">
                        📋 JSON
                        <input type="file" name="file" accept=".json" class="hidden" onchange="this.form.submit()">
                    </label>
                </form>
                <form action="{{ route('admin.projects.terms.import.csv', $project) }}" method="POST" enctype="multipart/form-data" class="inline-flex items-center gap-2">
                    @csrf
                    <input type="hidden" name="tab" value="terms">
                    <label class="px-3 py-1.5 bg-teal-500 text-white rounded-lg hover:bg-teal-600 text-sm transition cursor-pointer inline-flex items-center gap-1">
                        📊 CSV
                        <input type="file" name="file" accept=".csv,.txt" class="hidden" onchange="this.form.submit()">
                    </label>
                </form>
            </div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                    ✓ {{ session('success') }}
                </div>
            @endif
            @if($errors->has('file'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                    ✗ {{ $errors->first('file') }}
                </div>
            @endif

            <p class="text-sm text-gray-500 mb-4">
                Definieren Sie hier die projektspezifischen Begriffe, die die Platzhalter in der Dokumentation ersetzen.
            </p>

            <form action="{{ route('admin.projects.terms.update', $project) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="tab" value="terms">

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Platzhalter</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Beschreibung</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Deutsch</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">English</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($placeholderDefinitions as $definition)
                            @php
                                $term = $project->terms->firstWhere('placeholder_key', $definition->key);
                            @endphp
                            <tr>
                                <td class="px-4 py-2">
                                    <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $definition->key }}</code>
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-600">{{ $definition->description_de }}</td>
                                <td class="px-4 py-2">
                                    <input type="hidden" name="terms[{{ $definition->key }}][placeholder_key]" value="{{ $definition->key }}">
                                    <input type="text" name="terms[{{ $definition->key }}][value_de]" 
                                           value="{{ old('terms.' . $definition->key . '.value_de', $term?->value_de ?? $definition->default_value_de) }}"
                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="text" name="terms[{{ $definition->key }}][value_en]" 
                                           value="{{ old('terms.' . $definition->key . '.value_en', $term?->value_en ?? $definition->default_value_en) }}"
                                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end mt-4">
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        Begriffe speichern
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <!-- Content Tab -->
    @can('manageContent', $project)
    <div id="content" class="tab-content hidden">
        <!-- Guides -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Guides zuweisen</h3>
            <p class="text-sm text-gray-500 mb-4">
                Wählen Sie die Guides aus, die für dieses Projekt sichtbar sein sollen.
            </p>
            <form action="{{ route('admin.projects.guides.assign', $project) }}" method="POST">
                @csrf
                <input type="hidden" name="tab" value="content">
                <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-4">
                    @forelse(\App\Models\Guide::where('status', 'published')->orderBy('title')->get() as $guide)
                    <label class="flex items-center py-2">
                        <input type="checkbox" name="guide_ids[]" value="{{ $guide->id }}"
                               {{ $project->guides->contains($guide) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">{{ $guide->title }}</span>
                        <span class="ml-auto text-xs text-gray-400">{{ $guide->slug }}</span>
                    </label>
                    @empty
                    <p class="text-gray-500 text-sm">Keine Guides vorhanden.</p>
                    @endforelse
                </div>
                <div class="flex justify-end mt-4">
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        Guides zuweisen
                    </button>
                </div>
            </form>
        </div>

        <!-- Tutorials -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tutorials zuweisen</h3>
            <p class="text-sm text-gray-500 mb-4">
                Wählen Sie die Tutorials aus, die für dieses Projekt sichtbar sein sollen.
            </p>
            <form action="{{ route('admin.projects.tutorials.assign', $project) }}" method="POST">
                @csrf
                <input type="hidden" name="tab" value="content">
                <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-4">
                    @forelse(\App\Models\Tutorial::where('status', 'published')->orderBy('title')->get() as $tutorial)
                    <label class="flex items-center py-2">
                        <input type="checkbox" name="tutorial_ids[]" value="{{ $tutorial->id }}"
                               {{ $project->tutorials->contains($tutorial) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">{{ $tutorial->title }}</span>
                        <span class="ml-auto text-xs text-gray-400">{{ $tutorial->slug }}</span>
                    </label>
                    @empty
                    <p class="text-gray-500 text-sm">Keine Tutorials vorhanden.</p>
                    @endforelse
                </div>
                <div class="flex justify-end mt-4">
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        Tutorials zuweisen
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <!-- Screenshots Tab -->
    @can('manageScreenshots', $project)
    <div id="screenshots" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Projekt-spezifische Screenshots</h3>
            <p class="text-sm text-gray-500 mb-4">
                Laden Sie hier Screenshots hoch, die nur für dieses Projekt gelten. Sie überschreiben die Master-Screenshots.
            </p>

            <!-- Guide Screenshots -->
            <div class="mb-6">
                <h4 class="text-md font-semibold text-gray-700 mb-3">Guide-Screenshots</h4>
                <div id="guide-screenshots-list" class="space-y-4">
                    <p class="text-sm text-gray-400">Wählen Sie einen Guide aus, um die Schritte anzuzeigen.</p>
                    <select id="guide-screenshot-select" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Guide auswählen --</option>
                        @foreach($project->guides as $guide)
                        <option value="{{ $guide->id }}">{{ $guide->title }}</option>
                        @endforeach
                    </select>
                    <div id="guide-screenshot-steps" class="mt-4"></div>
                </div>
            </div>

            <!-- Tutorial Screenshots -->
            <div>
                <h4 class="text-md font-semibold text-gray-700 mb-3">Tutorial-Screenshots</h4>
                <div id="tutorial-screenshots-list" class="space-y-4">
                    <p class="text-sm text-gray-400">Wählen Sie ein Tutorial aus, um die Schritte anzuzeigen.</p>
                    <select id="tutorial-screenshot-select" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Tutorial auswählen --</option>
                        @foreach($project->tutorials as $tutorial)
                        <option value="{{ $tutorial->id }}">{{ $tutorial->title }}</option>
                        @endforeach
                    </select>
                    <div id="tutorial-screenshot-steps" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>
    @endcan

    <!-- Users Tab -->
    @can('manageUsers', $project)
    <div id="users" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Projekt-Users</h3>
            <p class="text-sm text-gray-500 mb-4">
                Weisen Sie Users diesem Projekt zu. Jeder User kann eine Rolle (Admin, Editor, Viewer) haben.
            </p>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                    ✓ {{ session('success') }}
                </div>
            @endif

            <!-- Zugewiesene Users -->
            <div class="mb-6">
                <h4 class="text-md font-semibold text-gray-700 mb-3">Zugewiesene Users</h4>
                @forelse($project->users as $user)
                <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-lg mb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-sm font-semibold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @php
                            $role = $user->pivot->role ?? 'viewer';
                        @endphp
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            {{ $role === 'admin' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $role === 'editor' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $role === 'viewer' ? 'bg-gray-100 text-gray-700' : '' }}">
                            {{ $role === 'admin' ? 'Admin' : ($role === 'editor' ? 'Editor' : 'Viewer') }}
                        </span>
                        <form action="{{ route('admin.projects.users.remove', [$project, $user]) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs" onclick="return confirm('{{ $user->name }} aus dem Projekt entfernen?')">
                                Entfernen
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 py-4">Noch keine Users diesem Projekt zugewiesen.</p>
                @endforelse
            </div>

            <!-- User hinzufügen -->
            <div>
                <h4 class="text-md font-semibold text-gray-700 mb-3">User hinzufügen</h4>
                <form action="{{ route('admin.projects.users.assign', $project) }}" method="POST">
                    @csrf
                    <input type="hidden" name="tab" value="users">
                    <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-4">
                        @forelse($allUsers as $user)
                        @php
                            $isAssigned = $project->users->contains($user);
                            $currentRole = $isAssigned ? ($project->users->find($user->id)?->pivot->role ?? 'viewer') : 'viewer';
                        @endphp
                        <div class="flex items-center py-2 {{ $isAssigned ? 'opacity-50' : '' }}">
                            <label class="flex items-center flex-1">
                                <input type="hidden" name="users[{{ $user->id }}][user_id]" value="{{ $user->id }}">
                                <input type="checkbox" name="users[{{ $user->id }}][checked]" value="1"
                                       {{ $isAssigned ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">{{ $user->name }}</span>
                                <span class="ml-1 text-xs text-gray-400">({{ $user->email }})</span>
                            </label>
                            <select name="users[{{ $user->id }}][role]" class="ml-auto text-xs px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" onclick="event.stopPropagation()">
                                <option value="viewer" {{ $currentRole === 'viewer' ? 'selected' : '' }}>Viewer</option>
                                <option value="editor" {{ $currentRole === 'editor' ? 'selected' : '' }}>Editor</option>
                                <option value="admin" {{ $currentRole === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                        @empty
                        <p class="text-gray-500 text-sm">Keine Users vorhanden.</p>
                        @endforelse
                    </div>
                    <div class="flex justify-end mt-4">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                            Users aktualisieren
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    const tabs = document.querySelectorAll('.tab-link');
    const contents = document.querySelectorAll('.tab-content');

    // Öffne den Tab aus dem Query-Parameter (nach Redirect vom Speichern)
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');
    if (activeTab) {
        tabs.forEach(t => {
            t.classList.remove('active', 'border-blue-500', 'text-blue-600');
            t.classList.add('border-transparent', 'text-gray-500');
        });
        contents.forEach(c => c.classList.add('hidden'));
        const targetTab = document.querySelector(`.tab-link[data-tab="${activeTab}"]`);
        if (targetTab) {
            targetTab.classList.add('active', 'border-blue-500', 'text-blue-600');
            targetTab.classList.remove('border-transparent', 'text-gray-500');
            const targetContent = document.getElementById(activeTab);
            if (targetContent) {
                targetContent.classList.remove('hidden');
            }
        }
        // Query-Parameter entfernen (für Back-Button)
        const newUrl = window.location.pathname;
        window.history.replaceState({}, '', newUrl);
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            tabs.forEach(t => {
                t.classList.remove('active', 'border-blue-500', 'text-blue-600');
                t.classList.add('border-transparent', 'text-gray-500');
            });
            this.classList.add('active', 'border-blue-500', 'text-blue-600');
            this.classList.remove('border-transparent', 'text-gray-500');

            contents.forEach(c => c.classList.add('hidden'));
            const targetId = this.getAttribute('href').substring(1);
            document.getElementById(targetId).classList.remove('hidden');
        });
    });

    // Suggest terms
    const suggestBtn = document.getElementById('suggestTermsBtn');
    if (suggestBtn) {
        suggestBtn.addEventListener('click', async function() {
            try {
                const response = await fetch('{{ route("admin.projects.terms.suggest", $project) }}');
                const data = await response.json();
                data.suggestions.forEach(suggestion => {
                    const deInput = document.querySelector(`input[name="terms[${suggestion.placeholder_key}][value_de]"]`);
                    const enInput = document.querySelector(`input[name="terms[${suggestion.placeholder_key}][value_en]"]`);
                    if (deInput && !suggestion.is_set) {
                        deInput.value = suggestion.value_de || '';
                    }
                    if (enInput && !suggestion.is_set) {
                        enInput.value = suggestion.value_en || '';
                    }
                });
                alert('Vorschläge geladen!');
            } catch (error) {
                console.error('Fehler beim Laden der Vorschläge:', error);
            }
        });
    }

    // Import terms from language files
    const importBtn = document.getElementById('importTermsBtn');
    if (importBtn) {
        importBtn.addEventListener('click', async function() {
            if (!confirm('Begriffe aus Language Files importieren? Bestehende Begriffe werden nicht überschrieben.')) {
                return;
            }
            try {
                const response = await fetch('{{ route("admin.projects.terms.import", $project) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ source: 'language_files' })
                });
                const data = await response.json();
                alert(data.message || 'Import abgeschlossen.');
                location.reload();
            } catch (error) {
                console.error('Fehler beim Import:', error);
            }
        });
    }

    // ========================================
    // Screenshot Management
    // ========================================
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // Guide Screenshot Upload
    const guideScreenshotSelect = document.getElementById('guide-screenshot-select');
    const guideScreenshotSteps = document.getElementById('guide-screenshot-steps');
    if (guideScreenshotSelect && guideScreenshotSteps) {
        guideScreenshotSelect.addEventListener('change', async function() {
            const guideId = this.value;
            if (!guideId) {
                guideScreenshotSteps.innerHTML = '';
                return;
            }

            guideScreenshotSteps.innerHTML = '<p class="text-sm text-gray-400">Lade Schritte...</p>';

            try {
                const response = await fetch('{{ route("admin.projects.guide-screenshots", $project) }}');
                const data = await response.json();
                const guide = data.guides?.find(g => g.id == guideId);
                if (!guide || !guide.steps || guide.steps.length === 0) {
                    guideScreenshotSteps.innerHTML = '<p class="text-sm text-gray-400">Dieser Guide hat keine Schritte.</p>';
                    return;
                }

                let html = '';
                guide.steps.forEach(step => {
                    const projectImage = step.project_guide_images?.[0] || null;
                    const masterImages = step.images || [];
                    const hasMaster = masterImages.length > 0;
                    const hasProject = !!projectImage;

                    html += `
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h5 class="text-sm font-semibold text-gray-700">Schritt ${step.step_number}: ${step.title || 'Ohne Titel'}</h5>
                                    <p class="text-xs text-gray-400 mt-1">
                                        ${hasMaster ? '✅ Master-Screenshot vorhanden' : '⚠️ Kein Master-Screenshot'}
                                        ${hasProject ? '• 🎯 Projekt-Screenshot vorhanden' : ''}
                                    </p>
                                </div>
                                ${hasProject ? `
                                    <form action="/admin/projects/{{ $project->id }}/guide-screenshots/${step.id}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="tab" value="screenshots">
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs" onclick="return confirm('Screenshot löschen?')">
                                            Löschen
                                        </button>
                                    </form>
                                ` : ''}
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                ${hasMaster ? `
                                    <div>
                                        <p class="text-xs font-medium text-gray-500 mb-1">Master-Bild (Referenz)</p>
                                        <img src="/storage/${masterImages[0].path}" alt="${masterImages[0].alt_text || ''}" class="max-w-xs rounded border border-gray-200">
                                        ${masterImages[0].caption ? `<p class="text-xs text-gray-500 mt-1">${masterImages[0].caption}</p>` : ''}
                                    </div>
                                ` : '<div></div>'}
                                ${hasProject ? `
                                    <div>
                                        <p class="text-xs font-medium text-blue-600 mb-1">Projekt-Bild (überschreibt Master)</p>
                                        <img src="/storage/${projectImage.path}" alt="${projectImage.alt_text || ''}" class="max-w-xs rounded border border-blue-200">
                                        ${projectImage.caption ? `<p class="text-xs text-gray-500 mt-1">${projectImage.caption}</p>` : ''}
                                    </div>
                                ` : '<div></div>'}
                            </div>
                            <div class="flex items-center space-x-2 flex-wrap gap-2">
                                <form action="{{ route('admin.projects.guide-screenshots.store', $project) }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-2">
                                    @csrf
                                    <input type="hidden" name="tab" value="screenshots">
                                    <input type="hidden" name="guide_id" value="${guideId}">
                                    <input type="hidden" name="step_id" value="${step.id}">
                                    <input type="file" name="image" accept="image/*" class="text-xs" required>
                                    <input type="text" name="caption" placeholder="Beschreibung" class="text-xs px-2 py-1 border border-gray-300 rounded" value="${projectImage?.caption || ''}">
                                    <button type="submit" class="px-3 py-1 bg-blue-500 text-white rounded text-xs hover:bg-blue-600">
                                        ${hasProject ? 'Ersetzen' : 'Hochladen'}
                                    </button>
                                </form>
                                <button type="button" onclick="startProjectGuideScreenshotCapture({{ $project->id }}, ${guideId}, ${step.id})" class="px-3 py-1 bg-green-500 text-white rounded text-xs hover:bg-green-600 transition">
                                    📸 Screenshot mit Bereichsauswahl
                                </button>
                            </div>
                        </div>
                    `;
                });
                guideScreenshotSteps.innerHTML = html;
            } catch (error) {
                console.error('Fehler beim Laden der Schritte:', error);
                guideScreenshotSteps.innerHTML = '<p class="text-sm text-red-500">Fehler beim Laden der Schritte.</p>';
            }
        });
    }

    // Tutorial Screenshot Upload
    const tutorialScreenshotSelect = document.getElementById('tutorial-screenshot-select');
    const tutorialScreenshotSteps = document.getElementById('tutorial-screenshot-steps');
    if (tutorialScreenshotSelect && tutorialScreenshotSteps) {
        tutorialScreenshotSelect.addEventListener('change', async function() {
            const tutorialId = this.value;
            if (!tutorialId) {
                tutorialScreenshotSteps.innerHTML = '';
                return;
            }

            tutorialScreenshotSteps.innerHTML = '<p class="text-sm text-gray-400">Lade Schritte...</p>';

            try {
                const response = await fetch('{{ route("admin.projects.tutorial-screenshots", $project) }}');
                const data = await response.json();
                const tutorial = data.tutorials?.find(t => t.id == tutorialId);
                if (!tutorial || !tutorial.steps || tutorial.steps.length === 0) {
                    tutorialScreenshotSteps.innerHTML = '<p class="text-sm text-gray-400">Dieses Tutorial hat keine Schritte.</p>';
                    return;
                }

                let html = '';
                tutorial.steps.forEach(step => {
                    const projectImage = step.project_tutorial_images?.[0] || null;
                    const hasMaster = !!step.screenshot_path;
                    const hasProject = !!projectImage;

                    html += `
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h5 class="text-sm font-semibold text-gray-700">Schritt ${step.step_number}: ${step.title || 'Ohne Titel'}</h5>
                                    <p class="text-xs text-gray-400 mt-1">
                                        ${hasMaster ? '✅ Master-Screenshot vorhanden' : '⚠️ Kein Master-Screenshot'}
                                        ${hasProject ? '• 🎯 Projekt-Screenshot vorhanden' : ''}
                                    </p>
                                </div>
                                ${hasProject ? `
                                    <form action="/admin/projects/{{ $project->id }}/tutorial-screenshots/${step.id}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="tab" value="screenshots">
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs" onclick="return confirm('Screenshot löschen?')">
                                            Löschen
                                        </button>
                                    </form>
                                ` : ''}
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                ${hasMaster ? `
                                    <div>
                                        <p class="text-xs font-medium text-gray-500 mb-1">Master-Screenshot (Referenz)</p>
                                        <img src="/storage/${step.screenshot_path}" alt="Screenshot" class="max-w-xs rounded border border-gray-200">
                                    </div>
                                ` : '<div></div>'}
                                ${hasProject ? `
                                    <div>
                                        <p class="text-xs font-medium text-blue-600 mb-1">Projekt-Screenshot (überschreibt Master)</p>
                                        <img src="/storage/${projectImage.screenshot_path}" alt="Screenshot" class="max-w-xs rounded border border-blue-200">
                                    </div>
                                ` : '<div></div>'}
                            </div>
                            <div class="flex items-center space-x-2 flex-wrap gap-2">
                                <form action="{{ route('admin.projects.tutorial-screenshots.store', $project) }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-2">
                                    @csrf
                                    <input type="hidden" name="tab" value="screenshots">
                                    <input type="hidden" name="tutorial_id" value="${tutorialId}">
                                    <input type="hidden" name="step_id" value="${step.id}">
                                    <input type="file" name="image" accept="image/*" class="text-xs" required>
                                    <button type="submit" class="px-3 py-1 bg-blue-500 text-white rounded text-xs hover:bg-blue-600">
                                        ${hasProject ? 'Ersetzen' : 'Hochladen'}
                                    </button>
                                </form>
                                <button type="button" onclick="startProjectTutorialScreenshotCapture({{ $project->id }}, ${tutorialId}, ${step.id})" class="px-3 py-1 bg-green-500 text-white rounded text-xs hover:bg-green-600 transition">
                                    📸 Screenshot mit Bereichsauswahl
                                </button>
                            </div>
                        </div>
                    `;
                });
                tutorialScreenshotSteps.innerHTML = html;
            } catch (error) {
                console.error('Fehler beim Laden der Schritte:', error);
                tutorialScreenshotSteps.innerHTML = '<p class="text-sm text-red-500">Fehler beim Laden der Schritte.</p>';
            }
        });
    }
});
</script>
@endpush
@endsection
