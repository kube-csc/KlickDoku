<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class QueryParamTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_query_param_resolves_project(): void
    {
        $project = Project::create([
            'name' => 'Test Projekt',
            'slug' => 'test-projekt',
            'domain' => '127.0.0.1',
            'app_name' => 'TestApp',
            'club_name' => 'TestClub',
            'is_active' => true,
        ]);

        $response = $this->get('/de?project=test-projekt');
        
        $response->assertOk();
        $this->assertNotNull(request()->attributes->get('project'));
        $this->assertEquals($project->id, request()->attributes->get('project')->id);
    }

    public function test_query_param_on_tutorial_page(): void
    {
        $project = Project::create([
            'name' => 'Tutorial Projekt',
            'slug' => 'tutorial-projekt',
            'app_name' => 'TutorialApp',
            'club_name' => 'TutorialClub',
            'is_active' => true,
        ]);

        $response = $this->get('/de/tutorials?project=tutorial-projekt');
        
        $response->assertOk();
        $this->assertNotNull(request()->attributes->get('project'));
        $this->assertEquals($project->slug, request()->attributes->get('project')->slug);
    }

    public function test_query_param_with_ip_domain_and_subdomain(): void
    {
        // Simuliert das echte Projekt "Sportverein Datteln" mit domain=127.0.0.1 und subdomain=sportverein
        $project = Project::create([
            'name' => 'Sportverein Datteln',
            'slug' => 'sportverein-datteln',
            'subdomain' => 'sportverein',
            'domain' => '127.0.0.1',
            'app_name' => 'Vereinsverwaltung',
            'club_name' => 'Sportverein Datteln',
            'is_active' => true,
        ]);

        // Query-Parameter sollte funktionieren
        $response = $this->get('/de/tutorials?project=sportverein-datteln');
        $response->assertOk();
        $resolved = request()->attributes->get('project');
        $this->assertNotNull($resolved, 'Projekt muss über Query-Parameter erkannt werden');
        $this->assertEquals('sportverein-datteln', $resolved->slug);
    }

    public function test_domain_resolution_with_ip(): void
    {
        // Wenn kein Query-Parameter, sollte Domain-Erkennung funktionieren
        $project = Project::create([
            'name' => 'IP Projekt',
            'slug' => 'ip-projekt',
            'domain' => '127.0.0.1',
            'app_name' => 'IPApp',
            'club_name' => 'IPClub',
            'is_active' => true,
        ]);

        $response = $this->get('/de');
        $response->assertOk();
        $resolved = request()->attributes->get('project');
        $this->assertNotNull($resolved, 'Projekt muss über Domain 127.0.0.1 erkannt werden');
        $this->assertEquals('ip-projekt', $resolved->slug);
    }
}
