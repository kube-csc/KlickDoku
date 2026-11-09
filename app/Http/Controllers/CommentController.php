<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Guide;
use App\Notifications\NewCommentNotification;
use App\Notifications\CommentApprovedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    // Kommentare auf einer Anleitung speichern (für authentifizierte Benutzer)
    public function store(string $locale, Request $request, Guide $guide)
    {
        $validated = $request->validate([
            'body' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        // Wenn Reply, muss der Parent zur Anleitung gehören
        if ($validated['parent_id']) {
            $parent = Comment::findOrFail($validated['parent_id']);
            if ($parent->commentable_id !== $guide->id || $parent->commentable_type !== Guide::class) {
                return redirect()->back()
                    ->with('error', 'Ungültiger Kommentar für die Antwort.');
            }
        }

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'commentable_id' => $guide->id,
            'commentable_type' => Guide::class,
            'parent_id' => $validated['parent_id'] ?? null,
            'body' => $validated['body'],
            'status' => 'pending', // Muss vom Admin genehmigt werden
        ]);

        // Admins benachrichtigen
        $admins = \App\Models\User::where('email', 'like', '%@verein.local')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewCommentNotification($comment));
        }

        return redirect()->back()
            ->with('success', 'Kommentar gesendet und wartet auf Genehmigung.');
    }

    // Admin: Alle Kommentare auflisten
    public function index()
    {
        $comments = Comment::with(['user', 'parent', 'replies'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.comments.index', compact('comments'));
    }

    // Admin: Kommentar genehmigen
    public function approve(string $locale, Comment $comment)
    {
        $comment->update(['status' => 'approved']);

        // Kommentator benachrichtigen
        $comment->user->notify(new CommentApprovedNotification($comment));

        return redirect()->back()
            ->with('success', 'Kommentar genehmigt.');
    }

    // Admin: Kommentar ablehnen
    public function reject(string $locale, Comment $comment)
    {
        $comment->update(['status' => 'rejected']);

        return redirect()->back()
            ->with('success', 'Kommentar abgelehnt.');
    }

    // Kommentar löschen (nur vom Autor oder Admin)
    public function destroy(string $locale, Comment $comment)
    {
        if (auth()->id() !== $comment->user_id && !auth()->user()->isAdmin) {
            abort(403);
        }

        $guideId = $comment->commentable_id;
        $comment->delete();

        // Wenn es ein Guide-Kommentar war, zur Anleitung zurück
        if ($comment->commentable_type === Guide::class) {
            $guide = Guide::find($guideId);
            if ($guide) {
                return redirect()->route('guides.show', $guide->slug)
                    ->with('success', 'Kommentar gelöscht.');
            }
        }

        return redirect()->back()
            ->with('success', 'Kommentar gelöscht.');
    }
}
