@extends('admin.layouts.app')

@section('title', 'Anleitung bearbeiten')
@section('heading', 'Anleitung bearbeiten')

@section('content')

<!-- Bild-Upload Formular (separat, vor dem Hauptformular) -->
<form action="{{ route('admin.guides.images.store', $guide->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6 mb-6">
    @csrf
    <h3 class="text-lg font-medium text-gray-900 mb-4">Bilder</h3>
    
    <!-- Bestehende Bilder -->
    @if($guide->images->whereNull('guide_step_id')->count() > 0)
        <div class="grid grid-cols-3 gap-4 mb-4">
            @foreach($guide->images->whereNull('guide_step_id') as $image)
                <div class="relative group">
                    <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $image->alt_text ?? '' }}"
                         loading="lazy"
                         class="w-full h-32 object-cover rounded-lg border">
                    <button type="button" 
                            onclick="confirmDelete('{{ route('admin.guide.images.destroy', $image->id) }}')"
                            class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition">
                        ✕
                    </button>
                    @if($image->caption)
                        <p class="text-xs text-gray-500 mt-1 text-center">{{ $image->caption }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <!-- Bild hochladen -->
    <div class="bg-gray-50 p-4 rounded-lg">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-3 py-2 rounded mb-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->has('image'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded mb-3 text-sm">
                {{ $errors->first('image') }}
            </div>
        @endif

        <div class="space-y-3">
            <div>
                <label class="block text-sm text-gray-600">Bild auswählen</label>
                <input type="file" name="image" accept="image/*" required
                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                @error('image')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm text-gray-600">Beschriftung (optional)</label>
                <input type="text" name="caption" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm text-gray-600">Alt-Text (optional)</label>
                <input type="text" name="alt_text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <button type="submit" class="px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 transition">
                Bild hochladen
            </button>
        </div>
    </div>
</form>

<!-- Hauptformular für Anleitung + Schritte -->
<form action="{{ route('admin.guides.update', $guide) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="bg-white rounded-lg shadow p-6 space-y-6">
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Titel *</label>
            <input type="text" name="title" id="title" value="{{ old('title', $guide->title) }}" required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('title')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Kurzbeschreibung *</label>
            <textarea name="description" id="description" rows="2" required
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $guide->description) }}</textarea>
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
                    <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $guide->meta_title) }}" maxlength="255"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Wird automatisch aus dem Titel generiert, wenn leer gelassen">
                    <p class="mt-1 text-xs text-gray-500">Empfohlen: 50-60 Zeichen für Suchmaschinen</p>
                </div>
                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700">SEO-Beschreibung (optional)</label>
                    <textarea name="meta_description" id="meta_description" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Wird automatisch aus der Kurzbeschreibung generiert, wenn leer gelassen">{{ old('meta_description', $guide->meta_description) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Empfohlen: 150-160 Zeichen für Suchmaschinen</p>
                </div>
                <div>
                    <label for="og_image" class="block text-sm font-medium text-gray-700">OpenGraph-Bild (optional)</label>
                    <input type="text" name="og_image" id="og_image" value="{{ old('og_image', $guide->og_image) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Pfad zum Bild, z.B. storage/guide-bilder/og-image.jpg">
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
                    <option value="{{ $category->id }}" {{ old('category_id', $guide->category_id) == $category->id ? 'selected' : '' }}>
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
                    <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', $guide->tags->pluck('id')->toArray())) ? 'selected' : '' }}>
                        {{ $tag->name }}
                    </option>
                    @endforeach
                </select>
                <p class="mt-1 text-sm text-gray-500">Strg+Klick für mehrere Tags</p>
            </div>
        </div>

        <div>
            <label for="content" class="block text-sm font-medium text-gray-700">Inhalt *</label>
            <textarea name="content" id="content" rows="6" required
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('content', $guide->content) }}</textarea>
            @error('content')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center">
            <input type="checkbox" name="published" id="published" value="1" 
                   {{ old('published', $guide->published) ? 'checked' : '' }}
                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <label for="published" class="ml-2 block text-sm text-gray-700">Veröffentlicht</label>
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
            
            <div id="stepsContainer">
                @foreach($guide->steps as $index => $step)
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
                            <textarea name="steps[{{ $index }}][description]" rows="3" required
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old("steps.$index.description", $step->description) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600">Tipp (optional)</label>
                            <textarea name="steps[{{ $index }}][tip]" rows="2"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old("steps.$index.tip", $step->tip) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600">Warnung (optional)</label>
                            <textarea name="steps[{{ $index }}][warning]" rows="2"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old("steps.$index.warning", $step->warning) }}</textarea>
                        </div>
                    </div>

                    <!-- Schritt-Übersetzungen -->
                    <div class="mt-4 border-t border-gray-200 pt-4">
                        <button type="button" class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center gap-1"
                                onclick="toggleStepTranslation({{ $step->id }}, 'guide')">
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
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <div class="flex justify-between items-center mb-2">
                                        <h5 class="text-sm font-medium text-blue-800">
                                            {{ $locale === 'en' ? '🇬🇧 English' : '🇩🇪 Deutsch' }}
                                        </h5>
                                        @if($trans)
                                            <form action="{{ route('admin.guide.step.translations.destroy', [$guide, $step->id, $locale]) }}" method="POST"
                                                  onsubmit="return confirm('Übersetzung wirklich löschen?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Löschen</button>
                                            </form>
                                        @endif
                                    </div>
                                    <form action="{{ route('admin.guide.step.translations.store', [$guide, $step->id]) }}" method="POST" class="space-y-2">
                                        @csrf
                                        <input type="hidden" name="locale" value="{{ $locale }}">
                                        <div>
                                            <label class="block text-xs text-blue-700">Titel</label>
                                            <input type="text" name="title" value="{{ old("step_trans.$step->id.$locale.title", $trans->title ?? '') }}"
                                                   class="mt-1 block w-full rounded-md border-blue-200 shadow-sm text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-blue-700">Beschreibung</label>
                                            <textarea name="instruction" rows="2"
                                                      class="mt-1 block w-full rounded-md border-blue-200 shadow-sm text-sm">{{ old("step_trans.$step->id.$locale.instruction", $trans->instruction ?? '') }}</textarea>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-xs text-blue-700">Tipp</label>
                                                <textarea name="tip" rows="1"
                                                          class="mt-1 block w-full rounded-md border-blue-200 shadow-sm text-sm">{{ old("step_trans.$step->id.$locale.tip", $trans->tip ?? '') }}</textarea>
                                            </div>
                                            <div>
                                                <label class="block text-xs text-blue-700">Warnung</label>
                                                <textarea name="warning" rows="1"
                                                          class="mt-1 block w-full rounded-md border-blue-200 shadow-sm text-sm">{{ old("step_trans.$step->id.$locale.warning", $trans->warning ?? '') }}</textarea>
                                            </div>
                                        </div>
                                        <button type="submit" class="mt-2 px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition">
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
        <a href="{{ route('admin.guides.index') }}" 
           class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
            Abbrechen
        </a>
        <button type="submit" 
                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
            Änderungen speichern
        </button>
    </div>
</form>

<script>
function confirmDelete(url) {
    if (confirm('Bild wirklich löschen?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        form.innerHTML = '@csrf\n@method("DELETE")';
        document.body.appendChild(form);
        form.submit();
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

let stepCount = {{ $guide->steps->count() }};

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
                <textarea name="steps[${stepCount}][description]" rows="3" required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
            </div>
            <div>
                <label class="block text-sm text-gray-600">Tipp (optional)</label>
                <textarea name="steps[${stepCount}][tip]" rows="2"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
            </div>
            <div>
                <label class="block text-sm text-gray-600">Warnung (optional)</label>
                <textarea name="steps[${stepCount}][warning]" rows="2"
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
</script>
@endsection
