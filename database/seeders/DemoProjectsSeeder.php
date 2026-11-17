<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\ProjectTerm;
use App\Models\User;

class DemoProjectsSeeder extends Seeder
{
    public function run(): void
    {
        // Projekt A: Sportverein Datteln
        $projectA = Project::updateOrCreate(
            ['slug' => 'sportverein-datteln'],
            [
                'name' => 'Sportverein Datteln',
                'subdomain' => 'sportverein',
                'domain' => null,
                'app_name' => 'Vereinsverwaltung',
                'club_name' => 'Sportverein Datteln',
                'logo_path' => null,
                'primary_color' => '#1a73e8',
                'secondary_color' => '#4285f4',
                'is_active' => true,
            ]
        );

        // Begriffe für Projekt A
        $termsA = [
            ['placeholder_key' => 'app_name', 'value_de' => 'Vereinsverwaltung', 'value_en' => 'Club Management'],
            ['placeholder_key' => 'club_name', 'value_de' => 'Sportverein Datteln', 'value_en' => 'Sports Club Datteln'],
            ['placeholder_key' => 'member', 'value_de' => 'Mitglied', 'value_en' => 'Member'],
            ['placeholder_key' => 'members', 'value_de' => 'Mitglieder', 'value_en' => 'Members'],
            ['placeholder_key' => 'event', 'value_de' => 'Veranstaltung', 'value_en' => 'Event'],
            ['placeholder_key' => 'events', 'value_de' => 'Veranstaltungen', 'value_en' => 'Events'],
            ['placeholder_key' => 'news', 'value_de' => 'Nachrichten', 'value_en' => 'News'],
            ['placeholder_key' => 'calendar', 'value_de' => 'Kalender', 'value_en' => 'Calendar'],
            ['placeholder_key' => 'dashboard', 'value_de' => 'Dashboard', 'value_en' => 'Dashboard'],
            ['placeholder_key' => 'settings', 'value_de' => 'Einstellungen', 'value_en' => 'Settings'],
        ];

        foreach ($termsA as $term) {
            ProjectTerm::updateOrCreate(
                ['project_id' => $projectA->id, 'placeholder_key' => $term['placeholder_key']],
                $term
            );
        }

        // Projekt B: RegattaPortal
        $projectB = Project::updateOrCreate(
            ['slug' => 'regatta-portal'],
            [
                'name' => 'RegattaPortal',
                'subdomain' => 'regatta',
                'domain' => null,
                'app_name' => 'RegattaPortal',
                'club_name' => 'Ruder-Club Example',
                'logo_path' => null,
                'primary_color' => '#0d6efd',
                'secondary_color' => '#6610f2',
                'is_active' => true,
            ]
        );

        // Begriffe für Projekt B
        $termsB = [
            ['placeholder_key' => 'app_name', 'value_de' => 'RegattaPortal', 'value_en' => 'Regatta Portal'],
            ['placeholder_key' => 'club_name', 'value_de' => 'Ruder-Club Example', 'value_en' => 'Rowing Club Example'],
            ['placeholder_key' => 'member', 'value_de' => 'Teilnehmer', 'value_en' => 'Participant'],
            ['placeholder_key' => 'members', 'value_de' => 'Teilnehmer', 'value_en' => 'Participants'],
            ['placeholder_key' => 'event', 'value_de' => 'Regatta', 'value_en' => 'Regatta'],
            ['placeholder_key' => 'events', 'value_de' => 'Regatten', 'value_en' => 'Regattas'],
            ['placeholder_key' => 'news', 'value_de' => 'Aktuelles', 'value_en' => 'News'],
            ['placeholder_key' => 'calendar', 'value_de' => 'Planer', 'value_en' => 'Planner'],
            ['placeholder_key' => 'dashboard', 'value_de' => 'Übersicht', 'value_en' => 'Overview'],
            ['placeholder_key' => 'settings', 'value_de' => 'Konfiguration', 'value_en' => 'Configuration'],
        ];

        foreach ($termsB as $term) {
            ProjectTerm::updateOrCreate(
                ['project_id' => $projectB->id, 'placeholder_key' => $term['placeholder_key']],
                $term
            );
        }

        // User-Projekt-Zuordnungen (Demo-Daten)
        // User 1 (Demo Admin) → Admin bei beiden Projekten
        // User 2 (Administrator) → Editor bei Projekt A, Viewer bei Projekt B
        $adminUser = User::find(1);
        $editorUser = User::find(2);

        if ($adminUser) {
            $projectA->users()->syncWithoutDetaching([
                $adminUser->id => ['role' => 'admin'],
            ]);
            $projectB->users()->syncWithoutDetaching([
                $adminUser->id => ['role' => 'admin'],
            ]);
        }

        if ($editorUser) {
            $projectA->users()->syncWithoutDetaching([
                $editorUser->id => ['role' => 'editor'],
            ]);
            $projectB->users()->syncWithoutDetaching([
                $editorUser->id => ['role' => 'viewer'],
            ]);
        }
    }
}
