<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectGuideImage;
use App\Models\ProjectTutorialImage;
use App\Models\GuideStep;
use App\Models\TutorialStep;
use Illuminate\Support\Collection;

class ProjectScreenshotService
{
    /**
     * Gibt die korrekten Bilder für einen Guide-Schritt zurück.
     * Wenn projekt-spezifische Bilder existieren, werden diese verwendet.
     * Andernfalls werden die Master-Bilder als Fallback genutzt.
     *
     * @param GuideStep $step Der Guide-Schritt
     * @param Project|null $project Das aktuelle Projekt (optional)
     * @return Collection Die Bilder für diesen Schritt
     */
    public function getGuideStepImages(GuideStep $step, ?Project $project = null): Collection
    {
        if ($project) {
            $projectImages = ProjectGuideImage::where('guide_step_id', $step->id)
                ->where('project_id', $project->id)
                ->orderBy('sort_order')
                ->get();

            if ($projectImages->isNotEmpty()) {
                return $projectImages;
            }
        }

        // Fallback: Master-Bilder
        return $step->images;
    }

    /**
     * Gibt den korrekten Screenshot für einen Tutorial-Schritt zurück.
     * Wenn ein projekt-spezifischer Screenshot existiert, wird dieser verwendet.
     * Andernfalls wird der Master-Screenshot als Fallback genutzt.
     *
     * @param TutorialStep $step Der Tutorial-Schritt
     * @param Project|null $project Das aktuelle Projekt (optional)
     * @return string|null Der Pfad zum Screenshot
     */
    public function getTutorialStepScreenshot(TutorialStep $step, ?Project $project = null): ?string
    {
        if ($project) {
            $projectImage = ProjectTutorialImage::where('tutorial_step_id', $step->id)
                ->where('project_id', $project->id)
                ->first();

            if ($projectImage && $projectImage->screenshot_path) {
                return $projectImage->screenshot_path;
            }
        }

        // Fallback: Master-Screenshot
        return $step->screenshot_path;
    }

    /**
     * Gibt die Screenshot-Abmessungen für einen Tutorial-Schritt zurück.
     *
     * @param TutorialStep $step Der Tutorial-Schritt
     * @param Project|null $project Das aktuelle Projekt (optional)
     * @return array ['width' => int|null, 'height' => int|null]
     */
    public function getTutorialStepScreenshotDimensions(TutorialStep $step, ?Project $project = null): array
    {
        if ($project) {
            $projectImage = ProjectTutorialImage::where('tutorial_step_id', $step->id)
                ->where('project_id', $project->id)
                ->first();

            if ($projectImage) {
                return [
                    'width' => $projectImage->screenshot_width,
                    'height' => $projectImage->screenshot_height,
                ];
            }
        }

        // Fallback: Master-Abmessungen
        return [
            'width' => $step->screenshot_width,
            'height' => $step->screenshot_height,
        ];
    }
}
