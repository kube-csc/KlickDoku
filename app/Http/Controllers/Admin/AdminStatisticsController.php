<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Guide;
use App\Models\Page;
use App\Models\Rating;
use App\Models\Tutorial;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminStatisticsController extends Controller
{
    /**
     * Admin-Dashboard mit Statistiken.
     */
    public function index()
    {
        // Gesamtzahlen
        $stats = [
            'guides' => Guide::count(),
            'guides_published' => Guide::where('status', 'published')->count(),
            'tutorials' => Tutorial::count(),
            'tutorials_published' => Tutorial::where('status', 'published')->count(),
            'pages' => Page::count(),
            'categories' => Category::count(),
            'users' => User::count(),
            'comments_pending' => Comment::where('status', 'pending')->count(),
            'comments_approved' => Comment::where('status', 'approved')->count(),
            'comments_rejected' => Comment::where('status', 'rejected')->count(),
            'total_views' => Guide::sum('view_count') + Tutorial::sum('view_count'),
        ];

        // Letzte 7 Tage: Kommentare pro Tag
        $commentsLast7Days = Comment::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Letzte 7 Tage: Aufrufe (geschätzt über created_at der Guides)
        $guidesLast7Days = Guide::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top-Anleitungen nach Aufrufen
        $topGuides = Guide::where('status', 'published')
            ->orderBy('view_count', 'desc')
            ->take(5)
            ->get();

        // Top-Tutorials nach Aufrufen
        $topTutorials = Tutorial::where('status', 'published')
            ->orderBy('view_count', 'desc')
            ->take(5)
            ->get();

        // Kategorien mit Anzahl Anleitungen
        $categoriesWithGuides = Category::withCount('guides')
            ->orderBy('guides_count', 'desc')
            ->get();

        // Letzte Kommentare
        $recentComments = Comment::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Durchschnittliche Bewertung
        $avgRating = Rating::avg('rating') ?? 0;

        return view('admin.dashboard', compact(
            'stats',
            'commentsLast7Days',
            'guidesLast7Days',
            'topGuides',
            'topTutorials',
            'categoriesWithGuides',
            'recentComments',
            'avgRating'
        ));
    }
}
