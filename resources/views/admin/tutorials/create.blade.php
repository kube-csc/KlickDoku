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
            <label for="description" class="block text-sm font-medium text-gray-700">Kurzbeschreibung *</label>
            <textarea name="description" id="description" rows="2" required
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
            @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
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
</script>
@endsection
