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

    public function test_rewriteDomain_kel_datteln_to_antraege_ssv_datteln(): void
    {
        // Hotspot-Link von kel-datteln.de soll auf antraege.ssv-datteln.de umgeschrieben werden
        $url = 'https://www.kel-datteln.de/user/profile';
        $targetDomain = 'https://antraege.ssv-datteln.de';

        $result = $this->service->rewriteDomain($url, null, $targetDomain);

        $this->assertEquals('https://antraege.ssv-datteln.de/user/profile', $result);
    }

    public function test_rewriteDomain_with_source_domain_exact_match(): void
    {
        // Exakte Übereinstimmung mit source_domain
        $url = 'https://www.kel-datteln.de/user/profile';
        $sourceDomain = 'https://www.kel-datteln.de';
        $targetDomain = 'https://antraege.ssv-datteln.de';

        $result = $this->service->rewriteDomain($url, $sourceDomain, $targetDomain);

        $this->assertEquals('https://antraege.ssv-datteln.de/user/profile', $result);
    }

    public function test_rewriteDomain_preserves_query_parameters(): void
    {
        $url = 'https://www.kel-datteln.de/user/profile?tab=settings';
        $targetDomain = 'https://antraege.ssv-datteln.de';

        $result = $this->service->rewriteDomain($url, null, $targetDomain);

        $this->assertEquals('https://antraege.ssv-datteln.de/user/profile?tab=settings', $result);
    }

    public function test_rewriteDomain_preserves_fragment(): void
    {
        $url = 'https://www.kel-datteln.de/user/profile#section1';
        $targetDomain = 'https://antraege.ssv-datteln.de';

        $result = $this->service->rewriteDomain($url, null, $targetDomain);

        $this->assertEquals('https://antraege.ssv-datteln.de/user/profile#section1', $result);
    }

    public function test_rewriteHotspotDomains_with_project_source_domain(): void
    {
        $project = Project::create([
            'name' => 'Testprojekt',
            'slug' => 'testprojekt',
            'app_name' => 'TestApp',
            'club_name' => 'TestClub',
            'source_domain' => 'https://antraege.ssv-datteln.de',
        ]);

        $hotspotData = [
            'link' => 'https://www.kel-datteln.de/user/profile',
            'label' => 'Profil öffnen',
        ];

        $result = $this->service->rewriteHotspotDomains($hotspotData, $project);

        $this->assertEquals('https://antraege.ssv-datteln.de/user/profile', $result['link']);
        $this->assertEquals('Profil öffnen', $result['label']);
    }

    public function test_rewriteDomain_returns_empty_for_null_url(): void
    {
        $result = $this->service->rewriteDomain(null, null, 'https://antraege.ssv-datteln.de');
        $this->assertEquals('', $result);
    }

    public function test_rewriteDomain_returns_url_unchanged_without_target(): void
    {
        $url = 'https://www.kel-datteln.de/user/profile';
        $result = $this->service->rewriteDomain($url, null, null);
        $this->assertEquals($url, $result);
    }

    public function test_rewriteDomain_leaves_relative_paths_unchanged(): void
    {
        $url = '/user/profile';
        $result = $this->service->rewriteDomain($url, null, 'https://antraege.ssv-datteln.de');
        $this->assertEquals('/user/profile', $result);
    }

    // === Umfassende Tests für Domain-Erkennung und -Ersetzung ===

    public function test_domain_recognition_protocol_subdomain_domain_tld(): void
    {
        // Test: Der gesamte Domain-Teil (Protokoll + Subdomain + Domain + TLD) soll erkannt und ersetzt werden
        // URL-Struktur: https://www.kel-datteln.de/user/profile
        // Domain-Teil:  https://www.kel-datteln.de (Protokoll + Subdomain + Domain + TLD)
        // Pfad-Teil:    /user/profile (soll erhalten bleiben)
        
        $url = 'https://www.kel-datteln.de/user/profile';
        $targetDomain = 'https://antraege.ssv-datteln.de';

        $result = $this->service->rewriteDomain($url, null, $targetDomain);

        // Der Domain-Teil "https://www.kel-datteln.de" wird durch "https://antraege.ssv-datteln.de" ersetzt
        $this->assertEquals('https://antraege.ssv-datteln.de/user/profile', $result);
    }

    public function test_domain_recognition_without_www(): void
    {
        // URL ohne Subdomain (www)
        $url = 'https://kel-datteln.de/user/profile';
        $targetDomain = 'https://antraege.ssv-datteln.de';

        $result = $this->service->rewriteDomain($url, null, $targetDomain);

        $this->assertEquals('https://antraege.ssv-datteln.de/user/profile', $result);
    }

    public function test_domain_recognition_with_http(): void
    {
        // HTTP statt HTTPS
        $url = 'http://www.kel-datteln.de/user/profile';
        $targetDomain = 'https://antraege.ssv-datteln.de';

        $result = $this->service->rewriteDomain($url, null, $targetDomain);

        // Protokoll wird durch das Protokoll der Ziel-Domain ersetzt
        $this->assertEquals('https://antraege.ssv-datteln.de/user/profile', $result);
    }

    public function test_domain_recognition_with_port(): void
    {
        // URL mit Port-Nummer
        $url = 'https://www.kel-datteln.de:8080/user/profile';
        $targetDomain = 'https://antraege.ssv-datteln.de';

        $result = $this->service->rewriteDomain($url, null, $targetDomain);

        // Port wird entfernt, da er Teil des Domain-Teils ist
        $this->assertEquals('https://antraege.ssv-datteln.de/user/profile', $result);
    }

    public function test_domain_recognition_with_complex_path(): void
    {
        // Komplexer Pfad mit mehreren Segmenten
        $url = 'https://www.kel-datteln.de/user/profile/settings/account';
        $targetDomain = 'https://antraege.ssv-datteln.de';

        $result = $this->service->rewriteDomain($url, null, $targetDomain);

        $this->assertEquals('https://antraege.ssv-datteln.de/user/profile/settings/account', $result);
    }

    public function test_domain_recognition_with_query_and_fragment(): void
    {
        // URL mit Query-Parameter UND Fragment
        $url = 'https://www.kel-datteln.de/user/profile?tab=settings#section1';
        $targetDomain = 'https://antraege.ssv-datteln.de';

        $result = $this->service->rewriteDomain($url, null, $targetDomain);

        // Query und Fragment bleiben erhalten
        $this->assertEquals('https://antraege.ssv-datteln.de/user/profile?tab=settings#section1', $result);
    }

    public function test_domain_recognition_with_multiple_query_parameters(): void
    {
        // Multiple Query-Parameter
        $url = 'https://www.kel-datteln.de/user/profile?tab=settings&action=edit&redirect=/home';
        $targetDomain = 'https://antraege.ssv-datteln.de';

        $result = $this->service->rewriteDomain($url, null, $targetDomain);

        $this->assertEquals('https://antraege.ssv-datteln.de/user/profile?tab=settings&action=edit&redirect=/home', $result);
    }

    public function test_domain_recognition_with_subdomain(): void
    {
        // URL mit anderer Subdomain
        $url = 'https://app.kel-datteln.de/user/profile';
        $targetDomain = 'https://antraege.ssv-datteln.de';

        $result = $this->service->rewriteDomain($url, null, $targetDomain);

        // Die gesamte Domain (inklusive Subdomain) wird ersetzt
        $this->assertEquals('https://antraege.ssv-datteln.de/user/profile', $result);
    }

    public function test_domain_recognition_with_deep_subdomain(): void
    {
        // URL mit verschachtelter Subdomain
        $url = 'https://test.app.kel-datteln.de/user/profile';
        $targetDomain = 'https://antraege.ssv-datteln.de';

        $result = $this->service->rewriteDomain($url, null, $targetDomain);

        $this->assertEquals('https://antraege.ssv-datteln.de/user/profile', $result);
    }

    public function test_domain_recognition_root_path(): void
    {
        // URL ohne Pfad (nur Domain)
        $url = 'https://www.kel-datteln.de';
        $targetDomain = 'https://antraege.ssv-datteln.de';

        $result = $this->service->rewriteDomain($url, null, $targetDomain);

        $this->assertEquals('https://antraege.ssv-datteln.de/', $result);
    }

    public function test_domain_recognition_with_trailing_slash(): void
    {
        // URL mit trailing slash
        $url = 'https://www.kel-datteln.de/user/profile/';
        $targetDomain = 'https://antraege.ssv-datteln.de';

        $result = $this->service->rewriteDomain($url, null, $targetDomain);

        $this->assertEquals('https://antraege.ssv-datteln.de/user/profile/', $result);
    }

    public function test_domain_recognition_different_tld(): void
    {
        // Verschiedene TLDs
        $url = 'https://www.kel-datteln.com/user/profile';
        $targetDomain = 'https://antraege.ssv-datteln.de';

        $result = $this->service->rewriteDomain($url, null, $targetDomain);

        $this->assertEquals('https://antraege.ssv-datteln.de/user/profile', $result);
    }

    public function test_domain_recognition_with_special_characters_in_path(): void
    {
        // Pfad mit Sonderzeichen
        $url = 'https://www.kel-datteln.de/user/my-profile%20v2/settings';
        $targetDomain = 'https://antraege.ssv-datteln.de';

        $result = $this->service->rewriteDomain($url, null, $targetDomain);

        $this->assertEquals('https://antraege.ssv-datteln.de/user/my-profile%20v2/settings', $result);
    }

    public function test_url_structure_breakdown(): void
    {
        // Dieser Test dokumentiert die URL-Struktur und welche Teile ersetzt werden
        $url = 'https://www.kel-datteln.de/user/profile?tab=settings#section1';
        
        // parse_url() zerlegt die URL in:
        // scheme:   https
        // host:     www.kel-datteln.de
        // path:     /user/profile
        // query:    tab=settings
        // fragment: section1
        
        // Der Domain-Teil = scheme + host = https://www.kel-datteln.de
        // Der Pfad-Teil = path + query + fragment = /user/profile?tab=settings#section1
        
        $targetDomain = 'https://antraege.ssv-datteln.de';
        $result = $this->service->rewriteDomain($url, null, $targetDomain);
        
        // Nur der Domain-Teil wird ersetzt
        $this->assertEquals('https://antraege.ssv-datteln.de/user/profile?tab=settings#section1', $result);
        
        // Verifizieren, dass der Pfad unverändert ist
        $this->assertStringContainsString('/user/profile', $result);
        // Verifizieren, dass der Query-Parameter unverändert ist
        $this->assertStringContainsString('?tab=settings', $result);
        // Verifizieren, dass das Fragment unverändert ist
        $this->assertStringContainsString('#section1', $result);
    }

    public function test_rewriteHotspotDomains_full_scenario(): void
    {
        // Vollständiges Szenario: Hotspot-Link mit projects.source_domain
        $project = Project::create([
            'name' => 'SSV Datteln',
            'slug' => 'ssv-datteln',
            'app_name' => 'SSV Anträge',
            'club_name' => 'SSV Datteln',
            'source_domain' => 'https://antraege.ssv-datteln.de',
        ]);

        $hotspotData = [
            'link' => 'https://www.kel-datteln.de/user/profile?tab=settings#section1',
            'label' => 'Profil öffnen',
            'description' => 'Zum Profil gehen',
        ];

        $result = $this->service->rewriteHotspotDomains($hotspotData, $project);

        // Domain ersetzt, Pfad + Query + Fragment erhalten
        $this->assertEquals('https://antraege.ssv-datteln.de/user/profile?tab=settings#section1', $result['link']);
        $this->assertEquals('Profil öffnen', $result['label']);
        $this->assertEquals('Zum Profil gehen', $result['description']);
    }

    // === Tests für IP-Adressen ===

    public function test_rewriteDomain_with_ip_address(): void
    {
        // IP-Adresse als Host: http://sportverein.127.0.0.1/login
        // parse_url() erkennt "sportverein" als user und "127.0.0.1" als host
        // Der gesamte Domain-Teil (scheme + user@host) soll ersetzt werden
        $url = 'http://sportverein.127.0.0.1/login';
        $targetDomain = 'https://antraege.ssv-datteln.de';

        $result = $this->service->rewriteDomain($url, null, $targetDomain);

        // IP-Adresse wird ersetzt, Pfad bleibt erhalten
        $this->assertEquals('https://antraege.ssv-datteln.de/login', $result);
    }

    public function test_rewriteDomain_with_ip_address_and_port(): void
    {
        // IP-Adresse mit Port
        $url = 'http://127.0.0.1:8080/user/profile';
        $targetDomain = 'https://antraege.ssv-datteln.de';

        $result = $this->service->rewriteDomain($url, null, $targetDomain);

        $this->assertEquals('https://antraege.ssv-datteln.de/user/profile', $result);
    }

    public function test_rewriteDomain_with_ip_address_and_query(): void
    {
        // IP-Adresse mit Query-Parametern
        $url = 'http://192.168.1.100/login?redirect=/dashboard';
        $targetDomain = 'https://antraege.ssv-datteln.de';

        $result = $this->service->rewriteDomain($url, null, $targetDomain);

        $this->assertEquals('https://antraege.ssv-datteln.de/login?redirect=/dashboard', $result);
    }

    public function test_rewriteDomain_with_ip_address_subdomain_like_prefix(): void
    {
        // URL wie http://sportverein.127.0.0.1/login (sportverein sieht aus wie Subdomain)
        $url = 'http://sportverein.127.0.0.1/login';
        $targetDomain = 'https://klickdoc.ssv-datteln.de';

        $result = $this->service->rewriteDomain($url, null, $targetDomain);

        // parse_url() interpretiert "sportverein" als username und "127.0.0.1" als host
        // Der Pfad /login bleibt erhalten
        $this->assertEquals('https://klickdoc.ssv-datteln.de/login', $result);
    }

    public function test_rewriteHotspotDomains_with_ip_address(): void
    {
        // Vollständiges Szenario: Hotspot-Link mit IP-Adresse und projects.source_domain
        $project = Project::create([
            'name' => 'Testprojekt IP',
            'slug' => 'testprojekt-ip',
            'app_name' => 'TestApp',
            'club_name' => 'TestClub',
            'source_domain' => 'https://antraege.ssv-datteln.de',
        ]);

        $hotspotData = [
            'link' => 'http://sportverein.127.0.0.1/login',
            'label' => 'Login',
            'description' => 'Einloggen',
        ];

        $result = $this->service->rewriteHotspotDomains($hotspotData, $project);

        // IP-Adresse wird durch source_domain ersetzt
        $this->assertEquals('https://antraege.ssv-datteln.de/login', $result['link']);
        $this->assertEquals('Login', $result['label']);
    }
}
