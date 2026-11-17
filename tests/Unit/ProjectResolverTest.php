<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Project;
use App\Services\ProjectResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectResolverTest extends TestCase
{
    use RefreshDatabase;

    protected ProjectResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = app(ProjectResolver::class);
    }

    public function test_resolve_from_subdomain_finds_project(): void
    {
        $project = Project::create([
            'name' => 'Testprojekt',
            'slug' => 'testprojekt',
            'subdomain' => 'test',
            'app_name' => 'TestApp',
            'club_name' => 'TestClub',
            'is_active' => true,
        ]);

        $found = $this->resolver->resolveFromSubdomain('test');
        $this->assertNotNull($found);
        $this->assertEquals($project->id, $found->id);
    }

    public function test_resolve_from_subdomain_ignores_reserved(): void
    {
        $result = $this->resolver->resolveFromSubdomain('www');
        $this->assertNull($result);

        $result = $this->resolver->resolveFromSubdomain('api');
        $this->assertNull($result);

        $result = $this->resolver->resolveFromSubdomain('admin');
        $this->assertNull($result);
    }

    public function test_resolve_from_subdomain_returns_null_for_unknown(): void
    {
        $result = $this->resolver->resolveFromSubdomain('unbekannt');
        $this->assertNull($result);
    }

    public function test_resolve_from_subdomain_returns_null_for_inactive(): void
    {
        $project = Project::create([
            'name' => 'Inaktives Projekt',
            'slug' => 'inaktiv',
            'subdomain' => 'inaktiv',
            'app_name' => 'InaktivApp',
            'club_name' => 'InaktivClub',
            'is_active' => false,
        ]);

        $result = $this->resolver->resolveFromSubdomain('inaktiv');
        $this->assertNull($result);
    }

    public function test_resolve_from_domain_finds_project(): void
    {
        $project = Project::create([
            'name' => 'Testprojekt',
            'slug' => 'testprojekt',
            'domain' => 'projekt-a.de',
            'app_name' => 'TestApp',
            'club_name' => 'TestClub',
            'is_active' => true,
        ]);

        $found = $this->resolver->resolveFromDomain('projekt-a.de');
        $this->assertNotNull($found);
        $this->assertEquals($project->id, $found->id);
    }

    public function test_resolve_from_domain_returns_null_for_unknown(): void
    {
        $result = $this->resolver->resolveFromDomain('unbekannt.de');
        $this->assertNull($result);
    }

    public function test_resolve_from_query_param_finds_project(): void
    {
        $project = Project::create([
            'name' => 'Testprojekt',
            'slug' => 'testprojekt',
            'app_name' => 'TestApp',
            'club_name' => 'TestClub',
            'is_active' => true,
        ]);

        $request = \Illuminate\Http\Request::create('/?project=testprojekt', 'GET');
        $found = $this->resolver->resolveFromQueryParam($request);
        $this->assertNotNull($found);
        $this->assertEquals($project->id, $found->id);
    }

    public function test_resolve_from_query_param_returns_null_without_param(): void
    {
        $request = \Illuminate\Http\Request::create('/', 'GET');
        $found = $this->resolver->resolveFromQueryParam($request);
        $this->assertNull($found);
    }

    public function test_resolve_from_request_prioritizes_subdomain(): void
    {
        $projectA = Project::create([
            'name' => 'Projekt A',
            'slug' => 'projekt-a',
            'subdomain' => 'a',
            'app_name' => 'AppA',
            'club_name' => 'ClubA',
            'is_active' => true,
        ]);

        $projectB = Project::create([
            'name' => 'Projekt B',
            'slug' => 'projekt-b',
            'app_name' => 'AppB',
            'club_name' => 'ClubB',
            'is_active' => true,
        ]);

        // Subdomain hat Priorität vor Query-Param (benötigt mind. 3 Teile: a.test.localhost)
        $request = \Illuminate\Http\Request::create('http://a.test.localhost/?project=projekt-b', 'GET');
        $found = $this->resolver->resolveFromRequest($request);
        $this->assertNotNull($found);
        $this->assertEquals($projectA->id, $found->id);
    }

    public function test_resolve_from_request_uses_query_param_fallback(): void
    {
        $project = Project::create([
            'name' => 'Testprojekt',
            'slug' => 'testprojekt',
            'app_name' => 'TestApp',
            'club_name' => 'TestClub',
            'is_active' => true,
        ]);

        $request = \Illuminate\Http\Request::create('http://localhost/?project=testprojekt', 'GET');
        $found = $this->resolver->resolveFromRequest($request);
        $this->assertNotNull($found);
        $this->assertEquals($project->id, $found->id);
    }

    public function test_resolve_from_request_returns_null_without_match(): void
    {
        $request = \Illuminate\Http\Request::create('http://localhost/', 'GET');
        $found = $this->resolver->resolveFromRequest($request);
        $this->assertNull($found);
    }

    public function test_ip_address_not_treated_as_subdomain(): void
    {
        // 127.0.0.1 soll nicht als Subdomain-Struktur interpretiert werden
        $project = Project::create([
            'name' => 'Localhost Projekt',
            'slug' => 'localhost-projekt',
            'domain' => '127.0.0.1',
            'app_name' => 'LocalApp',
            'club_name' => 'LocalClub',
            'is_active' => true,
        ]);

        $request = \Illuminate\Http\Request::create('http://127.0.0.1:8000/', 'GET');
        $found = $this->resolver->resolveFromRequest($request);
        $this->assertNotNull($found);
        $this->assertEquals($project->id, $found->id);
    }

    public function test_ip_address_domain_finds_project(): void
    {
        $project = Project::create([
            'name' => 'IP Projekt',
            'slug' => 'ip-projekt',
            'domain' => '192.168.1.100',
            'app_name' => 'IPApp',
            'club_name' => 'IPClub',
            'is_active' => true,
        ]);

        $found = $this->resolver->resolveFromDomain('192.168.1.100');
        $this->assertNotNull($found);
        $this->assertEquals($project->id, $found->id);
    }
}
