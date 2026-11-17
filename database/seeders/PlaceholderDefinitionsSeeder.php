<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlaceholderDefinition;

class PlaceholderDefinitionsSeeder extends Seeder
{
    public function run(): void
    {
        $placeholders = [
            [
                'key' => 'app_name',
                'description_de' => 'Name der Anwendung',
                'description_en' => 'Name of the application',
                'default_value_de' => 'Vereinsverwaltung',
                'default_value_en' => 'Club Management',
                'category' => 'general',
            ],
            [
                'key' => 'club_name',
                'description_de' => 'Name des Vereins oder Clubs',
                'description_en' => 'Name of the club',
                'default_value_de' => 'Sportverein Datteln',
                'default_value_en' => 'Sports Club Datteln',
                'category' => 'general',
            ],
            [
                'key' => 'member',
                'description_de' => 'Singular: Mitglied',
                'description_en' => 'Singular: Member',
                'default_value_de' => 'Mitglied',
                'default_value_en' => 'Member',
                'category' => 'content',
            ],
            [
                'key' => 'members',
                'description_de' => 'Plural: Mitglieder',
                'description_en' => 'Plural: Members',
                'default_value_de' => 'Mitglieder',
                'default_value_en' => 'Members',
                'category' => 'content',
            ],
            [
                'key' => 'event',
                'description_de' => 'Singular: Veranstaltung',
                'description_en' => 'Singular: Event',
                'default_value_de' => 'Veranstaltung',
                'default_value_en' => 'Event',
                'category' => 'content',
            ],
            [
                'key' => 'events',
                'description_de' => 'Plural: Veranstaltungen',
                'description_en' => 'Plural: Events',
                'default_value_de' => 'Veranstaltungen',
                'default_value_en' => 'Events',
                'category' => 'content',
            ],
            [
                'key' => 'news',
                'description_de' => 'Nachrichten',
                'description_en' => 'News',
                'default_value_de' => 'Nachrichten',
                'default_value_en' => 'News',
                'category' => 'navigation',
            ],
            [
                'key' => 'calendar',
                'description_de' => 'Kalender',
                'description_en' => 'Calendar',
                'default_value_de' => 'Kalender',
                'default_value_en' => 'Calendar',
                'category' => 'navigation',
            ],
            [
                'key' => 'tutorial',
                'description_de' => 'Tutorial',
                'description_en' => 'Tutorial',
                'default_value_de' => 'Tutorial',
                'default_value_en' => 'Tutorial',
                'category' => 'content',
            ],
            [
                'key' => 'comment',
                'description_de' => 'Kommentar',
                'description_en' => 'Comment',
                'default_value_de' => 'Kommentar',
                'default_value_en' => 'Comment',
                'category' => 'content',
            ],
            [
                'key' => 'board',
                'description_de' => 'Vorstand oder Team',
                'description_en' => 'Board or Team',
                'default_value_de' => 'Vorstand',
                'default_value_en' => 'Board',
                'category' => 'content',
            ],
            [
                'key' => 'team',
                'description_de' => 'Team',
                'description_en' => 'Team',
                'default_value_de' => 'Team',
                'default_value_en' => 'Team',
                'category' => 'content',
            ],
            [
                'key' => 'guide',
                'description_de' => 'Anleitung',
                'description_en' => 'Guide',
                'default_value_de' => 'Anleitung',
                'default_value_en' => 'Guide',
                'category' => 'content',
            ],
            [
                'key' => 'category',
                'description_de' => 'Kategorie',
                'description_en' => 'Category',
                'default_value_de' => 'Kategorie',
                'default_value_en' => 'Category',
                'category' => 'navigation',
            ],
            [
                'key' => 'dashboard',
                'description_de' => 'Dashboard oder Übersicht',
                'description_en' => 'Dashboard or Overview',
                'default_value_de' => 'Dashboard',
                'default_value_en' => 'Dashboard',
                'category' => 'navigation',
            ],
            [
                'key' => 'settings',
                'description_de' => 'Einstellungen',
                'description_en' => 'Settings',
                'default_value_de' => 'Einstellungen',
                'default_value_en' => 'Settings',
                'category' => 'navigation',
            ],
            [
                'key' => 'profile',
                'description_de' => 'Profil',
                'description_en' => 'Profile',
                'default_value_de' => 'Profil',
                'default_value_en' => 'Profile',
                'category' => 'navigation',
            ],
            [
                'key' => 'search',
                'description_de' => 'Suche',
                'description_en' => 'Search',
                'default_value_de' => 'Suche',
                'default_value_en' => 'Search',
                'category' => 'ui',
            ],
            [
                'key' => 'help',
                'description_de' => 'Hilfe',
                'description_en' => 'Help',
                'default_value_de' => 'Hilfe',
                'default_value_en' => 'Help',
                'category' => 'navigation',
            ],
            [
                'key' => 'admin',
                'description_de' => 'Admin oder Verwaltung',
                'description_en' => 'Admin',
                'default_value_de' => 'Admin',
                'default_value_en' => 'Admin',
                'category' => 'navigation',
            ],
        ];

        foreach ($placeholders as $placeholder) {
            PlaceholderDefinition::updateOrCreate(
                ['key' => $placeholder['key']],
                $placeholder
            );
        }
    }
}
