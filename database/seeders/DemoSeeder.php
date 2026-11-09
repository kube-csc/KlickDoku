<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\ContentTranslation;
use App\Models\Guide;
use App\Models\GuideStep;
use App\Models\Tutorial;
use App\Models\TutorialStep;
use App\Models\TutorialHotspot;
use App\Models\Page;
use App\Models\Tag;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Admin-Benutzer erstellen
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@verein.local',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Kategorien basierend auf den 5 Hauptprojekten
        $categories = [
            ['name' => 'Vereinsverwaltung', 'slug' => 'vereinsverwaltung', 'description' => 'Kernfunktionen der Vereinsverwaltung: Mitglieder, Anträge, Fördermittel'],
            ['name' => 'Regattaview', 'slug' => 'regattaview', 'description' => 'Live-Ansicht von Regatta-Ergebnissen und Rankings'],
            ['name' => 'Helferplanung', 'slug' => 'helferplanung', 'description' => 'Planung und Koordination von Helfern für Veranstaltungen'],
            ['name' => 'Kurse', 'slug' => 'kurse', 'description' => 'Verwaltung von Kursen, Schulungen und Anmeldungen'],
            ['name' => 'Regattamanager', 'slug' => 'regattamanager', 'description' => 'Komplettes Management von Regatten und Wettbewerben'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Tags erstellen
        $tags = ['Einsteiger', 'Fortgeschritten', 'Anträge', 'Mitglieder', 'Regatten', 'Helfer', 'Kurse'];
        foreach ($tags as $name) {
            Tag::create(['name' => $name, 'slug' => str($name)->slug()]);
        }

        // Beispiel-Anleitung: Fördermittel-Antrag erstellen
        $guide = Guide::create([
            'category_id' => 1,
            'title' => 'Fördermittel-Antrag erstellen',
            'slug' => 'foerdermittel-antrag-erstellen',
            'description' => 'Schritt-für-Schritt-Anleitung zum Erstellen eines Fördermittel-Antrags im System.',
            'content' => null,
            'version' => '1.0',
            'project_ref' => 'vereinsverwaltung_dev',
            'status' => 'published',
            'author_id' => $admin->id,
            'published_at' => now(),
        ]);

        GuideStep::create([
            'guide_id' => $guide->id,
            'step_number' => 1,
            'title' => 'Login und Navigation',
            'description' => 'Melden Sie sich im System an und navigieren Sie zum Anträge-Modul über das Hauptmenü.',
            'tip' => 'Stellen Sie sicher, dass Sie ein aktuelles Passwort verwenden.',
            'warning' => null,
        ]);

        GuideStep::create([
            'guide_id' => $guide->id,
            'step_number' => 2,
            'title' => 'Neuen Antrag anlegen',
            'description' => 'Klicken Sie auf den Button "Neuer Antrag" und wählen Sie "Fördermittel" als Antragstyp.',
            'tip' => null,
            'warning' => null,
        ]);

        GuideStep::create([
            'guide_id' => $guide->id,
            'step_number' => 3,
            'title' => 'Daten ausfüllen',
            'description' => 'Füllen Sie alle erforderlichen Felder aus: Förderprogramm, Betrag, Begründung und Zeitraum.',
            'tip' => 'Nutzen Sie die Autovervollständigung für Förderprogramme.',
            'warning' => 'Fehlende Pflichtfelder werden beim Absenden markiert.',
        ]);

        GuideStep::create([
            'guide_id' => $guide->id,
            'step_number' => 4,
            'title' => 'Anhänge hochladen',
            'description' => 'Laden Sie die erforderlichen Unterlagen hoch: Budgetplan, Konzeptpapier und Referenzen.',
            'tip' => 'Maximale Dateigröße: 10 MB pro Datei.',
            'warning' => null,
        ]);

        GuideStep::create([
            'guide_id' => $guide->id,
            'step_number' => 5,
            'title' => 'Antrag absenden',
            'description' => 'Überprüfen Sie alle Daten und klicken Sie auf "Antrag absenden". Sie erhalten eine Bestätigungs-E-Mail.',
            'tip' => null,
            'warning' => 'Nach dem Absenden können keine Änderungen mehr vorgenommen werden.',
        ]);

        // Statische Seiten
        Page::create([
            'title' => 'Willkommen',
            'slug' => 'willkommen',
            'content' => 'Willkommen bei der Vereinsverwaltung! Hier finden Sie Anleitungen und Tutorials für alle Module unseres Vereinsprojekts.',
            'meta_description' => 'Willkommen bei der Vereinsverwaltung',
            'meta_keywords' => 'Verein, Verwaltung, Anleitung',
            'status' => 'published',
            'author_id' => $admin->id,
        ]);

        Page::create([
            'title' => 'Über das Projekt',
            'slug' => 'ueber-das-projekt',
            'content' => 'Das Vereinsprojekt ist ein Multiprojekt-Workspace in PhpStorm, der 5 eigenständige Projekte umfasst: Vereinsverwaltung, Regattaview, Helferplanung, Kurse und Regattamanager. Alle Projekte teilen sich eine gemeinsame Datenbank.',
            'meta_description' => 'Über das Vereinsprojekt',
            'meta_keywords' => 'Vereinsprojekt, Multiprojekt, PhpStorm',
            'status' => 'published',
            'author_id' => $admin->id,
        ]);

        // "Über uns"-Seite (wird im Layout referenziert)
        Page::create([
            'title' => 'Über uns',
            'slug' => 'ueber-uns',
            'content' => 'Diese Plattform dient als zentrale Wissensdatenbank für alle Projekte der Vereinsverwaltung.\n\nHier finden Sie:\n\n- Anleitungen: Schritt-für-Schritt-Anleitungen für häufige Aufgaben\n- Tutorials: Interaktive, geführte Einweisungen in die verschiedenen Module\n- Statische Seiten: Hintergrundinformationen und Dokumentation\n\nDie Plattform wird kontinuierlich erweitert. Bei Fragen wenden Sie sich bitte an den Administrator.',
            'meta_description' => 'Über die Wissensdatenbank der Vereinsverwaltung',
            'meta_keywords' => 'Über uns, Wissensdatenbank, Dokumentation',
            'status' => 'published',
            'author_id' => $admin->id,
        ]);

        // Zweite Anleitung: Mitglied anlegen
        $guide2 = Guide::create([
            'category_id' => 1,
            'title' => 'Neues Mitglied anlegen',
            'slug' => 'neues-mitglied-anlegen',
            'description' => 'So legen Sie ein neues Mitglied in der Vereinsverwaltung an und weisen es einer Abteilung zu.',
            'content' => null,
            'version' => '1.0',
            'project_ref' => 'vereinsverwaltung_dev',
            'status' => 'published',
            'author_id' => $admin->id,
            'published_at' => now(),
        ]);

        GuideStep::create([
            'guide_id' => $guide2->id,
            'step_number' => 1,
            'title' => 'Zum Mitglieder-Modul navigieren',
            'description' => 'Öffnen Sie im Hauptmenü den Punkt "Mitglieder" und klicken Sie auf "Mitglieder verwalten".',
            'tip' => 'Sie können auch über die Schnellzugriff-Leiste navigieren.',
            'warning' => null,
        ]);

        GuideStep::create([
            'guide_id' => $guide2->id,
            'step_number' => 2,
            'title' => 'Neues Mitglied erstellen',
            'description' => 'Klicken Sie auf "Neues Mitglied" und geben Sie die persönlichen Daten ein: Vorname, Nachname, E-Mail und Geburtsdatum.',
            'tip' => 'Die E-Mail-Adresse wird als Login verwendet.',
            'warning' => 'Die E-Mail-Adresse muss eindeutig sein.',
        ]);

        GuideStep::create([
            'guide_id' => $guide2->id,
            'step_number' => 3,
            'title' => 'Abteilung zuweisen',
            'description' => 'Wählen Sie die Abteilung aus, der das Mitglied zugeordnet werden soll, und setzen Sie die Mitgliedschaft auf "Aktiv".',
            'tip' => null,
            'warning' => null,
        ]);

        GuideStep::create([
            'guide_id' => $guide2->id,
            'step_number' => 4,
            'title' => 'Einladungs-E-Mail senden',
            'description' => 'Klicken Sie auf "Einladung senden", damit das Mitglied sich mit einem temporären Passwort anmelden kann.',
            'tip' => 'Das Mitglied muss bei der ersten Anmeldung ein neues Passwort setzen.',
            'warning' => 'Die Einladungs-E-Mail kann nur einmal gesendet werden.',
        ]);

        // Beispiel-Tutorial: Erste Schritte in der Vereinsverwaltung
        $tutorial = Tutorial::create([
            'category_id' => 1,
            'title' => 'Erste Schritte in der Vereinsverwaltung',
            'slug' => 'erste-schritte-vereinsverwaltung',
            'description' => 'Ein interaktives Tutorial, das Sie durch die wichtigsten Funktionen der Vereinsverwaltung führt.',
            'thumbnail_path' => null,
            'estimated_duration_minutes' => 15,
            'status' => 'published',
            'author_id' => $admin->id,
        ]);

        $step1 = TutorialStep::create([
            'tutorial_id' => $tutorial->id,
            'step_number' => 1,
            'title' => 'Das Dashboard kennenlernen',
            'instruction' => 'Nach dem Login sehen Sie das Dashboard. Hier finden Sie eine Übersicht über aktuelle Anträge, anstehende Termine und Ihre Aufgaben.',
            'screenshot_path' => 'screenshots/dashboard.png',
            'screenshot_width' => 1920,
            'screenshot_height' => 1080,
            'tip' => 'Das Dashboard kann individuell angepasst werden.',
            'warning' => null,
            'auto_advance' => false,
            'auto_advance_delay_ms' => 2000,
        ]);

        TutorialHotspot::create([
            'tutorial_step_id' => $step1->id,
            'hotspot_type' => 'click',
            'position_x' => 15,
            'position_y' => 25,
            'radius' => 20,
            'label' => 'Klicken Sie auf "Mitglieder" im Menü',
            'cursor_type' => 'pointer',
            'click_count' => 1,
            'is_clickable' => true,
            'sort_order' => 1,
        ]);

        $step2 = TutorialStep::create([
            'tutorial_id' => $tutorial->id,
            'step_number' => 2,
            'title' => 'Mitglieder durchsuchen',
            'instruction' => 'Nutzen Sie die Suchleiste, um Mitglieder nach Name, E-Mail oder Abteilung zu finden.',
            'screenshot_path' => 'screenshots/mitglieder-suche.png',
            'screenshot_width' => 1920,
            'screenshot_height' => 1080,
            'tip' => 'Sie können auch nach Teilbegriffen suchen.',
            'warning' => null,
            'auto_advance' => false,
            'auto_advance_delay_ms' => 2000,
        ]);

        TutorialHotspot::create([
            'tutorial_step_id' => $step2->id,
            'hotspot_type' => 'type',
            'position_x' => 50,
            'position_y' => 15,
            'radius' => 25,
            'label' => 'Geben Sie hier den Suchbegriff ein',
            'cursor_type' => 'text',
            'click_count' => 1,
            'is_clickable' => true,
            'sort_order' => 1,
        ]);

        $step3 = TutorialStep::create([
            'tutorial_id' => $tutorial->id,
            'step_number' => 3,
            'title' => 'Anträge einsehen',
            'instruction' => 'Im Anträge-Modul sehen Sie alle eingegangenen Anträge mit ihrem aktuellen Status.',
            'screenshot_path' => 'screenshots/antraege.png',
            'screenshot_width' => 1920,
            'screenshot_height' => 1080,
            'tip' => 'Filtern Sie nach Status, um nur offene Anträge zu sehen.',
            'warning' => 'Nur Anträge mit dem Status "Offen" können bearbeitet werden.',
            'auto_advance' => false,
            'auto_advance_delay_ms' => 2000,
        ]);

        TutorialHotspot::create([
            'tutorial_step_id' => $step3->id,
            'hotspot_type' => 'click',
            'position_x' => 30,
            'position_y' => 40,
            'radius' => 20,
            'label' => 'Klicken Sie auf einen Antrag zum Öffnen',
            'cursor_type' => 'pointer',
            'click_count' => 1,
            'is_clickable' => true,
            'sort_order' => 1,
        ]);

        TutorialHotspot::create([
            'tutorial_step_id' => $step3->id,
            'hotspot_type' => 'click',
            'position_x' => 70,
            'position_y' => 10,
            'radius' => 15,
            'label' => 'Filter nach Status anwenden',
            'cursor_type' => 'pointer',
            'click_count' => 1,
            'is_clickable' => true,
            'sort_order' => 2,
        ]);

        // === ENGLISCHE ÜBERSETZUNGEN ===

        // Kategorien
        $enCategories = [
            ['id' => 1, 'title' => 'Association Management', 'description' => 'Core features: members, applications, grants'],
            ['id' => 2, 'title' => 'Regatta View', 'description' => 'Live view of regatta results and rankings'],
            ['id' => 3, 'title' => 'Volunteer Planning', 'description' => 'Planning and coordination of volunteers for events'],
            ['id' => 4, 'title' => 'Courses', 'description' => 'Management of courses, training and registrations'],
            ['id' => 5, 'title' => 'Regatta Manager', 'description' => 'Complete management of regattas and competitions'],
        ];

        foreach ($enCategories as $cat) {
            ContentTranslation::create([
                'translatable_type' => Category::class,
                'translatable_id' => $cat['id'],
                'locale' => 'en',
                'title' => $cat['title'],
                'description' => $cat['description'],
            ]);
        }

        // Guide 1: Fördermittel-Antrag erstellen
        ContentTranslation::create([
            'translatable_type' => Guide::class,
            'translatable_id' => $guide->id,
            'locale' => 'en',
            'title' => 'Create a Grant Application',
            'description' => 'Step-by-step guide to creating a grant application in the system.',
        ]);

        // Guide 2: Neues Mitglied anlegen
        ContentTranslation::create([
            'translatable_type' => Guide::class,
            'translatable_id' => $guide2->id,
            'locale' => 'en',
            'title' => 'Add a New Member',
            'description' => 'How to add a new member to the association management and assign them to a department.',
        ]);

        // Tutorial: Erste Schritte
        ContentTranslation::create([
            'translatable_type' => Tutorial::class,
            'translatable_id' => $tutorial->id,
            'locale' => 'en',
            'title' => 'First Steps in Association Management',
            'description' => 'An interactive tutorial that guides you through the most important features.',
        ]);

        // Seiten
        $pages = Page::all();
        foreach ($pages as $page) {
            $enTitle = '';
            $enContent = '';
            $enMeta = '';

            switch ($page->slug) {
                case 'willkommen':
                    $enTitle = 'Welcome';
                    $enContent = 'Welcome to the Association Management! Here you will find guides and tutorials for all modules of our association project.';
                    $enMeta = 'Welcome to the Association Management';
                    break;
                case 'ueber-das-projekt':
                    $enTitle = 'About the Project';
                    $enContent = 'The Association Project is a multi-project workspace in PhpStorm, covering 5 independent projects: Association Management, Regatta View, Volunteer Planning, Courses and Regatta Manager. All projects share a common database.';
                    $enMeta = 'About the Association Project';
                    break;
                case 'ueber-uns':
                    $enTitle = 'About us';
                    $enContent = 'This platform serves as a central knowledge base for all projects of the association management.\n\nHere you will find:\n\n- Guides: Step-by-step instructions for common tasks\n- Tutorials: Interactive, guided introductions to the various modules\n- Static pages: Background information and documentation\n\nThe platform is continuously being expanded. If you have any questions, please contact the administrator.';
                    $enMeta = 'About the knowledge base of the association management';
                    break;
            }

            if ($enTitle) {
                ContentTranslation::create([
                    'translatable_type' => Page::class,
                    'translatable_id' => $page->id,
                    'locale' => 'en',
                    'title' => $enTitle,
                    'content' => $enContent,
                    'meta_description' => $enMeta,
                ]);
            }
        }
    }
}
