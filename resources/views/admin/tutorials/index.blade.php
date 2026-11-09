@extends('admin.layouts.app')

@section('title', 'Tutorials')
@section('heading', 'Tutorials')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-gray-600">{{ $tutorials->total() }} Tutorials</p>
    </div>
    <a href="{{ route('admin.tutorials.create') }}" 
       class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition">
        + Neues Tutorial
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titel</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategorie</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Datum</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aktionen</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($tutorials as $tutorial)
            <tr>
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ $tutorial->title }}</div>
                    <div class="text-sm text-gray-500">{{ Str::limit($tutorial->description, 60) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $tutorial->category->name ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($tutorial->published)
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Veröffentlicht
                    </span>
                    @else
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        Entwurf
                    </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $tutorial->created_at->format('d.m.Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="{{ route('admin.tutorials.edit', $tutorial) }}" 
                       class="text-blue-600 hover:text-blue-900 mr-3">Bearbeiten</a>
                    <form action="{{ route('admin.tutorials.destroy', $tutorial) }}" 
                          method="POST" 
                          class="inline"
                          onsubmit="return confirm('Sind Sie sicher, dass Sie dieses Tutorial löschen möchten?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Löschen</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                    Keine Tutorials gefunden. Erstellen Sie Ihr erstes Tutorial!
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $tutorials->links() }}
</div>
@endsection
