<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Project;
use App\Models\ProjectTerm;
use App\Models\PlaceholderDefinition;
use App\Services\PlaceholderService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlaceholderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PlaceholderService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PlaceholderService::class);
    }

    public function test_resolve_returns_empty_string_for_null_text(): void
    {
        $this->assertEquals('', $this->service->resolve(null));
    }

    public function test_resolve_returns_text_unchanged_without_project(): void
    {
        $text = 'Willkommen bei {{app_name}}';
        $result = $this->service->resolve($text, null);
        $this->assertEquals($text, $result);
    }

    public function test_resolve_replaces_placeholders_with_project_terms(): void
    {
        $project = Project::create([
            'name' => 'Testprojekt',
            'slug' => 'testprojekt',
            'app_name' => 'TestApp',
            'club_name' => 'TestClub',
        ]);

        ProjectTerm::create([
            'project_id' => $project->id,
            'placeholder_key' => 'events',
            'value_de' => 'Termine',
            'value_en' => 'Appointments',
        ]);

        $text = 'Öffnen Sie das Menü {{events}}.';
        $result = $this->service->resolve($text, $project, 'de');
        $this->assertEquals('Öffnen Sie das Menü Termine.', $result);
    }

    public function test_resolve_uses_english_locale(): void
    {
        $project = Project::create([
            'name' => 'Testprojekt',
            'slug' => 'testprojekt',
            'app_name' => 'TestApp',
            'club_name' => 'TestClub',
        ]);

        ProjectTerm::create([
            'project_id' => $project->id,
            'placeholder_key' => 'events',
            'value_de' => 'Termine',
            'value_en' => 'Appointments',
        ]);

        $text = 'Open the {{events}} menu.';
        $result = $this->service->resolve($text, $project, 'en');
        $this->assertEquals('Open the Appointments menu.', $result);
    }

    public function test_resolve_uses_app_name_fallback(): void
    {
        $project = Project::create([
            'name' => 'Testprojekt',
            'slug' => 'testprojekt',
            'app_name' => 'TestApp',
            'club_name' => 'TestClub',
        ]);

        $text = 'Willkommen bei {{app_name}}';
        $result = $this->service->resolve($text, $project);
        $this->assertEquals('Willkommen bei TestApp', $result);
    }

    public function test_resolve_uses_club_name_fallback(): void
    {
        $project = Project::create([
            'name' => 'Testprojekt',
            'slug' => 'testprojekt',
            'app_name' => 'TestApp',
            'club_name' => 'TestClub',
        ]);

        $text = 'Willkommen im {{club_name}}';
        $result = $this->service->resolve($text, $project);
        $this->assertEquals('Willkommen im TestClub', $result);
    }

    public function test_resolve_keeps_unknown_placeholders(): void
    {
        $project = Project::create([
            'name' => 'Testprojekt',
            'slug' => 'testprojekt',
            'app_name' => 'TestApp',
            'club_name' => 'TestClub',
        ]);

        $text = 'Unbekannter Platzhalter {{xyz}} bleibt erhalten';
        $result = $this->service->resolve($text, $project);
        $this->assertEquals('Unbekannter Platzhalter {{xyz}} bleibt erhalten', $result);
    }

    public function test_resolve_replaces_multiple_placeholders(): void
    {
        $project = Project::create([
            'name' => 'Testprojekt',
            'slug' => 'testprojekt',
            'app_name' => 'TestApp',
            'club_name' => 'TestClub',
        ]);

        ProjectTerm::create([
            'project_id' => $project->id,
            'placeholder_key' => 'events',
            'value_de' => 'Termine',
        ]);

        $text = '{{app_name}} im {{club_name}} mit {{events}}';
        $result = $this->service->resolve($text, $project);
        $this->assertEquals('TestApp im TestClub mit Termine', $result);
    }

    public function test_resolve_array_recursively(): void
    {
        $project = Project::create([
            'name' => 'Testprojekt',
            'slug' => 'testprojekt',
            'app_name' => 'TestApp',
            'club_name' => 'TestClub',
        ]);

        $data = [
            'title' => 'Willkommen bei {{app_name}}',
            'nested' => [
                'description' => 'Im {{club_name}}',
            ],
        ];

        $result = $this->service->resolveArray($data, $project);
        $this->assertEquals('Willkommen bei TestApp', $result['title']);
        $this->assertEquals('Im TestClub', $result['nested']['description']);
    }

    public function test_clear_project_cache(): void
    {
        $project = Project::create([
            'name' => 'Testprojekt',
            'slug' => 'testprojekt',
            'app_name' => 'TestApp',
            'club_name' => 'TestClub',
        ]);

        // Terms laden, um Cache zu füllen
        $this->service->getProjectTerms($project);

        // Cache invalidieren
        $this->service->clearProjectCache($project);

        // Keine Exception – Test bestanden
        $this->assertTrue(true);
    }

    public function test_get_all_placeholders(): void
    {
        PlaceholderDefinition::create([
            'key' => 'test_placeholder',
            'description_de' => 'Test',
            'default_value_de' => 'Standard',
        ]);

        $placeholders = $this->service->getAllPlaceholders();
        $this->assertArrayHasKey('test_placeholder', $placeholders);
    }
}
