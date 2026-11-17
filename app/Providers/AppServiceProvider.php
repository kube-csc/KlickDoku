<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use App\Models\Project;
use App\Policies\ProjectPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Helper-Funktionen für Multi-Projekt-System laden
        require_once __DIR__ . '/../Helpers/ProjectHelper.php';

        // Blade-Directive für Platzhalter-Ersetzung
        // Nutzung: @placeholders($guide->title)
        Blade::directive('placeholders', function ($expression) {
            return "<?php echo replacePlaceholders({$expression}); ?>";
        });

        // Registriere ProjectPolicy
        Gate::policy(Project::class, ProjectPolicy::class);
    }
}
