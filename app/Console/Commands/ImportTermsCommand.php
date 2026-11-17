<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\TermImportService;
use Illuminate\Console\Command;

class ImportTermsCommand extends Command
{
    protected $signature = 'docs:import-terms {--project=} {--source=language_files} {--path=} {--overwrite}';
    protected $description = 'Begriffe aus Hauptprojekt importieren';

    public function __construct(
        protected TermImportService $termImportService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $projectSlug = $this->option('project');
        $source = $this->option('source');
        $overwrite = $this->option('overwrite');

        // Projekt ermitteln
        $project = null;
        if ($projectSlug) {
            $project = Project::where('slug', $projectSlug)->first();
            if (!$project) {
                $this->error("Projekt '{$projectSlug}' nicht gefunden.");
                return Command::FAILURE;
            }
        }

        // Begriffe importieren
        if ($source === 'language_files') {
            $path = $this->option('path') ?? base_path('lang/de');
            $terms = $this->termImportService->importFromLanguageFiles($path);
        } elseif ($source === 'api') {
            $url = $this->ask('API-URL');
            $token = $this->secret('API-Token (optional)');
            $terms = $this->termImportService->importFromApi($url, $token);
        } else {
            $this->error("Unbekannte Quelle: {$source}");
            return Command::FAILURE;
        }

        if (empty($terms)) {
            $this->info('Keine Begriffe gefunden.');
            return Command::SUCCESS;
        }

        // Wenn kein Projekt angegeben, für alle Projekte anwenden
        $projects = $project ? [$project] : Project::all();

        foreach ($projects as $p) {
            $count = $this->termImportService->applyTerms($p, $terms, $overwrite);
            $this->info("Projekt '{$p->name}': {$count} Begriffe importiert.");
        }

        return Command::SUCCESS;
    }
}
