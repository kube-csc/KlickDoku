<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TutorialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
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
            'steps' => $this->whenLoaded('steps', function () {
                return $this->steps->map(function ($step) {
                    return [
                        'id' => $step->id,
                        'step_number' => $step->step_number,
                        'title' => $step->title,
                        'content' => $step->content,
                        'hotspots' => $step->whenLoaded('hotspots', function () {
                            return $step->hotspots->map(fn($hs) => [
                                'id' => $hs->id,
                                'x' => $hs->x,
                                'y' => $hs->y,
                                'label' => $hs->label,
                                'description' => $hs->description,
                            ]);
                        }),
                    ];
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
