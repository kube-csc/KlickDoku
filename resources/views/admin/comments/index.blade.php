@extends('admin.layouts.app')

@section('title', 'Kommentare moderieren')
@section('heading', 'Kommentare moderieren')

@section('content')
<!-- Statistiken -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-orange-500 rounded-md p-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-5">
                <p class="text-gray-500 text-sm">Ausstehend</p>
                <p class="text-2xl font-semibold text-gray-800">
                    {{ $comments->where('status', 'pending')->count() }}
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="ml-5">
                <p class="text-gray-500 text-sm">Genehmigt</p>
                <p class="text-2xl font-semibold text-gray-800">
                    {{ $comments->where('status', 'approved')->count() }}
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div class="ml-5">
                <p class="text-gray-500 text-sm">Abgelehnt</p>
                <p class="text-2xl font-semibold text-gray-800">
                    {{ $comments->where('status', 'rejected')->count() }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Kommentare-Liste -->
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b">
        <h3 class="text-lg font-semibold text-gray-800">Alle Kommentare ({{ $comments->total() }})</h3>
    </div>

    @if($comments->count() > 0)
        <div class="divide-y">
            @foreach($comments as $comment)
                <div class="p-6 hover:bg-gray-50 transition">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center font-bold text-sm">
                            {{ substr($comment->user->name ?? '?', 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <span class="font-semibold text-gray-900">{{ $comment->user->name ?? 'Unbekannt' }}</span>
                                <span class="text-sm text-gray-500">{{ $comment->user->email ?? '' }}</span>
                                <span class="text-sm text-gray-400">{{ $comment->created_at->format('d.m.Y, H:i') }}</span>
                                @if($comment->status === 'pending')
                                    <span class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded-full text-xs font-medium">Ausstehend</span>
                                @elseif($comment->status === 'approved')
                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">Genehmigt</span>
                                @else
                                    <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-medium">Abgelehnt</span>
                                @endif
                                @if($comment->parent_id)
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs">Antwort auf #{{ $comment->parent_id }}</span>
                                @endif
                            </div>
                            <p class="text-gray-700 mb-3">{{ $comment->body }}</p>
                            
                            @if($comment->commentable)
                                <p class="text-sm text-gray-500 mb-3">
                                    Zu: <a href="{{ route('guides.show', ['locale' => app()->getLocale(), 'slug' => $comment->commentable->slug]) }}" class="text-blue-600 hover:underline">
                                        {{ $comment->commentable->title }}
                                    </a>
                                </p>
                            @endif

                            <div class="flex items-center gap-2">
                                @if($comment->status !== 'approved')
                                    <form action="{{ route('admin.comments.approve', $comment->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition">
                                            Genehmigen
                                        </button>
                                    </form>
                                @endif
                                @if($comment->status !== 'rejected')
                                    <form action="{{ route('admin.comments.reject', $comment->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition">
                                            Ablehnen
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('comments.destroy', ['locale' => app()->getLocale(), 'comment' => $comment->id]) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Kommentar wirklich löschen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300 transition">
                                        Löschen
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="p-6 border-t">
            {{ $comments->links() }}
        </div>
    @else
        <div class="p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Keine Kommentare</h3>
            <p class="mt-1 text-sm text-gray-500">Es sind noch keine Kommentare vorhanden.</p>
        </div>
    @endif
</div>
@endsection
