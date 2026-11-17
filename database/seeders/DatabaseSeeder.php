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
        $this->call([
            PlaceholderDefinitionsSeeder::class,
            DemoProjectsSeeder::class,
            DemoContentSeeder::class,
            DemoSeeder::class,
        ]);
    }
}
