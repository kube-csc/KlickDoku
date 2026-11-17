<?php

namespace App\Console\Commands;

use App\Models\PlaceholderDefinition;
use Illuminate\Console\Command;

class SeedPlaceholdersCommand extends Command
{
    protected $signature = 'docs:seed-placeholders';
    protected $description = 'Platzhalter-Katalog initial befüllen';

    public function handle(): int
    {
        $this->call('db:seed', ['--class' => 'PlaceholderDefinitionsSeeder', '--force' => true]);
        $this->info('Platzhalter-Katalog wurde befüllt.');
        return Command::SUCCESS;
    }
}
