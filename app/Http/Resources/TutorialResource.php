<?php

namespace App\Http\Resources;

use App\Models\Project;
use App\Services\PlaceholderService;
use App\Services\ProjectScreenshotService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TutorialResource extends JsonResource
{
    protected ?Project $project;
    protected PlaceholderService $placeholderService;
    protected ProjectScreenshotService $screenshotService;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->project = request()->attributes->get('project');
        $this->placeholderService = app(PlaceholderService::class);
        $this->screenshotService = app(ProjectScreenshotService::class);
    }

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $ps = $this->placeholderService;
        $p = $this->project;

        return [
            'id' => $this->id,
            'title' => $ps->resolve($this->title, $p),
            'slug' => $this->slug,
            'description' => $ps->resolve($this->description, $p),
            'estimated_minutes' => $this->estimated_minutes,
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ];
            }),
            'author' => $this->whenLoaded('author', function () {
                return [
                    'id' => $this->author->id,
                    'name' => $this->author->name,
                ];
            }),
            'steps' => $this->whenLoaded('steps', function () use ($ps, $p) {
                return $this->steps->map(function ($step) use ($ps, $p) {
                    $data = [
                        'id' => $step->id,
                        'step_number' => $step->step_number,
                        'title' => $ps->resolve($step->title, $p),
                        'instruction' => $ps->resolve($step->instruction, $p),
                    ];
                    // Projekt-spezifischer Screenshot
                    $screenshotPath = $this->screenshotService->getTutorialStepScreenshot($step, $p);
                    if ($screenshotPath) {
                        $data['screenshot'] = asset($screenshotPath);
                        $data['screenshot_is_project_specific'] = (bool)$p;
                    }
                    // Hotspots mit Platzhalter-Ersetzung und Domain-Rewriting
                    if ($step->relationLoaded('hotspots')) {
                        $data['hotspots'] = $step->hotspots->map(function ($hs) use ($ps, $p) {
                            $hotspotData = [
                                'id' => $hs->id,
                                'x' => $hs->x,
                                'y' => $hs->y,
                                'label' => $ps->resolve($hs->label, $p),
                                'link' => $ps->resolve($hs->link, $p),
                                'description' => $ps->resolve($hs->description, $p),
                            ];
                            // Domain-Rewriting: Quelldomain durch Projekt-Domain ersetzen
                            $hotspotData['link'] = $ps->rewriteDomain(
                                $hotspotData['link'],
                                $p?->source_domain,
                                $ps->getProjectTargetDomain($p)
                            );
                            return $hotspotData;
                        });
                    }
                    return $data;
                });
            }),
            'tags' => $this->whenLoaded('tags', function () {
                return $this->tags->map(fn($tag) => $tag->name);
            }),
            'view_count' => $this->view_count,
            'published_at' => $this->published_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
