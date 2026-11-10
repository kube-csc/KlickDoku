@extends('admin.layouts.app')

@section('title', 'Neues Tutorial')
@section('heading', 'Neues Tutorial')

@section('content')
<form action="{{ route('admin.tutorials.store') }}" method="POST">
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

        <div class="flex items-center">
            <input type="checkbox" name="published" id="published" value="1" 
                   {{ old('published') ? 'checked' : '' }}
                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <label for="published" class="ml-2 block text-sm text-gray-700">Sofort veröffentlichen</label>
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
        <a href="{{ route('admin.tutorials.index') }}" 
           class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
            Abbrechen
        </a>
        <button type="submit" 
                class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition">
            Tutorial erstellen
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
                    <button type="button" onclick="aiGenerateStepText(${stepCount}, 'instruction', 'tutorial_step')"
                            class="text-xs px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition"
                            title="Anweisung automatisch generieren">
                        ✨ KI
                    </button>
                </div>
                <textarea name="steps[${stepCount}][instruction]" id="step_${stepCount}_instruction" rows="3" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
            </div>
            <div class="border-t pt-3">
                <p class="text-sm text-gray-600 mb-2">Screenshot hochladen:</p>
                <div class="flex gap-2 items-end">
                    <div class="flex-1">
                        <input type="file" name="screenshot_new_${stepCount}" accept="image/*"
                               class="block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700">
                    </div>
                    <div>
                        <button type="button" onclick="startScreenshotCaptureForNew(${stepCount})"
                                class="px-3 py-2 bg-green-500 text-white text-xs rounded hover:bg-green-600 whitespace-nowrap"
                                title="Wählen Sie einen Bereich auf dem Bildschirm aus, der als Screenshot hochgeladen wird">
                            📸 Screenshot mit Bereichsauswahl
                        </button>
                    </div>
                </div>
                <div id="screenshotPreview_${stepCount}" class="mt-2 hidden">
                    <p class="text-xs text-gray-400">🔍 Auf das Bild klicken, um Hotspot-Positionen visuell zu setzen</p>
                </div>
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
            <button type="button" onclick="openHotspotEditor('', 0, 0, '${stepIndex}', '${hotspotCount}')"
                    class="px-2 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600 whitespace-nowrap" title="Position auf dem Bild setzen">📍 Position</button>
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

/* ===== Hotspot-Editor Funktionen ===== */
let currentHotspotStepIndex = null;
let currentHotspotIndex = null;
let hotspotEditorZoom = 1.0;

function openHotspotEditor(imageSrc, imageWidth, imageHeight, stepIndex, hotspotIndex) {
    const modal = document.getElementById('hotspotEditorModal');
    const img = document.getElementById('hotspotEditorImage');
    const wrapper = document.getElementById('hotspotImageWrapper');
    
    // Wenn keine Bild-URL übergeben, aus dem Schritt ermitteln
    if (!imageSrc) {
        const stepItem = document.querySelector(`.step-item[data-step="${stepIndex}"]`);
        if (stepItem) {
            const screenshotImg = stepItem.querySelector('.screenshot-preview-img');
            if (screenshotImg) {
                imageSrc = screenshotImg.src;
            }
        }
    }
    
    // Wenn immer noch kein Bild
    if (!imageSrc) {
        alert('Kein Screenshot vorhanden. Laden Sie zuerst einen Screenshot hoch.');
        return;
    }
    
    img.src = imageSrc;
    hotspotEditorZoom = 1.0;
    wrapper.style.transform = 'scale(1)';
    document.getElementById('zoomLevel').textContent = '100%';
    
    currentHotspotStepIndex = stepIndex;
    currentHotspotIndex = hotspotIndex;
    modal.classList.remove('hidden');
    document.getElementById('selectedPosition').textContent = 'Noch keine Position ausgewählt';
    
    img.onload = function() {
        renderHotspotMarkers(stepIndex);
    };
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
    
    const stepItem = document.querySelector(`.step-item[data-step="${currentHotspotStepIndex}"]`);
    if (!stepItem) {
        console.error('Schritt nicht gefunden:', currentHotspotStepIndex);
        return;
    }
    
    console.log('Hotspot-Position gesetzt:', {posX, posY, stepIndex: currentHotspotStepIndex, hotspotIndex: currentHotspotIndex});
    
    if (currentHotspotIndex === null || currentHotspotIndex === '-1') {
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
            <button type="button" onclick="openHotspotEditor('', 0, 0, '${currentHotspotStepIndex}', '${hotspotCount}')"
                    class="px-2 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600 whitespace-nowrap" title="Position auf dem Bild setzen">📍 Position</button>
            <button type="button" class="remove-hotspot text-red-500 hover:text-red-700 text-sm">✕</button>
        </div>`;
        
        hotspotsContainer.insertAdjacentHTML('beforeend', hotspotHtml);
    } else {
        const posXInput = stepItem.querySelector(`input[name="steps[${currentHotspotStepIndex}][hotspots][${currentHotspotIndex}][position_x]"]`);
        const posYInput = stepItem.querySelector(`input[name="steps[${currentHotspotStepIndex}][hotspots][${currentHotspotIndex}][position_y]"]`);
        
        if (posXInput) posXInput.value = posX;
        if (posYInput) posYInput.value = posY;
        
        const posDisplay = stepItem.querySelector(`.hotspot-pos-display[data-hotspot="${currentHotspotIndex}"]`);
        if (posDisplay) posDisplay.textContent = `${posX}%, ${posY}%`;
    }
    
    document.getElementById('selectedPosition').textContent = `X: ${posX}%, Y: ${posY}% — Position wurde im Formular gespeichert`;
    
    // Hinweis ausblenden nach erfolgreichem Klick
    const hint = document.getElementById('hotspotHint');
    if (hint) hint.classList.add('hidden');
    
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

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeHotspotEditor();
    }
});

// Klick-Handler für das Bild im Hotspot-Editor (am Container, fängt alle Klicks ab)
const hotspotContainer = document.getElementById('hotspotImageContainer');
if (hotspotContainer) {
    hotspotContainer.addEventListener('click', function(event) {
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
                type: 'tutorial',
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

// KI-Schritttext generieren (Anweisung)
async function aiGenerateStepText(stepIndex, field, type) {
    const stepItem = document.querySelectorAll('.step-item')[stepIndex];
    if (!stepItem) return;

    const titleInput = stepItem.querySelector('input[name*="[title]"]');
    const title = titleInput ? titleInput.value.trim() : '';
    if (!title) {
        alert('Bitte geben Sie zuerst einen Schritt-Titel ein.');
        return;
    }

    const tutorialTitle = document.getElementById('title')?.value.trim() || '';

    // Kontext-Daten aus dem KI-Panel
    const { keywords, referenceUrls, customPrompt: panelCustomPrompt } = getAiContextData();

    // Feld-spezifische Anweisung mit benutzerdefinierter Anweisung kombinieren
    const fieldPrompt = 'Formuliere eine klare, schrittweise Anweisung.';
    const customPrompt = panelCustomPrompt
        ? panelCustomPrompt + ' ' + fieldPrompt
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
                category: tutorialTitle,
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
@endsection
