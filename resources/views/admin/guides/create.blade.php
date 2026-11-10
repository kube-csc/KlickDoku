@extends('admin.layouts.app')

@section('title', 'Neue Anleitung')
@section('heading', 'Neue Anleitung')

@section('content')
<form action="{{ route('admin.guides.store') }}" method="POST">
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
            <div class="flex items-center justify-between mb-1">
                <label for="description" class="block text-sm font-medium text-gray-700">Kurzbeschreibung *</label>
                <button type="button" id="aiGenerateDesc"
                        class="text-xs px-2 py-1 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition"
                        title="Beschreibung automatisch generieren">
                    ✨ KI generieren
                </button>
            </div>
            <textarea name="description" id="description" rows="2" required
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
            @error('description')
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
                        <input type="text" id="aiKeywords" placeholder="z.B. Docker, Firewall, Backup (kommagetrennt)"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="aiReferenceUrls" class="block text-xs font-medium text-gray-700">Referenz-Links</label>
                        <textarea id="aiReferenceUrls" rows="2" placeholder="Eine URL pro Zeile, z.B. https://docs.example.com"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    <div>
                        <label for="aiCustomPrompt" class="block text-xs font-medium text-gray-700">Eigene Anweisung für die KI</label>
                        <textarea id="aiCustomPrompt" rows="2" placeholder="z.B. Kurz und technisch formulieren, Fokus auf Sicherheit"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700">Kategorie *</label>
                <select name="category_id" id="category_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Kategorie wählen...</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
                @error('category_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="tags" class="block text-sm font-medium text-gray-700">Tags</label>
                <select name="tags[]" id="tags" multiple
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach($tags as $tag)
                    <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'selected' : '' }}>
                        {{ $tag->name }}
                    </option>
                    @endforeach
                </select>
                <p class="mt-1 text-sm text-gray-500">Strg+Klick für mehrere Tags</p>
            </div>
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="content" class="block text-sm font-medium text-gray-700">Inhalt *</label>
                <button type="button" id="aiGenerateContent"
                        class="text-xs px-2 py-1 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition"
                        title="Inhalt automatisch generieren">
                    ✨ KI generieren
                </button>
            </div>
            <textarea name="content" id="content" rows="6" required
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('content') }}</textarea>
            @error('content')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">Die KI erstellt einen Entwurf basierend auf Titel, Kategorie und Schritten — diesen können Sie dann anpassen.</p>
        </div>

        <div class="flex items-center">
            <input type="checkbox" name="published" id="published" value="1" 
                   {{ old('published') ? 'checked' : '' }}
                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <label for="published" class="ml-2 block text-sm text-gray-700">Sofort veröffentlichen</label>
        </div>

        <!-- Schritte -->
        <div class="border-t pt-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Schritte</h3>
                <button type="button" id="addStep" 
                        class="px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 transition">
                    + Schritt hinzufügen
                </button>
            </div>

            <!-- KI-Kontext für Schritte -->
            <div class="mb-4 p-3 bg-indigo-50 rounded-lg">
                <button type="button" id="toggleAiContextSteps" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium underline">
                    ✨ Zusätzlicher Kontext für KI (Schlüsselbegriffe, Referenz-Links, eigene Anweisung)
                </button>
                <div id="aiContextPanelSteps" class="hidden mt-3 p-3 bg-white rounded-lg space-y-3 border border-indigo-200">
                    <p class="text-xs text-gray-600">Geben Sie Schlüsselbegriffe, Referenz-Links oder eine eigene Anweisung ein, damit die KI bessere Schrittbeschreibungen erstellt.</p>
                    <div>
                        <label for="aiKeywordsSteps" class="block text-xs font-medium text-gray-700">Schlüsselbegriffe</label>
                        <input type="text" id="aiKeywordsSteps" placeholder="z.B. Docker, Firewall, Backup (kommagetrennt)"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="aiReferenceUrlsSteps" class="block text-xs font-medium text-gray-700">Referenz-Links</label>
                        <textarea id="aiReferenceUrlsSteps" rows="2" placeholder="Eine URL pro Zeile, z.B. https://docs.example.com"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    <div>
                        <label for="aiCustomPromptSteps" class="block text-xs font-medium text-gray-700">Eigene Anweisung für die KI</label>
                        <textarea id="aiCustomPromptSteps" rows="2" placeholder="z.B. Kurz und technisch formulieren, Fokus auf Sicherheit"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                </div>
            </div>
            
            <div id="stepsContainer">
                <!-- Schritte werden hier dynamisch hinzugefügt -->
            </div>
        </div>
    </div>

    <div class="mt-6 flex justify-end space-x-4">
        <a href="{{ route('admin.guides.index') }}" 
           class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
            Abbrechen
        </a>
        <button type="submit" 
                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
            Anleitung erstellen
        </button>
    </div>
</form>

<script>
let stepCount = 0;

document.getElementById('addStep').addEventListener('click', function() {
    const stepHtml = `
    <div class="step-item bg-gray-50 p-4 rounded-lg mb-4" data-step="${stepCount}">
        <div class="flex justify-between items-center mb-3">
            <h4 class="font-medium text-gray-700">Schritt ${stepCount + 1}</h4>
            <button type="button" class="remove-step text-red-500 hover:text-red-700 text-sm">Entfernen</button>
        </div>
        <div class="space-y-3">
            <div>
                <label class="block text-sm text-gray-600">Titel *</label>
                <input type="text" name="steps[${stepCount}][title]" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-sm text-gray-600">Beschreibung *</label>
                    <button type="button" onclick="aiGenerateStepText(${stepCount}, 'description', 'guide_step')"
                            class="text-xs px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition"
                            title="Beschreibung automatisch generieren">
                        ✨ KI
                    </button>
                </div>
                <textarea name="steps[${stepCount}][description]" id="step_${stepCount}_description" rows="3" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-sm text-gray-600">Tipp (optional)</label>
                    <button type="button" onclick="aiGenerateStepText(${stepCount}, 'tip', 'guide_step')"
                            class="text-xs px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition"
                            title="Tipp automatisch generieren">
                        ✨ KI
                    </button>
                </div>
                <textarea name="steps[${stepCount}][tip]" id="step_${stepCount}_tip" rows="2"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-sm text-gray-600">Warnung (optional)</label>
                    <button type="button" onclick="aiGenerateStepText(${stepCount}, 'warning', 'guide_step')"
                            class="text-xs px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition"
                            title="Warnung automatisch generieren">
                        ✨ KI
                    </button>
                </div>
                <textarea name="steps[${stepCount}][warning]" id="step_${stepCount}_warning" rows="2"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
            </div>
        </div>
    </div>`;
    
    document.getElementById('stepsContainer').insertAdjacentHTML('beforeend', stepHtml);
    stepCount++;
    updateStepNumbers();
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-step')) {
        e.target.closest('.step-item').remove();
        updateStepNumbers();
    }
});

function updateStepNumbers() {
    const steps = document.querySelectorAll('.step-item');
    steps.forEach((step, index) => {
        step.querySelector('h4').textContent = `Schritt ${index + 1}`;
    });
}

// KI-Kontext-Panel ein-/ausblenden
document.getElementById('toggleAiContext').addEventListener('click', function() {
    const panel = document.getElementById('aiContextPanel');
    panel.classList.toggle('hidden');
    this.textContent = panel.classList.contains('hidden')
        ? '+ Zusätzlicher Kontext für KI (optional)'
        : '- Zusätzlicher Kontext für KI (optional)';
});

// KI-Kontext-Panel für Schritte ein-/ausblenden
document.getElementById('toggleAiContextSteps').addEventListener('click', function() {
    const panel = document.getElementById('aiContextPanelSteps');
    panel.classList.toggle('hidden');
    this.textContent = panel.classList.contains('hidden')
        ? '✨ Zusätzlicher Kontext für KI (Schlüsselbegriffe, Referenz-Links, eigene Anweisung)'
        : '✕ Kontext für KI ausblenden';
});

// KI-Beschreibung generieren
document.getElementById('aiGenerateDesc').addEventListener('click', async function() {
    const title = document.getElementById('title').value.trim();
    if (!title) {
        alert('Bitte geben Sie zuerst einen Titel ein.');
        return;
    }

    const categorySelect = document.getElementById('category_id');
    const categoryOption = categorySelect.options[categorySelect.selectedIndex];
    const category = categoryOption && categoryOption.value ? categoryOption.text : '';

    const tagSelect = document.getElementById('tags');
    const tags = Array.from(tagSelect.selectedOptions).map(opt => opt.text);

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
                type: 'guide',
                category: category,
                tags: tags,
                keywords: keywords,
                referenceUrls: referenceUrls,
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

// Kontext-Daten aus dem KI-Panel lesen
// Priorisiert das Schritte-Panel, falls vorhanden
function getAiContextData() {
    // Zuerst das Schritte-Panel prüfen (priorisiert)
    const keywordsSteps = document.getElementById('aiKeywordsSteps');
    const urlsSteps = document.getElementById('aiReferenceUrlsSteps');
    const promptSteps = document.getElementById('aiCustomPromptSteps');

    // Wenn Schritte-Panel Werte hat, nutzen
    if (keywordsSteps && keywordsSteps.value.trim() ||
        urlsSteps && urlsSteps.value.trim() ||
        promptSteps && promptSteps.value.trim()) {
        const keywords = keywordsSteps && keywordsSteps.value.trim()
            ? keywordsSteps.value.split(',').map(k => k.trim()).filter(k => k)
            : [];

        const referenceUrls = urlsSteps && urlsSteps.value.trim()
            ? urlsSteps.value.split('\n').map(u => u.trim()).filter(u => u)
            : [];

        const customPrompt = promptSteps && promptSteps.value.trim()
            ? promptSteps.value.trim()
            : '';

        return { keywords, referenceUrls, customPrompt };
    }

    // Ansonsten das Hauptpanel nutzen
    const keywordsInput = document.getElementById('aiKeywords');
    const keywords = keywordsInput && keywordsInput.value.trim()
        ? keywordsInput.value.split(',').map(k => k.trim()).filter(k => k)
        : [];

    const urlsInput = document.getElementById('aiReferenceUrls');
    const referenceUrls = urlsInput && urlsInput.value.trim()
        ? urlsInput.value.split('\n').map(u => u.trim()).filter(u => u)
        : [];

    const customPromptInput = document.getElementById('aiCustomPrompt');
    const customPrompt = customPromptInput && customPromptInput.value.trim()
        ? customPromptInput.value.trim()
        : '';

    return { keywords, referenceUrls, customPrompt };
}

// KI-Inhalt generieren (vollständiger Anleitungsentwurf)
document.getElementById('aiGenerateContent').addEventListener('click', async function() {
    const title = document.getElementById('title').value.trim();
    if (!title) {
        alert('Bitte geben Sie zuerst einen Titel ein.');
        return;
    }

    const categorySelect = document.getElementById('category_id');
    const categoryOption = categorySelect ? categorySelect.options[categorySelect.selectedIndex] : null;
    const category = categoryOption && categoryOption.value ? categoryOption.text : '';

    const tagSelect = document.getElementById('tags');
    const tags = tagSelect ? Array.from(tagSelect.selectedOptions).map(opt => opt.text) : [];

    // Schritte sammeln
    const stepItems = document.querySelectorAll('.step-item');
    const steps = [];
    stepItems.forEach(stepItem => {
        const titleInput = stepItem.querySelector('input[name*="[title]"]');
        if (titleInput && titleInput.value.trim()) {
            steps.push({ title: titleInput.value.trim() });
        }
    });

    // Kontext-Daten aus dem KI-Panel
    const { keywords, referenceUrls, customPrompt } = getAiContextData();

    const btn = this;
    btn.disabled = true;
    btn.textContent = '⏳ Generiere...';

    try {
        const response = await fetch('{{ route("admin.ai.generate-content") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                title: title,
                type: 'guide',
                category: category,
                tags: tags,
                steps: steps,
                keywords: keywords,
                referenceUrls: referenceUrls,
                customPrompt: customPrompt
            })
        });

        const data = await response.json();
        if (data.content) {
            document.getElementById('content').value = data.content;
        }
    } catch (error) {
        console.error('Fehler bei der KI-Generierung:', error);
        alert('Fehler beim Generieren des Inhalts.');
    } finally {
        btn.disabled = false;
        btn.textContent = '✨ KI generieren';
    }
});

// KI-Schritttext generieren (Beschreibung, Tipp oder Warnung)
async function aiGenerateStepText(stepIndex, field, type) {
    const stepItem = document.querySelectorAll('.step-item')[stepIndex];
    if (!stepItem) return;

    const titleInput = stepItem.querySelector('input[name*="[title]"]');
    const title = titleInput ? titleInput.value.trim() : '';
    if (!title) {
        alert('Bitte geben Sie zuerst einen Schritt-Titel ein.');
        return;
    }

    const guideTitle = document.getElementById('title')?.value.trim() || '';

    // Kontext-Daten aus dem KI-Panel
    const { keywords, referenceUrls, customPrompt: panelCustomPrompt } = getAiContextData();

    // Feld-spezifische Anweisung mit benutzerdefinierter Anweisung kombinieren
    const fieldPrompt = field === 'tip'
        ? 'Formuliere einen hilfreichen Tipp oder einen alternativen Ansatz.'
        : field === 'warning'
        ? 'Formuliere eine klare Warnung mit möglichen Risiken.'
        : '';

    const customPrompt = panelCustomPrompt
        ? (fieldPrompt ? panelCustomPrompt + ' ' + fieldPrompt : panelCustomPrompt)
        : fieldPrompt;

    const textarea = document.getElementById(`step_${stepIndex}_${field}`);
    if (!textarea) return;

    const btn = textarea.closest('div').querySelector('button[onclick*="aiGenerateStepText"]');
    if (btn) {
        btn.disabled = true;
        btn.textContent = '⏳';
    }

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
                type: type,
                category: guideTitle,
                keywords: keywords,
                referenceUrls: referenceUrls,
                customPrompt: customPrompt
            })
        });

        const data = await response.json();
        if (data.description) {
            textarea.value = data.description;
        }
    } catch (error) {
        console.error('Fehler bei der KI-Generierung:', error);
        alert('Fehler beim Generieren des Textes.');
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.textContent = '✨ KI';
        }
    }
}
</script>
@endsection
