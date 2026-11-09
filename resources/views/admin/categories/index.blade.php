@extends('admin.layouts.app')

@section('title', 'Kategorien')
@section('heading', 'Kategorien')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-gray-600">{{ $categories->count() }} Kategorien</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" 
       class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
        + Neue Kategorie
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Anleitungen</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tutorials</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reihenfolge</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aktionen</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($categories as $category)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                    @if($category->description)
                    <div class="text-sm text-gray-500">{{ Str::limit($category->description, 50) }}</div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->slug }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->guides_count }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->tutorials_count }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->sort_order }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="{{ route('admin.categories.edit', $category) }}" 
                       class="text-blue-600 hover:text-blue-900 mr-3">Bearbeiten</a>
                    @if($category->guides_count == 0 && $category->tutorials_count == 0)
                    <form action="{{ route('admin.categories.destroy', $category) }}" 
                          method="POST" 
                          class="inline"
                          onsubmit="return confirm('Sind Sie sicher, dass Sie diese Kategorie löschen möchten?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Löschen</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    Keine Kategorien gefunden. Erstellen Sie Ihre erste Kategorie!
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
