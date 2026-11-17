<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use App\Models\GuideStep;
use App\Models\Project;
use App\Models\ProjectGuideImage;
use App\Models\ProjectTerm;
use App\Models\ProjectTutorialImage;
use App\Models\PlaceholderDefinition;
use App\Models\Tutorial;
use App\Models\TutorialStep;
use App\Models\User;
use App\Services\PlaceholderService;
use App\Services\TermImportService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminProjectController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        protected PlaceholderService $placeholderService,
        protected TermImportService $termImportService
    ) {
    }

    /**
     * Alle Projekte auflisten.
     */
    public function index()
    {
        $projects = Project::withCount(['terms', 'guides', 'tutorials', 'users'])->orderBy('name')->get();
        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Neues Projekt erstellen (Formular).
     */
    public function create()
    {
        $placeholderDefinitions = PlaceholderDefinition::orderBy('key')->get();
        return view('admin.projects.create', compact('placeholderDefinitions'));
    }

    /**
     * Neues Projekt speichern.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:projects,slug',
            'subdomain' => 'nullable|string|max:255|unique:projects,subdomain',
            'domain' => 'nullable|string|max:255',
            'app_name' => 'required|string|max:255',
            'club_name' => 'required|string|max:255',
            'logo_path' => 'nullable|string|max:255',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
        ]);

        if ($validated['subdomain']) {
            $validated['subdomain'] = strtolower($validated['subdomain']);
        }

        $project = Project::create($validated);

        return redirect()->route('admin.projects.edit', $project)
            ->with('success', 'Projekt "' . $project->name . '" erfolgreich erstellt.');
    }

    /**
     * Projekt bearbeiten (Formular).
     */
    public function edit(Project $project)
    {
        $this->authorize('view', $project);
        $project->load(['terms', 'users']);
        $placeholderDefinitions = PlaceholderDefinition::orderBy('key')->get();
        $allUsers = User::orderBy('name')->get();
        return view('admin.projects.edit', compact('project', 'placeholderDefinitions', 'allUsers'));
    }

    /**
     * Projekt aktualisieren.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:projects,slug,' . $project->id,
            'subdomain' => 'nullable|string|max:255|unique:projects,subdomain,' . $project->id,
            'domain' => 'nullable|string|max:255',
            'app_name' => 'required|string|max:255',
            'club_name' => 'required|string|max:255',
            'logo_path' => 'nullable|string|max:255',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
        ]);

        if ($validated['subdomain']) {
            $validated['subdomain'] = strtolower($validated['subdomain']);
        }

        $project->update($validated);

        $redirect = route('admin.projects.edit', $project);
        if ($request->filled('tab')) {
            $redirect .= '?tab=' . urlencode($request->input('tab'));
        }
        return redirect($redirect)
            ->with('success', 'Projekt erfolgreich aktualisiert.');
    }

    /**
     * Projekt löschen.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();

        return redirect()->route('admin.projects.index')
            ->with('success', 'Projekt erfolgreich gelöscht.');
    }

    // ============================================================
    // Begriffe (Terms)
    // ============================================================

    /**
     * Begriffe eines Projekts aktualisieren.
     */
    public function updateTerms(Request $request, Project $project)
    {
        $this->authorize('editTerms', $project);
        $validated = $request->validate([
            'terms' => 'array',
            'terms.*.placeholder_key' => 'required|string',
            'terms.*.value_de' => 'nullable|string|max:255',
            'terms.*.value_en' => 'nullable|string|max:255',
        ]);

        foreach ($validated['terms'] as $termData) {
            ProjectTerm::updateOrCreate(
                [
                    'project_id' => $project->id,
                    'placeholder_key' => $termData['placeholder_key'],
                ],
                [
                    'value_de' => $termData['value_de'] ?? null,
                    'value_en' => $termData['value_en'] ?? null,
                ]
            );
        }

        // Cache invalidieren
        $this->placeholderService->clearProjectCache($project);

        $redirect = route('admin.projects.edit', $project);
        if ($request->filled('tab')) {
            $redirect .= '?tab=' . urlencode($request->input('tab'));
        }
        return redirect($redirect)
            ->with('success', 'Begriffe erfolgreich aktualisiert.');
    }

    /**
     * Begriffe aus Language Files importieren (Vorschlag).
     */
    public function importTerms(Request $request, Project $project)
    {
        $this->authorize('editTerms', $project);
        $source = $request->input('source', 'language_files');

        if ($source === 'language_files') {
            $path = $request->input('lang_path', base_path('lang/de'));
            $terms = $this->termImportService->importFromLanguageFiles($path);
        } else {
            $url = $request->input('api_url');
            $token = $request->input('api_token');
            $terms = $this->termImportService->importFromApi($url, $token);
        }

        return response()->json([
            'terms' => $terms,
            'message' => count($terms) . ' Begriffe importiert.',
        ]);
    }

    /**
     * Vorschläge für Begriffe eines Projekts zurückgeben.
     */
    public function suggestTerms(Project $project)
    {
        $this->authorize('editTerms', $project);
        $suggestions = $this->termImportService->suggestTerms($project);
        return response()->json(['suggestions' => $suggestions]);
    }

    // ============================================================
    // Inhaltsfreigabe (Content Assignment)
    // ============================================================

    /**
     * Guides einem Projekt zuweisen.
     */
    public function assignGuides(Request $request, Project $project)
    {
        $this->authorize('manageContent', $project);
        $validated = $request->validate([
            'guide_ids' => 'array',
            'guide_ids.*' => 'exists:guides,id',
        ]);

        $project->guides()->sync($validated['guide_ids'] ?? []);

        $redirect = route('admin.projects.edit', $project);
        if ($request->filled('tab')) {
            $redirect .= '?tab=' . urlencode($request->input('tab'));
        }
        return redirect($redirect)
            ->with('success', 'Guides erfolgreich zugewiesen.');
    }

    /**
     * Tutorials einem Projekt zuweisen.
     */
    public function assignTutorials(Request $request, Project $project)
    {
        $this->authorize('manageContent', $project);
        $validated = $request->validate([
            'tutorial_ids' => 'array',
            'tutorial_ids.*' => 'exists:tutorials,id',
        ]);

        $project->tutorials()->sync($validated['tutorial_ids'] ?? []);

        $redirect = route('admin.projects.edit', $project);
        if ($request->filled('tab')) {
            $redirect .= '?tab=' . urlencode($request->input('tab'));
        }
        return redirect($redirect)
            ->with('success', 'Tutorials erfolgreich zugewiesen.');
    }

    // ============================================================
    // Projekt-spezifische Screenshots
    // ============================================================

    /**
     * Liste aller Guides mit Schritten für Screenshot-Upload.
     */
    public function getGuideScreenshots(Project $project)
    {
        $this->authorize('manageScreenshots', $project);
        $project->loadMissing('guides');
        $guides = Guide::with(['steps' => function ($query) use ($project) {
            $query->with(['images', 'projectGuideImages' => function ($q) use ($project) {
                $q->where('project_id', $project->id);
            }])->orderBy('step_number');
        }])->whereIn('id', $project->guides->pluck('id'))->get();

        return response()->json(['guides' => $guides]);
    }

    /**
     * Liste aller Tutorials mit Schritten für Screenshot-Upload.
     */
    public function getTutorialScreenshots(Project $project)
    {
        $this->authorize('manageScreenshots', $project);
        $project->loadMissing('tutorials');
        $tutorials = Tutorial::with(['steps' => function ($query) use ($project) {
            $query->with(['projectTutorialImages' => function ($q) use ($project) {
                $q->where('project_id', $project->id);
            }])->orderBy('step_number');
        }])->whereIn('id', $project->tutorials->pluck('id'))->get();

        return response()->json(['tutorials' => $tutorials]);
    }

    /**
     * Screenshot für einen Guide-Schritt hochladen.
     */
    public function storeGuideScreenshot(Request $request, Project $project)
    {
        $this->authorize('manageScreenshots', $project);
        $validated = $request->validate([
            'guide_id' => 'required|exists:guides,id',
            'step_id' => 'required|exists:guide_steps,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'caption' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
        ]);

        // Prüfen, dass der Guide dem Projekt zugewiesen ist
        if (!$project->guides->contains($validated['guide_id'])) {
            return back()->withErrors(['image' => 'Der Guide ist diesem Projekt nicht zugewiesen.']);
        }

        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;
        $path = 'project_screenshots/guides/' . $project->slug . '/' . $filename;

        // Bild komprimieren und speichern
        try {
            $manager = \Intervention\Image\ImageManager::gd();
            $image = $manager->read($file->getPathname());
            $image->scaleDown(1920, null);
            $contents = $image->encodeByExtension($extension, quality: 80);
            \Illuminate\Support\Facades\Storage::disk('public')->put($path, $contents);
        } catch (\Exception $e) {
            // Fallback: Datei direkt speichern
            \Illuminate\Support\Facades\Storage::disk('public')->put($path, file_get_contents($file->getPathname()));
        }

        // Lösche bestehenden Screenshot für diesen Schritt, falls vorhanden
        ProjectGuideImage::where('project_id', $project->id)
            ->where('guide_step_id', $validated['step_id'])
            ->delete();

        ProjectGuideImage::create([
            'project_id' => $project->id,
            'guide_step_id' => $validated['step_id'],
            'filename' => $filename,
            'path' => $path,
            'caption' => $validated['caption'] ?? null,
            'alt_text' => $validated['alt_text'] ?? null,
            'sort_order' => 0,
        ]);

        // Cache invalidieren
        $this->placeholderService->clearProjectCache($project);

        if ($request->headers->get('X-Requested-With') === 'XMLHttpRequest' || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Screenshot erfolgreich hochgeladen.',
                'path' => $path,
            ]);
        }

        $redirect = route('admin.projects.edit', $project);
        if (request()->filled('tab')) {
            $redirect .= '?tab=' . urlencode(request()->input('tab'));
        }
        return redirect($redirect)
            ->with('success', 'Screenshot für Guide-Schritt erfolgreich hochgeladen.');
    }

    /**
     * Screenshot für einen Tutorial-Schritt hochladen.
     */
    public function storeTutorialScreenshot(Request $request, Project $project)
    {
        $this->authorize('manageScreenshots', $project);
        $validated = $request->validate([
            'tutorial_id' => 'required|exists:tutorials,id',
            'step_id' => 'required|exists:tutorial_steps,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        // Prüfen, dass das Tutorial dem Projekt zugewiesen ist
        if (!$project->tutorials->contains($validated['tutorial_id'])) {
            return back()->withErrors(['image' => 'Das Tutorial ist diesem Projekt nicht zugewiesen.']);
        }

        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;
        $path = 'project_screenshots/tutorials/' . $project->slug . '/' . $filename;

        // Bild komprimieren und speichern
        try {
            $manager = \Intervention\Image\ImageManager::gd();
            $image = $manager->read($file->getPathname());
            $image->scaleDown(1920, null);
            $contents = $image->encodeByExtension($extension, quality: 80);
            \Illuminate\Support\Facades\Storage::disk('public')->put($path, $contents);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Storage::disk('public')->put($path, file_get_contents($file->getPathname()));
        }

        // Bildgröße ermitteln
        [$width, $height] = getimagesize($file->getPathname());

        // Lösche bestehenden Screenshot für diesen Schritt, falls vorhanden
        ProjectTutorialImage::where('project_id', $project->id)
            ->where('tutorial_step_id', $validated['step_id'])
            ->delete();

        ProjectTutorialImage::create([
            'project_id' => $project->id,
            'tutorial_step_id' => $validated['step_id'],
            'screenshot_path' => $path,
            'screenshot_width' => $width,
            'screenshot_height' => $height,
        ]);

        // Cache invalidieren
        $this->placeholderService->clearProjectCache($project);

        if ($request->headers->get('X-Requested-With') === 'XMLHttpRequest' || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Screenshot erfolgreich hochgeladen.',
                'path' => $path,
            ]);
        }

        $redirect = route('admin.projects.edit', $project);
        if (request()->filled('tab')) {
            $redirect .= '?tab=' . urlencode(request()->input('tab'));
        }
        return redirect($redirect)
            ->with('success', 'Screenshot für Tutorial-Schritt erfolgreich hochgeladen.');
    }

    /**
     * Screenshot für einen Guide-Schritt löschen.
     */
    public function destroyGuideScreenshot(Project $project, $guideStepId)
    {
        $this->authorize('manageScreenshots', $project);
        $image = ProjectGuideImage::where('project_id', $project->id)
            ->where('guide_step_id', $guideStepId)
            ->first();

        if ($image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($image->path);
            $image->delete();
        }

        // Cache invalidieren
        $this->placeholderService->clearProjectCache($project);

        $redirect = route('admin.projects.edit', $project);
        if (request()->filled('tab')) {
            $redirect .= '?tab=' . urlencode(request()->input('tab'));
        }
        return redirect($redirect)
            ->with('success', 'Screenshot erfolgreich gelöscht.');
    }

    /**
     * Screenshot für einen Tutorial-Schritt löschen.
     */
    public function destroyTutorialScreenshot(Project $project, $tutorialStepId)
    {
        $this->authorize('manageScreenshots', $project);
        $image = ProjectTutorialImage::where('project_id', $project->id)
            ->where('tutorial_step_id', $tutorialStepId)
            ->first();

        if ($image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($image->screenshot_path);
            $image->delete();
        }

        // Cache invalidieren
        $this->placeholderService->clearProjectCache($project);

        $redirect = route('admin.projects.edit', $project);
        if (request()->filled('tab')) {
            $redirect .= '?tab=' . urlencode(request()->input('tab'));
        }
        return redirect($redirect)
            ->with('success', 'Screenshot erfolgreich gelöscht.');
    }

    // ============================================================
    // Begriffe Export/Import (JSON & CSV)
    // ============================================================

    /**
     * Begriffe als JSON-Datei exportieren.
     */
    public function exportTermsJson(Project $project)
    {
        $this->authorize('view', $project);
        $terms = $project->terms->sortBy('placeholder_key')->mapWithKeys(function ($term) {
            return [$term->placeholder_key => [
                'value_de' => $term->value_de,
                'value_en' => $term->value_en,
            ]];
        })->toArray();

        $data = [
            'project' => $project->name,
            'slug' => $project->slug,
            'exported_at' => now()->toISOString(),
            'terms' => $terms,
        ];

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $project->slug . '-terms.json', [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Begriffe als CSV-Datei exportieren.
     */
    public function exportTermsCsv(Project $project)
    {
        $this->authorize('view', $project);
        return response()->streamDownload(function () use ($project) {
            $handle = fopen('php://output', 'w');
            
            // UTF-8 BOM für Excel-Kompatibilität
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // Header
            fputcsv($handle, ['Platzhalter', 'Deutsch', 'English'], ';');
            
            // Daten
            foreach ($project->terms->sortBy('placeholder_key') as $term) {
                fputcsv($handle, [$term->placeholder_key, $term->value_de ?? '', $term->value_en ?? ''], ';');
            }
            
            fclose($handle);
        }, $project->slug . '-terms.csv', [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }

    /**
     * Begriffe aus JSON-Datei importieren.
     */
    public function importTermsJson(Request $request, Project $project)
    {
        $this->authorize('editTerms', $project);
        $validated = $request->validate([
            'file' => 'required|file|mimes:json|max:1024',
        ]);

        $content = file_get_contents($validated['file']);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors(['file' => 'Die JSON-Datei ist ungültig.']);
        }

        if (!isset($data['terms']) || !is_array($data['terms'])) {
            return back()->withErrors(['file' => 'Die JSON-Datei hat das falsche Format. Erwartet: {"terms": {...}}']);
        }

        $count = 0;
        foreach ($data['terms'] as $key => $values) {
            if (!is_array($values)) {
                continue;
            }

            ProjectTerm::updateOrCreate(
                [
                    'project_id' => $project->id,
                    'placeholder_key' => $key,
                ],
                [
                    'value_de' => $values['value_de'] ?? null,
                    'value_en' => $values['value_en'] ?? null,
                ]
            );
            $count++;
        }

        // Cache invalidieren
        $this->placeholderService->clearProjectCache($project);

        $redirect = route('admin.projects.edit', $project);
        if ($request->filled('tab')) {
            $redirect .= '?tab=' . urlencode($request->input('tab'));
        }
        return redirect($redirect)
            ->with('success', "$count Begriffe aus JSON-Datei importiert.");
    }

    /**
     * Begriffe aus CSV-Datei importieren.
     */
    public function importTermsCsv(Request $request, Project $project)
    {
        $this->authorize('editTerms', $project);
        $validated = $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:1024',
        ]);

        $handle = fopen($validated['file'], 'r');
        if ($handle === false) {
            return back()->withErrors(['file' => 'Die CSV-Datei konnte nicht geöffnet werden.']);
        }

        // UTF-8 BOM entfernen (Excel-Kompatibilität)
        $bom = fread($handle, 3);
        if ($bom !== chr(0xEF) . chr(0xBB) . chr(0xBF)) {
            rewind($handle);
        }

        // Header lesen
        $header = fgetcsv($handle, 0, ';');
        if ($header === false || count($header) < 2) {
            fclose($handle);
            return back()->withErrors(['file' => 'Die CSV-Datei hat das falsche Format. Erwartet: Platzhalter;Deutsch;English']);
        }

        $count = 0;
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (count($row) < 2 || empty($row[0])) {
                continue;
            }

            $key = trim($row[0]);
            $valueDe = trim($row[1] ?? '');
            $valueEn = trim($row[2] ?? '');

            ProjectTerm::updateOrCreate(
                [
                    'project_id' => $project->id,
                    'placeholder_key' => $key,
                ],
                [
                    'value_de' => $valueDe ?: null,
                    'value_en' => $valueEn ?: null,
                ]
            );
            $count++;
        }
        fclose($handle);

        // Cache invalidieren
        $this->placeholderService->clearProjectCache($project);

        $redirect = route('admin.projects.edit', $project);
        if ($request->filled('tab')) {
            $redirect .= '?tab=' . urlencode($request->input('tab'));
        }
        return redirect($redirect)
            ->with('success', "$count Begriffe aus CSV-Datei importiert.");
    }

    // ============================================================
    // User-Zuweisungen (Many-to-Many)
    // ============================================================

    /**
     * Users einem Projekt zuweisen.
     * Nur gecheckte Users werden hinzugefügt/aktualisiert.
     * Bereits zugewiesene Users werden NICHT entfernt – das geht nur über den "Entfernen"-Button.
     */
    public function assignUsers(Request $request, Project $project)
    {
        $this->authorize('manageUsers', $project);
        $validated = $request->validate([
            'users' => 'nullable|array',
            'users.*.user_id' => 'integer|exists:users,id',
            'users.*.checked' => 'nullable|integer',
            'users.*.role' => 'string|in:admin,editor,viewer',
        ]);

        // Nur die gecheckten Users hinzufügen/aktualisieren, bestehende nicht entfernen
        $sync = [];
        if (!empty($validated['users'])) {
            foreach ($validated['users'] as $userData) {
                if (!empty($userData['checked'])) {
                    $sync[$userData['user_id']] = ['role' => $userData['role'] ?? 'viewer'];
                }
            }
        }

        // syncWithoutDetaching: fügt neue hinzu und aktualisiert bestehende, entfernt nichts
        $project->users()->syncWithoutDetaching($sync);

        $count = count($sync);
        $redirect = route('admin.projects.edit', $project);
        if ($request->filled('tab')) {
            $redirect .= '?tab=' . urlencode($request->input('tab'));
        }
        return redirect($redirect)
            ->with('success', "$count User(s) dem Projekt zugewiesen.");
    }

    /**
     * User aus Projekt entfernen.
     */
    public function removeUser(Project $project, User $user)
    {
        $this->authorize('manageUsers', $project);
        $project->users()->detach($user->id);

        $redirect = route('admin.projects.edit', $project);
        if (request()->filled('tab')) {
            $redirect .= '?tab=' . urlencode(request()->input('tab'));
        }
        return redirect($redirect)
            ->with('success', '"' . $user->name . '" aus dem Projekt entfernt.');
    }
}
