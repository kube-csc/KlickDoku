<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AIDescriptionTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function it_generates_mock_description_for_guide()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-description'), [
            'title' => 'Neuen Server einrichten',
            'type' => 'guide',
            'category' => 'IT-Infrastruktur',
            'tags' => ['Server', 'Installation', 'Linux'],
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['description']);

        $description = $response->json('description');
        $this->assertNotEmpty($description);
        $this->assertStringContainsString('Server', $description);
    }

    /** @test */
    public function it_generates_mock_description_for_tutorial()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-description'), [
            'title' => 'Laravel Grundlagen',
            'type' => 'tutorial',
            'category' => 'Programmierung',
            'tags' => ['Laravel', 'PHP', 'Webentwicklung'],
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['description']);

        $description = $response->json('description');
        $this->assertNotEmpty($description);
        $this->assertStringContainsString('Laravel', $description);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-description'), []);

        $response->assertInvalid(['title', 'type']);
    }

    /** @test */
    public function it_validates_type_field()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-description'), [
            'title' => 'Test',
            'type' => 'invalid_type',
        ]);

        $response->assertInvalid(['type']);
    }

    /** @test */
    public function it_generates_description_without_category_and_tags()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-description'), [
            'title' => 'Einfache Anleitung',
            'type' => 'guide',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['description']);

        $description = $response->json('description');
        $this->assertNotEmpty($description);
    }

    /** @test */
    public function service_returns_description_under_200_chars()
    {
        $service = app(\App\Services\AIDescriptionService::class);

        $description = $service->generateDescription(
            'Sehr langer Titel, der sicherstellt, dass die Beschreibung nicht zu lang wird und korrekt gekürzt wird',
            'guide',
            'Kategorie',
            ['Tag1', 'Tag2', 'Tag3']
        );

        $this->assertLessThanOrEqual(200, mb_strlen($description));
    }

    /** @test */
    public function it_generates_description_with_keywords()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-description'), [
            'title' => 'Docker Container einrichten',
            'type' => 'guide',
            'keywords' => ['Docker', 'Container', 'Virtualisierung'],
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['description']);

        $description = $response->json('description');
        $this->assertNotEmpty($description);
        // Beschreibung sollte den Titel enthalten
        $this->assertStringContainsString('Docker', $description);
    }

    /** @test */
    public function it_generates_description_with_reference_urls()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-description'), [
            'title' => 'Laravel Deployment',
            'type' => 'tutorial',
            'referenceUrls' => [
                'https://laravel.com/docs/deployment',
                'https://example.com/guide',
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['description']);

        $description = $response->json('description');
        $this->assertNotEmpty($description);
    }

    /** @test */
    public function it_generates_description_with_keywords_and_urls()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-description'), [
            'title' => 'Firewall konfigurieren',
            'type' => 'guide',
            'category' => 'Sicherheit',
            'keywords' => ['Firewall', 'iptables', 'Netzwerksicherheit'],
            'referenceUrls' => [
                'https://www.nmap.org/book.html',
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['description']);

        $description = $response->json('description');
        $this->assertNotEmpty($description);
        $this->assertStringContainsString('Firewall', $description);
    }

    /** @test */
    public function it_validates_reference_urls_format()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-description'), [
            'title' => 'Test',
            'type' => 'guide',
            'referenceUrls' => ['nicht-gueltig-keine-url'],
        ]);

        $response->assertInvalid(['referenceUrls.0']);
    }

    /** @test */
    public function service_incorporates_keywords_into_description()
    {
        $service = app(\App\Services\AIDescriptionService::class);

        $description = $service->generateDescription(
            'Backup Strategie',
            'guide',
            '',
            [],
            'de',
            ['Backup', 'Datenrettung', 'Automatisierung'],
            []
        );

        $this->assertNotEmpty($description);
        // Mindestens ein Keyword sollte in der Beschreibung vorkommen
        $found = false;
        foreach (['Backup', 'Datenrettung', 'Automatisierung'] as $keyword) {
            if (str_contains($description, $keyword)) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Keines der Keywords wurde in die Beschreibung eingebettet.');
    }

    /** @test */
    public function it_generates_description_with_custom_prompt()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-description'), [
            'title' => 'Serverwartung durchführen',
            'type' => 'guide',
            'customPrompt' => 'Kurz und technisch formulieren, Fokus auf Sicherheit',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['description']);

        $description = $response->json('description');
        $this->assertNotEmpty($description);
        // Beschreibung sollte den Titel enthalten
        $this->assertStringContainsString('Serverwartung', $description);
    }

    /** @test */
    public function it_accepts_custom_prompt_with_all_context_fields()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-description'), [
            'title' => 'Netzwerk konfigurieren',
            'type' => 'tutorial',
            'category' => 'IT-Infrastruktur',
            'keywords' => ['Router', 'Switch', 'VLAN'],
            'referenceUrls' => ['https://docs.example.com/networking'],
            'customPrompt' => 'Für Einsteiger einfach erklären',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['description']);

        $description = $response->json('description');
        $this->assertNotEmpty($description);
    }

    /** @test */
    public function it_validates_custom_prompt_max_length()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $longPrompt = str_repeat('a', 501);

        $response = $this->postJson(route('admin.ai.generate-description'), [
            'title' => 'Test',
            'type' => 'guide',
            'customPrompt' => $longPrompt,
        ]);

        $response->assertInvalid(['customPrompt']);
    }

    /** @test */
    public function service_uses_custom_prompt_in_description()
    {
        $service = app(\App\Services\AIDescriptionService::class);

        $description = $service->generateDescription(
            'Datenbank sichern',
            'guide',
            '',
            [],
            'de',
            [],
            [],
            'Praxisnah und mit konkreten Beispielen erklären'
        );

        $this->assertNotEmpty($description);
        $this->assertStringContainsString('Datenbank sichern', $description);
        $this->assertStringContainsString('konkreten Beispielen', $description);
    }

    /** @test */
    public function it_generates_description_with_openai_provider()
    {
        \Illuminate\Support\Facades\Http::fake([
            'https://api.openai.com/v1/chat/completions' => [
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Diese Anleitung zeigt Schritt für Schritt, wie Sie einen neuen Server sicher einrichten und konfigurieren.',
                        ],
                    ],
                ],
            ],
        ]);

        config(['services.ai.provider' => 'openai']);
        config(['services.ai.openai.api_key' => 'test-key']);

        $service = app(\App\Services\AIDescriptionService::class);

        $description = $service->generateDescription(
            'Neuen Server einrichten',
            'guide',
            'IT-Infrastruktur',
            ['Server', 'Installation'],
            'de',
            [],
            [],
            ''
        );

        $this->assertNotEmpty($description);
        $this->assertStringContainsString('Anleitung', $description);
        $this->assertLessThanOrEqual(200, mb_strlen($description));

        // Config zurücksetzen
        config(['services.ai.provider' => 'mock']);
        config(['services.ai.openai.api_key' => null]);
    }

    /** @test */
    public function openai_falls_back_to_mock_when_no_api_key()
    {
        config(['services.ai.provider' => 'openai']);
        config(['services.ai.openai.api_key' => '']);

        $service = app(\App\Services\AIDescriptionService::class);

        $description = $service->generateDescription(
            'Test ohne API-Key',
            'guide',
            '',
            [],
            'de'
        );

        $this->assertNotEmpty($description);
        $this->assertStringContainsString('Test ohne API-Key', $description);

        // Config zurücksetzen
        config(['services.ai.provider' => 'mock']);
        config(['services.ai.openai.api_key' => null]);
    }

    /** @test */
    public function it_generates_description_with_groq_provider()
    {
        \Illuminate\Support\Facades\Http::fake([
            'https://api.groq.com/openai/v1/chat/completions' => [
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Dieses Tutorial erklärt die Grundlagen von Docker-Containern mit praktischen Beispielen.',
                        ],
                    ],
                ],
            ],
        ]);

        config(['services.ai.provider' => 'groq']);
        config(['services.ai.groq.api_key' => 'test-groq-key']);

        $service = app(\App\Services\AIDescriptionService::class);

        $description = $service->generateDescription(
            'Docker Grundlagen',
            'tutorial',
            'Container',
            ['Docker', 'Container'],
            'de',
            [],
            [],
            ''
        );

        $this->assertNotEmpty($description);
        $this->assertStringContainsString('Tutorial', $description);
        $this->assertLessThanOrEqual(200, mb_strlen($description));

        // Config zurücksetzen
        config(['services.ai.provider' => 'mock']);
        config(['services.ai.groq.api_key' => null]);
    }

    /** @test */
    public function groq_falls_back_to_mock_when_no_api_key()
    {
        config(['services.ai.provider' => 'groq']);
        config(['services.ai.groq.api_key' => '']);

        $service = app(\App\Services\AIDescriptionService::class);

        $description = $service->generateDescription(
            'Test ohne Groq Key',
            'tutorial',
            '',
            [],
            'de'
        );

        $this->assertNotEmpty($description);
        $this->assertStringContainsString('Test ohne Groq Key', $description);

        // Config zurücksetzen
        config(['services.ai.provider' => 'mock']);
        config(['services.ai.groq.api_key' => null]);
    }

    /** @test */
    public function openai_falls_back_to_mock_on_api_error()
    {
        \Illuminate\Support\Facades\Http::fake([
            'https://api.openai.com/v1/chat/completions' => \Illuminate\Support\Facades\Http::response([], 500),
        ]);

        config(['services.ai.provider' => 'openai']);
        config(['services.ai.openai.api_key' => 'test-key']);

        $service = app(\App\Services\AIDescriptionService::class);

        $description = $service->generateDescription(
            'API Fehler Test',
            'guide',
            '',
            [],
            'de'
        );

        $this->assertNotEmpty($description);
        $this->assertStringContainsString('API Fehler Test', $description);

        // Config zurücksetzen
        config(['services.ai.provider' => 'mock']);
        config(['services.ai.openai.api_key' => null]);
    }

    /** @test */
    public function groq_falls_back_to_mock_on_api_error()
    {
        \Illuminate\Support\Facades\Http::fake([
            'https://api.groq.com/openai/v1/chat/completions' => \Illuminate\Support\Facades\Http::response([], 500),
        ]);

        config(['services.ai.provider' => 'groq']);
        config(['services.ai.groq.api_key' => 'test-key']);

        $service = app(\App\Services\AIDescriptionService::class);

        $description = $service->generateDescription(
            'Groq Fehler Test',
            'tutorial',
            '',
            [],
            'de'
        );

        $this->assertNotEmpty($description);
        $this->assertStringContainsString('Groq Fehler Test', $description);

        // Config zurücksetzen
        config(['services.ai.provider' => 'mock']);
        config(['services.ai.groq.api_key' => null]);
    }

    /** @test */
    public function openai_includes_all_context_in_request()
    {
        \Illuminate\Support\Facades\Http::fake([
            'https://api.openai.com/v1/chat/completions' => [
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Beschreibung mit allen Kontext-Informationen.',
                        ],
                    ],
                ],
            ],
        ]);

        config(['services.ai.provider' => 'openai']);
        config(['services.ai.openai.api_key' => 'test-key']);

        $service = app(\App\Services\AIDescriptionService::class);

        $service->generateDescription(
            'Vollständiger Kontext',
            'guide',
            'IT-Sicherheit',
            ['Firewall', 'VPN'],
            'de',
            ['Sicherheit', 'Verschlüsselung'],
            ['https://example.com'],
            'Kurz und technisch'
        );

        // Überprüfen, dass der Request alle Kontext-Informationen enthält
        \Illuminate\Support\Facades\Http::assertSent(function ($request) {
            $body = json_decode($request->body(), true);
            $userContent = $body['messages'][1]['content'] ?? '';

            return str_contains($userContent, 'Vollständiger Kontext')
                && str_contains($userContent, 'IT-Sicherheit')
                && str_contains($userContent, 'Sicherheit')
                && str_contains($userContent, 'Kurz und technisch')
                && str_contains($userContent, 'https://example.com');
        });

        // Config zurücksetzen
        config(['services.ai.provider' => 'mock']);
        config(['services.ai.openai.api_key' => null]);
    }

    /** @test */
    public function it_generates_mock_description_for_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-description'), [
            'title' => 'Über unseren Verein',
            'type' => 'page',
            'keywords' => ['Team', 'Kontakt', 'Geschichte'],
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['description']);

        $description = $response->json('description');
        $this->assertNotEmpty($description);
        $this->assertStringContainsString('Verein', $description);
    }

    /** @test */
    public function it_generates_mock_description_for_category()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-description'), [
            'title' => 'IT-Infrastruktur',
            'type' => 'category',
            'keywords' => ['Server', 'Netzwerk', 'Linux'],
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['description']);

        $description = $response->json('description');
        $this->assertNotEmpty($description);
        $this->assertStringContainsString('IT-Infrastruktur', $description);
    }

    /** @test */
    public function service_handles_page_type_label()
    {
        $service = app(\App\Services\AIDescriptionService::class);

        $description = $service->generateDescription(
            'Kontaktseite',
            'page',
            '',
            [],
            'de',
            ['Kontakt', 'Adresse'],
            [],
            ''
        );

        $this->assertNotEmpty($description);
        $this->assertStringContainsString('Seite', $description);
    }

    /** @test */
    public function service_handles_category_type_label()
    {
        $service = app(\App\Services\AIDescriptionService::class);

        $description = $service->generateDescription(
            'Netzwerk',
            'category',
            '',
            [],
            'de',
            ['Router', 'Switch'],
            [],
            ''
        );

        $this->assertNotEmpty($description);
        $this->assertStringContainsString('Kategorie', $description);
    }

    /** @test */
    public function page_with_all_context_fields()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-description'), [
            'title' => 'Impressum',
            'type' => 'page',
            'keywords' => ['Rechtliches', 'Kontakt'],
            'referenceUrls' => ['https://example.com/impressum'],
            'customPrompt' => 'Formell und rechtssicher formulieren',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['description']);

        $description = $response->json('description');
        $this->assertNotEmpty($description);
        $this->assertLessThanOrEqual(200, mb_strlen($description));
    }

    /** @test */
    public function category_with_custom_prompt()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-description'), [
            'title' => 'Sicherheit',
            'type' => 'category',
            'customPrompt' => 'Fokus auf Datenschutz und Verschlüsselung',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['description']);

        $description = $response->json('description');
        $this->assertNotEmpty($description);
    }

    /** @test */
    public function it_generates_mock_description_for_guide_step()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-description'), [
            'title' => 'Konfigurationsdatei erstellen',
            'type' => 'guide_step',
            'category' => 'Neuen Server einrichten',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['description']);

        $description = $response->json('description');
        $this->assertNotEmpty($description);
        $this->assertStringContainsString('Konfigurationsdatei', $description);
    }

    /** @test */
    public function it_generates_mock_description_for_tutorial_step()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-description'), [
            'title' => 'Button klicken',
            'type' => 'tutorial_step',
            'category' => 'Laravel Grundlagen',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['description']);

        $description = $response->json('description');
        $this->assertNotEmpty($description);
        $this->assertStringContainsString('Button', $description);
    }

    /** @test */
    public function service_handles_guide_step_type_label()
    {
        $service = app(\App\Services\AIDescriptionService::class);

        $description = $service->generateDescription(
            'Datei speichern',
            'guide_step',
            'Server einrichten',
            [],
            'de',
            [],
            [],
            'Formuliere einen hilfreichen Tipp oder einen alternativen Ansatz.'
        );

        $this->assertNotEmpty($description);
        $this->assertStringContainsString('Anleitungsschritt', $description);
    }

    /** @test */
    public function service_handles_tutorial_step_type_label()
    {
        $service = app(\App\Services\AIDescriptionService::class);

        $description = $service->generateDescription(
            'Feld ausfüllen',
            'tutorial_step',
            'Tutorial',
            [],
            'de',
            [],
            [],
            'Formuliere eine klare, schrittweise Anweisung.'
        );

        $this->assertNotEmpty($description);
        $this->assertStringContainsString('Tutorial-Schritt', $description);
    }

    /** @test */
    public function guide_step_with_tip_prompt()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-description'), [
            'title' => 'Datenbank verbinden',
            'type' => 'guide_step',
            'category' => 'Server einrichten',
            'customPrompt' => 'Formuliere einen hilfreichen Tipp oder einen alternativen Ansatz.',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['description']);
        $this->assertNotEmpty($response->json('description'));
    }

    /** @test */
    public function guide_step_with_warning_prompt()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-description'), [
            'title' => 'Firewall konfigurieren',
            'type' => 'guide_step',
            'customPrompt' => 'Formuliere eine klare Warnung mit möglichen Risiken.',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['description']);
        $this->assertNotEmpty($response->json('description'));
    }

    /** @test */
    public function it_generates_translation_mock()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-translation'), [
            'originalText' => 'Willkommen bei unserem Verein',
            'sourceLocale' => 'de',
            'targetLocale' => 'en',
            'context' => 'Titel',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['translation']);

        $translation = $response->json('translation');
        $this->assertNotEmpty($translation);
        $this->assertStringContainsString('Willkommen', $translation);
    }

    /** @test */
    public function translation_validates_required_fields()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-translation'), []);

        $response->assertInvalid(['originalText', 'sourceLocale', 'targetLocale']);
    }

    /** @test */
    public function translation_service_returns_mock_with_target_locale()
    {
        $service = app(\App\Services\AIDescriptionService::class);

        $translation = $service->generateTranslation(
            'Testbeschreibung',
            'de',
            'en',
            'Beschreibung'
        );

        $this->assertStringContainsString('Englisch', $translation);
        $this->assertStringContainsString('Testbeschreibung', $translation);
    }

    /** @test */
    public function translation_service_handles_french_locale()
    {
        $service = app(\App\Services\AIDescriptionService::class);

        $translation = $service->generateTranslation(
            'Test',
            'de',
            'fr',
            ''
        );

        $this->assertStringContainsString('Französisch', $translation);
    }

    /** @test */
    public function it_generates_mock_content_for_guide()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-content'), [
            'title' => 'Neuen Server einrichten',
            'type' => 'guide',
            'category' => 'IT-Infrastruktur',
            'tags' => ['Server', 'Installation'],
            'steps' => [
                ['title' => 'Server vorbereiten'],
                ['title' => 'Software installieren'],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['content']);

        $content = $response->json('content');
        $this->assertNotEmpty($content);
        $this->assertStringContainsString('Server', $content);
        $this->assertStringContainsString('Einleitung', $content);
    }

    /** @test */
    public function it_generates_mock_content_for_tutorial()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-content'), [
            'title' => 'Laravel Grundlagen',
            'type' => 'tutorial',
            'steps' => [
                ['title' => 'Projekt erstellen'],
                ['title' => 'Route definieren'],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['content']);

        $content = $response->json('content');
        $this->assertNotEmpty($content);
        $this->assertStringContainsString('Laravel', $content);
    }

    /** @test */
    public function it_validates_generate_content_required_fields()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-content'), []);

        $response->assertInvalid(['title', 'type']);
    }

    /** @test */
    public function it_validates_generate_content_type_field()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('admin.ai.generate-content'), [
            'title' => 'Test',
            'type' => 'invalid_type',
        ]);

        $response->assertInvalid(['type']);
    }

    /** @test */
    public function service_generates_content_with_steps()
    {
        $service = app(\App\Services\AIDescriptionService::class);

        $content = $service->generateContent(
            'Backup einrichten',
            'guide',
            'Sicherheit',
            ['Backup', 'Automatisierung'],
            [
                ['title' => 'Backup-Tool installieren'],
                ['title' => 'Zeitplan konfigurieren'],
                ['title' => 'Test-Durchlauf starten'],
            ],
            'de',
            ['Datenrettung'],
            [],
            ''
        );

        $this->assertNotEmpty($content);
        $this->assertStringContainsString('Backup-Tool installieren', $content);
        $this->assertStringContainsString('Zeitplan konfigurieren', $content);
        $this->assertStringContainsString('Datenrettung', $content);
    }

    /** @test */
    public function service_generates_content_without_steps()
    {
        $service = app(\App\Services\AIDescriptionService::class);

        $content = $service->generateContent(
            'Einfache Seite',
            'page',
            '',
            [],
            [],
            'de',
            [],
            [],
            ''
        );

        $this->assertNotEmpty($content);
        $this->assertStringContainsString('Einfache Seite', $content);
        $this->assertStringContainsString('Einleitung', $content);
        $this->assertStringContainsString('Zusammenfassung', $content);
    }

    /** @test */
    public function openai_content_falls_back_to_mock_when_no_api_key()
    {
        config(['services.ai.provider' => 'openai']);
        config(['services.ai.openai.api_key' => '']);

        $service = app(\App\Services\AIDescriptionService::class);

        $content = $service->generateContent(
            'Test ohne API-Key',
            'guide',
            '',
            [],
            [],
            'de'
        );

        $this->assertNotEmpty($content);
        $this->assertStringContainsString('Test ohne API-Key', $content);

        // Config zurücksetzen
        config(['services.ai.provider' => 'mock']);
        config(['services.ai.openai.api_key' => null]);
    }

    /** @test */
    public function groq_content_falls_back_to_mock_when_no_api_key()
    {
        config(['services.ai.provider' => 'groq']);
        config(['services.ai.groq.api_key' => '']);

        $service = app(\App\Services\AIDescriptionService::class);

        $content = $service->generateContent(
            'Test ohne Groq Key',
            'tutorial',
            '',
            [],
            [],
            'de'
        );

        $this->assertNotEmpty($content);
        $this->assertStringContainsString('Test ohne Groq Key', $content);

        // Config zurücksetzen
        config(['services.ai.provider' => 'mock']);
        config(['services.ai.groq.api_key' => null]);
    }
}
