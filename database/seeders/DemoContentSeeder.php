<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Category;
use App\Models\User;
use App\Models\Guide;
use App\Models\GuideStep;
use App\Models\Tutorial;
use App\Models\TutorialStep;
use App\Models\TutorialHotspot;

class DemoContentSeeder extends Seeder
{
    /**
     * Befüllt die Demo-Projekte mit Beispiel-Guides und -Tutorials.
     */
    public function run(): void
    {
        // Hole die Demo-Projekte
        $projectA = Project::where('slug', 'sportverein-datteln')->first();
        $projectB = Project::where('slug', 'regatta-portal')->first();

        if (!$projectA || !$projectB) {
            $this->command->warn('Demo-Projekte nicht gefunden. Bitte zuerst DemoProjectsSeeder ausführen.');
            return;
        }

        // Hole oder erstelle einen Demo-Autor
        $author = User::first();
        if (!$author) {
            $author = User::factory()->create([
                'name' => 'Demo Admin',
                'email' => 'demo@example.com',
            ]);
        }

        // Hole oder erstelle Kategorien
        $categoryGettingStarted = Category::firstOrCreate(
            ['slug' => 'erste-schritte'],
            ['name' => 'Erste Schritte', 'sort_order' => 1]
        );

        $categoryFeatures = Category::firstOrCreate(
            ['slug' => 'funktionen'],
            ['name' => 'Funktionen', 'sort_order' => 2]
        );

        $categoryAdmin = Category::firstOrCreate(
            ['slug' => 'verwaltung'],
            ['name' => 'Verwaltung', 'sort_order' => 3]
        );

        $this->command->info('Erstelle Demo-Guides und -Tutorials...');

        // ==========================================
        // GUIDE 1: Anmeldung (für beide Projekte)
        // ==========================================
        $guide1 = Guide::create([
            'category_id' => $categoryGettingStarted->id,
            'title' => 'Willkommen bei {{app_name}} – So melden Sie sich an',
            'slug' => 'anmeldung',
            'description' => 'In dieser {{guide}} erfahren Sie, wie Sie sich bei {{app_name}} anmelden und Ihr {{profile}} einrichten.',
            'content' => 'Die Anmeldung bei {{app_name}} ist einfach. Folgen Sie den unten beschriebenen Schritten.',
            'status' => 'published',
            'author_id' => $author->id,
            'published_at' => now(),
        ]);

        GuideStep::create([
            'guide_id' => $guide1->id,
            'step_number' => 1,
            'title' => 'Öffnen Sie {{app_name}}',
            'description' => 'Rufen Sie die {{app_name}} über Ihren Browser auf oder öffnen Sie die App auf Ihrem Gerät.',
            'tip' => null,
            'warning' => null,
        ]);

        GuideStep::create([
            'guide_id' => $guide1->id,
            'step_number' => 2,
            'title' => 'Geben Sie Ihre Zugangsdaten ein',
            'description' => 'Tragen Sie Ihre E-Mail-Adresse und Ihr Passwort ein, die Sie bei der Registrierung erhalten haben.',
            'tip' => 'Wenn Sie Ihr Passwort vergessen haben, klicken Sie auf "Passwort zurücksetzen".',
            'warning' => null,
        ]);

        GuideStep::create([
            'guide_id' => $guide1->id,
            'step_number' => 3,
            'title' => 'Klicken Sie auf Anmelden',
            'description' => 'Nach dem Klicken werden Sie zum {{dashboard}} weitergeleitet.',
            'tip' => 'Aktivieren Sie die Zwei-Faktor-Authentifizierung in Ihren {{settings}} für zusätzliche Sicherheit.',
            'warning' => null,
        ]);

        // Guide 1 für beide Projekte zuweisen
        $guide1->projects()->attach([$projectA->id, $projectB->id]);

        // ==========================================
        // GUIDE 2: {{events}} verwalten (für beide Projekte)
        // ==========================================
        $guide2 = Guide::create([
            'category_id' => $categoryFeatures->id,
            'title' => 'So verwalten Sie Ihre {{events}}',
            'slug' => 'events-verwalten',
            'description' => 'Lernen Sie, wie Sie {{events}} erstellen, bearbeiten und löschen. Diese {{guide}} zeigt Ihnen alle wichtigen Funktionen.',
            'content' => 'Mit {{app_name}} können Sie Ihre {{events}} einfach verwalten. Ob neue {{event}} erstellen oder bestehende bearbeiten – hier erfahren Sie wie.',
            'status' => 'published',
            'author_id' => $author->id,
            'published_at' => now(),
        ]);

        GuideStep::create([
            'guide_id' => $guide2->id,
            'step_number' => 1,
            'title' => 'Navigieren zu {{events}}',
            'description' => 'Klicken Sie in der Hauptnavigation auf "{{events}}", um zur Übersicht aller {{events}} zu gelangen.',
            'tip' => 'Sie können auch über das {{search}}-Feld direkt nach einer {{event}} suchen.',
            'warning' => null,
        ]);

        GuideStep::create([
            'guide_id' => $guide2->id,
            'step_number' => 2,
            'title' => 'Neue {{event}} erstellen',
            'description' => 'Klicken Sie auf den Button "Neue {{event}} erstellen". Füllen Sie den Titel, das Datum und die Beschreibung aus.',
            'tip' => 'Nutzen Sie den {{calendar}}, um das Datum und die Uhrzeit auszuwählen.',
            'warning' => null,
        ]);

        GuideStep::create([
            'guide_id' => $guide2->id,
            'step_number' => 3,
            'title' => '{{event}} bearbeiten',
            'description' => 'Klicken Sie auf eine bestehende {{event}}, um sie zu bearbeiten. Sie können Titel, Datum, Beschreibung und {{members}} ändern.',
            'tip' => null,
            'warning' => 'Das Löschen einer {{event}} kann nicht rückgängig gemacht werden.',
        ]);

        GuideStep::create([
            'guide_id' => $guide2->id,
            'step_number' => 4,
            'title' => '{{members}} zu {{event}} einladen',
            'description' => 'In der {{event}}-Detailansicht können Sie {{members}} einladen. Wählen Sie die {{members}} aus der Liste oder laden Sie neue {{members}} per E-Mail ein.',
            'tip' => 'Sie können auch eine Gruppe von {{members}} auswählen, um mehrere {{members}} auf einmal einzuladen.',
            'warning' => null,
        ]);

        // Guide 2 für beide Projekte zuweisen
        $guide2->projects()->attach([$projectA->id, $projectB->id]);

        // ==========================================
        // GUIDE 3: {{members}} verwalten (für beide Projekte)
        // ==========================================
        $guide3 = Guide::create([
            'category_id' => $categoryAdmin->id,
            'title' => '{{members}} hinzufügen und verwalten',
            'slug' => 'members-verwalten',
            'description' => 'In dieser {{guide}} lernen Sie, wie Sie neue {{members}} zu {{club_name}} hinzufügen und die {{members}}-Daten verwalten.',
            'content' => 'Die Verwaltung von {{members}} ist eine der wichtigsten Funktionen in {{app_name}}. Hier erfahren Sie, wie Sie {{members}} hinzufügen, bearbeiten und organisieren.',
            'status' => 'published',
            'author_id' => $author->id,
            'published_at' => now(),
        ]);

        GuideStep::create([
            'guide_id' => $guide3->id,
            'step_number' => 1,
            'title' => 'Zum {{members}}-Bereich navigieren',
            'description' => 'Klicken Sie in der Hauptnavigation auf "{{members}}", um zur {{members}}-Übersicht zu gelangen.',
            'tip' => 'Über das {{dashboard}} sehen Sie eine schnelle Übersicht über die Anzahl der aktiven {{members}}.',
            'warning' => null,
        ]);

        GuideStep::create([
            'guide_id' => $guide3->id,
            'step_number' => 2,
            'title' => 'Neues {{member}} hinzufügen',
            'description' => 'Klicken Sie auf "Neues {{member}} hinzufügen". Tragen Sie Name, E-Mail und weitere Daten des {{member}} ein.',
            'tip' => 'Sie können auch mehrere {{members}} gleichzeitig per CSV-Datei importieren.',
            'warning' => null,
        ]);

        GuideStep::create([
            'guide_id' => $guide3->id,
            'step_number' => 3,
            'title' => '{{member}}-Daten bearbeiten',
            'description' => 'Klicken Sie auf ein {{member}}, um die Daten zu bearbeiten. Sie können Kontaktdaten, {{events}}-Teilnahmen und {{team}}-Zuordnungen ändern.',
            'tip' => null,
            'warning' => 'Als {{admin}} können Sie auch {{members}} deaktivieren, wenn sie nicht mehr aktiv sind.',
        ]);

        // Guide 3 für beide Projekte zuweisen
        $guide3->projects()->attach([$projectA->id, $projectB->id]);

        // ==========================================
        // GUIDE 4: {{dashboard}} Übersicht (nur Projekt A)
        // ==========================================
        $guide4 = Guide::create([
            'category_id' => $categoryGettingStarted->id,
            'title' => 'Ihr {{dashboard}} – Die zentrale Übersicht',
            'slug' => 'dashboard-uebersicht',
            'description' => 'Das {{dashboard}} in {{app_name}} zeigt Ihnen alle wichtigen Informationen auf einen Blick: {{news}}, {{events}} und mehr.',
            'content' => 'Nach der Anmeldung sehen Sie Ihr persönliches {{dashboard}. Hier finden Sie eine Übersicht über Ihre {{events}}, {{news}} und die Aktivitäten von {{club_name}}.',
            'status' => 'published',
            'author_id' => $author->id,
            'published_at' => now(),
        ]);

        GuideStep::create([
            'guide_id' => $guide4->id,
            'step_number' => 1,
            'title' => '{{dashboard}} verstehen',
            'description' => 'Das {{dashboard}} zeigt Ihnen die nächsten {{events}}, aktuelle {{news}} und eine Übersicht Ihrer {{members}}.',
            'tip' => 'Sie können das {{dashboard}} in Ihren {{settings}} anpassen und Widgets hinzufügen oder entfernen.',
            'warning' => null,
        ]);

        GuideStep::create([
            'guide_id' => $guide4->id,
            'step_number' => 2,
            'title' => '{{news}} auf dem {{dashboard}}',
            'description' => 'Die {{news}}-Sektion zeigt die neuesten Beiträge von {{club_name}}. Klicken Sie auf einen Beitrag, um den vollständigen Artikel zu lesen.',
            'tip' => 'Sie können {{comment}} zu {{news}}-Beiträgen hinterlassen.',
            'warning' => null,
        ]);

        // Guide 4 nur für Projekt A zuweisen
        $guide4->projects()->attach($projectA->id);

        // ==========================================
        // TUTORIAL 1: Erste {{event}} erstellen (für beide Projekte)
        // ==========================================
        $tutorial1 = Tutorial::create([
            'category_id' => $categoryFeatures->id,
            'title' => 'Interaktives Tutorial: Ihre erste {{event}} erstellen',
            'slug' => 'erste-event-erstellen',
            'description' => 'Dieses {{tutorial}} führt Sie Schritt für Schritt durch den Prozess, Ihre erste {{event}} in {{app_name}} zu erstellen.',
            'estimated_duration_minutes' => 5,
            'status' => 'published',
            'author_id' => $author->id,
        ]);

        TutorialStep::create([
            'tutorial_id' => $tutorial1->id,
            'step_number' => 1,
            'title' => 'Navigieren Sie zu {{events}}',
            'instruction' => 'Klicken Sie in der linken Navigation auf "{{events}}".',
            'tip' => null,
            'warning' => null,
        ]);

        TutorialStep::create([
            'tutorial_id' => $tutorial1->id,
            'step_number' => 2,
            'title' => 'Starten Sie den {{event}}-Ersteller',
            'instruction' => 'Klicken Sie auf den Button "Neue {{event}} erstellen" in der oberen rechten Ecke.',
            'tip' => null,
            'warning' => null,
        ]);

        TutorialStep::create([
            'tutorial_id' => $tutorial1->id,
            'step_number' => 3,
            'title' => 'Füllen Sie die {{event}}-Details aus',
            'instruction' => 'Geben Sie einen Titel für Ihre {{event}} ein, wählen Sie Datum und Uhrzeit über den {{calendar}}, und fügen Sie eine Beschreibung hinzu.',
            'tip' => 'Eine gute Beschreibung hilft den {{members}}, die {{event}} besser zu verstehen.',
            'warning' => null,
        ]);

        TutorialStep::create([
            'tutorial_id' => $tutorial1->id,
            'step_number' => 4,
            'title' => 'Speichern und {{members}} einladen',
            'instruction' => 'Klicken Sie auf "Speichern". Anschließend können Sie {{members}} zu Ihrer {{event}} einladen.',
            'tip' => 'Nutzen Sie die {{search}}, um bestimmte {{members}} zu finden.',
            'warning' => null,
        ]);

        // Tutorial 1 für beide Projekte zuweisen
        $tutorial1->projects()->attach([$projectA->id, $projectB->id]);

        // ==========================================
        // TUTORIAL 2: {{profile}} einrichten (für beide Projekte)
        // ==========================================
        $tutorial2 = Tutorial::create([
            'category_id' => $categoryGettingStarted->id,
            'title' => 'Tutorial: Ihr {{profile}} in {{app_name}} einrichten',
            'slug' => 'profile-einrichten',
            'description' => 'Lernen Sie in diesem {{tutorial}}, wie Sie Ihr {{profile}} in {{app_name}} personalisieren und Ihre {{settings}} anpassen.',
            'estimated_duration_minutes' => 3,
            'status' => 'published',
            'author_id' => $author->id,
        ]);

        TutorialStep::create([
            'tutorial_id' => $tutorial2->id,
            'step_number' => 1,
            'title' => 'Öffnen Sie Ihr {{profile}}',
            'instruction' => 'Klicken Sie oben rechts auf Ihr Profilbild und wählen Sie "{{profile}}".',
            'tip' => null,
            'warning' => null,
        ]);

        TutorialStep::create([
            'tutorial_id' => $tutorial2->id,
            'step_number' => 2,
            'title' => 'Bearbeiten Sie Ihre Daten',
            'instruction' => 'Aktualisieren Sie Ihren Namen, Ihre E-Mail-Adresse und fügen Sie ein Profilbild hinzu.',
            'tip' => null,
            'warning' => null,
        ]);

        TutorialStep::create([
            'tutorial_id' => $tutorial2->id,
            'step_number' => 3,
            'title' => 'Passen Sie Ihre {{settings}} an',
            'instruction' => 'Gehen Sie zu "{{settings}}" und passen Sie Benachrichtigungen, Sprache und andere Präferenzen an.',
            'tip' => 'Aktivieren Sie Push-Benachrichtigungen, um über neue {{events}} informiert zu werden.',
            'warning' => null,
        ]);

        // Tutorial 2 für beide Projekte zuweisen
        $tutorial2->projects()->attach([$projectA->id, $projectB->id]);

        $this->command->info('Demo-Inhalte erfolgreich erstellt!');
        $this->command->info('- 4 Guides mit Platzhaltern');
        $this->command->info('- 2 Tutorials mit Platzhaltern');
        $this->command->info('- Guides und Tutorials den Demo-Projekten zugewiesen');
    }
}
