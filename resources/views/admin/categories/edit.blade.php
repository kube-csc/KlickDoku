@extends('admin.layouts.app')

@section('title', 'Kategorie bearbeiten')
@section('heading', 'Kategorie bearbeiten')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-lg shadow p-6 space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="description" class="block text-sm font-medium text-gray-700">Beschreibung</label>
                    <button type="button" id="aiGenerateDesc"
                            class="text-xs px-2 py-1 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition"
                            title="Beschreibung automatisch generieren">
                        ✨ KI generieren
                    </button>
                </div>
                <textarea name="description" id="description" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $category->description) }}</textarea>
                @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <!-- Zusätzlicher Kontext für KI-Generierung -->
                <div class="mt-3">
                    <button type="button" id="toggleAiContext" class="text-xs text-indigo-600 hover:text-indigo-800 underline">
                        + Zusätzlicher Kontext für KI (optional)
                    </button>
                    <div id="aiContextPanel" class="hidden mt-2 p-3 bg-indigo-50 rounded-lg space-y-3">
                        <p class="text-xs text-gray-600">Geben Sie Schlüsselbegriffe oder eine eigene Anweisung ein, damit die KI eine passendere Beschreibung erstellt.</p>
                        <div>
                            <label for="aiKeywords" class="block text-xs font-medium text-gray-700">Schlüsselbegriffe</label>
                            <input type="text" id="aiKeywords" placeholder="z.B. Server, Netzwerk, Linux (kommagetrennt)"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="aiCustomPrompt" class="block text-xs font-medium text-gray-700">Eigene Anweisung für die KI</label>
                            <textarea id="aiCustomPrompt" rows="2" placeholder="z.B. Kurz und technisch formulieren"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label for="parent_id" class="block text-sm font-medium text-gray-700">Übergeordnete Kategorie</label>
                <select name="parent_id" id="parent_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Keine (Hauptkategorie)</option>
                    @foreach($parents as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                        {{ $parent->name }}
                    </option>
                    @endforeach
                </select>
                @error('parent_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700">Reihenfolge</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $category->sort_order) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('sort_order')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-4">
            <a href="{{ route('admin.categories.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Abbrechen
            </a>
            <button type="submit" 
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                Änderungen speichern
            </button>
        </div>
    </form>
</div>

<script>
// KI-Kontext-Panel ein-/ausblenden
document.getElementById('toggleAiContext').addEventListener('click', function() {
    const panel = document.getElementById('aiContextPanel');
    panel.classList.toggle('hidden');
    this.textContent = panel.classList.contains('hidden')
        ? '+ Zusätzlicher Kontext für KI (optional)'
        : '- Zusätzlicher Kontext für KI (optional)';
});

// KI-Beschreibung generieren
document.getElementById('aiGenerateDesc').addEventListener('click', async function() {
    const title = document.getElementById('name').value.trim();
    if (!title) {
        alert('Bitte geben Sie zuerst einen Namen ein.');
        return;
    }

    // Schlüsselbegriffe aus dem Kontext-Panel lesen (kommagetrennt)
    const keywordsInput = document.getElementById('aiKeywords').value.trim();
    const keywords = keywordsInput
        ? keywordsInput.split(',').map(k => k.trim()).filter(k => k.length > 0)
        : [];

    // Benutzerdefinierter Prompt aus dem Kontext-Panel lesen
    const customPrompt = document.getElementById('aiCustomPrompt').value.trim();

    const btn = this;
    btn.disabled = true;
    btn.textContent = '⏳ Generiere...';

    try {
        const response = await fetch('{{ route("admin.ai.generate-description") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                title: title,
                type: 'category',
                keywords: keywords,
                customPrompt: customPrompt
            })
        });

        const data = await response.json();
        if (data.description) {
            document.getElementById('description').value = data.description;
        }
    } catch (error) {
        console.error('Fehler bei der KI-Generierung:', error);
        alert('Fehler beim Generieren der Beschreibung.');
    } finally {
        btn.disabled = false;
        btn.textContent = '✨ KI generieren';
    }
});
</script>
@endsection
