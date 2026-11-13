@extends('layouts.public')

@section('title', $tutorial->getTranslationAttribute('title', $locale) ?? $tutorial->title)
@section('meta_description', $tutorial->meta_description ?? $tutorial->getTranslationAttribute('description', $locale) ?? $tutorial->description)
@section('canonical', url()->current())
@section('og_title', $tutorial->getTranslationAttribute('title', $locale) ?? $tutorial->title)
@section('og_description', $tutorial->meta_description ?? $tutorial->getTranslationAttribute('description', $locale) ?? $tutorial->description)
@section('og_type', 'article')
@if($tutorial->og_image)
    @section('og_image', asset($tutorial->og_image))
@endif

@section('content')
@php
    $tutTitle = $tutorial->getTranslationAttribute('title', $locale) ?? $tutorial->title;
    $tutDesc = $tutorial->getTranslationAttribute('description', $locale) ?? $tutorial->description;
    $tutCatName = $tutorial->category ? ($tutorial->category->getTranslationAttribute('title', $locale) ?? $tutorial->category->name) : null;
    $currentStep = $progress && $progress->current_step_id
        ? $tutorial->steps->find($progress->current_step_id)
        : $tutorial->steps->first();
    $currentStepNumber = $currentStep ? $currentStep->step_number : 1;
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('tutorials.index', ['locale' => $locale]) }}"
           class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            {{ __('common.back_to_tutorials') }}
        </a>

        @if($tutCatName)
            <span class="inline-block px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full mb-2">
                {{ $tutCatName }}
            </span>
        @endif

        <h1 class="text-3xl font-bold text-gray-900">{{ $tutTitle }}</h1>
        <p class="mt-2 text-gray-600">{{ $tutDesc }}</p>

        <div class="flex items-center gap-4 mt-3 text-sm text-gray-500">
            <span>{{ $tutorial->steps->count() }} {{ __('common.steps') }}</span>
            @if($tutorial->estimated_duration_minutes)
                <span>~{{ $tutorial->estimated_duration_minutes }} {{ __('common.minutes') }}</span>
            @endif
            <span>{{ $tutorial->view_count }} {{ __('common.views') }}</span>
        </div>
    </div>

    <!-- Fortschrittsbalken -->
    <div class="mb-6">
        <div class="flex justify-between text-sm text-gray-500 mb-2">
            <span id="progress-text">{{ __('common.progress') }}: {{ $currentStepNumber }} / {{ $tutorial->steps->count() }}</span>
            <span id="progress-percent">{{ round(($currentStepNumber / $tutorial->steps->count()) * 100) }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-3">
            <div id="progress-bar"
                 class="bg-purple-600 h-3 rounded-full transition-all duration-300"
                 style="width: {{ round(($currentStepNumber / $tutorial->steps->count()) * 100) }}%"></div>
        </div>
    </div>

    <!-- Tutorial-Player -->
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <!-- Schritt-Titel -->
        <div class="px-6 py-4 bg-purple-50 border-b border-purple-100">
            <h2 class="text-xl font-semibold text-purple-900" id="step-title">
                @if($currentStep)
                    {{ $currentStep->title }}
                @else
                    {{ __('common.no_steps') }}
                @endif
            </h2>
        </div>

        <!-- Screenshot-Bereich mit Hotspots -->
        <div class="relative bg-gray-100" id="screenshot-container"
             style="position: relative; min-height: 400px;">
            @if($currentStep && $currentStep->screenshot_path)
                <img id="screenshot-image"
                     src="{{ asset('storage/' . $currentStep->screenshot_path) }}"
                     alt="{{ $currentStep->title }}"
                     loading="lazy"
                     class="w-full h-auto"
                     style="display: block;">
            @else
                <div class="flex items-center justify-center h-96 bg-gradient-to-br from-purple-100 to-blue-100">
                    <div class="text-center">
                        <svg class="w-20 h-20 text-purple-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="mt-4 text-gray-400">{{ __('common.no_screenshot') }}</p>
                    </div>
                </div>
            @endif

            <!-- Hotspot-Overlays -->
            @if($currentStep)
                @foreach($currentStep->hotspots as $hotspot)
                    <div class="tutorial-hotspot"
                         data-hotspot-id="{{ $hotspot->id }}"
                         data-clickable="{{ $hotspot->is_clickable ? '1' : '0' }}"
                         data-label="{{ $hotspot->label ?? '' }}"
                         data-link="{{ $hotspot->link ?? '' }}"
                         style="
                            position: absolute;
                            left: {{ $hotspot->position_x }}%;
                            top: {{ $hotspot->position_y }}%;
                            width: {{ $hotspot->radius ?? 30 }}px;
                            height: {{ $hotspot->radius ?? 30 }}px;
                            transform: translate(-50%, -50%);
                            cursor: {{ $hotspot->is_clickable ? 'pointer' : 'default' }};
                            z-index: 10;
                         ">
                        <!-- Pulsierender Kreis -->
                        <div class="hotspot-pulse"
                             style="
                                width: 100%;
                                height: 100%;
                                border-radius: 50%;
                                background: rgba(147, 51, 234, 0.3);
                                animation: hotspot-pulse 2s infinite;
                             "></div>
                        <div class="hotspot-core"
                             style="
                                position: absolute;
                                top: 50%;
                                left: 50%;
                                transform: translate(-50%, -50%);
                                width: 28px;
                                height: 28px;
                                border-radius: 50%;
                                background: {{ $hotspot->link ? '#2563eb' : '#9333ea' }};
                                border: 2px solid white;
                                box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-size: 14px;
                             ">
                            @if($hotspot->link)
                                🔗
                            @endif
                        </div>
                        <!-- Label -->
                        @if($hotspot->label)
                            <div class="hotspot-label"
                                 style="
                                    position: absolute;
                                    bottom: 100%;
                                    left: 50%;
                                    transform: translateX(-50%);
                                    margin-bottom: 8px;
                                    padding: 4px 8px;
                                    background: rgba(0,0,0,0.8);
                                    color: white;
                                    font-size: 12px;
                                    border-radius: 4px;
                                    white-space: nowrap;
                                    opacity: 0;
                                    transition: opacity 0.2s;
                                    pointer-events: none;
                                 ">
                                {{ $hotspot->label }}
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Anweisungstext -->
        @if($currentStep && $currentStep->instruction)
            <div class="px-6 py-4 border-t border-gray-100">
                <p class="text-gray-700 leading-relaxed" id="step-instruction">
                    {!! nl2br(e($currentStep->instruction)) !!}
                </p>
            </div>
        @endif

        <!-- Tipp / Warnung -->
        <div id="tip-warning-container">
        @if($currentStep)
            @if($currentStep->tip)
                <div class="mx-6 mt-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-r">
                    <p class="text-sm text-yellow-800">
                        <strong>💡 {{ __('common.tip') }}:</strong> {{ $currentStep->tip }}
                    </p>
                </div>
            @endif
            @if($currentStep->warning)
                <div class="mx-6 mt-4 p-4 bg-red-50 border-l-4 border-red-400 rounded-r">
                    <p class="text-sm text-red-800">
                        <strong>⚠️ {{ __('common.warning') }}:</strong> {{ $currentStep->warning }}
                    </p>
                </div>
            @endif
        @endif
        </div>

        <!-- Navigation -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
            <button id="prev-step-btn"
                    onclick="prevStep()"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    @if($currentStepNumber <= 1) disabled @endif>
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('common.previous') }}
            </button>

            <span class="text-sm text-gray-500 step-counter-text">
                {{ __('common.step') }} {{ $currentStepNumber }} / {{ $tutorial->steps->count() }}
            </span>

            @if($currentStepNumber < $tutorial->steps->count())
                <button id="next-step-btn"
                        onclick="nextStep()"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md text-white hover:bg-purple-700">
                    {{ __('common.next') }}
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            @else
                <button id="complete-btn"
                        onclick="completeTutorial()"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md text-white hover:bg-green-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ __('common.complete_tutorial') }}
                </button>
            @endif
        </div>
    </div>

    <!-- Schritt-Übersicht (Sidebar) -->
    <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('common.steps_overview') }}</h3>
        <div class="space-y-2" id="steps-overview-list">
            @foreach($tutorial->steps as $step)
                <button onclick="goToStep({{ $step->step_number }})"
                        data-step="{{ $step->step_number }}"
                        class="step-overview-btn w-full flex items-center px-4 py-3 rounded-md text-left transition-colors
                               {{ $step->step_number === $currentStepNumber
                                   ? 'bg-purple-50 text-purple-700 border-l-4 border-purple-500'
                                   : 'hover:bg-gray-50 text-gray-700 border-l-4 border-transparent' }}">
                    <span class="step-number-badge flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full
                                 {{ $step->step_number === $currentStepNumber
                                     ? 'bg-purple-500 text-white'
                                     : 'bg-gray-200 text-gray-600' }}">
                        {{ $step->step_number }}
                    </span>
                    <span class="ml-3 text-sm font-medium step-title-text">{{ $step->title }}</span>
                    @if($step->hotspots->count() > 0)
                        <span class="ml-auto text-xs text-gray-400">
                            {{ $step->hotspots->count() }} {{ __('common.hotspots') }}
                        </span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
</div>

<!-- Hotspot-Animation CSS -->
<style>
    @keyframes hotspot-pulse {
        0% { transform: scale(1); opacity: 0.6; }
        50% { transform: scale(1.5); opacity: 0.2; }
        100% { transform: scale(1); opacity: 0.6; }
    }
    .tutorial-hotspot:hover .hotspot-label {
        opacity: 1 !important;
    }
    .hotspot-clicked .hotspot-core {
        background: #16a34a !important;
    }
    .hotspot-clicked .hotspot-pulse {
        animation: none !important;
        background: rgba(22, 163, 74, 0.3) !important;
    }
</style>

<!-- Tutorial-Player JavaScript -->
<script>
    const tutorialId = {{ $tutorial->id }};
    const locale = '{{ $locale }}';
    const steps = {!! json_encode($stepsData) !!};

    let currentStepIndex = {{ $currentStepNumber - 1 }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Gemeinsames Fenster für Hotspot-Links — wird über alle Schritte wiederverwendet
    let hotspotWindow = null;

    function openHotspotLink(link) {
        if (!link) return;
        if (!hotspotWindow || hotspotWindow.closed) {
            hotspotWindow = window.open(link, 'hotspotWindow', 'width=1024,height=768,menubar=yes,scrollbars=yes,resizable=yes,toolbar=yes,location=yes');
        } else {
            hotspotWindow.location.href = link;
            hotspotWindow.focus();
        }
    }

    // Klick-Handler für PHP-generierte Hotspots (erster Schritt beim Laden)
    document.querySelectorAll('.tutorial-hotspot[data-link]').forEach(el => {
        el.addEventListener('click', function() {
            const link = this.dataset.link;
            if (link) {
                openHotspotLink(link);
            }
        });
    });

    function renderStep(index) {
        const step = steps[index];
        if (!step) return;

        currentStepIndex = index;

        // Titel
        document.getElementById('step-title').textContent = step.title;

        // Anweisung
        const instructionEl = document.getElementById('step-instruction');
        if (step.instruction) {
            instructionEl.innerHTML = step.instruction.replace(/\n/g, '<br>');
            instructionEl.parentElement.style.display = 'block';
        } else {
            instructionEl.parentElement.style.display = 'none';
        }

        // Screenshot
        const container = document.getElementById('screenshot-container');
        // Alte Hotspots entfernen
        container.querySelectorAll('.tutorial-hotspot').forEach(el => el.remove());

        if (step.screenshot_path) {
            let imgEl = document.getElementById('screenshot-image');
            if (!imgEl) {
                imgEl = document.createElement('img');
                imgEl.id = 'screenshot-image';
                imgEl.className = 'w-full h-auto';
                imgEl.style.display = 'block';
                // No-screenshot placeholder entfernen
                const placeholder = container.querySelector('.flex.items-center.justify-center');
                if (placeholder) placeholder.remove();
                container.insertBefore(imgEl, container.firstChild);
            }
            imgEl.src = '{{ asset("storage") }}/' + step.screenshot_path;
            imgEl.style.display = 'block';
        } else {
            const imgEl = document.getElementById('screenshot-image');
            if (imgEl) imgEl.style.display = 'none';
        }

        // Hotspots rendern
        step.hotspots.forEach(hotspot => {
            const div = document.createElement('div');
            div.className = 'tutorial-hotspot';
            div.dataset.hotspotId = hotspot.id;
            div.dataset.clickable = hotspot.is_clickable ? '1' : '0';
            div.style.cssText = `
                position: absolute;
                left: ${hotspot.position_x}%;
                top: ${hotspot.position_y}%;
                width: ${hotspot.radius}px;
                height: ${hotspot.radius}px;
                transform: translate(-50%, -50%);
                cursor: ${hotspot.is_clickable ? 'pointer' : 'default'};
                z-index: 10;
            `;

            div.innerHTML = `
                <div class="hotspot-pulse" style="
                    width: 100%; height: 100%; border-radius: 50%;
                    background: rgba(147, 51, 234, 0.3);
                    animation: hotspot-pulse 2s infinite;
                "></div>
                <div class="hotspot-core" style="
                    position: absolute; top: 50%; left: 50%;
                    transform: translate(-50%, -50%);
                    width: 28px; height: 28px; border-radius: 50%;
                    background: ${hotspot.link ? '#2563eb' : '#9333ea'}; border: 2px solid white;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                    display: flex; align-items: center; justify-content: center;
                    font-size: 14px;
                ">${hotspot.link ? '🔗' : ''}</div>
                ${hotspot.label ? `
                <div class="hotspot-label" style="
                    position: absolute; bottom: 100%; left: 50%;
                    transform: translateX(-50%); margin-bottom: 8px;
                    padding: 4px 8px; background: rgba(0,0,0,0.8);
                    color: white; font-size: 12px; border-radius: 4px;
                    white-space: nowrap; opacity: 0;
                    transition: opacity 0.2s; pointer-events: none;
                ">${hotspot.label}</div>
                ` : ''}
            `;

            // Link-Attribute setzen
            if (hotspot.link) {
                div.dataset.link = hotspot.link;
                div.style.cursor = 'pointer';
            }

            // Klick-Handler: Hotspots mit Link sind immer klickbar
            if (hotspot.is_clickable || hotspot.link) {
                div.addEventListener('click', function() {
                    // Wenn Link vorhanden, im gemeinsamen Fenster öffnen
                    if (hotspot.link) {
                        openHotspotLink(hotspot.link);
                        return;
                    }
                    if (!div.classList.contains('hotspot-clicked')) {
                        div.classList.add('hotspot-clicked');
                        // Fortschritt speichern
                        saveProgress(step.id);
                    }
                });
            }

            container.appendChild(div);
        });

        // Fortschrittsbalken
        const percent = Math.round(((index + 1) / steps.length) * 100);
        document.getElementById('progress-bar').style.width = percent + '%';
        document.getElementById('progress-percent').textContent = percent + '%';

        // Fortschritt-Text oben aktualisieren (z. B. "Fortschritt: 2 / 4")
        const progressText = document.getElementById('progress-text');
        if (progressText) {
            progressText.textContent = '{{ __('common.progress') }}: ' + (index + 1) + ' / ' + steps.length;
        }

        // Navigation
        const prevBtn = document.getElementById('prev-step-btn');
        prevBtn.disabled = index <= 0;

        const nextBtn = document.getElementById('next-step-btn');
        const completeBtn = document.getElementById('complete-btn');

        if (index < steps.length - 1) {
            if (nextBtn) nextBtn.style.display = 'inline-flex';
            if (completeBtn) completeBtn.style.display = 'none';
        } else {
            if (nextBtn) nextBtn.style.display = 'none';
            if (completeBtn) completeBtn.style.display = 'inline-flex';
        }

        // Schritt-Übersicht aktualisieren
        const overviewBtns = document.querySelectorAll('.step-overview-btn');
        overviewBtns.forEach((btn, i) => {
            const badge = btn.querySelector('.step-number-badge');
            if (i === index) {
                btn.className = btn.className.replace('hover:bg-gray-50 text-gray-700 border-l-4 border-transparent',
                    'bg-purple-50 text-purple-700 border-l-4 border-purple-500');
                if (badge) {
                    badge.className = badge.className.replace('bg-gray-200 text-gray-600', 'bg-purple-500 text-white');
                }
            } else {
                btn.className = btn.className.replace('bg-purple-50 text-purple-700 border-l-4 border-purple-500',
                    'hover:bg-gray-50 text-gray-700 border-l-4 border-transparent');
                if (badge) {
                    badge.className = badge.className.replace('bg-purple-500 text-white', 'bg-gray-200 text-gray-600');
                }
            }
        });

        // Schritt-Zähler in der Navigation aktualisieren
        const stepCounter = document.querySelector('.step-counter-text');
        if (stepCounter) {
            stepCounter.textContent = '{{ __('common.step') }} ' + (index + 1) + ' / ' + steps.length;
        }

        // Tipp/Warning-Bereich aktualisieren
        const tipEl = document.getElementById('tip-warning-container');
        if (tipEl) {
            tipEl.innerHTML = '';
            if (step.tip) {
                tipEl.innerHTML += `
                    <div class="mx-6 mt-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-r">
                        <p class="text-sm text-yellow-800">
                            <strong>💡 {{ __('common.tip') }}:</strong> ${step.tip}
                        </p>
                    </div>`;
            }
            if (step.warning) {
                tipEl.innerHTML += `
                    <div class="mx-6 mt-4 p-4 bg-red-50 border-l-4 border-red-400 rounded-r">
                        <p class="text-sm text-red-800">
                            <strong>⚠️ {{ __('common.warning') }}:</strong> ${step.warning}
                        </p>
                    </div>`;
            }
        }

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function nextStep() {
        if (currentStepIndex < steps.length - 1) {
            renderStep(currentStepIndex + 1);
            saveProgress(steps[currentStepIndex + 1].id);
        }
    }

    function prevStep() {
        if (currentStepIndex > 0) {
            renderStep(currentStepIndex - 1);
            saveProgress(steps[currentStepIndex - 1].id);
        }
    }

    function goToStep(stepNumber) {
        const index = stepNumber - 1;
        if (index >= 0 && index < steps.length) {
            renderStep(index);
            saveProgress(steps[index].id);
        }
    }

    function saveProgress(stepId) {
        @auth
        fetch('{{ route("tutorials.progress", ["locale" => $locale, "slug" => $tutorial->slug]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ step_id: stepId })
        }).catch(err => console.error('Fortschritt speichern fehlgeschlagen:', err));
        @endauth
    }

    function completeTutorial() {
        @auth
        fetch('{{ route("tutorials.complete", ["locale" => $locale, "slug" => $tutorial->slug]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || '{{ __('common.tutorial_completed') }}');
        })
        .catch(err => console.error('Tutorial abschließen fehlgeschlagen:', err));
        @else
        window.location.href = '{{ route("login") }}?redirect=' + window.location.href;
        @endauth
    }
</script>
@endsection
