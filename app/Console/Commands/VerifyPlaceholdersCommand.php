<?php

namespace App\Console\Commands;

use App\Models\Guide;
use App\Models\Project;
use App\Services\PlaceholderService;
use Illuminate\Console\Command;

class VerifyPlaceholdersCommand extends Command
{
    protected $signature = 'docs:verify-placeholders {--project=}';
    protected $description = 'Unersetzte Platzhalter in Inhalten finden';

    public function __construct(
        protected PlaceholderService $placeholderService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $projectSlug = $this->option('project');
        $project = null;

        if ($projectSlug) {
            $project = Project::where('slug', $projectSlug)->first();
            if (!$project) {
                $this->error("Projekt '{$projectSlug}' nicht gefunden.");
                return Command::FAILURE;
            }
        }

        $pattern = '/\{\{(\w+)\}\}/';
        $unresolved = [];

        // Guides prüfen
        $guides = Guide::where('status', 'published')->get();
        if ($project) {
            $guides = $guides->filter(fn($g) => $g->projects->contains($project));
        }

        foreach ($guides as $guide) {
            // Titel und Beschreibung prüfen
            foreach (['title', 'description', 'content'] as $field) {
                $text = $guide->{$field} ?? '';
                if (preg_match_all($pattern, $text, $matches)) {
                    foreach ($matches[1] as $placeholder) {
                        $resolved = $project ? $this->placeholderService->resolve("{{$placeholder}}", $project) : "{{$placeholder}}";
                        if (str_contains($resolved, "{{$placeholder}}")) {
                            $unresolved[] = [
                                'type' => 'guide',
                                'name' => $guide->title,
                                'field' => $field,
                                'placeholder' => $placeholder,
                            ];
                        }
                    }
                }
            }

            // Schritte prüfen
            foreach ($guide->steps as $step) {
                foreach (['title', 'description', 'tip', 'warning'] as $field) {
                    $text = $step->{$field} ?? '';
                    if (preg_match_all($pattern, $text, $matches)) {
                        foreach ($matches[1] as $placeholder) {
                            $resolved = $project ? $this->placeholderService->resolve("{{$placeholder}}", $project) : "{{$placeholder}}";
                            if (str_contains($resolved, "{{$placeholder}}")) {
                                $unresolved[] = [
                                    'type' => 'guide_step',
                                    'name' => $guide->title . ' > ' . $step->title,
                                    'field' => $field,
                                    'placeholder' => $placeholder,
                                ];
                            }
                        }
                    }
                }
            }
        }

        if (empty($unresolved)) {
            $this->info('Alle Platzhalter wurden erfolgreich ersetzt!');
            return Command::SUCCESS;
        }

        $this->warn(count($unresolved) . ' unersetzte Platzhalter gefunden:');
        foreach ($unresolved as $item) {
            $this->line("  - {$item['placeholder']} in {$item['type']}: {$item['name']} ({$item['field']})");
        }

        return Command::WARNING;
    }
}
