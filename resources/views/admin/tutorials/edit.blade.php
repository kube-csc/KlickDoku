@extends('admin.layouts.app')

@section('title', 'Tutorial bearbeiten')
@section('heading', 'Tutorial bearbeiten')

@section('content')
<form action="{{ route('admin.tutorials.update', $tutorial) }}" method="POST" novalidate>
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
                        <input type="hidden" name="steps[{{ $index }}][screenshot_path]" value="{{ old("steps.$index.screenshot_path", $step->screenshot_path) }}">
                        <input type="hidden" name="steps[{{ $index }}][screenshot_width]" value="{{ old("steps.$index.screenshot_width", $step->screenshot_width) }}">
                        <input type="hidden" name="steps[{{ $index }}][screenshot_height]" value="{{ old("steps.$index.screenshot_height", $step->screenshot_height) }}">
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
                                             alt="Screenshot" loading="lazy" class="w-64 h-auto rounded-lg border cursor-pointer"
                                             onclick="openHotspotEditor('{{ asset('storage/' . $step->screenshot_path) }}', {{ $step->screenshot_width ?? '0' }}, {{ $step->screenshot_height ?? '0' }}, '{{ $index }}', '{{ $hIndex ?? -1 }}')"
                                             title="Klicken zum Vergrößern und Hotspot-Position setzen">
                                        <button type="button" onclick="confirmDeleteScreenshot({{ $tutorial->id }}, {{ $step->id }})"
                                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                            ✕
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">🔍 Auf das Bild klicken, um Hotspot-Positionen visuell zu setzen</p>
                                </div>
                            @endif
                            <div class="flex gap-2 items-end">
                                <div class="flex-1">
                                    <label class="block text-sm text-gray-600">Screenshot hochladen</label>
                                    <input type="file" name="screenshot_{{ $step->id }}" accept="image/*"
                                           onchange="uploadScreenshot({{ $tutorial->id }}, {{ $step->id }}, this)"
                                           class="mt-1 block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700">
                                </div>
                                <div>
                                    <button type="button" onclick="startScreenshotCapture({{ $tutorial->id }}, {{ $step->id }})"
                                            class="mt-1 px-3 py-2 bg-green-500 text-white text-xs rounded hover:bg-green-600 whitespace-nowrap"
                                            title="Wählen Sie einen Bereich auf dem Bildschirm aus, der als Screenshot hochgeladen wird">
                                        📸 Screenshot mit Bereichsauswahl
                                    </button>
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
                                <div class="hotspot-item flex gap-2 mb-2 items-center">
                                    <input type="hidden" name="steps[{{ $index }}][hotspots][{{ $hIndex }}][hotspot_type]" value="{{ old("steps.$index.hotspots.$hIndex.hotspot_type", $hotspot->hotspot_type) }}">
                                    <input type="hidden" name="steps[{{ $index }}][hotspots][{{ $hIndex }}][position_x]" value="{{ old("steps.$index.hotspots.$hIndex.position_x", $hotspot->position_x) }}">
                                    <input type="hidden" name="steps[{{ $index }}][hotspots][{{ $hIndex }}][position_y]" value="{{ old("steps.$index.hotspots.$hIndex.position_y", $hotspot->position_y) }}">
                                    <input type="text" name="steps[{{ $index }}][hotspots][{{ $hIndex }}][selector]" 
                                           value="{{ old("steps.$index.hotspots.$hIndex.selector", $hotspot->selector ?? '') }}"
                                           placeholder="CSS-Selector (z.B. #button)"
                                           class="flex-1 rounded-md border-gray-300 shadow-sm text-sm">
                                    <input type="text" name="steps[{{ $index }}][hotspots][{{ $hIndex }}][label]" 
                                           value="{{ old("steps.$index.hotspots.$hIndex.label", $hotspot->label) }}"
                                           placeholder="Label"
                                           class="flex-1 rounded-md border-gray-300 shadow-sm text-sm">
                                    <input type="text" name="steps[{{ $index }}][hotspots][{{ $hIndex }}][link]" 
                                           value="{{ old("steps.$index.hotspots.$hIndex.link", $hotspot->link) }}"
                                           placeholder="Link (optional)"
                                           class="flex-1 rounded-md border-gray-300 shadow-sm text-sm">
                                    <span class="hotspot-pos-display text-xs text-gray-500 whitespace-nowrap" data-hotspot="{{ $hIndex }}">
                                        {{ $hotspot->position_x ?? '-' }}%, {{ $hotspot->position_y ?? '-' }}%
                                    </span>
                                    @if($step->screenshot_path)
                                    <button type="button" onclick="openHotspotEditor('{{ asset('storage/' . $step->screenshot_path) }}', {{ $step->screenshot_width ?? '0' }}, {{ $step->screenshot_height ?? '0' }}, '{{ $index }}', '{{ $hIndex }}')"
                                            class="px-2 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600 whitespace-nowrap" title="Position auf dem Bild setzen">
                                        📍 Position
                                    </button>
                                    @endif
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
                                            <button type="button" onclick="deleteStepTranslation({{ $tutorial->id }}, {{ $step->id }}, '{{ $locale }}')"
                                                    class="text-xs text-red-500 hover:text-red-700">Löschen</button>
                                        @endif
                                    </div>
                                    <div class="space-y-2">
                                        <input type="hidden" name="step_translations[{{ $step->id }}][{{ $locale }}][locale]" value="{{ $locale }}">
                                        <div>
                                            <label class="block text-xs text-purple-700">Titel</label>
                                            <input type="text" name="step_translations[{{ $step->id }}][{{ $locale }}][title]" value="{{ old("step_trans.$step->id.$locale.title", $trans->title ?? '') }}"
                                                   class="mt-1 block w-full rounded-md border-purple-200 shadow-sm text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-purple-700">Beschreibung</label>
                                            <textarea name="step_translations[{{ $step->id }}][{{ $locale }}][instruction]" rows="2"
                                                      class="mt-1 block w-full rounded-md border-purple-200 shadow-sm text-sm">{{ old("step_trans.$step->id.$locale.instruction", $trans->instruction ?? '') }}</textarea>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-xs text-purple-700">Tipp</label>
                                                <textarea name="step_translations[{{ $step->id }}][{{ $locale }}][tip]" rows="1"
                                                          class="mt-1 block w-full rounded-md border-purple-200 shadow-sm text-sm">{{ old("step_trans.$step->id.$locale.tip", $trans->tip ?? '') }}</textarea>
                                            </div>
                                            <div>
                                                <label class="block text-xs text-purple-700">Warnung</label>
                                                <textarea name="step_translations[{{ $step->id }}][{{ $locale }}][warning]" rows="1"
                                                          class="mt-1 block w-full rounded-md border-purple-200 shadow-sm text-sm">{{ old("step_trans.$step->id.$locale.warning", $trans->warning ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
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

<!-- Hotspot-Editor Modal -->
<div id="hotspotEditorModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)closeHotspotEditor()">
    <div class="bg-white rounded-lg shadow-xl max-w-7xl max-h-full overflow-auto">
        <div class="flex justify-between items-center p-4 border-b">
            <h3 class="text-lg font-medium text-gray-900">Hotspot-Position auf dem Bild setzen</h3>
            <div class="flex items-center gap-2">
                <button type="button" onclick="zoomHotspotImage(-0.1)" class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300 text-sm" title="Verkleinern">➖</button>
                <span id="zoomLevel" class="text-sm text-gray-600">100%</span>
                <button type="button" onclick="zoomHotspotImage(0.1)" class="px-2 py-1 bg-gray-200 rounded hover:bg-gray-300 text-sm" title="Vergrößern">➕</button>
                <button type="button" onclick="closeHotspotEditor()" class="ml-2 text-red-500 hover:text-red-700 text-lg">✕</button>
            </div>
        </div>
        <div class="p-4">
            <!-- Deutliche Anleitung mit Animation -->
            <div id="hotspotHint" class="mb-3 p-4 bg-yellow-50 border-2 border-yellow-300 rounded-lg">
                <p class="text-base font-bold text-yellow-800 mb-1">📍 So verschieben Sie die Hotspot-Position:</p>
                <ol class="list-decimal list-inside text-sm text-yellow-700 space-y-1">
                    <li>Klicken Sie auf den <span class="font-bold">📍 Position</span> Button beim Hotspot, den Sie ändern möchten.</li>
                    <li>Klicken Sie dann <span class="font-bold">direkt auf das Bild</span> an der Stelle, wo der Hotspot erscheinen soll.</li>
                    <li>Die Position wird sofort gespeichert. Sie können den Zoom (➕/➖) für mehr Genauigkeit verwenden.</li>
                </ol>
            </div>
            <div id="hotspotImageContainer" class="relative inline-block overflow-auto max-h-[70vh]">
                <div id="hotspotImageWrapper" class="inline-block relative" style="transform-origin: top left;">
                    <img id="hotspotEditorImage" src="" alt="Screenshot" class="block cursor-crosshair" onwheel="handleZoom(event)" />
                    <!-- Hotspot Marker werden hier dynamisch hinzugefügt -->
                    <div id="hotspotMarkers" class="absolute inset-0 pointer-events-none"></div>
                </div>
            </div>
            <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                <p class="text-sm font-medium text-blue-800">Ausgewählte Position:</p>
                <p id="selectedPosition" class="text-sm text-blue-600">Noch keine Position ausgewählt</p>
            </div>
        </div>
    </div>
</div>

<script>
let stepCount = {{ $tutorial->steps->count() }};
let currentHotspotStepIndex = null;
let currentHotspotIndex = null;
let hotspotEditorZoom = 1.0;

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
        
        // Screenshot-URL aus dem Schritt holen (falls vorhanden)
        const screenshotPath = step.querySelector('input[name$="[screenshot_path]"]');
        const hasScreenshot = screenshotPath && screenshotPath.value;
        
        const hotspotHtml = `
        <div class="hotspot-item flex gap-2 mb-2 items-center">
            <input type="hidden" name="steps[${stepIndex}][hotspots][${hotspotCount}][hotspot_type]" value="circle">
            <input type="hidden" name="steps[${stepIndex}][hotspots][${hotspotCount}][position_x]" value="">
            <input type="hidden" name="steps[${stepIndex}][hotspots][${hotspotCount}][position_y]" value="">
            <input type="text" name="steps[${stepIndex}][hotspots][${hotspotCount}][selector]" 
                   placeholder="CSS-Selector (z.B. #button)"
                   class="flex-1 rounded-md border-gray-300 shadow-sm text-sm">
            <input type="text" name="steps[${stepIndex}][hotspots][${hotspotCount}][label]" 
                   placeholder="Label"
                   class="flex-1 rounded-md border-gray-300 shadow-sm text-sm">
            <input type="text" name="steps[${stepIndex}][hotspots][${hotspotCount}][link]" 
                   placeholder="Link (optional)"
                   class="flex-1 rounded-md border-gray-300 shadow-sm text-sm">
            <span class="hotspot-pos-display text-xs text-gray-500 whitespace-nowrap" data-hotspot="${hotspotCount}">-</span>
            ${hasScreenshot ? `<button type="button" onclick="openHotspotEditor('', 0, 0, '${stepIndex}', '${hotspotCount}')"
                    class="px-2 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600 whitespace-nowrap" title="Position auf dem Bild setzen">📍 Position</button>` : ''}
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
        console.log('Upload-Antwort:', response.status, response.redirected);
        if (response.ok || response.redirected) {
            window.location.reload();
            return;
        }
        return response.text().then(text => {
            console.error('Upload-Fehler:', response.status, text);
            alert('Fehler beim Hochladen des Screenshots: HTTP ' + response.status);
        });
    })
    .catch(error => {
        console.error('Upload Fehler:', error);
        alert('Fehler beim Hochladen des Screenshots: ' + error.message);
    });
}

function confirmDeleteScreenshot(tutorialId, stepId) {
    if (confirm('Screenshot wirklich löschen?')) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch(`/admin/tutorials/${tutorialId}/steps/${stepId}/screenshot`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
        })
        .then(response => {
            console.log('Delete-Antwort:', response.status, response.redirected);
            if (response.ok || response.redirected) {
                window.location.reload();
                return;
            }
            return response.text().then(text => {
                console.error('Lösch-Fehler:', response.status, text);
                alert('Fehler beim Löschen des Screenshots: HTTP ' + response.status);
            });
        })
        .catch(error => {
            console.error('Lösch-Fehler:', error);
            alert('Fehler beim Löschen des Screenshots: ' + error.message);
        });
    }
}

function toggleStepTranslation(stepId, type) {
    const el = document.getElementById('stepTranslation_' + stepId);
    if (el.classList.contains('hidden')) {
        el.classList.remove('hidden');
    } else {
        el.classList.add('hidden');
    }
}

function deleteStepTranslation(tutorialId, stepId, locale) {
    if (confirm('Übersetzung wirklich löschen?')) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch(`/admin/tutorials/${tutorialId}/steps/${stepId}/translations/${locale}`, {
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

/* ===== Hotspot-Editor Funktionen ===== */

function openHotspotEditor(imageSrc, imageWidth, imageHeight, stepIndex, hotspotIndex) {
    const modal = document.getElementById('hotspotEditorModal');
    const img = document.getElementById('hotspotEditorImage');
    const wrapper = document.getElementById('hotspotImageWrapper');
    
    // Wenn keine Bild-URL übergeben, aus dem Schritt ermitteln
    if (!imageSrc) {
        const stepItem = document.querySelector(`.step-item[data-step="${stepIndex}"]`);
        if (stepItem) {
            const screenshotImg = stepItem.querySelector('img[onclick*="openHotspotEditor"]');
            if (screenshotImg) {
                imageSrc = screenshotImg.src;
            }
        }
    }
    
    // Wenn immer noch kein Bild, Modal schließen
    if (!imageSrc) {
        alert('Kein Screenshot vorhanden. Laden Sie zuerst einen Screenshot hoch.');
        return;
    }
    
    // Bild laden
    img.src = imageSrc;
    hotspotEditorZoom = 1.0;
    wrapper.style.transform = 'scale(1)';
    document.getElementById('zoomLevel').textContent = '100%';
    
    // Schritt- und Hotspot-Index speichern
    currentHotspotStepIndex = stepIndex;
    currentHotspotIndex = hotspotIndex;
    
    // Modal anzeigen
    modal.classList.remove('hidden');
    
    // Ausgewählte Position zurücksetzen
    document.getElementById('selectedPosition').textContent = 'Noch keine Position ausgewählt';
    
    // Bild-Größe laden und Marker rendern nach dem Laden
    img.onload = function() {
        renderHotspotMarkers(stepIndex);
    };
    // Falls bereits gecacht
    if (img.complete) {
        renderHotspotMarkers(stepIndex);
    }
}

function closeHotspotEditor() {
    document.getElementById('hotspotEditorModal').classList.add('hidden');
    currentHotspotStepIndex = null;
    currentHotspotIndex = null;
}

function setHotspotPosition(event) {
    event.stopPropagation();
    event.preventDefault();
    
    const img = document.getElementById('hotspotEditorImage');
    const wrapper = document.getElementById('hotspotImageWrapper');
    
    // Zoom-Faktor aus dem CSS transform: scale(X) des Wrappers extrahieren
    let zoom = 1;
    if (wrapper) {
        const transform = window.getComputedStyle(wrapper).transform;
        const match = transform.match(/matrix\(([^)]+)\)/);
        if (match) {
            // matrix(a, b, c, d, tx, ty) - a und d sind der Skalierungsfaktor
            zoom = parseFloat(match[1].split(',')[0]);
        }
    }
    
    // Klick-Position relativ zum Bild berechnen
    // Bei Zoom muss die Klick-Position durch den Zoom-Faktor geteilt werden,
    // um die Position in der natürlichen Bildgröße zu erhalten
    const naturalWidth = img.naturalWidth;
    const naturalHeight = img.naturalHeight;
    
    // rect ist die sichtbare (gezoomte) Größe des Bildes
    const rect = img.getBoundingClientRect();
    
    // Klick-Position in Pixeln des sichtbaren (gezoomten) Bildes
    const clickXVisible = event.clientX - rect.left;
    const clickYVisible = event.clientY - rect.top;
    
    // Durch Zoom-Faktor teilen, um die Position in der natürlichen Bildgröße zu erhalten
    const clickX = clickXVisible / zoom;
    const clickY = clickYVisible / zoom;
    
    // Prozentwerte basierend auf der natürlichen Bildgröße berechnen
    const x = (clickX / naturalWidth) * 100;
    const y = (clickY / naturalHeight) * 100;
    
    // Auf Ganzzahl runden (Datenbank speichert integer)
    const posX = Math.round(x);
    const posY = Math.round(y);
    
    // Position im Formular setzen
    const stepItem = document.querySelector(`.step-item[data-step="${currentHotspotStepIndex}"]`);
    if (!stepItem) {
        console.error('Schritt nicht gefunden:', currentHotspotStepIndex);
        return;
    }
    
    // Wenn kein Hotspot ausgewählt ist, einen neuen erstellen
    if (currentHotspotIndex === null || currentHotspotIndex === '-1') {
        // Neuen Hotspot hinzufügen
        const hotspotsContainer = stepItem.querySelector('.hotspots-container');
        const hotspotCount = hotspotsContainer.querySelectorAll('.hotspot-item').length;
        
        const hotspotHtml = `
        <div class="hotspot-item flex gap-2 mb-2 items-center">
            <input type="hidden" name="steps[${currentHotspotStepIndex}][hotspots][${hotspotCount}][hotspot_type]" value="circle">
            <input type="hidden" name="steps[${currentHotspotStepIndex}][hotspots][${hotspotCount}][position_x]" value="${posX}">
            <input type="hidden" name="steps[${currentHotspotStepIndex}][hotspots][${hotspotCount}][position_y]" value="${posY}">
            <input type="text" name="steps[${currentHotspotStepIndex}][hotspots][${hotspotCount}][selector]" 
                   placeholder="CSS-Selector (z.B. #button)"
                   class="flex-1 rounded-md border-gray-300 shadow-sm text-sm">
            <input type="text" name="steps[${currentHotspotStepIndex}][hotspots][${hotspotCount}][label]" 
                   placeholder="Label"
                   class="flex-1 rounded-md border-gray-300 shadow-sm text-sm">
            <input type="text" name="steps[${currentHotspotStepIndex}][hotspots][${hotspotCount}][link]" 
                   placeholder="Link (optional)"
                   class="flex-1 rounded-md border-gray-300 shadow-sm text-sm">
            <span class="hotspot-pos-display text-xs text-gray-500 whitespace-nowrap" data-hotspot="${hotspotCount}">${posX}%, ${posY}%</span>
            <button type="button" class="remove-hotspot text-red-500 hover:text-red-700 text-sm">✕</button>
        </div>`;
        
        hotspotsContainer.insertAdjacentHTML('beforeend', hotspotHtml);
    } else {
        // Bestehenden Hotspot aktualisieren
        const posXInput = stepItem.querySelector(`input[name="steps[${currentHotspotStepIndex}][hotspots][${currentHotspotIndex}][position_x]"]`);
        const posYInput = stepItem.querySelector(`input[name="steps[${currentHotspotStepIndex}][hotspots][${currentHotspotIndex}][position_y]"]`);
        
        if (posXInput) posXInput.value = posX;
        if (posYInput) posYInput.value = posY;
        
        // Positionsanzeige aktualisieren
        const posDisplay = stepItem.querySelector(`.hotspot-pos-display[data-hotspot="${currentHotspotIndex}"]`);
        if (posDisplay) posDisplay.textContent = `${posX}%, ${posY}%`;
    }
    
    // Ausgewählte Position anzeigen
    document.getElementById('selectedPosition').textContent = `X: ${posX}%, Y: ${posY}% — Position wurde im Formular gespeichert`;
    
    // Hinweis ausblenden nach erfolgreichem Klick
    const hint = document.getElementById('hotspotHint');
    if (hint) hint.classList.add('hidden');
    
    // Marker neu rendern
    renderHotspotMarkers(currentHotspotStepIndex);
}

function renderHotspotMarkers(stepIndex) {
    const markersContainer = document.getElementById('hotspotMarkers');
    markersContainer.innerHTML = '';
    
    const stepItem = document.querySelector(`.step-item[data-step="${stepIndex}"]`);
    if (!stepItem) return;
    
    const hotspotItems = stepItem.querySelectorAll('.hotspot-item');
    hotspotItems.forEach((item, hIndex) => {
        const posXInput = item.querySelector('input[name$="[position_x]"]');
        const posYInput = item.querySelector('input[name$="[position_y]"]');
        
        if (posXInput && posYInput && posXInput.value && posYInput.value) {
            const marker = document.createElement('div');
            marker.className = 'absolute w-6 h-6 -ml-3 -mt-3 rounded-full bg-red-500 border-2 border-white shadow pointer-events-auto cursor-pointer hover:bg-red-600';
            marker.style.left = posXInput.value + '%';
            marker.style.top = posYInput.value + '%';
            marker.title = `Hotspot ${hIndex + 1} (${posXInput.value}%, ${posYInput.value}%)`;
            marker.onclick = function(e) {
                e.stopPropagation();
                currentHotspotIndex = hIndex;
            };
            markersContainer.appendChild(marker);
        }
    });
}

function zoomHotspotImage(delta) {
    hotspotEditorZoom = Math.max(0.1, Math.min(5.0, hotspotEditorZoom + delta));
    const wrapper = document.getElementById('hotspotImageWrapper');
    wrapper.style.transform = `scale(${hotspotEditorZoom})`;
    document.getElementById('zoomLevel').textContent = Math.round(hotspotEditorZoom * 100) + '%';
}

function handleZoom(event) {
    event.preventDefault();
    const delta = event.deltaY > 0 ? -0.1 : 0.1;
    zoomHotspotImage(delta);
}

// ESC-Taste schließt Modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeHotspotEditor();
    }
});

// Klick-Handler für das Bild im Hotspot-Editor (am Container, fängt alle Klicks ab)
const hotspotContainer = document.getElementById('hotspotImageContainer');
if (hotspotContainer) {
    hotspotContainer.addEventListener('click', function(event) {
        // Nur verarbeiten, wenn das Modal sichtbar ist
        const modal = document.getElementById('hotspotEditorModal');
        if (modal.classList.contains('hidden')) return;
        
        const target = event.target;
        
        // Wenn auf den roten Marker geklickt wurde, nur auswählen, nicht Position setzen
        if (target.classList && target.classList.contains('rounded-full') && target.classList.contains('bg-red-500')) {
            return;
        }
        
        // Alle anderen Klicks im Bild-Bereich: Position setzen
        setHotspotPosition(event);
    });
}
</script>
@endsection
