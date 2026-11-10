@extends('admin.layouts.app')

@section('title', 'Neue Seite')
@section('heading', 'Neue Seite')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('admin.pages.store') }}" method="POST">
        @csrf
        
        <div class="bg-white rounded-lg shadow p-6 space-y-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Titel *</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="content" class="block text-sm font-medium text-gray-700">Inhalt *</label>
                <textarea name="content" id="content" rows="12" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('content') }}</textarea>
                @error('content')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta-Beschreibung (SEO)</label>
                    <button type="button" id="aiGenerateDesc"
                            class="text-xs px-2 py-1 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition"
                            title="Meta-Beschreibung automatisch generieren">
                        ✨ KI generieren
                    </button>
                </div>
                <textarea name="meta_description" id="meta_description" rows="2" maxlength="200"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('meta_description') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Max. 200 Zeichen für Suchmaschinen-Optimierung</p>
                @error('meta_description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <!-- Zusätzlicher Kontext für KI-Generierung -->
                <div class="mt-3">
                    <button type="button" id="toggleAiContext" class="text-xs text-indigo-600 hover:text-indigo-800 underline">
                        + Zusätzlicher Kontext für KI (optional)
                    </button>
                    <div id="aiContextPanel" class="hidden mt-2 p-3 bg-indigo-50 rounded-lg space-y-3">
                        <p class="text-xs text-gray-600">Geben Sie Schlüsselbegriffe, Referenz-Links oder eine eigene Anweisung ein, damit die KI eine passendere Beschreibung erstellt.</p>
                        <div>
                            <label for="aiKeywords" class="block text-xs font-medium text-gray-700">Schlüsselbegriffe</label>
                            <input type="text" id="aiKeywords" placeholder="z.B. Über uns, Team, Kontakt (kommagetrennt)"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="aiReferenceUrls" class="block text-xs font-medium text-gray-700">Referenz-Links</label>
                            <textarea id="aiReferenceUrls" rows="2" placeholder="Eine URL pro Zeile, z.B. https://example.com"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                        <div>
                            <label for="aiCustomPrompt" class="block text-xs font-medium text-gray-700">Eigene Anweisung für die KI</label>
                            <textarea id="aiCustomPrompt" rows="2" placeholder="z.B. Freundlich und einladend formulieren"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-center">
                    <input type="checkbox" name="show_in_nav" id="show_in_nav" value="1" 
                           {{ old('show_in_nav') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <label for="show_in_nav" class="ml-2 block text-sm text-gray-700">In Navigation anzeigen</label>
                </div>

                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700">Reihenfolge in Navigation</label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Leer lassen für automatische Reihenfolge</p>
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
                Seite erstellen
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

// KI-Meta-Beschreibung generieren
document.getElementById('aiGenerateDesc').addEventListener('click', async function() {
    const title = document.getElementById('title').value.trim();
    if (!title) {
        alert('Bitte geben Sie zuerst einen Titel ein.');
        return;
    }

    // Schlüsselbegriffe aus dem Kontext-Panel lesen (kommagetrennt)
    const keywordsInput = document.getElementById('aiKeywords').value.trim();
    const keywords = keywordsInput
        ? keywordsInput.split(',').map(k => k.trim()).filter(k => k.length > 0)
        : [];

    // Referenz-URLs aus dem Kontext-Panel lesen (eine pro Zeile)
    const urlsInput = document.getElementById('aiReferenceUrls').value.trim();
    const referenceUrls = urlsInput
        ? urlsInput.split('\n').map(u => u.trim()).filter(u => u.length > 0)
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
                type: 'page',
                keywords: keywords,
                referenceUrls: referenceUrls,
                customPrompt: customPrompt
            })
        });

        const data = await response.json();
        if (data.description) {
            document.getElementById('meta_description').value = data.description;
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
