<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;

/**
 * DefaultSeeder – Legt die minimalen Stammdaten für eine funktionierende Installation an.
 * 
 * Dieser Seeder läuft bei jeder Installation automatisch und erstellt:
 * - Einen Admin-User zum ersten Login
 * - Basis-Kategorien für Guides und Tutorials
 * - Basis-Tags für die Inhaltskategorisierung
 * 
 * Alle Daten werden nur erstellt, wenn noch keine existieren (idempotent).
 */
class DefaultSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nur ausführen, wenn noch keine Users existieren
        if (User::count() > 0) {
            return;
        }

        // 1. Admin-User erstellen
        User::create([
            'name' => 'Admin',
            'email' => 'admin@klickdoku.local',
            'password' => Hash::make('admin'),
            'email_verified_at' => now(),
        ]);

        // 2. Basis-Kategorien erstellen
        $categories = [
            [
                'name' => 'Allgemein',
                'slug' => 'allgemein',
                'description' => 'Allgemeine Anleitungen und Informationen',
            ],
            [
                'name' => 'Mitglieder',
                'slug' => 'mitglieder',
                'description' => 'Mitgliederverwaltung und -pflege',
            ],
            [
                'name' => 'Veranstaltungen',
                'slug' => 'veranstaltungen',
                'description' => 'Veranstaltungsplanung und -management',
            ],
            [
                'name' => 'Einstellungen',
                'slug' => 'einstellungen',
                'description' => 'Systemeinstellungen und Konfiguration',
            ],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // 3. Basis-Tags erstellen
        $tags = [
            'Einsteiger',
            'Fortgeschritten',
            'Mitglieder',
            'Veranstaltungen',
            'Einstellungen',
            'Wichtig',
        ];

        foreach ($tags as $name) {
            Tag::create([
                'name' => $name,
                'slug' => str($name)->slug(),
            ]);
        }
    }
}
