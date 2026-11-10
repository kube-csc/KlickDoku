@extends('admin.layouts.app')

@section('title', 'Anleitung bearbeiten')
@section('heading', 'Anleitung bearbeiten')

@section('content')

<!-- Globale Bilder (nicht einem Schritt zugeordnet) -->
<form action="{{ route('admin.guides.images.store', $guide->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6 mb-6">
    @csrf
    <h3 class="text-lg font-medium text-gray-900 mb-4">Globale Bilder</h3>
    
    <!-- Bestehende globale Bilder -->
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

    <!-- Globales Bild hochladen -->
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
            <div class="flex items-center justify-between mb-1">
                <label for="description" class="block text-sm font-medium text-gray-700">Kurzbeschreibung *</label>
                <button type="button" id="aiGenerateDesc"
                        class="text-xs px-2 py-1 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition"
                        title="Beschreibung automatisch generieren">
                    ✨ KI generieren
                </button>
            </div>
            <textarea name="description" id="description" rows="2" required
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $guide->description) }}</textarea>
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
            <div class="flex items-center justify-between mb-1">
                <label for="content" class="block text-sm font-medium text-gray-700">Inhalt *</label>
                <button type="button" id="aiGenerateContent"
                        class="text-xs px-2 py-1 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition"
                        title="Inhalt automatisch generieren">
                    ✨ KI generieren
                </button>
            </div>
            <textarea name="content" id="content" rows="6" required
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('content', $guide->content) }}</textarea>
            @error('content')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">Die KI erstellt einen Entwurf basierend auf Titel, Kategorie und Schritten — diesen können Sie dann anpassen.</p>
        </div>

        <div class="flex items-center">
            <input type="checkbox" name="published" id="published" value="1" 
                   {{ old('published', $guide->published) ? 'checked' : '' }}
                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <label for="published" class="ml-2 block text-sm text-gray-700">Veröffentlicht</label>
        </div>

        <!-- Schritte -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Schritte</h3>

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
                @foreach($guide->steps as $index => $step)
                <div class="step-item bg-gray-50 p-4 rounded-lg mb-4" data-step="{{ $index }}">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-medium text-gray-700">Schritt {{ $index + 1 }}</h4>
                        <button type="button" class="remove-step text-red-500 hover:text-red-700 text-sm">Entfernen</button>
                    </div>
                    <div class="space-y-3">
                        <input type="hidden" name="steps[{{ $index }}][id]" value="{{ $step->id }}">
                        <div>
                            <label class="block text-sm text-gray-600">Titel *</label>
                            <input type="text" name="steps[{{ $index }}][title]" value="{{ old("steps.$index.title", $step->title) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm text-gray-600">Beschreibung *</label>
                                <button type="button" onclick="aiGenerateStepText({{ $index }}, 'description', 'guide_step')"
                                        class="text-xs px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition"
                                        title="Beschreibung automatisch generieren">
                                    ✨ KI
                                </button>
                            </div>
                            <textarea name="steps[{{ $index }}][description]" id="step_{{ $index }}_description" rows="3" required
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old("steps.$index.description", $step->description) }}</textarea>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm text-gray-600">Tipp (optional)</label>
                                <button type="button" onclick="aiGenerateStepText({{ $index }}, 'tip', 'guide_step')"
                                        class="text-xs px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition"
                                        title="Tipp automatisch generieren">
                                    ✨ KI
                                </button>
                            </div>
                            <textarea name="steps[{{ $index }}][tip]" id="step_{{ $index }}_tip" rows="2"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old("steps.$index.tip", $step->tip) }}</textarea>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm text-gray-600">Warnung (optional)</label>
                                <button type="button" onclick="aiGenerateStepText({{ $index }}, 'warning', 'guide_step')"
                                        class="text-xs px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition"
                                        title="Warnung automatisch generieren">
                                    ✨ KI
                                </button>
                            </div>
                            <textarea name="steps[{{ $index }}][warning]" id="step_{{ $index }}_warning" rows="2"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old("steps.$index.warning", $step->warning) }}</textarea>
                        </div>
                    </div>

                    <!-- Schritt-Bilder -->
                    <div class="step-images-section mt-4 border-t border-gray-200 pt-4">
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Bilder für diesen Schritt</h5>
                        
                        <!-- Bestehende Schritt-Bilder -->
                        @if($step->images->count() > 0)
                            <div class="grid grid-cols-3 gap-4 mb-3">
                                @foreach($step->images as $stepImage)
                                    <div class="relative group">
                                        <img src="{{ asset('storage/' . $stepImage->path) }}" alt="{{ $stepImage->alt_text ?? '' }}"
                                             loading="lazy"
                                             class="w-full h-32 object-cover rounded-lg border">
                                        <button type="button" 
                                                onclick="confirmDelete('{{ route('admin.guide.images.destroy', $stepImage->id) }}')"
                                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition">
                                            ✕
                                        </button>
                                        @if($stepImage->caption)
                                            <p class="text-xs text-gray-500 mt-1 text-center">{{ $stepImage->caption }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Bild für Schritt hochladen -->
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <div class="space-y-2">
                                <div>
                                    <label class="block text-xs text-blue-700">Bild auswählen</label>
                                    <input type="file" id="stepImageFileInput_{{ $step->id }}" accept="image/*"
                                           class="mt-1 block w-full text-xs text-gray-500 file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-xs text-blue-700">Beschriftung</label>
                                        <input type="text" id="stepImageCaptionInput_{{ $step->id }}" class="mt-1 block w-full rounded-md border-blue-200 shadow-sm text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-blue-700">Alt-Text</label>
                                        <input type="text" id="stepImageAltTextInput_{{ $step->id }}" class="mt-1 block w-full rounded-md border-blue-200 shadow-sm text-sm">
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" onclick="uploadStepImage({{ $step->id }})" class="px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition">
                                        Bild hochladen
                                    </button>
                                    <button type="button" onclick="startGuideScreenshotCapture({{ $guide->id }}, {{ $step->id }})" class="px-3 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600 transition">
                                        📸 Screenshot mit Bereichsauswahl
                                    </button>
                                </div>
                            </div>
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
                                            <button type="button" onclick="deleteStepTranslation({{ $step->id }}, '{{ $locale }}', '{{ route('admin.guide.step.translations.destroy', [$guide, $step->id, $locale]) }}')"
                                                    class="text-xs text-red-500 hover:text-red-700">Löschen</button>
                                        @endif
                                    </div>
                                    <div class="space-y-2" id="stepTranslationForm_{{ $step->id }}_{{ $locale }}">
                                        <input type="hidden" name="locale" value="{{ $locale }}">
                                        <div>
                                            <label class="block text-xs text-blue-700">Titel</label>
                                            <input type="text" id="stepTransTitle_{{ $step->id }}_{{ $locale }}" value="{{ old("step_trans.$step->id.$locale.title", $trans->title ?? '') }}"
                                                   class="mt-1 block w-full rounded-md border-blue-200 shadow-sm text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-blue-700">Beschreibung</label>
                                            <textarea id="stepTransInstruction_{{ $step->id }}_{{ $locale }}" rows="2"
                                                      class="mt-1 block w-full rounded-md border-blue-200 shadow-sm text-sm">{{ old("step_trans.$step->id.$locale.instruction", $trans->instruction ?? '') }}</textarea>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-xs text-blue-700">Tipp</label>
                                                <textarea id="stepTransTip_{{ $step->id }}_{{ $locale }}" rows="1"
                                                          class="mt-1 block w-full rounded-md border-blue-200 shadow-sm text-sm">{{ old("step_trans.$step->id.$locale.tip", $trans->tip ?? '') }}</textarea>
                                            </div>
                                            <div>
                                                <label class="block text-xs text-blue-700">Warnung</label>
                                                <textarea id="stepTransWarning_{{ $step->id }}_{{ $locale }}" rows="1"
                                                          class="mt-1 block w-full rounded-md border-blue-200 shadow-sm text-sm">{{ old("step_trans.$step->id.$locale.warning", $trans->warning ?? '') }}</textarea>
                                            </div>
                                        </div>
                                        <button type="button" onclick="saveStepTranslation({{ $step->id }}, '{{ $locale }}', '{{ route('admin.guide.step.translations.store', [$guide, $step->id]) }}')"
                                                class="mt-2 px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition">
                                            {{ $trans ? 'Aktualisieren' : 'Speichern' }}
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Schritt hinzufügen Button unten -->
            <div class="mt-4">
                <button type="button" id="addStep" 
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                    + Schritt hinzufügen
                </button>
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
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value;

function confirmDelete(url) {
    if (confirm('Bild wirklich löschen?')) {
        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('_method', 'DELETE');
        
        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok || response.redirected) {
                location.reload();
            } else {
                alert('Fehler beim Löschen des Bildes.');
            }
        })
        .catch(error => {
            console.error('Fehler:', error);
            alert('Fehler beim Löschen des Bildes.');
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

// Bild für einen Schritt hochladen (per AJAX, vermeidet verschachtelte Formulare)
function uploadStepImage(stepId) {
    const fileInput = document.getElementById('stepImageFileInput_' + stepId);
    const captionInput = document.getElementById('stepImageCaptionInput_' + stepId);
    const altTextInput = document.getElementById('stepImageAltTextInput_' + stepId);
    
    if (!fileInput.files || fileInput.files.length === 0) {
        alert('Bitte wählen Sie ein Bild aus.');
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value;
    
    const formData = new FormData();
    formData.append('image', fileInput.files[0]);
    formData.append('step_id', stepId);
    formData.append('_token', csrfToken);
    if (captionInput.value) {
        formData.append('caption', captionInput.value);
    }
    if (altTextInput.value) {
        formData.append('alt_text', altTextInput.value);
    }
    
    const url = "{{ route('admin.guides.images.step.store', $guide->id) }}";
    console.log('Upload Schritt-Bild:', { url: url, stepId: stepId, file: fileInput.files[0].name, csrfToken: csrfToken ? 'vorhanden' : 'FEHLEND' });
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        body: formData
    })
    .then(response => {
        console.log('Antwort Status:', response.status, 'OK:', response.ok, 'Redirected:', response.redirected);
        
        // Bei Validierungsfehler (422) oder anderem Fehler
        if (!response.ok) {
            // Versuche JSON zu parsen, falls HTML zurückkommt, zeige Status
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json().then(err => {
                    console.error('Fehlerantwort:', err);
                    if (err.errors) {
                        const messages = Object.values(err.errors).flat().join('\n');
                        alert('Fehler:\n' + messages);
                    } else {
                        alert('Fehler beim Hochladen: ' + (err.message || 'Unbekannter Fehler'));
                    }
                    throw err;
                });
            } else {
                // HTML-Antwort (z.B. CSRF-Fehler Seite oder Validierungs-Redirect)
                console.error('Nicht-JSON Fehlerantwort (HTML), Status:', response.status);
                alert('Fehler beim Hochladen des Bildes (HTTP ' + response.status + '). Bitte prüfen Sie die Browser-Konsole.');
                throw new Error('HTTP ' + response.status);
            }
        }
        
        // Erfolgreiche Antwort - prüfe ob JSON oder HTML (Redirect)
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json().then(data => {
                console.log('Erfolgreich hochgeladen (JSON):', data);
                if (data.success) {
                    location.reload();
                }
            });
        } else {
            // HTML-Antwort nach Redirect (Laravel redirect()->back())
            console.log('Erfolgreich hochgeladen (Redirect), Seite wird neu geladen');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Fetch-Fehler:', error);
    });
}

// Schritt-Übersetzung speichern (per AJAX, vermeidet verschachtelte Formulare)
function saveStepTranslation(stepId, locale, url) {
    const title = document.getElementById('stepTransTitle_' + stepId + '_' + locale).value;
    const instruction = document.getElementById('stepTransInstruction_' + stepId + '_' + locale).value;
    const tip = document.getElementById('stepTransTip_' + stepId + '_' + locale).value;
    const warning = document.getElementById('stepTransWarning_' + stepId + '_' + locale).value;
    
    const formData = new FormData();
    formData.append('locale', locale);
    formData.append('title', title);
    formData.append('instruction', instruction);
    formData.append('tip', tip);
    formData.append('warning', warning);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value);
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok || response.redirected) {
            location.reload();
        } else {
            throw new Error('Speichern fehlgeschlagen');
        }
    })
    .catch(error => {
        console.error('Fehler:', error);
        alert('Fehler beim Speichern der Übersetzung. Bitte versuchen Sie es erneut.');
    });
}

// Schritt-Übersetzung löschen (per AJAX, vermeidet verschachtelte Formulare)
function deleteStepTranslation(stepId, locale, url) {
    if (!confirm('Übersetzung wirklich löschen?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value);
    formData.append('_method', 'DELETE');
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok || response.redirected) {
            location.reload();
        } else {
            throw new Error('Löschen fehlgeschlagen');
        }
    })
    .catch(error => {
        console.error('Fehler:', error);
        alert('Fehler beim Löschen der Übersetzung. Bitte versuchen Sie es erneut.');
    });
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
    const categoryOption = categorySelect ? categorySelect.options[categorySelect.selectedIndex] : null;
    const category = categoryOption && categoryOption.value ? categoryOption.text : '';

    const tagSelect = document.getElementById('tags');
    const tags = tagSelect ? Array.from(tagSelect.selectedOptions).map(opt => opt.text) : [];

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
