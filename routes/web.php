<?php

use App\Http\Controllers\GuideController;
use App\Http\Controllers\TutorialController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminGuideController;
use App\Http\Controllers\Admin\AdminTutorialController;
use App\Http\Controllers\Admin\AdminPageController;
use App\Http\Controllers\Admin\AdminImageController;
use App\Http\Controllers\Admin\AdminStatisticsController;
use App\Http\Controllers\Admin\AdminTranslationController;
use App\Http\Controllers\Admin\AdminStepTranslationController;
use App\Http\Controllers\Api\ApiController;
use App\Models\TutorialStep;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

// Supported locales
// ACHTUNG: Bei Hinzufügen einer dritten Sprache (z. B. 'fr' für Französisch)
// sind nur folgende Schritte erforderlich:
//   1. Sprachcode in config/app.php (supported_locales) und SetLocale.php Middleware hinzufügen
//   2. Übersetzungsdateien in lang/{code}/ erstellen (z. B. lang/fr/navigation.php)
//   3. Übersetzungen in content_translations-Tabelle anlegen
// Kein weiterer Code-Änderungen notwendig!
$locales = config('app.supported_locales', ['de', 'en']);

// Startseite (ohne Locale-Präfix – Sprachauswahl)
Route::get('/', function () {
    return view('language-select');
})->name('home');

// Öffentliche Routen mit Sprach-Präfix
Route::prefix('{locale}')->where(['locale' => implode('|', $locales)])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('home.locale');

    Route::get('/anleitungen', [GuideController::class, 'index'])->name('guides.index');
    Route::get('/anleitungen/{slug}/pdf', [GuideController::class, 'pdf'])->where(['slug' => '.*'])->name('guides.pdf');
    Route::get('/anleitungen/{slug}', [GuideController::class, 'show'])->where(['slug' => '.*'])->name('guides.show');

    Route::get('/tutorials', [TutorialController::class, 'index'])->name('tutorials.index');
    Route::get('/tutorials/{slug}', [TutorialController::class, 'show'])->where(['slug' => '.*'])->name('tutorials.show');

    Route::get('/seite/{slug}', [PageController::class, 'show'])->where(['slug' => '.*'])->name('pages.show');

    Route::get('/suche', [SearchController::class, 'search'])->name('search');

    // Kommentare (authentifizierte Benutzer können kommentieren)
    Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
        Route::post('/anleitungen/{guide:slug}/kommentare', [CommentController::class, 'store'])
            ->where(['guide' => '.*'])
            ->name('guides.comments.store');
        Route::delete('/kommentare/{comment}', [CommentController::class, 'destroy'])
            ->name('comments.destroy');
    });

    // Authentifizierte Routen für Tutorial-Fortschritt
    Route::middleware([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
    ])->group(function () {
        Route::post('/tutorials/{slug}/progress', [TutorialController::class, 'updateProgress'])
            ->where(['slug' => '.*'])
            ->name('tutorials.progress');
        Route::post('/tutorials/{slug}/complete', [TutorialController::class, 'complete'])
            ->where(['slug' => '.*'])
            ->name('tutorials.complete');
    });
});

// Dashboard (ohne Sprach-Präfix – Jetstream Login-Redirect Ziel)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Admin-Panel (ohne Sprach-Präfix, immer auf der App-Sprache)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminStatisticsController::class, 'index'])->name('dashboard');

    // Kategorien
    Route::resource('categories', AdminCategoryController::class);

    // Anleitungen
    Route::resource('guides', AdminGuideController::class);

    // Tutorials
    Route::resource('tutorials', AdminTutorialController::class);

    // Seiten
    Route::resource('pages', AdminPageController::class);

    // Bild-Upload
    Route::post('guides/{guide:id}/images/step', [AdminImageController::class, 'storeGuideImage'])
        ->name('guides.images.step.store');
    Route::post('guides/{guide:id}/images', [AdminImageController::class, 'storeGuideGlobalImage'])
        ->name('guides.images.store');
    Route::delete('guide-images/{image}', [AdminImageController::class, 'destroyGuideImage'])
        ->name('guide.images.destroy');
    Route::post('guides/{guide:id}/screenshot', [AdminImageController::class, 'storeGuideScreenshot'])
        ->name('guides.screenshot.store');
    Route::post('tutorials/{tutorial}/screenshot', [AdminImageController::class, 'storeTutorialScreenshot'])
        ->name('tutorials.screenshot.store');
    Route::delete('tutorials/{tutorial}/steps/{step}/screenshot', [AdminImageController::class, 'destroyTutorialScreenshot'])
        ->name('tutorials.screenshot.destroy');

    // Kommentar-Moderation
    Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
    Route::patch('/comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');
    Route::patch('/comments/{comment}/reject', [CommentController::class, 'reject'])->name('comments.reject');

    // Übersetzungsverwaltung
    Route::get('/translations', [AdminTranslationController::class, 'index'])->name('translations.index');
    Route::get('/translations/{type}', [AdminTranslationController::class, 'show'])->name('translations.show');
    Route::post('/translations/{type}/{id}/store', [AdminTranslationController::class, 'store'])->name('translations.store');
    Route::delete('/translations/{type}/{id}/{locale}/destroy', [AdminTranslationController::class, 'destroy'])->name('translations.destroy');

    // Schritt-Übersetzungen
    Route::post('guides/{guide}/steps/{stepId}/translations', [AdminStepTranslationController::class, 'storeGuideStep'])
        ->name('guide.step.translations.store');
    Route::delete('guides/{guide}/steps/{stepId}/translations/{locale}', [AdminStepTranslationController::class, 'destroyGuideStep'])
        ->name('guide.step.translations.destroy');
    Route::post('tutorials/{tutorial}/steps/{stepId}/translations', [AdminStepTranslationController::class, 'storeTutorialStep'])
        ->name('tutorial.step.translations.store');
    Route::delete('tutorials/{tutorial}/steps/{stepId}/translations/{locale}', [AdminStepTranslationController::class, 'destroyTutorialStep'])
        ->name('tutorial.step.translations.destroy');
});

// API-Endpunkte (öffentlich, JSON)
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/guides', [ApiController::class, 'guides'])->name('guides');
    Route::get('/guides/{guide:slug}', [ApiController::class, 'guideShow'])->name('guides.show');
    Route::get('/tutorials', [ApiController::class, 'tutorials'])->name('tutorials');
    Route::get('/tutorials/{tutorial:slug}', [ApiController::class, 'tutorialShow'])->name('tutorials.show');
    Route::get('/categories', [ApiController::class, 'categories'])->name('categories');
    Route::get('/search', [ApiController::class, 'search'])->name('search');
});

// Health Check (öffentlich, für Monitoring und Load Balancer)
Route::get('/health', [HealthController::class, 'check'])->name('health.check');
Route::get('/ping', [HealthController::class, 'ping'])->name('health.ping');
