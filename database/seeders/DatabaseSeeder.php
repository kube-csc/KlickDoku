<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Zuerst die minimalen Stammdaten (Admin-User, Kategorien, Tags)
        // Dieser Seeder läuft immer und ist idempotent – erstellt nur bei leerer Datenbank
        $this->call(DefaultSeeder::class);

        // 2. Multi-Projekt-System: Platzhalter-Katalog (immer nötig)
        $this->call(PlaceholderDefinitionsSeeder::class);

        // 3. Demo-Daten: Nur wenn die Umgebungsvariable DOCS_DEMO=true gesetzt ist
        // Ohne diese Variable werden keine Demo-Projekte, -Guides oder -Tutorials erstellt
        $demoMode = filter_var(env('DOCS_DEMO', false), FILTER_VALIDATE_BOOLEAN);
        if ($demoMode) {
            $this->call([
                DemoProjectsSeeder::class,
                DemoContentSeeder::class,
                DemoSeeder::class,
            ]);
        }
    }
}
