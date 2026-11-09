@extends('admin.layouts.app')

@section('title', 'Tutorial bearbeiten')
@section('heading', 'Tutorial bearbeiten')

@section('content')
<form action="{{ route('admin.tutorials.update', $tutorial) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="bg-white rounded-lg shadow p-6 space-y-6">
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Titel *</label>
            <input type="text" name="title" id="title" value="{{ old('title', $tutorial->title) }}" required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('title')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Kurzbeschreibung *</label>
            <textarea name="description" id="description" rows="2" required
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $tutorial->description) }}</textarea>
            @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- SEO-Felder -->
        <div class="border-t pt-6 bg-gray-50 -mx-6 px-6 py-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">SEO-Einstellungen</h3>
            <div class="space-y-4">
                <div>
                    <label for="meta_title" class="block text-sm font-medium text-gray-700">SEO-Titel (optional)</label>
                    <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $tutorial->meta_title) }}" maxlength="255"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Wird automatisch aus dem Titel generiert, wenn leer gelassen">
                    <p class="mt-1 text-xs text-gray-500">Empfohlen: 50-60 Zeichen für Suchmaschinen</p>
                </div>
                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700">SEO-Beschreibung (optional)</label>
                    <textarea name="meta_description" id="meta_description" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Wird automatisch aus der Kurzbeschreibung generiert, wenn leer gelassen">{{ old('meta_description', $tutorial->meta_description) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Empfohlen: 150-160 Zeichen für Suchmaschinen</p>
                </div>
                <div>
                    <label for="og_image" class="block text-sm font-medium text-gray-700">OpenGraph-Bild (optional)</label>
                    <input type="text" name="og_image" id="og_image" value="{{ old('og_image', $tutorial->og_image) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Pfad zum Bild, z.B. storage/tutorial-bilder/og-image.jpg">
                    <p class="mt-1 text-xs text-gray-500">Wird bei Social-Media-Shares angezeigt (empfohlen: 1200x630px)</p>
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
                    <option value="{{ $category->id }}" {{ old('category_id', $tutorial->category_id) == $category->id ? 'selected' : '' }}>
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
                    <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', $tutorial->tags->pluck('id')->toArray())) ? 'selected' : '' }}>
                        {{ $tag->name }}
                    </option>
                    @endforeach
                </select>
                <p class="mt-1 text-sm text-gray-500">Strg+Klick für mehrere Tags</p>
            </div>
        </div>

        <div class="flex items-center">
            <input type="checkbox" name="published" id="published" value="1" 
                   {{ old('published', $tutorial->published) ? 'checked' : '' }}
                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <label for="published" class="ml-2 block text-sm text-gray-700">Veröffentlicht</label>
        </div>

        <!-- Screenshot-Upload pro Schritt -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Screenshots</h3>
            <p class="text-sm text-gray-500 mb-4">Screenshots werden pro Schritt im nächsten Bereich hochgeladen.</p>
        </div>

        <!-- Schritte -->
        <div class="border-t pt-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Schritte mit Hotspots</h3>
                <button type="button" id="addStep" 
                        class="px-3 py-1 bg-purple-500 text-white text-sm rounded hover:bg-purple-600 transition">
                    + Schritt hinzufügen
                </button>
            </div>
            
            <div id="stepsContainer">
                @foreach($tutorial->steps as $index => $step)
                <div class="step-item bg-gray-50 p-4 rounded-lg mb-4" data-step="{{ $index }}">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-medium text-gray-700">Schritt {{ $index + 1 }}</h4>
                        <button type="button" class="remove-step text-red-500 hover:text-red-700 text-sm">Entfernen</button>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm text-gray-600">Titel *</label>
                            <input type="text" name="steps[{{ $index }}][title]" value="{{ old("steps.$index.title", $step->title) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600">Beschreibung *</label>
                            <textarea name="steps[{{ $index }}][instruction]" rows="3" required
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old("steps.$index.instruction", $step->instruction) }}</textarea>
                        </div>
                        <!-- Screenshot für diesen Schritt -->
                        <div class="border-t pt-3">
                            @if($step->screenshot_path)
                                <div class="mb-3">
                                    <p class="text-sm text-gray-600 mb-2">Aktueller Screenshot:</p>
                                    <div class="relative inline-block">
                                        <img src="{{ asset('storage/' . $step->screenshot_path) }}" 
                                             alt="Screenshot" loading="lazy" class="w-64 h-auto rounded-lg border">
                                        <button type="button" onclick="confirmDeleteScreenshot({{ $tutorial->id }}, {{ $step->id }})"
                                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                            ✕
                                        </button>
                                    </div>
                                </div>
                            @endif
                            <div class="flex gap-2 items-end">
                                <div class="flex-1">
                                    <label class="block text-sm text-gray-600">Screenshot hochladen</label>
                                    <input type="file" name="screenshot_{{ $step->id }}" accept="image/*"
                                           onchange="uploadScreenshot({{ $tutorial->id }}, {{ $step->id }}, this)"
                                           class="mt-1 block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700">
                                </div>
                            </div>
                        </div>
                        <div class="border-t pt-3">
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-sm text-gray-600">Hotspots (optional)</label>
                                <button type="button" class="add-hotspot px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                                    + Hotspot
                                </button>
                            </div>
                            <div class="hotspots-container" data-step="{{ $index }}">
                                @foreach($step->hotspots as $hIndex => $hotspot)
                                <div class="hotspot-item flex gap-2 mb-2">
                                    <input type="text" name="steps[{{ $index }}][hotspots][{{ $hIndex }}][selector]" 
                                           value="{{ old("steps.$index.hotspots.$hIndex.selector", $hotspot->selector) }}"
                                           placeholder="CSS-Selector (z.B. #button)" required
                                           class="flex-1 rounded-md border-gray-300 shadow-sm text-sm">
                                    <input type="text" name="steps[{{ $index }}][hotspots][{{ $hIndex }}][label]" 
                                           value="{{ old("steps.$index.hotspots.$hIndex.label", $hotspot->label) }}"
                                           placeholder="Label" required
                                           class="flex-1 rounded-md border-gray-300 shadow-sm text-sm">
                                    <button type="button" class="remove-hotspot text-red-500 hover:text-red-700 text-sm">✕</button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Schritt-Übersetzungen -->
                    <div class="mt-4 border-t border-gray-200 pt-4">
                        <button type="button" class="text-sm font-medium text-purple-600 hover:text-purple-800 flex items-center gap-1"
                                onclick="toggleStepTranslation({{ $step->id }}, 'tutorial')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                            Übersetzungen verwalten
                        </button>
                        <div id="stepTranslation_{{ $step->id }}" class="hidden mt-3 space-y-4">
                            @foreach(['en'] as $locale)
                                @php
                                    $trans = $step->stepTranslations->where('locale', $locale)->first();
                                @endphp
                                <div class="bg-purple-50 p-4 rounded-lg">
                                    <div class="flex justify-between items-center mb-2">
                                        <h5 class="text-sm font-medium text-purple-800">
                                            {{ $locale === 'en' ? '🇬🇧 English' : '🇩🇪 Deutsch' }}
                                        </h5>
                                        @if($trans)
                                            <form action="{{ route('admin.tutorial.step.translations.destroy', [$tutorial, $step->id, $locale]) }}" method="POST"
                                                  onsubmit="return confirm('Übersetzung wirklich löschen?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Löschen</button>
                                            </form>
                                        @endif
                                    </div>
                                    <form action="{{ route('admin.tutorial.step.translations.store', [$tutorial, $step->id]) }}" method="POST" class="space-y-2">
                                        @csrf
                                        <input type="hidden" name="locale" value="{{ $locale }}">
                                        <div>
                                            <label class="block text-xs text-purple-700">Titel</label>
                                            <input type="text" name="title" value="{{ old("step_trans.$step->id.$locale.title", $trans->title ?? '') }}"
                                                   class="mt-1 block w-full rounded-md border-purple-200 shadow-sm text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-purple-700">Beschreibung</label>
                                            <textarea name="instruction" rows="2"
                                                      class="mt-1 block w-full rounded-md border-purple-200 shadow-sm text-sm">{{ old("step_trans.$step->id.$locale.instruction", $trans->instruction ?? '') }}</textarea>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-xs text-purple-700">Tipp</label>
                                                <textarea name="tip" rows="1"
                                                          class="mt-1 block w-full rounded-md border-purple-200 shadow-sm text-sm">{{ old("step_trans.$step->id.$locale.tip", $trans->tip ?? '') }}</textarea>
                                            </div>
                                            <div>
                                                <label class="block text-xs text-purple-700">Warnung</label>
                                                <textarea name="warning" rows="1"
                                                          class="mt-1 block w-full rounded-md border-purple-200 shadow-sm text-sm">{{ old("step_trans.$step->id.$locale.warning", $trans->warning ?? '') }}</textarea>
                                            </div>
                                        </div>
                                        <button type="submit" class="mt-2 px-3 py-1 bg-purple-500 text-white text-xs rounded hover:bg-purple-600 transition">
                                            {{ $trans ? 'Aktualisieren' : 'Speichern' }}
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mt-6 flex justify-end space-x-4">
        <a href="{{ route('admin.tutorials.index') }}" 
           class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
            Abbrechen
        </a>
        <button type="submit" 
                class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition">
            Änderungen speichern
        </button>
    </div>
</form>

<script>
let stepCount = {{ $tutorial->steps->count() }};

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
                <label class="block text-sm text-gray-600">Beschreibung *</label>
                <textarea name="steps[${stepCount}][instruction]" rows="3" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
            </div>
            <div class="border-t pt-3">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm text-gray-600">Hotspots (optional)</label>
                    <button type="button" class="add-hotspot px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                        + Hotspot
                    </button>
                </div>
                <div class="hotspots-container" data-step="${stepCount}">
                    <!-- Hotspots werden hier hinzugefügt -->
                </div>
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
    
    if (e.target.classList.contains('add-hotspot')) {
        const step = e.target.closest('.step-item');
        const stepIndex = step.dataset.step;
        const hotspotsContainer = step.querySelector('.hotspots-container');
        const hotspotCount = hotspotsContainer.children.length;
        
        const hotspotHtml = `
        <div class="hotspot-item flex gap-2 mb-2">
            <input type="text" name="steps[${stepIndex}][hotspots][${hotspotCount}][selector]" 
                   placeholder="CSS-Selector (z.B. #button)" required
                   class="flex-1 rounded-md border-gray-300 shadow-sm text-sm">
            <input type="text" name="steps[${stepIndex}][hotspots][${hotspotCount}][label]" 
                   placeholder="Label" required
                   class="flex-1 rounded-md border-gray-300 shadow-sm text-sm">
            <button type="button" class="remove-hotspot text-red-500 hover:text-red-700 text-sm">✕</button>
        </div>`;
        
        hotspotsContainer.insertAdjacentHTML('beforeend', hotspotHtml);
    }
    
    if (e.target.classList.contains('remove-hotspot')) {
        e.target.closest('.hotspot-item').remove();
    }
});

function updateStepNumbers() {
    const steps = document.querySelectorAll('.step-item');
    steps.forEach((step, index) => {
        step.querySelector('h4').textContent = `Schritt ${index + 1}`;
    });
}

function uploadScreenshot(tutorialId, stepId, input) {
    const file = input.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('screenshot', file);
    formData.append('step_id', stepId);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    fetch(`/admin/tutorials/${tutorialId}/screenshot`, {
        method: 'POST',
        body: formData,
    })
    .then(response => {
        if (response.redirected) {
            window.location.reload();
        } else {
            alert('Fehler beim Hochladen des Screenshots.');
        }
    })
    .catch(error => {
        console.error('Upload Fehler:', error);
        alert('Fehler beim Hochladen des Screenshots.');
    });
}

function confirmDeleteScreenshot(tutorialId, stepId) {
    if (confirm('Screenshot wirklich löschen?')) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch(`/admin/tutorials/${tutorialId}/screenshot/${stepId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'X-HTTP-Method-Override': 'DELETE',
            },
        })
        .then(response => {
            if (response.redirected) {
                window.location.reload();
            }
        });
    }
}
</script>
@endsection
