<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Project;
use App\Models\ProjectTerm;
use App\Models\Guide;
use App\Models\GuideStep;
use App\Models\Tutorial;
use App\Models\TutorialStep;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiProjectContextTest extends TestCase
{
    use RefreshDatabase;

    protected Project $project;
    protected Category $category;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = Category::create([
            'name' => 'Test',
            'slug' => 'test',
            'sort_order' => 1,
        ]);

        $this->project = Project::create([
            'name' => 'Testprojekt',
            'slug' => 'testprojekt',
            'subdomain' => 'test',
            'app_name' => 'TestApp',
            'club_name' => 'TestClub',
            'primary_color' => '#ff0000',
            'is_active' => true,
        ]);

        // Projekt-Terme anlegen
        ProjectTerm::create([
            'project_id' => $this->project->id,
            'placeholder_key' => 'events',
            'value_de' => 'Termine',
            'value_en' => 'Appointments',
        ]);
        ProjectTerm::create([
            'project_id' => $this->project->id,
            'placeholder_key' => 'members',
            'value_de' => 'Teilnehmer',
            'value_en' => 'Participants',
        ]);
    }

    public function test_api_guides_without_project_returns_all(): void
    {
        $guide1 = Guide::create([
            'category_id' => $this->category->id,
            'title' => 'Guide 1',
            'slug' => 'guide-1',
            'description' => 'Beschreibung 1',
            'status' => 'published',
            'author_id' => $this->user->id,
            'published_at' => now(),
        ]);

        $guide2 = Guide::create([
            'category_id' => $this->category->id,
            'title' => 'Guide 2',
            'slug' => 'guide-2',
            'description' => 'Beschreibung 2',
            'status' => 'published',
            'author_id' => $this->user->id,
            'published_at' => now(),
        ]);

        $response = $this->getJson('/api/guides');

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('meta.total'));
    }

    public function test_api_guides_with_project_returns_only_assigned(): void
    {
        $assignedGuide = Guide::create([
            'category_id' => $this->category->id,
            'title' => 'Zugewiesener Guide',
            'slug' => 'assigned-guide',
            'description' => 'Beschreibung',
            'status' => 'published',
            'author_id' => $this->user->id,
            'published_at' => now(),
        ]);
        $assignedGuide->projects()->attach($this->project);

        $unassignedGuide = Guide::create([
            'category_id' => $this->category->id,
            'title' => 'Nicht zugewiesener Guide',
            'slug' => 'unassigned-guide',
            'description' => 'Beschreibung',
            'status' => 'published',
            'author_id' => $this->user->id,
            'published_at' => now(),
        ]);

        $response = $this->getJson('/api/guides?project=testprojekt');

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('meta.total'));
        $this->assertEquals('assigned-guide', $response->json('data.0.slug'));
    }

    public function test_api_guide_show_with_project_checks_assignment(): void
    {
        $guide = Guide::create([
            'category_id' => $this->category->id,
            'title' => 'Nicht zugewiesener Guide',
            'slug' => 'unassigned-guide',
            'description' => 'Beschreibung',
            'status' => 'published',
            'author_id' => $this->user->id,
            'published_at' => now(),
        ]);

        // Ohne Projekt: sollte gefunden werden
        $response = $this->getJson('/api/guides/' . $guide->slug);
        $response->assertStatus(200);
        $this->assertEquals('unassigned-guide', $response->json('data.slug'));

        // Mit Projekt: sollte 404 sein, da nicht zugewiesen
        $response = $this->getJson('/api/guides/' . $guide->slug . '?project=testprojekt');
        $response->assertStatus(404);
    }

    public function test_api_guides_replaces_placeholders(): void
    {
        $guide = Guide::create([
            'category_id' => $this->category->id,
            'title' => 'Willkommen bei {{app_name}}',
            'slug' => 'welcome-guide',
            'description' => 'Verwalten Sie Ihre {{events}} und {{members}}.',
            'status' => 'published',
            'author_id' => $this->user->id,
            'published_at' => now(),
        ]);
        $guide->projects()->attach($this->project);

        // API verwendet Standard-Locale (en) – englische Terms werden erwartet
        $response = $this->getJson('/api/guides?project=testprojekt');

        $response->assertStatus(200);
        $guideData = $response->json('data.0');
        $this->assertEquals('Willkommen bei TestApp', $guideData['title']);
        $this->assertEquals('Verwalten Sie Ihre Appointments und Participants.', $guideData['description']);
    }

    public function test_api_guides_without_project_no_placeholder_replacement(): void
    {
        $guide = Guide::create([
            'category_id' => $this->category->id,
            'title' => 'Willkommen bei {{app_name}}',
            'slug' => 'welcome-guide',
            'description' => 'Verwalten Sie Ihre {{events}}.',
            'status' => 'published',
            'author_id' => $this->user->id,
            'published_at' => now(),
        ]);

        $response = $this->getJson('/api/guides');

        $response->assertStatus(200);
        $guideData = $response->json('data.0');
        $this->assertEquals('Willkommen bei {{app_name}}', $guideData['title']);
        $this->assertEquals('Verwalten Sie Ihre {{events}}.', $guideData['description']);
    }

    public function test_api_tutorials_with_project_returns_only_assigned(): void
    {
        $assignedTutorial = Tutorial::create([
            'category_id' => $this->category->id,
            'title' => 'Zugewiesenes Tutorial',
            'slug' => 'assigned-tutorial',
            'description' => 'Beschreibung',
            'status' => 'published',
            'author_id' => $this->user->id,
        ]);
        $assignedTutorial->projects()->attach($this->project);

        $unassignedTutorial = Tutorial::create([
            'category_id' => $this->category->id,
            'title' => 'Nicht zugewiesenes Tutorial',
            'slug' => 'unassigned-tutorial',
            'description' => 'Beschreibung',
            'status' => 'published',
            'author_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/tutorials?project=testprojekt');

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('meta.total'));
        $this->assertEquals('assigned-tutorial', $response->json('data.0.slug'));
    }

    public function test_api_tutorials_replaces_placeholders(): void
    {
        $tutorial = Tutorial::create([
            'category_id' => $this->category->id,
            'title' => 'Tutorial für {{app_name}}',
            'slug' => 'tutorial-guide',
            'description' => 'Lernen Sie {{events}} zu verwalten.',
            'status' => 'published',
            'author_id' => $this->user->id,
        ]);
        $tutorial->projects()->attach($this->project);

        // API verwendet Standard-Locale (en) – englische Terms werden erwartet
        $response = $this->getJson('/api/tutorials?project=testprojekt');

        $response->assertStatus(200);
        $tutorialData = $response->json('data.0');
        $this->assertEquals('Tutorial für TestApp', $tutorialData['title']);
        $this->assertEquals('Lernen Sie Appointments zu verwalten.', $tutorialData['description']);
    }

    public function test_api_search_with_project_filters_results(): void
    {
        $assignedGuide = Guide::create([
            'category_id' => $this->category->id,
            'title' => 'Suchtest Guide',
            'slug' => 'search-guide',
            'description' => 'Suchtest',
            'status' => 'published',
            'author_id' => $this->user->id,
            'published_at' => now(),
        ]);
        $assignedGuide->projects()->attach($this->project);

        $unassignedGuide = Guide::create([
            'category_id' => $this->category->id,
            'title' => 'Suchtest Guide 2',
            'slug' => 'search-guide-2',
            'description' => 'Suchtest',
            'status' => 'published',
            'author_id' => $this->user->id,
            'published_at' => now(),
        ]);

        // Da Scout in Tests nicht konfiguriert ist, testen wir die Projekt-Filterung über die guides-Endpunkte
        // Die search-Methode verwendet Scout, was in Tests nicht verfügbar ist
        $response = $this->getJson('/api/guides?project=testprojekt&search=Suchtest');

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('meta.total'));
        $this->assertEquals('search-guide', $response->json('data.0.slug'));
    }

    public function test_api_guide_steps_replaces_placeholders(): void
    {
        $guide = Guide::create([
            'category_id' => $this->category->id,
            'title' => 'Schritt Guide',
            'slug' => 'step-guide',
            'description' => 'Beschreibung',
            'status' => 'published',
            'author_id' => $this->user->id,
            'published_at' => now(),
        ]);
        $guide->projects()->attach($this->project);

        $step = GuideStep::create([
            'guide_id' => $guide->id,
            'step_number' => 1,
            'title' => 'Öffnen Sie {{events}}',
            'description' => 'Klicken Sie auf {{members}}.',
            'tip' => 'Tipp zu {{app_name}}',
        ]);

        // API verwendet Standard-Locale (en) – englische Terms werden erwartet
        $response = $this->getJson('/api/guides?project=testprojekt');

        $response->assertStatus(200);
        $steps = $response->json('data.0.steps');
        $this->assertCount(1, $steps);
        $this->assertEquals('Öffnen Sie Appointments', $steps[0]['title']);
        $this->assertEquals('Klicken Sie auf Participants.', $steps[0]['description']);
        $this->assertEquals('Tipp zu TestApp', $steps[0]['tip']);
    }

    public function test_api_tutorial_steps_replaces_placeholders(): void
    {
        $tutorial = Tutorial::create([
            'category_id' => $this->category->id,
            'title' => 'Tutorial Schritt',
            'slug' => 'tutorial-step',
            'description' => 'Beschreibung',
            'status' => 'published',
            'author_id' => $this->user->id,
        ]);
        $tutorial->projects()->attach($this->project);

        $step = TutorialStep::create([
            'tutorial_id' => $tutorial->id,
            'step_number' => 1,
            'title' => 'Tutorial {{events}}',
            'instruction' => 'Inhalt zu {{members}}.',
            'screenshot_path' => null,
            'screenshot_width' => null,
            'screenshot_height' => null,
        ]);

        // API verwendet Standard-Locale (en) – englische Terms werden erwartet
        $response = $this->getJson('/api/tutorials?project=testprojekt');

        $response->assertStatus(200);
        $steps = $response->json('data.0.steps');
        $this->assertCount(1, $steps);
        $this->assertEquals('Tutorial Appointments', $steps[0]['title']);
        $this->assertEquals('Inhalt zu Participants.', $steps[0]['instruction']);
    }

    public function test_api_unknown_project_returns_all_guides(): void
    {
        $guide = Guide::create([
            'category_id' => $this->category->id,
            'title' => 'Guide',
            'slug' => 'guide',
            'description' => 'Beschreibung',
            'status' => 'published',
            'author_id' => $this->user->id,
            'published_at' => now(),
        ]);

        $response = $this->getJson('/api/guides?project=unknown-project');

        // Unbekanntes Projekt – Fallback zu allen Guides
        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('meta.total'));
    }
}
