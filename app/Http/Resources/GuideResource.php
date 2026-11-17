<?php

namespace App\Http\Resources;

use App\Models\Project;
use App\Services\PlaceholderService;
use App\Services\ProjectScreenshotService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuideResource extends JsonResource
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
            'content' => $ps->resolve($this->content, $p),
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
                        'description' => $ps->resolve($step->description, $p),
                        'tip' => $ps->resolve($step->tip, $p),
                        'warning' => $ps->resolve($step->warning, $p),
                    ];
                    // Projekt-spezifische Bilder hinzufügen
                    $images = $this->screenshotService->getGuideStepImages($step, $p);
                    if ($images->isNotEmpty()) {
                        $data['images'] = $images->map(function ($img) use ($p) {
                            $isProjectSpecific = $p && $img instanceof \App\Models\ProjectGuideImage;
                            return [
                                'id' => $img->id,
                                'filename' => $img->filename,
                                'url' => asset($img->path),
                                'caption' => $img->caption,
                                'alt_text' => $img->alt_text,
                                'is_project_specific' => $isProjectSpecific,
                            ];
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
