@extends('admin.layouts.app')

@section('title', 'Anleitungen')
@section('heading', 'Anleitungen')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-gray-600">{{ $guides->total() }} Anleitungen</p>
    </div>
    <a href="{{ route('admin.guides.create') }}" 
       class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
        + Neue Anleitung
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
            @forelse($guides as $guide)
            <tr>
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ $guide->title }}</div>
                    <div class="text-sm text-gray-500">{{ Str::limit($guide->description, 60) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $guide->category->name ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($guide->published)
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
                    {{ $guide->created_at->format('d.m.Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="{{ route('admin.guides.edit', $guide) }}" 
                       class="text-blue-600 hover:text-blue-900 mr-3">Bearbeiten</a>
                    <form action="{{ route('admin.guides.destroy', $guide) }}" 
                          method="POST" 
                          class="inline"
                          onsubmit="return confirm('Sind Sie sicher, dass Sie diese Anleitung löschen möchten?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Löschen</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                    Keine Anleitungen gefunden. Erstellen Sie Ihre erste Anleitung!
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $guides->links() }}
</div>
@endsection
