@extends('admin.layouts.app')

@section('title', 'Seiten')
@section('heading', 'Seiten')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-gray-600">{{ $pages->count() }} Seiten</p>
    </div>
    <a href="{{ route('admin.pages.create') }}" 
       class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
        + Neue Seite
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titel</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Navigation</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reihenfolge</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aktionen</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($pages as $page)
            <tr>
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ $page->title }}</div>
                    <div class="text-sm text-gray-500">{{ Str::limit($page->content, 60) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $page->slug }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($page->show_in_nav)
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Ja
                    </span>
                    @else
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                        Nein
                    </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $page->sort_order }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="{{ route('admin.pages.edit', $page) }}" 
                       class="text-blue-600 hover:text-blue-900 mr-3">Bearbeiten</a>
                    <form action="{{ route('admin.pages.destroy', $page) }}" 
                          method="POST" 
                          class="inline"
                          onsubmit="return confirm('Sind Sie sicher, dass Sie diese Seite löschen möchten?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Löschen</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                    Keine Seiten gefunden. Erstellen Sie Ihre erste Seite!
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
