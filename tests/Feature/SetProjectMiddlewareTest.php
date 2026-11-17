<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Project;
use App\Models\Guide;
use App\Models\Category;
use App\Models\User;
use App\Services\ProjectResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SetProjectMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected ProjectResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = app(ProjectResolver::class);
    }

    public function test_project_is_resolved_via_subdomain(): void
    {
        $project = Project::create([
            'name' => 'Testprojekt',
            'slug' => 'testprojekt',
            'subdomain' => 'test',
            'app_name' => 'TestApp',
            'club_name' => 'TestClub',
            'is_active' => true,
        ]);

        // Simuliere Request mit Subdomain (benötigt mind. 3 Teile)
        $request = \Illuminate\Http\Request::create('http://test.localhost.de/de/', 'GET');
        $resolved = $this->resolver->resolveFromRequest($request);

        $this->assertNotNull($resolved);
        $this->assertEquals($project->id, $resolved->id);
    }

    public function test_no_project_resolved_without_subdomain(): void
    {
        $request = \Illuminate\Http\Request::create('http://localhost/de/', 'GET');
        $resolved = $this->resolver->resolveFromRequest($request);

        $this->assertNull($resolved);
    }

    public function test_reserved_subdomain_does_not_resolve_project(): void
    {
        $request = \Illuminate\Http\Request::create('http://www.localhost/de/', 'GET');
        $resolved = $this->resolver->resolveFromRequest($request);

        $this->assertNull($resolved);
    }

    public function test_project_is_resolved_via_query_param(): void
    {
        $project = Project::create([
            'name' => 'Testprojekt',
            'slug' => 'testprojekt',
            'app_name' => 'TestApp',
            'club_name' => 'TestClub',
            'is_active' => true,
        ]);

        $request = \Illuminate\Http\Request::create('http://localhost/de/?project=testprojekt', 'GET');
        $resolved = $this->resolver->resolveFromRequest($request);

        $this->assertNotNull($resolved);
        $this->assertEquals($project->id, $resolved->id);
    }

    public function test_inactive_project_is_not_resolved(): void
    {
        $project = Project::create([
            'name' => 'Inaktives Projekt',
            'slug' => 'inaktiv',
            'subdomain' => 'inaktiv',
            'app_name' => 'InaktivApp',
            'club_name' => 'InaktivClub',
            'is_active' => false,
        ]);

        $request = \Illuminate\Http\Request::create('http://inaktiv.localhost.de/de/', 'GET');
        $resolved = $this->resolver->resolveFromRequest($request);

        $this->assertNull($resolved);
    }

    public function test_scope_for_project_filters_guides(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Test',
            'slug' => 'test',
            'sort_order' => 1,
        ]);

        $project = Project::create([
            'name' => 'Testprojekt',
            'slug' => 'testprojekt',
            'subdomain' => 'test',
            'app_name' => 'TestApp',
            'club_name' => 'TestClub',
            'is_active' => true,
        ]);

        // Guide, der dem Projekt zugewiesen ist
        $assignedGuide = Guide::create([
            'category_id' => $category->id,
            'title' => 'Zugewiesener Guide',
            'slug' => 'assigned-guide',
            'description' => 'Beschreibung',
            'status' => 'published',
            'author_id' => $user->id,
            'published_at' => now(),
        ]);
        $assignedGuide->projects()->attach($project);

        // Guide, der NICHT dem Projekt zugewiesen ist
        $unassignedGuide = Guide::create([
            'category_id' => $category->id,
            'title' => 'Nicht zugewiesener Guide',
            'slug' => 'unassigned-guide',
            'description' => 'Beschreibung',
            'status' => 'published',
            'author_id' => $user->id,
            'published_at' => now(),
        ]);

        // Scope-Filter: nur zugewiesener Guide
        $filtered = Guide::forProject($project)->where('status', 'published')->get();
        $this->assertEquals(1, $filtered->count());
        $this->assertEquals('assigned-guide', $filtered->first()->slug);

        // Ohne Filter: beide Guides
        $all = Guide::where('status', 'published')->get();
        $this->assertEquals(2, $all->count());
    }

    public function test_scope_for_project_filters_tutorials(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Test',
            'slug' => 'test',
            'sort_order' => 1,
        ]);

        $project = Project::create([
            'name' => 'Testprojekt',
            'slug' => 'testprojekt',
            'app_name' => 'TestApp',
            'club_name' => 'TestClub',
            'is_active' => true,
        ]);

        $tutorial = \App\Models\Tutorial::create([
            'category_id' => $category->id,
            'title' => 'Zugewiesenes Tutorial',
            'slug' => 'assigned-tutorial',
            'description' => 'Beschreibung',
            'status' => 'published',
            'author_id' => $user->id,
        ]);
        $tutorial->projects()->attach($project);

        $unassignedTutorial = \App\Models\Tutorial::create([
            'category_id' => $category->id,
            'title' => 'Nicht zugewiesenes Tutorial',
            'slug' => 'unassigned-tutorial',
            'description' => 'Beschreibung',
            'status' => 'published',
            'author_id' => $user->id,
        ]);

        // Scope-Filter: nur zugewiesenes Tutorial
        $filtered = \App\Models\Tutorial::forProject($project)->where('status', 'published')->get();
        $this->assertEquals(1, $filtered->count());
        $this->assertEquals('assigned-tutorial', $filtered->first()->slug);
    }
}
