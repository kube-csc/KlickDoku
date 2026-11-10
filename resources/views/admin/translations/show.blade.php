@extends('admin.layouts.app')

@section('title', 'Übersetzungen')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        @if($type === 'guides') 📖 Anleitungen
                        @elseif($type === 'tutorials') 🎓 Tutorials
                        @elseif($type === 'pages') 📄 Seiten
                        @else 🏷️ Kategorien
                        @endif
                        Übersetzungen
                    </h1>
                    <p class="mt-2 text-gray-600">
                        Übersetzungen für {{ $items->total() }} {{ str_plural($type) }} verwalten.
                    </p>
                </div>
                <a href="{{ route('admin.translations.index') }}"
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                    ← Zurück zur Übersicht
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        <!-- Inhaltsliste -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Titel ({{ strtoupper($defaultLocale) }})
                        </th>
                        @foreach($locales as $locale)
                            @if($locale !== $defaultLocale)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ strtoupper($locale) }}
                            </th>
                            @endif
                        @endforeach
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aktionen
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $item->title }}</div>
                            @if($item->description)
                            <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($item->description, 80) }}</div>
                            @endif
                        </td>

                        @foreach($locales as $locale)
                            @if($locale !== $defaultLocale)
                            <td class="px-6 py-4">
                                @php
                                    $translation = $item->translations->where('locale', $locale)->first();
                                @endphp
                                @if($translation)
                                <div class="text-sm text-green-600 font-medium">✓ Übersetzt</div>
                                <div class="text-xs text-gray-500 truncate max-w-xs">{{ Str::limit($translation->title, 50) }}</div>
                                @else
                                <div class="text-sm text-yellow-600">⚠ Nicht übersetzt</div>
                                @endif
                            </td>
                            @endif
                        @endforeach

                        <td class="px-6 py-4 text-sm">
                            <button onclick="openTranslationModal({{ $item->id }}, '{{ $item->title }}')"
                                    class="text-indigo-600 hover:text-indigo-800">
                                Bearbeiten
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Übersetzungs-Modal -->
<div id="translationModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Übersetzung bearbeiten</h3>
            <button onclick="closeTranslationModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>

        <form id="translationForm" method="POST" action="" class="mt-4 space-y-4">
            @csrf
            <input type="hidden" id="itemId" name="id">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Originaltitel ({{ strtoupper($defaultLocale) }})
                </label>
                <input type="text" id="originalTitle" readonly
                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600">
            </div>

            @foreach($locales as $locale)
                @if($locale !== $defaultLocale)
                <div class="border-t pt-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">
                        {{ strtoupper($locale) }} Übersetzung
                    </h4>

                    <input type="hidden" name="locale" value="{{ $locale }}">

                    <div class="space-y-3">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-medium text-gray-700">
                                    Titel *
                                </label>
                                <button type="button" onclick="aiTranslateField('{{ $locale }}', 'title', '{{ $defaultLocale }}')"
                                        class="text-xs px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition"
                                        title="Titel automatisch übersetzen">
                                    ✨ KI übersetzen
                                </button>
                            </div>
                            <input type="text" name="title" id="title_{{ $locale }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-medium text-gray-700">
                                    Beschreibung
                                </label>
                                <button type="button" onclick="aiTranslateField('{{ $locale }}', 'description', '{{ $defaultLocale }}')"
                                        class="text-xs px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition"
                                        title="Beschreibung automatisch übersetzen">
                                    ✨ KI übersetzen
                                </button>
                            </div>
                            <textarea name="description" id="description_{{ $locale }}" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>

                        @if($type !== 'categories')
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-medium text-gray-700">
                                    Inhalt
                                </label>
                                <button type="button" onclick="aiTranslateField('{{ $locale }}', 'content', '{{ $defaultLocale }}')"
                                        class="text-xs px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition"
                                        title="Inhalt automatisch übersetzen">
                                    ✨ KI übersetzen
                                </button>
                            </div>
                            <textarea name="content" id="content_{{ $locale }}" rows="6"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                        @endif
                    </div>

                    <div class="mt-4 flex justify-between">
                        <button type="button" onclick="deleteTranslation({{ $type }}, {{ $item->id }}, '{{ $locale }}')"
                                class="text-red-600 hover:text-red-800 text-sm">
                            Übersetzung löschen
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Speichern
                        </button>
                    </div>
                </div>
                @endif
            @endforeach
        </form>
    </div>
</div>

@push('scripts')
<script>
let currentItemId = null;

function openTranslationModal(itemId, originalTitle) {
    currentItemId = itemId;
    document.getElementById('translationModal').classList.remove('hidden');
    document.getElementById('originalTitle').value = originalTitle;
    document.getElementById('translationForm').action = `/admin/translations/${{!! json_encode($type) !!}/${itemId}/store`;

    // Lade bestehende Übersetzungen
    @foreach($locales as $locale)
        @if($locale !== $defaultLocale)
        @php
            $translation = $item->translations->where('locale', $locale)->first();
        @endphp
        if (itemId === {{ $item->id }}) {
            document.getElementById('title_{{ $locale }}').value = '{{ $translation->title ?? '' }}';
            document.getElementById('description_{{ $locale }}').value = '{{ $translation->description ?? '' }}';
            @if($type !== 'categories')
            document.getElementById('content_{{ $locale }}').value = '{{ $translation->content ?? '' }}';
            @endif
        }
        @endif
    @endforeach
}

function closeTranslationModal() {
    document.getElementById('translationModal').classList.add('hidden');
    currentItemId = null;
}

function deleteTranslation(type, itemId, locale) {
    if (confirm(`Möchten Sie die ${locale} Übersetzung wirklich löschen?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/translations/${type}/${itemId}/${locale}/destroy`;
        form.innerHTML = '@csrf' + '@method("DELETE")';
        document.body.appendChild(form);
        form.submit();
    }
}

// Modal schließen bei Klick außerhalb
document.getElementById('translationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeTranslationModal();
    }
});

// KI-Übersetzung eines einzelnen Feldes
async function aiTranslateField(targetLocale, field, sourceLocale) {
    const originalField = document.getElementById(field + '_' + sourceLocale);
    const originalText = originalField ? originalField.value.trim() : '';
    
    if (!originalText) {
        alert('Bitte geben Sie zuerst den Originaltext ein.');
        return;
    }

    const targetField = document.getElementById(field + '_' + targetLocale);
    if (!targetField) return;

    const btn = targetField.closest('div').querySelector('button[onclick*="aiTranslateField"]');
    if (btn) {
        btn.disabled = true;
        btn.textContent = '⏳';
    }

    try {
        const response = await fetch('{{ route("admin.ai.generate-translation") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                originalText: originalText,
                sourceLocale: sourceLocale,
                targetLocale: targetLocale,
                context: field === 'content' ? 'Seiteninhalt' : field === 'description' ? 'Kurze Beschreibung' : 'Titel'
            })
        });

        const data = await response.json();
        if (data.translation) {
            targetField.value = data.translation;
        }
    } catch (error) {
        console.error('Fehler bei der KI-Übersetzung:', error);
        alert('Fehler beim Übersetzen.');
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.textContent = '✨ KI übersetzen';
        }
    }
}
</script>
@endpush
@endsection
